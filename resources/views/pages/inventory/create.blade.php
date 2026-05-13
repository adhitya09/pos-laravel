@extends('layouts.app')

@section('title', 'Buat Inventory')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <p class="text-sm text-slate-500 dark:text-slate-400">Inventory</p>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Buat inventory</h1>
        </div>
        <a href="{{ route('inventory.index') }}" class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
            Kembali
        </a>
    </div>

    @if($errors->any())
        <div class="rounded-3xl bg-rose-50 p-4 text-sm text-rose-700 shadow-sm dark:bg-rose-950 dark:text-rose-200">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('inventory.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="rounded-3xl bg-white p-6 shadow-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-900">
            <div class="grid gap-6 lg:grid-cols-[1.5fr_1fr]">
                <div class="space-y-6">
                    <div>
                        <p class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Tipe Stok</p>
                        <div class="grid grid-cols-3 gap-3">
                            <button type="button" id="type-in" onclick="selectType('in')" class="rounded-2xl border border-slate-300 dark:border-gray-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-4 py-3 text-sm font-semibold transition">Masuk</button>
                            <button type="button" id="type-out" onclick="selectType('out')" class="rounded-2xl border border-slate-300 dark:border-gray-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-4 py-3 text-sm font-semibold transition">Keluar</button>
                            <button type="button" id="type-adjustment" onclick="selectType('adjustment')" class="rounded-2xl border border-slate-300 dark:border-gray-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white px-4 py-3 text-sm font-semibold transition">Penyesuaian</button>
                        </div>
                        <input type="hidden" name="type" id="inventory-type" value="{{ old('type', 'in') }}">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Sumber *</label>
                        <select name="source" required class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:border-gray-700 dark:bg-slate-800 dark:text-white">
                            <option value="">Pilih salah satu opsi</option>
                            <option value="Penambahan Stock" {{ old('source') === 'Penambahan Stock' ? 'selected' : '' }}>Penambahan Stock</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Total Modal *</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-500">Rp</span>
                            <input type="text" id="total-modal" value="0" readonly class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-12 py-3 text-sm text-slate-500 outline-none dark:border-gray-700 dark:bg-slate-800 dark:text-slate-400" />
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Tanggal Inventory *</label>
                        <input type="date" name="inventory_date" value="{{ old('inventory_date', date('Y-m-d')) }}" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:border-gray-700 dark:bg-slate-800 dark:text-white" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="rounded-3xl bg-white p-6 shadow-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-900">
            <div class="flex items-center justify-between gap-4 mb-6">
                <div>
                    <p class="text-sm font-medium text-slate-700 dark:text-slate-300">Pemilihan Produk</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Pilih produk yang akan ditambahkan/ dikurangi stoknya</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[720px] border-separate border-spacing-y-3">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">
                            <th class="px-4 py-3">Produk *</th>
                            <th class="px-4 py-3">Harga Modal *</th>
                            <th class="px-4 py-3">Stok Saat Ini *</th>
                            <th class="px-4 py-3">Jumlah *</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody id="inventory-items" class="space-y-3">
                    </tbody>
                </table>
            </div>

            <button type="button" onclick="addItem()" class="mt-4 inline-flex items-center rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                Tambahkan inventory items
            </button>
        </div>

        <div class="rounded-3xl bg-white p-6 shadow-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-900">
            <p class="text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Catatan</p>
            <textarea name="notes" rows="4" class="w-full rounded-3xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:border-gray-700 dark:bg-slate-800 dark:text-white" placeholder="Masukkan catatan tambahan">{{ old('notes') }}</textarea>
        </div>

        <div class="flex flex-wrap justify-end gap-3">
            <a href="{{ route('inventory.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-gray-700 dark:text-slate-200 dark:hover:bg-slate-800">
                Batal
            </a>
            <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">
                Buat
            </button>
        </div>
    </form>
</div>

@php
    $productsJson = $products->map(function ($product) {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'stock' => $product->stock,
            'cost_price' => $product->cost_price ?? 0,
        ];
    });
@endphp

<script>
const products = @json($productsJson);

let itemIndex = 0;
const initialItems = @json(old('items', []));
const inventoryTypeInput = document.getElementById('inventory-type');
const totalModalInput = document.getElementById('total-modal');

