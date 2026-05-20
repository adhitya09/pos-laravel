<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryItem;
use App\Models\Product;
use App\Models\CashFlow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 10);
        $search = $request->get('search');

        $query = Inventory::with('inventoryItems.product')
            ->when($search, function ($q) use ($search) {
                $q->where('reference_no', 'like', "%{$search}%")
                  ->orWhere('source', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('inventoryItems.product', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            })
            ->latest();

        $inventories = $query->paginate($perPage)->withQueryString();
        $products = Product::all();

        return view('pages.inventory.index', compact('inventories', 'search', 'products'));
    }

    public function create()
    {
        $products = Product::all();
        return view('pages.inventory.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:in,out,adjustment',
            'source' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.cost_price' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $totalCost = 0;

            $inventory = Inventory::create([
                'reference_no' => 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
                'type' => $request->type,
                'source' => $request->source,
                'notes' => $request->notes,
                'inventory_date' => now(),
                'total_modal' => 0,
            ]);

            foreach ($request->items as $item) {
                $costPrice = $item['cost_price'] ?? 0;
                $quantity = $item['quantity'];

                InventoryItem::create([
                    'inventory_id' => $inventory->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $quantity,
                    'cost_price' => $costPrice,
                ]);

                $product = Product::findOrFail($item['product_id']);
                $itemTotal = $costPrice * $quantity;
                $totalCost += $itemTotal;

                if ($request->type === 'in') {
                    $product->increment('stock', $quantity);
                } elseif ($request->type === 'out') {
                    if ($product->stock < $quantity) {
                        throw new \Exception("Stock {$product->name} tidak mencukupi");
                    }
                    $product->decrement('stock', $quantity);
                } elseif ($request->type === 'adjustment') {
                    $product->stock = $quantity;
                    $product->save();
                }
            }

            $inventory->update(['total_modal' => $totalCost]);

            if ($request->type === 'out' && $totalCost > 0) {
                CashFlow::create([
                    'type' => 'OUT',
                    'amount' => $totalCost,
                    'source' => 'Pembelian Inventory',
                    'reference_id' => $inventory->id,
                    'reference_type' => 'inventory',
                    'description' => "Inventory {$inventory->reference_no}",
                    'flow_date' => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('inventory.index')->with('success', 'Inventory berhasil disimpan');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show(Inventory $inventory)
    {
        $inventory->load('inventoryItems.product');
        return view('pages.inventory.show', compact('inventory'));
    }

    public function edit(Inventory $inventory)
    {
        $products = Product::all();
        $inventory->load('inventoryItems');
        return view('pages.inventory.edit', compact('inventory', 'products'));
    }

    public function update(Request $request, Inventory $inventory)
    {
        // Similar logic to store, but for updates
        // This would be more complex as it needs to reverse previous changes
        return redirect()->route('inventory.index')->with('info', 'Update inventory belum diimplementasikan');
    }

    public function destroy(Inventory $inventory)
    {
        // Reverse stock changes before deleting
        foreach ($inventory->inventoryItems as $item) {
            $product = $item->product;
            if ($inventory->type === 'in') {
                $product->decrement('stock', $item->quantity);
            } elseif ($inventory->type === 'out') {
                $product->increment('stock', $item->quantity);
            }
        }

        $inventory->delete();
        return redirect()->route('inventory.index')->with('success', 'Inventory berhasil dihapus');
    }
}
