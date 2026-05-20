@extends('layouts.app')

@section('title', 'Edit Transaction')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                <a href="{{ route('transaksi.index') }}" class="hover:text-gray-700 dark:hover:text-white">Transaksi</a>
                <span>/</span>
                <span class="text-gray-700 dark:text-gray-300">Edit</span>
            </div>
            <h1 class="mt-2 text-3xl font-semibold text-gray-900 dark:text-white">Edit Transaction</h1>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            @if(!in_array($transaksi->status, ['returned', 'cancelled'], true))
                <form action="{{ route('transaksi.destroy', $transaksi->id) }}" method="POST" onsubmit="return confirm('Batalkan transaksi ini?')" class="inline-flex">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-md inline-flex items-center justify-center">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        Cancel Transaction
                    </button>
                </form>
            @endif
            <a href="{{ route('transaksi.show', $transaksi->id) }}" class="btn btn-secondary btn-md">Back</a>
        </div>
    </div>

    @if($errors->any())
        <div class="rounded-3xl border border-red-500/30 bg-red-500/10 p-4 text-sm text-red-900 dark:text-red-100">
            <p class="font-semibold">Please fix the following errors:</p>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('transaksi.update', $transaksi->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-6 dark:border-gray-800 dark:bg-gray-900">
            <div class="grid gap-6 lg:grid-cols-2">
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                    <input
                        type="text"
                        name="customer_name"
                        value="{{ old('customer_name', $transaksi->customer_name) }}"
                        class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-teal-500 focus:ring-teal-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500"
                        placeholder="Customer name"
                    />
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-6 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Produk dipesan</h2>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Pastikan cek terlebih dahulu ketersediaan stok produk.</p>
                </div>
                <button
                    type="button"
                    onclick="addItem()"
                    class="btn btn-primary btn-md"
                >
                    Tambah Item
                </button>
            </div>

            <div class="mt-6 overflow-x-auto rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <table class="min-w-full text-left text-sm text-gray-700 dark:text-gray-300">
                    <thead>
                        <tr class="border-b border-gray-200 text-xs uppercase tracking-wide text-gray-500 dark:border-gray-800 dark:text-gray-300">
                            <th class="px-4 py-3 w-12"><input type="checkbox" class="h-4 w-4 rounded border-gray-300 text-teal-500 focus:ring-teal-500 dark:border-gray-600" disabled></th>
                            <th class="px-4 py-3">Produk</th>
                            <th class="px-4 py-3 text-right">Jumlah</th>
                            <th class="px-4 py-3 text-right">Harga Modal</th>
                            <th class="px-4 py-3 text-right">Harga Jual</th>
                            <th class="px-4 py-3 text-right">Profit</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="items-body" class="divide-y divide-gray-200 dark:divide-gray-800"></tbody>
                    <tfoot>
                        <tr class="border-t border-gray-200 text-sm text-gray-900 dark:border-gray-800 dark:text-gray-300">
                            <td colspan="5" class="px-4 py-4 text-right font-semibold">Total Profit</td>
                            <td colspan="2" class="px-4 py-4 text-right font-semibold text-teal-600 dark:text-teal-400" id="total-profit">Rp 0</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-6 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Total</h3>
                <div class="mt-5 space-y-5">
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Total Amount</label>
                        <input
                            type="text"
                            id="total_amount"
                            readonly
                            value="{{ number_format(old('total_amount', $transaksi->total_amount), 0, ',', '.') }}"
                            class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                        />
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white shadow-sm p-6 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Pembayaran</h3>
                <div class="mt-5 space-y-5 text-sm text-gray-700 dark:text-gray-300">
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Metode Pembayaran</label>
                        <select
                            name="payment_method_id"
                            id="payment_method_id"
                            class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-teal-500 focus:ring-teal-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                        >
                            <option value="">Pilih metode</option>
                            @foreach($paymentMethods as $method)
                                <option value="{{ $method->id }}" {{ old('payment_method_id', $transaksi->payment_method_id) == $method->id ? 'selected' : '' }}>{{ $method->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Nominal Bayar</label>
                        <input
                            type="number"
                            name="paid_amount"
                            id="paid_amount"
                            value="{{ old('paid_amount', $transaksi->paid_amount) }}"
                            min="0"
                            step="0.01"
                            class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-teal-500 focus:ring-teal-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500"
                        />
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Kembalian</label>
                        <input
                            type="text"
                            id="change_amount"
                            readonly
                            value="{{ number_format(old('change_amount', $transaksi->change_amount), 0, ',', '.') }}"
                            class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                        />
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
            <a href="{{ route('transaksi.show', $transaksi->id) }}" class="btn btn-secondary btn-md">Cancel</a>
            <button type="submit" class="btn btn-primary btn-md">Save changes</button>
        </div>
    </form>
</div>

@php
    $productsForJs = $products->map(function ($product) {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'stock' => (int) $product->stock,
            'cost_price' => (float) $product->cost_price,
            'price' => (float) $product->price,
        ];
    })->values();

    $existingItemsForJs = old('items');
    if (!$existingItemsForJs) {
        $existingItemsForJs = $transaksi->transactionItems->map(function ($item) {
            return [
                'product_id' => $item->product_id,
                'quantity' => (int) $item->quantity,
                'price' => (float) $item->price,
            ];
        })->values();
    }
@endphp

<script>
    const products = @json($productsForJs);
    const existingItems = @json($existingItemsForJs);

    let itemIndex = 0;
    let itemsBody;

    function formatCurrency(value) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        }).format(Number(value) || 0);
    }

    function findProduct(id) {
        return products.find((product) => Number(product.id) === Number(id)) || null;
    }

    function buildProductOptions(selected = '') {
        return [`<option value="">Pilih produk</option>`, ...products.map((product) => {
            return `<option value="${product.id}"${product.id === Number(selected) ? ' selected' : ''}>${product.name} (Stok: ${product.stock})</option>`;
        })].join('');
    }

    function calculateProfit(costPrice, quantity, price) {
        return (Number(price) - Number(costPrice)) * Number(quantity);
    }

    function updateTotals() {
        const rows = Array.from(itemsBody.querySelectorAll('tr[data-index]'));
        let totalProfit = 0;
        let totalAmount = 0;

        rows.forEach((row) => {
            const idx = row.dataset.index;
            const productId = row.querySelector(`select[name='items[${idx}][product_id]']`).value;
            const quantity = Number(row.querySelector(`input[name='items[${idx}][quantity]']`).value) || 0;
            const price = Number(row.querySelector(`input[name='items[${idx}][price]']`).value) || 0;
            const product = findProduct(productId);
            const costPrice = product ? product.cost_price : 0;
            const profit = calculateProfit(costPrice, quantity, price);
            row.querySelector(`#item-profit-${idx}`).textContent = formatCurrency(profit);
            totalProfit += profit;
            totalAmount += Number(price) * quantity;
        });

        document.getElementById('total-profit').textContent = formatCurrency(totalProfit);
        document.getElementById('total_amount').value = formatCurrency(totalAmount);
        refreshChangeAmount(totalAmount);
    }

    function refreshChangeAmount(totalAmount = null) {
        const paidInput = document.getElementById('paid_amount');
        const changeInput = document.getElementById('change_amount');
        const paidValue = Number(paidInput.value) || 0;
        const total = totalAmount === null ? parseFloat(paidInput.dataset.currentTotal || 0) : Number(totalAmount);
        const change = paidValue - total;
        changeInput.value = formatCurrency(change);
    }

    function onItemProductChange(index) {
        const row = itemsBody.querySelector(`tr[data-index='${index}']`);
        if (!row) return;
        const select = row.querySelector(`select[name='items[${index}][product_id]']`);
        const priceInput = row.querySelector(`input[name='items[${index}][price]']`);
        const costElement = row.querySelector(`#item-cost-${index}`);
        const product = findProduct(select.value);

        if (product) {
            costElement.textContent = formatCurrency(product.cost_price);
            if (!priceInput.value) {
                priceInput.value = product.price;
            }
        } else {
            costElement.textContent = '-';
        }

        updateTotals();
    }

    function onItemQuantityChange(index) {
        const row = itemsBody.querySelector(`tr[data-index='${index}']`);
        if (!row) return;
        const quantity = Number(row.querySelector(`input[name='items[${index}][quantity]']`).value) || 0;
        if (quantity < 1) {
            row.querySelector(`input[name='items[${index}][quantity]']`).value = 1;
        }
        updateTotals();
    }

    function onItemPriceChange(index) {
        const row = itemsBody.querySelector(`tr[data-index='${index}']`);
        if (!row) return;
        const price = Number(row.querySelector(`input[name='items[${index}][price]']`).value) || 0;
        if (price < 0) {
            row.querySelector(`input[name='items[${index}][price]']`).value = 0;
        }
        updateTotals();
    }

    function removeItemRow(index) {
        const row = itemsBody.querySelector(`tr[data-index='${index}']`);
        if (row) {
            row.remove();
        }
        updateTotals();
    }

    function addItem(data = {}) {
        const index = itemIndex++;
        const productId = data.product_id || '';
        const quantity = Number(data.quantity) || 1;
        const price = data.price !== undefined ? data.price : '';
        const product = findProduct(productId);
        const costPrice = product ? product.cost_price : 0;

        const row = document.createElement('tr');
        row.dataset.index = index;
        row.className = 'border-b border-gray-200 dark:border-gray-800';
        row.innerHTML = `
            <td class="px-4 py-4 align-middle"><input type="checkbox" class="h-4 w-4 rounded border-gray-300 text-teal-500 focus:ring-teal-500 dark:border-gray-600"></td>
            <td class="px-4 py-4 align-middle">
                <select name="items[${index}][product_id]" onchange="onItemProductChange(${index})" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-teal-500 focus:ring-teal-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    ${buildProductOptions(productId)}
                </select>
            </td>
            <td class="px-4 py-4 align-middle text-right">
                <input type="number" min="1" name="items[${index}][quantity]" value="${quantity}" onchange="onItemQuantityChange(${index})" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-teal-500 focus:ring-teal-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
            </td>
            <td class="px-4 py-4 align-middle text-right text-gray-700 dark:text-gray-300" id="item-cost-${index}">${product ? formatCurrency(costPrice) : '-'}</td>
            <td class="px-4 py-4 align-middle text-right">
                <input type="number" min="0" step="0.01" name="items[${index}][price]" value="${price}" onchange="onItemPriceChange(${index})" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-teal-500 focus:ring-teal-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
            </td>
            <td class="px-4 py-4 align-middle text-right text-teal-600 dark:text-teal-400" id="item-profit-${index}">${formatCurrency(calculateProfit(costPrice, quantity, price || 0))}</td>
            <td class="px-4 py-4 align-middle text-right">
                <button type="button" onclick="removeItemRow(${index})" class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-red-600 text-white hover:bg-red-700">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </td>
        `;

        itemsBody.appendChild(row);
        updateTotals();
    }

    document.addEventListener('DOMContentLoaded', function () {
        itemsBody = document.getElementById('items-body');
        const totalAmountElement = document.getElementById('total_amount');
        const currentTotal = Number('{{ $transaksi->total_amount }}') || 0;
        totalAmountElement.dataset.currentTotal = currentTotal;

        if (Array.isArray(existingItems) && existingItems.length > 0) {
            existingItems.forEach((item) => addItem(item));
        } else {
            addItem();
        }

        document.getElementById('paid_amount').addEventListener('input', function () {
            updateTotals();
        });

        updateTotals();
    });
</script>
@endsection
