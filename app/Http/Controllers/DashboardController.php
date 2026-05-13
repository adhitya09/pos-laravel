<?php

namespace App\Http\Controllers;

use App\Models\CashboxFlow;
use App\Models\CashFlow;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $range = $request->get('range', 'today');
        [$startDate, $endDate] = $this->rangeDates($range, $request);

        $transactions = Transaction::with('paymentMethod')
            ->whereBetween('created_at', [$startDate, $endDate]);

        $totalPenjualan = $transactions->sum('total_amount');
        $totalTransaksi = $transactions->count();

        $cashMethodIds = PaymentMethod::where('is_cash', true)->pluck('id');
        $cashSales = (clone $transactions)->whereIn('payment_method_id', $cashMethodIds)->sum('total_amount');

        // Cashbox flows (manual entries)
        $cashboxIn = CashboxFlow::where('type', 'in')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');
        $cashboxOut = CashboxFlow::where('type', 'out')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        // Legacy cash_flows table used by some parts (inventory/outflows)
        $legacyIn = CashFlow::where('type', 'IN')
            ->whereBetween('flow_date', [$startDate, $endDate])
            ->sum('amount');
        $legacyOut = CashFlow::where('type', 'OUT')
            ->whereBetween('flow_date', [$startDate, $endDate])
            ->sum('amount');

        // Modal (initial + additional) from cashbox sources and legacy cash_flows
        $totalModal = CashboxFlow::whereHas('source', function ($q) {
                $q->whereIn('name', ['Modal Awal', 'Modal Tambahan']);
            })->whereBetween('date', [$startDate, $endDate])->sum('amount')
            + CashFlow::whereIn('source', ['Modal Awal', 'Modal Tambahan'])
                ->whereBetween('flow_date', [$startDate, $endDate])->sum('amount');

        $totalUangMasuk = $cashSales + $cashboxIn + $legacyIn;
        $totalUangKeluar = $cashboxOut + $legacyOut;
        $totalUangToko = $totalModal + $totalUangMasuk - $totalUangKeluar;

        // compatibility variables for view
        $cashInflow = $totalUangMasuk;
        $cashOutflow = $totalUangKeluar;

        $totalProduk = Product::count();

        $produkTerlaris = DB::table('transaction_items')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->join('products', 'products.id', '=', 'transaction_items.product_id')
            ->whereBetween('transactions.created_at', [$startDate, $endDate])
            ->select('products.name', DB::raw('SUM(transaction_items.quantity) as total_quantity'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        $stockStatus = Product::orderBy('stock', 'asc')
            ->limit(5)
            ->get(['name', 'stock']);

        $transactionsByMethod = DB::table('transactions')
            ->leftJoin('payment_methods', 'transactions.payment_method_id', '=', 'payment_methods.id')
            ->whereBetween('transactions.created_at', [$startDate, $endDate])
            ->groupBy('payment_methods.name')
            ->select('payment_methods.name as method_name', DB::raw('COUNT(transactions.id) as total'))
            ->get();

        $transactionsByMethod = $transactionsByMethod->map(function ($item) {
            return [
                'name' => $item->method_name ?: 'Lainnya',
                'total' => $item->total,
            ];
        });

        $transactionsByMethodCount = $transactionsByMethod->sum('total');

        return view('pages.dashboard', compact(
            'range',
            'totalPenjualan',
            'totalTransaksi',
            'cashInflow',
            'cashOutflow',
            'totalUangMasuk',
            'totalUangKeluar',
            'totalUangToko',
            'totalProduk',
            'produkTerlaris',
            'stockStatus',
            'transactionsByMethod',
            'transactionsByMethodCount'
        ));
    }

    protected function rangeDates(string $range, Request $request): array
    {
        $today = Carbon::today();

        return match ($range) {
            'week' => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'month' => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            'year' => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
            default => [$today->copy()->startOfDay(), $today->copy()->endOfDay()],
        };
    }
}
