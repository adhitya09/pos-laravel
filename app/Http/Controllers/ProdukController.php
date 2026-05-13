<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Milon\Barcode\DNS1D;

class ProdukController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status', 'all');
        $perPage = (int) $request->get('per_page', 10);
        $query = Product::with('category')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('sku', 'like', "%{$search}%")
                      ->orWhere('barcode', 'like', "%{$search}%");
                });
            })
            ->when(in_array($status, ['plenty', 'banyak']), fn ($query) => $query->where('stock', '>', 10))
            ->when(in_array($status, ['low', 'sedikit']), fn ($query) => $query->whereBetween('stock', [1, 10]))
            ->when(in_array($status, ['out', 'habis']), fn ($query) => $query->where('stock', 0))
            ->orderBy('name');

        $products = $query->paginate($perPage)->withQueryString();

        $stats = [
            'all' => Product::count(),
            'plenty' => Product::where('stock', '>', 10)->count(),
            'low' => Product::whereBetween('stock', [1, 10])->count(),
            'out' => Product::where('stock', 0)->count(),
        ];

        return view('pages.produk.index', compact('products', 'search', 'status', 'stats'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('pages.produk.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'nullable|string|unique:products,sku',
            'barcode' => 'nullable|string|unique:products,barcode',
            'cost_price' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'is_active' => 'sometimes|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only([
            'name',
            'description',
            'sku',
            'barcode',
            'cost_price',
            'price',
            'stock',
            'category_id',
            'is_active',
        ]);

        if (empty($data['sku'])) {
            $data['sku'] = $this->generateSku($request->name);
        }

        if (empty($data['barcode'])) {
            $data['barcode'] = $this->generateBarcode();
        }

        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);

        if ($request->has('create_another')) {
            return redirect()->route('produk.create')->with('success', 'Produk berhasil ditambahkan. Anda dapat membuat produk baru lagi.');
        }

        return redirect()->route('produk.index')->with('success', 'Produk berhasil ditambahkan');
    }

    public function edit(Product $produk)
    {
        $categories = Category::all();
        return view('pages.produk.edit', compact('produk', 'categories'));
    }

    public function update(Request $request, Product $produk)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sku' => 'nullable|string|unique:products,sku,' . $produk->id,
            'barcode' => 'nullable|string|unique:products,barcode,' . $produk->id,
            'cost_price' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'is_active' => 'sometimes|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only([
            'name',
            'description',
            'sku',
            'barcode',
            'cost_price',
            'price',
            'stock',
            'category_id',
            'is_active',
        ]);

        if (empty($data['sku'])) {
            $data['sku'] = $this->generateSku($request->name);
        }

        if (empty($data['barcode'])) {
            $data['barcode'] = $produk->barcode ?: $this->generateBarcode();
        }

        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $produk->update($data);

        return redirect()->route('produk.index')->with('success', 'Produk berhasil diupdate');
    }

    public function show(Product $produk)
    {
        return view('pages.produk.show', compact('produk'));
    }

    public function destroy(Product $produk)
    {
        $produk->delete();
        return redirect()->route('produk.index')->with('success', 'Produk berhasil dihapus');
    }

    private function generateSku(string $name): string
    {
        $cleanName = preg_replace('/[^A-Za-z0-9\s]/', '', strtoupper($name));
        $parts = preg_split('/\s+/', trim($cleanName));

        $segments = [];
        foreach (array_slice($parts, 0, 3) as $part) {
            $segments[] = substr($part, 0, min(3, strlen($part)));
        }

        while (count($segments) < 3) {
            $segments[] = 'XX';
        }

        $suffix = rand(10, 99);

        return implode('-', $segments) . '-' . $suffix;
    }

    private function generateBarcode(): string
    {
        do {
            $code = str_pad((string) mt_rand(0, 999999999999), 12, '0', STR_PAD_LEFT);
        } while (Product::where('barcode', $code)->exists());

        return $code;
    }

    /**
     * Cetak barcode semua produk aktif
     */
    public function cetakBarcode()
    {
        $products = Product::whereNotNull('barcode')
            ->where('barcode', '!=', '')
            ->where('is_active', true)
            ->get();

        $pdf = Pdf::loadView('pages.produk.barcode-pdf', [
            'products' => $products,
            'copies' => 45,
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('barcodes.pdf');
    }

    /**
     * Cetak barcode produk yang dipilih
     */
    public function cetakBarcodeSelected(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:products,id',
        ]);

        $products = Product::whereIn('id', $request->product_ids)
            ->whereNotNull('barcode')
            ->where('barcode', '!=', '')
            ->get();

        if ($products->isEmpty()) {
            return back()->with('error', 'Produk yang dipilih tidak memiliki barcode.');
        }

        if ($products->count() === 1) {
            $filename = 'barcode_' . str_replace(' ', '_', $products->first()->name) . '.pdf';
        } else {
            $filename = 'barcodes_selected_' . now()->format('YmdHis') . '.pdf';
        }

        $pdf = Pdf::loadView('pages.produk.barcode-pdf', [
            'products' => $products,
            'copies' => 45,
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download($filename);
    }

    /**
     * Bulk delete produk yang dipilih
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:products,id',
        ]);

        $count = Product::whereIn('id', $request->product_ids)->count();
        Product::whereIn('id', $request->product_ids)->delete();

        return redirect()->route('produk.index')
            ->with('success', $count . ' produk berhasil dihapus.');
    }

    /**
     * Reset stok produk yang dipilih ke 0
     */
    public function resetStok(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:products,id',
        ]);

        Product::whereIn('id', $request->product_ids)->update(['stock' => 0]);

        return redirect()->route('produk.index')
            ->with('success', 'Stok berhasil direset ke 0.');
    }
}
