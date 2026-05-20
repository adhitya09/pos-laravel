<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\CashboxFlow;
use App\Models\Setting;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanKeuanganExport;

class ReportController extends Controller
{
    public function index()
    {
        // Monthly statistics
        $totalPenjualan = Transaction::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        $totalTransaksi = Transaction::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $produkTerjual = TransactionItem::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('quantity');

        $cashFlowIn = CashboxFlow::where('type', 'in')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');

        $cashFlowOut = CashboxFlow::where('type', 'out')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');

        $recentTransactions = Transaction::with('paymentMethod')
            ->latest()
            ->limit(10)
            ->get();

        $reports = Report::orderByDesc('created_at')->get();

        return view('pages.report.index', compact(
            'totalPenjualan',
            'totalTransaksi',
            'produkTerjual',
            'cashFlowIn',
            'cashFlowOut',
            'recentTransactions',
            'reports'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in([
                Report::TYPE_INCOME,
                Report::TYPE_EXPENSE,
                Report::TYPE_SALES,
            ])],
            'from_date' => ['required', 'date'],
            'to_date' => ['required', 'date', 'after_or_equal:from_date'],
        ]);

        Report::create([
            'type' => $validated['type'],
            'from_date' => $validated['from_date'],
            'to_date' => $validated['to_date'],
            'name' => Report::createReportName($validated['type'], $validated['from_date'], $validated['to_date']),
            'code' => Report::generateReportCode($validated['type']),
        ]);

        $redirect = redirect()->route('report.index')->with('success', 'Laporan berhasil dibuat.');

        if ($request->has('create_another')) {
            $redirect->with('open_create_modal', true);
        }

        return $redirect;
    }

    public function update(Request $request, Report $report)
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in([
                Report::TYPE_INCOME,
                Report::TYPE_EXPENSE,
                Report::TYPE_SALES,
            ])],
            'from_date' => ['required', 'date'],
            'to_date' => ['required', 'date', 'after_or_equal:from_date'],
        ]);

        $report->update([
            'type' => $validated['type'],
            'from_date' => $validated['from_date'],
            'to_date' => $validated['to_date'],
            'name' => Report::createReportName($validated['type'], $validated['from_date'], $validated['to_date']),
        ]);

        return redirect()->route('report.index')->with('success', 'Laporan berhasil disimpan.');
    }

    public function exportPdf(Request $request)
    {
        $from = $request->get('from') ?? $request->get('from_date');
        $to = $request->get('to') ?? $request->get('to_date');
        $periode = null;
        $month = null;
        $year = null;

        if ($from && $to) {
            $fromDate = Carbon::parse($from)->startOfDay();
            $toDate = Carbon::parse($to)->endOfDay();
            $periode = sprintf('%s - %s', $fromDate->translatedFormat('d F Y'), $toDate->translatedFormat('d F Y'));
            $month = $fromDate->month;
            $year = $fromDate->year;

            $transactionQuery = Transaction::whereBetween('transaction_date', [$fromDate, $toDate]);
            $cashboxQuery = CashboxFlow::whereBetween('date', [$fromDate, $toDate]);
        } else {
            $month = $request->get('month', now()->month);
            $year  = $request->get('year', now()->year);
            $fromDate = Carbon::create($year, $month, 1)->startOfDay();
            $toDate = $fromDate->copy()->endOfMonth()->endOfDay();
            $periode = Carbon::create($year, $month)->translatedFormat('F Y');

            $transactionQuery = Transaction::whereMonth('transaction_date', $month)
                ->whereYear('transaction_date', $year);
            $cashboxQuery = CashboxFlow::whereMonth('date', $month)
                ->whereYear('date', $year);
        }

        $totalPenjualan = (clone $transactionQuery)->sum('total_amount');
        $totalTransaksi = (clone $transactionQuery)->count();

        // Sum quantity from transaction_items using relationship to transactions
        $produkTerjual = TransactionItem::whereHas('transaction', function ($q) use ($fromDate, $toDate) {
            $q->whereBetween('transaction_date', [$fromDate, $toDate]);
        })->sum('quantity');

        $cashFlowIn = (clone $cashboxQuery)
            ->where('type', 'in')
            ->sum('amount');

        $cashFlowOut = (clone $cashboxQuery)
            ->where('type', 'out')
            ->sum('amount');

        $recentTransactions = (clone $transactionQuery)
            ->with('paymentMethod')
            ->latest()
            ->get();

        $cashFlows = (clone $cashboxQuery)
            ->with('source')
            ->latest()
            ->get();

        $setting = Setting::first();

        $pdf = Pdf::loadView('pages.report.laporan-pdf', compact(
            'totalPenjualan',
            'totalTransaksi',
            'produkTerjual',
            'cashFlowIn',
            'cashFlowOut',
            'recentTransactions',
            'cashFlows',
            'setting',
            'periode',
            'month',
            'year'
        ))->setPaper('a4', 'portrait');

        $fileName = sprintf(
            'laporan-keuangan-%s-%s.pdf',
            now()->format('YmdHis'),
            str_replace(' ', '-', strtolower($periode))
        );

        return $pdf->download($fileName);
    }

    public function exportExcel(Request $request)
    {
        $month    = $request->get('month', now()->month);
        $year     = $request->get('year', now()->year);
        $filename = 'laporan-keuangan-' . $year . '-' . $month . '.xlsx';
        return Excel::download(
            new LaporanKeuanganExport($month, $year),
            $filename
        );
    }
}