function selectType(type) {
    inventoryTypeInput.value = type;

    const mapping = {
        'in': 'type-in',
        'out': 'type-out',
        'adjustment': 'type-adjustment'
    };

    const activeId = mapping[type] || 'type-in';
    const btnIds = Object.values(mapping);

    const activeClasses = ['bg-emerald-600', 'text-white', 'border-emerald-600', 'dark:bg-emerald-700', 'dark:text-white'];
    const inactiveClasses = ['bg-white', 'text-slate-900', 'border-slate-300', 'dark:bg-slate-800', 'dark:text-white'];

    btnIds.forEach(id => {
        const btn = document.getElementById(id);
        if (!btn) return;
        if (id === activeId) {
            // remove inactive
            btn.classList.remove(...inactiveClasses);
            // add active
            activeClasses.forEach(c => btn.classList.add(c));
        } else {
            // remove active
            btn.classList.remove(...activeClasses);
            // ensure inactive
            inactiveClasses.forEach(c => {
                if (!btn.classList.contains(c)) btn.classList.add(c);
            });
            // ensure neutral border when inactive
            btn.classList.remove('border-emerald-600');
        }
    });
}

function formatCurrency(value) {
    return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(value);
}

function productOptions(selectedId = null) {
    return ['<option value="">Pilih salah satu opsi</option>', ...products.map(product => {
        const selected = product.id === Number(selectedId) ? 'selected' : '';
        return `<option value="${product.id}" ${selected}>${product.name} (${product.stock}) - ${product.sku}</option>`;
    })].join('');
}

function createItemRow(data = {}) {
    const row = document.createElement('tr');
    row.id = `inventory-item-${itemIndex}`;
    row.className = 'bg-slate-50 dark:bg-slate-800 rounded-3xl';

    const selectedProduct = data.product_id ?? '';
    const productDetails = products.find(p => p.id === Number(selectedProduct));
    const stockValue = productDetails ? productDetails.stock : '';
    const costPriceValue = data.cost_price ?? (productDetails ? productDetails.cost_price : 0);
    const quantityValue = data.quantity ?? 1;

    row.innerHTML = `
        <td class="px-4 py-4 align-top">
            <select name="items[${itemIndex}][product_id]" onchange="onProductChange(${itemIndex}, this.value)" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:border-gray-700 dark:bg-slate-900 dark:text-white" required>
                ${productOptions(selectedProduct)}
            </select>
        </td>
        <td class="px-4 py-4 align-top">
            <div class="relative">
                <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-500">Rp</span>
                <input type="number" name="items[${itemIndex}][cost_price]" value="${costPriceValue}" min="0" step="0.01" oninput="updateTotals()" class="w-full rounded-2xl border border-slate-300 bg-white px-12 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:border-gray-700 dark:bg-slate-900 dark:text-white" required />
            </div>
        </td>
        <td class="px-4 py-4 align-top">
            <input type="text" id="stock-current-${itemIndex}" value="${stockValue}" readonly class="w-full rounded-2xl border border-slate-300 bg-slate-100 px-4 py-3 text-sm text-slate-500 outline-none dark:border-gray-700 dark:bg-slate-800 dark:text-slate-400" />
        </td>
        <td class="px-4 py-4 align-top">
            <input type="number" name="items[${itemIndex}][quantity]" value="${quantityValue}" min="1" oninput="updateTotals()" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:border-gray-700 dark:bg-slate-900 dark:text-white" required />
        </td>
        <td class="px-4 py-4 align-top text-right">
            <button type="button" onclick="removeItem(${itemIndex})" class="inline-flex items-center rounded-full border border-rose-200 px-4 py-2 text-xs font-semibold text-rose-600 transition hover:bg-rose-50 dark:border-rose-700 dark:text-rose-300 dark:hover:bg-rose-950">Hapus</button>
        </td>
    `;

    itemIndex++;
    return row;
}

function addItem(data = {}) {
    const container = document.getElementById('inventory-items');
    container.appendChild(createItemRow(data));
    updateTotals();
}

function removeItem(index) {
    const row = document.getElementById(`inventory-item-${index}`);
    if (row) {
        row.remove();
        updateTotals();
    }
}

function onProductChange(index, productId) {
    const product = products.find(p => p.id === Number(productId));
    const stockInput = document.getElementById(`stock-current-${index}`);
    if (product && stockInput) {
        stockInput.value = product.stock;
    }
    updateTotals();
}

function updateTotals() {
    const rows = document.querySelectorAll('#inventory-items tr');
    let total = 0;
    rows.forEach(row => {
        const costInput = row.querySelector('input[name^="items"][name$="[cost_price]"]');
        const qtyInput = row.querySelector('input[name^="items"][name$="[quantity]"]');
        const productSelect = row.querySelector('select[name^="items"][name$="[product_id]"]');
        if (!productSelect || !costInput || !qtyInput) return;
        const qty = parseFloat(qtyInput.value) || 0;
        const cost = parseFloat(costInput.value) || 0;
        total += qty * cost;
    });
    totalModalInput.value = formatCurrency(total);
}

window.addEventListener('DOMContentLoaded', () => {
    selectType('{{ old('type', 'in') }}');
    if (initialItems.length > 0) {
        initialItems.forEach(item => addItem(item));
    } else {
        addItem();
    }
});
</script>
@endsection
