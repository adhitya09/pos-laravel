<?php

namespace App\Http\Controllers;

use App\Models\CashboxFlow;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);

        $transactions = Transaction::with(['paymentMethod', 'transactionItems.product'])
            ->latest('transaction_date')
            ->paginate($perPage)
            ->withQueryString();

        return view('pages.transaksi.index', compact('transactions'));
    }

    public function show(Transaction $transaksi)
    {
        $transaksi->load(['paymentMethod', 'transactionItems.product']);
        return view('pages.transaksi.show', compact('transaksi'));
    }

    public function destroy(Transaction $transaksi)
    {
        // Return stock to products
        foreach ($transaksi->transactionItems as $item) {
            $product = $item->product ?? Product::withTrashed()->find($item->product_id);
            if ($product) {
                $product->increment('stock', $item->quantity);
            }
        }

        // Remove auto-generated cash flow
        CashboxFlow::where('reference_id', $transaksi->id)
            ->where('reference_type', 'transaction')
            ->where('is_auto', true)
            ->delete();

        $transaksi->delete();

        return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil dibatalkan');
    }
}
