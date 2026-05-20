<?php

namespace App\Http\Controllers;

use App\Models\CashboxFlow;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\PaymentMethod;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);

        $transactions = Transaction::with('paymentMethod')
            ->latest('transaction_date')
            ->paginate($perPage)
            ->withQueryString();

        // Provide products and payment methods for modal usage
        $products = Product::select('id', 'name', 'sku', 'price', 'cost_price', 'stock')->get();
        $paymentMethods = PaymentMethod::where('is_active', true)->get();

        return view('pages.transaksi.index', compact('transactions', 'products', 'paymentMethods'));
    }

    public function create()
    {
        $products = Product::select('id', 'name', 'sku', 'price', 'cost_price', 'stock')->get();
        $paymentMethods = PaymentMethod::where('is_active', true)->get();
        return view('pages.transaksi.create', compact('products', 'paymentMethods'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'paid_amount' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $total = 0;
            $itemsData = [];
            foreach ($validated['items'] as $it) {
                $product = Product::findOrFail($it['product_id']);
                if ($product->stock < $it['quantity']) {
                    return back()->withInput()->withErrors(['items' => "Stok {$product->name} tidak mencukupi"]);
                }
                $subtotal = $it['price'] * $it['quantity'];
                $total += $subtotal;
                $itemsData[] = [
                    'product' => $product,
                    'quantity' => $it['quantity'],
                    'price' => $it['price'],
                    'subtotal' => $subtotal,
                ];
            }

            if ($validated['paid_amount'] < $total) {
                return back()->withInput()->withErrors(['paid_amount' => 'Jumlah bayar kurang dari total']);
            }

            $invoiceNo = 'INV-' . date('Ymd') . '-' . str_pad(
                Transaction::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT
            );

            $transaction = Transaction::create([
                'invoice_no' => $invoiceNo,
                'customer_name' => $validated['customer_name'] ?? null,
                'payment_method_id' => $validated['payment_method_id'],
                'total_amount' => $total,
                'paid_amount' => $validated['paid_amount'],
                'change_amount' => $validated['paid_amount'] - $total,
                'status' => 'completed',
                'transaction_date' => now(),
            ]);

            foreach ($itemsData as $d) {
                $transaction->transactionItems()->create([
                    'product_id' => $d['product']->id,
                    'quantity' => $d['quantity'],
                    'price' => $d['price'],
                    'subtotal' => $d['subtotal'],
                ]);
                $d['product']->decrement('stock', $d['quantity']);
            }

            DB::commit();

            return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil dibuat');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function edit(Transaction $transaksi)
    {
        $transaksi->load(['transactionItems.product', 'paymentMethod']);
        $products = Product::select('id', 'name', 'sku', 'price', 'cost_price', 'stock', 'image')->get();
        $paymentMethods = PaymentMethod::where('is_active', true)->get();
        return view('pages.transaksi.edit', compact('transaksi', 'products', 'paymentMethods'));
    }

    public function update(Request $request, Transaction $transaksi)
    {
        $validated = $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'paid_amount' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // revert stock from existing items
            foreach ($transaksi->transactionItems as $existing) {
                $prod = $existing->product ?? Product::withTrashed()->find($existing->product_id);
                if ($prod) {
                    $prod->increment('stock', $existing->quantity);
                }
            }

            $transaksi->transactionItems()->delete();

            $total = 0;
            foreach ($validated['items'] as $it) {
                $product = Product::findOrFail($it['product_id']);
                if ($product->stock < $it['quantity']) {
                    DB::rollBack();
                    return back()->withInput()->withErrors(['items' => "Stok {$product->name} tidak mencukupi"]);
                }
                $subtotal = $it['price'] * $it['quantity'];
                $total += $subtotal;
                $transaksi->transactionItems()->create([
                    'product_id' => $product->id,
                    'quantity' => $it['quantity'],
                    'price' => $it['price'],
                    'subtotal' => $subtotal,
                ]);
                $product->decrement('stock', $it['quantity']);
            }

            if ($validated['paid_amount'] < $total) {
                DB::rollBack();
                return back()->withInput()->withErrors(['paid_amount' => 'Jumlah bayar kurang dari total']);
            }

            $transaksi->update([
                'customer_name' => $validated['customer_name'] ?? null,
                'payment_method_id' => $validated['payment_method_id'],
                'total_amount' => $total,
                'paid_amount' => $validated['paid_amount'],
                'change_amount' => $validated['paid_amount'] - $total,
            ]);

            DB::commit();
            return redirect()->route('transaksi.show', $transaksi->id)->with('success', 'Transaksi berhasil diperbarui');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show(Transaction $transaksi)
    {
        $transaksi->load(['paymentMethod', 'transactionItems.product']);
        return view('pages.transaksi.show', compact('transaksi'));
    }

    public function destroy(Transaction $transaksi)
    {
        if (in_array($transaksi->status, ['returned', 'cancelled'], true)) {
            return back()->withErrors(['message' => 'Transaksi sudah dibatalkan atau dikembalikan sebelumnya.']);
        }

        DB::beginTransaction();
        try {
            foreach ($transaksi->transactionItems as $item) {
                $product = $item->product ?? Product::withTrashed()->find($item->product_id);
                if ($product) {
                    $product->increment('stock', $item->quantity);
                }
            }

            CashboxFlow::where('reference_id', $transaksi->id)
                ->where('reference_type', 'transaction')
                ->where('is_auto', true)
                ->delete();

            $transaksi->update(['status' => 'cancelled']);

            DB::commit();
            return redirect()->route('transaksi.index')->with('success', 'Transaksi berhasil dibatalkan');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function downloadPdf(Transaction $transaksi)
    {
        $transaksi->load(['paymentMethod', 'transactionItems.product']);

        $totalProfit = $transaksi->transactionItems->sum(function ($item) {
            return ($item->price - ($item->product->cost_price ?? 0)) * $item->quantity;
        });

        $data = compact('transaksi', 'totalProfit');

        return Pdf::loadView('pages.transaksi.pdf', $data)
            ->download('transaksi-' . $transaksi->invoice_no . '.pdf');
    }

    public function returnTransaction(Transaction $transaksi)
    {
        if (in_array($transaksi->status, ['returned', 'cancelled'], true)) {
            return back()->withErrors(['message' => 'Transaksi sudah tidak dapat di-return.']);
        }

        if ($transaksi->status !== 'completed') {
            return back()->withErrors(['message' => 'Hanya transaksi yang completed dapat di-return.']);
        }

        DB::beginTransaction();
        try {
            foreach ($transaksi->transactionItems as $item) {
                $product = $item->product ?? Product::withTrashed()->find($item->product_id);
                if ($product) {
                    $product->increment('stock', $item->quantity);
                }
            }

            $transaksi->update(['status' => 'returned']);

            DB::commit();

            return redirect()->route('transaksi.show', $transaksi->id)->with('success', 'Transaksi berhasil di-return');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
