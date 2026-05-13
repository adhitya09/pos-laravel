<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;
use App\Models\CashFlow;
use App\Models\CashboxFlow;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        // Sales report - last 30 days
        $salesReport = Transaction::select(
                DB::raw('DATE(transaction_date) as date'),
                DB::raw('COUNT(*) as total_transactions'),
                DB::raw('SUM(total_amount) as total_sales')
            )
            ->where('transaction_date', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Product stock report
        $productReport = Product::with('category')
            ->select('name', 'stock', 'price', 'category_id')
            ->orderBy('stock', 'asc')
            ->get();

        // Cash flow report
        $cashFlowReport = CashboxFlow::select(
                'type',
                DB::raw('SUM(amount) as total_amount')
            )
            ->where('date', '>=', now()->subDays(30))
            ->groupBy('type')
            ->get();

        // Recent transactions
        $recentTransactions = Transaction::with('paymentMethod')
            ->latest()
            ->limit(10)
            ->get();

        return view('pages.report.index', compact(
            'totalPenjualan',
            'totalTransaksi',
            'produkTerjual',
            'cashFlowIn',
            'cashFlowOut',
            'salesReport',
            'productReport',
            'cashFlowReport',
            'recentTransactions'
        ));
    }

    public function exportPdf(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year  = $request->get('year',  now()->year);

        $totalPenjualan   = Transaction::whereMonth('created_at', $month)
                              ->whereYear('created_at', $year)->sum('total_amount');
        $totalTransaksi   = Transaction::whereMonth('created_at', $month)
                              ->whereYear('created_at', $year)->count();
        $produkTerjual    = TransactionItem::whereMonth('created_at', $month)
                              ->whereYear('created_at', $year)->sum('quantity');
        $cashFlowIn       = CashboxFlow::where('type','in')
                              ->whereMonth('date', $month)->whereYear('date', $year)
                              ->sum('amount');
        $cashFlowOut      = CashboxFlow::where('type','out')
                              ->whereMonth('date', $month)->whereYear('date', $year)
                              ->sum('amount');
        $recentTransactions = Transaction::with('paymentMethod')
                              ->whereMonth('created_at', $month)
                              ->whereYear('created_at', $year)
                              ->latest()->get();
        $cashFlows        = CashboxFlow::with('source')
                              ->whereMonth('date', $month)->whereYear('date', $year)
                              ->latest()->get();
        $setting          = Setting::first();
        $bulan            = $month;
        $tahun            = $year;

        $pdf = Pdf::loadView('pages.report.laporan-pdf', compact(
            'totalPenjualan','totalTransaksi','produkTerjual',
            'cashFlowIn','cashFlowOut','recentTransactions',
            'cashFlows','setting','bulan','tahun'
        ))->setPaper('a4', 'portrait');

        return $pdf->download('laporan-keuangan-' . $year . '-' . $month . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $month    = $request->get('month', now()->month);
        $year     = $request->get('year',  now()->year);
        $filename = 'laporan-keuangan-' . $year . '-' . $month . '.xlsx';
        return Excel::download(
            new LaporanKeuanganExport($month, $year),
            $filename
        );
    }
}
