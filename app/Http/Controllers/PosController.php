<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\PaymentMethod;
use App\Models\CashboxFlow;
use App\Models\CashFlowSource;
use App\Models\Category;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PosController extends Controller
{
    public function index()
    {
        $products = Product::where('is_active', true)->where('stock', '>', 0)->get();
        $paymentMethods = PaymentMethod::where('is_active', true)->get();
        $categories = Category::whereHas('products', function($q) {
            $q->where('is_active', true)->where('stock', '>', 0);
        })->get();

        return view('pages.pos.index', compact('products', 'paymentMethods', 'categories'));
    }

    /**
     * Get product by barcode for scanner
     */
    public function getProductByBarcode(Request $request)
    {
        $request->validate(['barcode' => 'required|string']);

        $product = Product::where('barcode', $request->barcode)
                          ->where('stock', '>', 0)
                          ->where('is_active', true)
                          ->first();

        if (!$product) {
            return response()->json(['found' => false, 'message' => 'Produk tidak ditemukan atau stok habis.']);
        }

        return response()->json([
            'found'   => true,
            'product' => [
                'id'        => $product->id,
                'name'      => $product->name,
                'price'     => $product->price,
                'stock'     => $product->stock,
                'image_url' => $product->image_url,
            ],
        ]);
    }

    public function scanBarcode(Request $request)
    {
        $barcode = $request->get('barcode');
        if (!$barcode) {
            return response()->json(['found' => false, 'message' => 'Barcode kosong.']);
        }

        $product = Product::where('barcode', $barcode)
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->first();

        if (!$product) {
            return response()->json([
                'found'   => false,
                'message' => 'Produk dengan barcode "' . $barcode . '" tidak ditemukan.',
            ]);
        }

        return response()->json([
            'found'   => true,
            'product' => [
                'id'    => $product->id,
                'name'  => $product->name,
                'price' => $product->price,
                'stock' => $product->stock,
                'sku'   => $product->sku,
            ],
        ]);
    }

    /**
     * Print receipt for a transaction
     */
    public function cetakResi(Transaction $transaction)
    {
        $transaction->load(['transactionItems.product', 'paymentMethod']);
        $setting = Setting::first();
        return view('pages.pos.resi', compact('transaction', 'setting'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items'             => 'required|array|min:1',
            'items.*.product_id'=> 'required|exists:products,id',
            'items.*.quantity'  => 'required|integer|min:1',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'paid_amount'       => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $total = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                if ($product->stock < $item['quantity']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Stok ' . $product->name . ' tidak mencukupi.',
                    ], 422);
                }
                $subtotal = $product->price * $item['quantity'];
                $total   += $subtotal;
                $itemsData[] = [
                    'product'  => $product,
                    'quantity' => $item['quantity'],
                    'price'    => $product->price,
                    'subtotal' => $subtotal,
                ];
            }

            if ($request->paid_amount < $total) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Jumlah bayar kurang dari total.',
                ], 422);
            }

            $invoiceNo = 'INV-' . date('Ymd') . '-' . str_pad(
                Transaction::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT
            );

            $transaction = Transaction::create([
                'invoice_no'        => $invoiceNo,
                'customer_name'     => $request->customer_name ?? null,
                'payment_method_id' => $request->payment_method_id,
                'total_amount'      => $total,
                'paid_amount'       => $request->paid_amount,
                'change_amount'     => $request->paid_amount - $total,
                'status'            => 'completed',
                'transaction_date'  => now(),
            ]);

            foreach ($itemsData as $d) {
                $transaction->transactionItems()->create([
                    'product_id' => $d['product']->id,
                    'quantity'   => $d['quantity'],
                    'price'      => $d['price'],
                    'subtotal'   => $d['subtotal'],
                ]);
                $d['product']->decrement('stock', $d['quantity']);
            }

            DB::commit();

            return response()->json([
                'success'        => true,
                'transaction_id' => $transaction->id,
                'invoice_no'     => $transaction->invoice_no,
                'change'         => $transaction->change_amount,
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('POS store error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine() . '\nTrace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.',
            ], 500);
        }
    }
}
