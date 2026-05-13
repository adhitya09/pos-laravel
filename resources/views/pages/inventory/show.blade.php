@extends('layouts.app')

@section('title', 'Detail Inventory')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Detail Inventory</h1>
        <a href="{{ route('inventory.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
            Kembali
        </a>
    </div>

    <!-- Inventory Header -->
    <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">No. Referensi</label>
                <p class="text-gray-900 dark:text-white font-medium">{{ $inventory->reference_no }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipe</label>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    @if($inventory->type === 'in') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                    @elseif($inventory->type === 'out') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                    @else bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 @endif">
                    {{ ucfirst($inventory->type) }}
                </span>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal</label>
                <p class="text-gray-900 dark:text-white font-medium">{{ $inventory->inventory_date->format('d M Y, H:i') }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                    Selesai
                </span>
            </div>
        </div>

        @if($inventory->notes)
        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Catatan</label>
            <p class="text-gray-600 dark:text-gray-400">{{ $inventory->notes }}</p>
        </div>
        @endif
    </div>

    <!-- Inventory Items -->
    <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Detail Item</h3>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-3 px-4 font-semibold text-gray-900 dark:text-white">Produk</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-900 dark:text-white">Jumlah</th>
                            @if($inventory->type === 'in' || $inventory->type === 'out')
                            <th class="text-left py-3 px-4 font-semibold text-gray-900 dark:text-white">Harga Pokok</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-900 dark:text-white">Total</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventory->inventoryItems as $item)
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <td class="py-3 px-4 text-gray-900 dark:text-white">{{ $item->product->name }}</td>
                            <td class="py-3 px-4 text-gray-900 dark:text-white">{{ $item->quantity }}</td>
                            @if($inventory->type === 'in' || $inventory->type === 'out')
                            <td class="py-3 px-4 text-gray-900 dark:text-white">Rp {{ number_format($item->cost_price ?? 0, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-gray-900 dark:text-white">Rp {{ number_format(($item->cost_price ?? 0) * $item->quantity, 0, ',', '.') }}</td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ ($inventory->type === 'in' || $inventory->type === 'out') ? 4 : 2 }}" class="py-8 px-4 text-center text-gray-500 dark:text-gray-400">
                                Tidak ada item
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if(($inventory->type === 'in' || $inventory->type === 'out') && $inventory->inventoryItems->count() > 0)
                    <tfoot>
                        <tr class="border-t border-gray-200 dark:border-gray-700">
                            <td colspan="3" class="py-3 px-4 text-right font-semibold text-gray-900 dark:text-white">Total:</td>
                            <td class="py-3 px-4 font-semibold text-gray-900 dark:text-white">
                                Rp {{ number_format($inventory->inventoryItems->sum(function($item) { return ($item->cost_price ?? 0) * $item->quantity; }), 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
