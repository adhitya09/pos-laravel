@extends('layouts.app')

@section('title', 'Transaksi')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Riwayat Transaksi</h1>
        <div>
            <a href="{{ route('transaksi.create') }}" onclick="event.preventDefault(); openTransactionCreateModal()" class="btn btn-primary btn-md">
                Buat
            </a>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 shadow-sm dark:border-gray-800">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Daftar Transaksi</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Menampilkan semua transaksi dari berbagai metode pembayaran</p>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[720px]">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                                <label class="flex items-center gap-2">
                                    <input type="checkbox" class="h-4 w-4 rounded border-gray-300 text-teal-600 focus:ring-teal-500 dark:border-gray-600 dark:bg-gray-700" aria-label="Pilih semua transaksi" />
                                </label>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">#No.Transaksi</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Nama Pemesan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Total Harga</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Pembayaran</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-800">
                        @forelse($transactions as $transaction)
                        <tr class="border-t border-gray-200 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-4 py-4">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" class="h-4 w-4 rounded border-gray-300 text-teal-600 focus:ring-teal-500 dark:border-gray-600 dark:bg-gray-700" aria-label="Pilih transaksi {{ $transaction->invoice_no }}" />
                                </label>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">#{{ $transaction->invoice_no }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $transaction->customer_name ?: 'Umum' }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm">
                                <span class="inline-flex items-center rounded-md border border-teal-500/30 bg-teal-50 px-2 py-1 text-xs font-medium text-teal-700 dark:border-teal-400/30 dark:bg-teal-900/30 dark:text-teal-300">
                                    {{ $transaction->paymentMethod->name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-4 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">
                                <div class="flex flex-wrap items-center justify-end gap-2">
                                    <a href="{{ route('pos.resi', $transaction->id) }}" target="_blank" rel="noopener noreferrer" class="rounded-full border border-teal-100 bg-teal-50 px-3 py-1 text-xs font-semibold text-teal-700 hover:bg-teal-100 dark:border-teal-500/20 dark:bg-teal-900/30 dark:text-teal-200 dark:hover:bg-teal-800">
                                        Cetak Resi
                                    </a>
                                    <a href="{{ route('transaksi.pdf', $transaction->id) }}" target="_blank" rel="noopener noreferrer" class="rounded-full border border-blue-100 bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 hover:bg-blue-100 dark:border-blue-500/20 dark:bg-blue-900/30 dark:text-blue-200 dark:hover:bg-blue-800">
                                        Download PDF
                                    </a>
                                    <div class="relative">
                                        <button type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300" aria-expanded="false" onclick="toggleKebabMenu({{ $transaction->id }}, event)">
                                            <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zm6 0a2 2 0 11-4 0 2 2 0 014 0zm6 0a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        </button>
                                        <div id="kebab-menu-{{ $transaction->id }}" class="kebab-menu hidden absolute right-0 mt-2 w-48 rounded-xl border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900 z-50" style="min-width:12rem;">
                                            <a href="{{ route('transaksi.edit', $transaction->id) }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-white/5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                                Edit
                                            </a>
                                            <a href="{{ route('transaksi.show', $transaction->id) }}" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-white/5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                                Detail
                                            </a>
                                            @if($transaction->status === 'completed')
                                                <form action="{{ route('transaksi.return', $transaction->id) }}" method="POST" class="m-0">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="flex items-center gap-2 w-full px-4 py-2 text-sm text-slate-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-white/5" onclick="return confirm('Return transaksi ini? Stok akan dikembalikan jika memungkinkan.')">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" /></svg>
                                                        Return pelanggan
                                                    </button>
                                                </form>
                                                <form action="{{ route('transaksi.destroy', $transaction->id) }}" method="POST" class="m-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20" onclick="return confirm('Batalkan transaksi ini? Stok akan dikembalikan jika memungkinkan.')">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                        Batalkan Transaksi
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-8 px-4 text-center text-gray-500 dark:text-gray-400">
                                Belum ada transaksi
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                <x-table-footer :paginator="$transactions" />
            </div>
        </div>
    </div>
</div>
<!-- Create Transaction Modal -->
<div id="transaction-modal-wrapper" class="hidden pointer-events-none fixed inset-0 z-50 flex items-center justify-center p-4">
    <div id="transaction-modal-backdrop" class="fixed inset-0 z-40 bg-slate-900/30 backdrop-blur-sm opacity-0 transition-opacity duration-200 dark:bg-black/60"></div>
    <div id="transaction-modal-panel" class="relative z-50 w-full max-w-4xl transform rounded-2xl border border-gray-200 bg-white shadow-xl opacity-0 scale-95 transition-all duration-200 dark:border-gray-800 dark:bg-slate-900">
        <form id="transaction-modal-form" action="{{ route('transaksi.store') }}" method="POST" class="space-y-6 p-6">
            @csrf
            <div class="flex items-start justify-between gap-4 border-b border-slate-200 pb-4 dark:border-slate-700">
                <div>
                    <h2 class="text-xl font-semibold text-slate-900 dark:text-white">Buat Transaksi Baru</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Tambah transaksi manual</p>
                </div>
                <button type="button" onclick="closeTransactionModal()" class="inline-flex h-10 w-10 items-center justify-center rounded-full text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 dark:hover:bg-slate-800 dark:hover:text-slate-200" aria-label="Tutup">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l8 8M14 6l-8 8" />
                    </svg>
                </button>
            </div>

            <div class="grid gap-6 lg:grid-cols-[1fr_1fr]">
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 block">Nama</label>
                        <input type="text" name="customer_name" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-teal-500 focus:ring-teal-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 block">Email</label>
                        <input type="email" name="customer_email" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-teal-500 focus:ring-teal-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 block">Phone</label>
                        <input type="text" name="customer_phone" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-teal-500 focus:ring-teal-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
                    </div>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 block">Metode Pembayaran</label>
                        <select name="payment_method_id" id="transaction-payment-method" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            <option value="">Pilih metode</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 block">Jumlah Bayar</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-500 dark:text-gray-400">Rp</span>
                            <input type="number" name="paid_amount" id="transaction-paid-amount" value="0" min="0" step="0.01" class="w-full rounded-lg border border-gray-300 bg-white px-10 py-2 text-sm text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
                        </div>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 block">Kembalian</label>
                        <input type="text" id="transaction-change" readonly class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-900 dark:border-gray-700 dark:bg-slate-800 dark:text-white" />
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-slate-950">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Produk Dipesan</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Tambahkan produk yang dipesan</p>
                    </div>
                    <button type="button" onclick="addTransactionItem()" class="btn btn-primary btn-sm">Tambah Item</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[720px] border-separate border-spacing-y-3">
                        <thead>
                            <tr class="text-left text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                <th class="px-4 py-2">Produk</th>
                                <th class="px-4 py-2">Qty</th>
                                <th class="px-4 py-2">Harga Jual</th>
                                <th class="px-4 py-2">Subtotal</th>
                                <th class="px-4 py-2"></th>
                            </tr>
                        </thead>
                        <tbody id="transaction-items"></tbody>
                    </table>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div>
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Catatan</label>
                    <textarea name="notes" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-white"></textarea>
                </div>
                <div class="w-64">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total</p>
                    <div class="mt-2 text-xl font-semibold">Rp <span id="transaction-total">0</span></div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeTransactionModal()" class="btn btn-secondary btn-md">Batal</button>
                <button type="submit" class="btn btn-primary btn-md">Buat</button>
            </div>
        </form>
    </div>
</div>
<script>
@php
    $_productsForJs = [];
    foreach ($products as $p) {
        $_productsForJs[] = [
            'id' => $p->id,
            'name' => $p->name,
            'sku' => $p->sku ?? null,
            'price' => $p->price ?? 0,
            'cost_price' => $p->cost_price ?? 0,
            'stock' => $p->stock ?? 0,
        ];
    }
    $_paymentMethodsForJs = [];
    foreach ($paymentMethods as $m) {
        $_paymentMethodsForJs[] = [
            'id' => $m->id,
            'name' => $m->name,
            'is_cash' => $m->is_cash ?? false,
        ];
    }
@endphp

const transactionProducts = @json($_productsForJs);
const transactionPaymentMethods = @json($_paymentMethodsForJs);

const transactionModalWrapper = document.getElementById('transaction-modal-wrapper');
const transactionModalBackdrop = document.getElementById('transaction-modal-backdrop');
const transactionModalPanel = document.getElementById('transaction-modal-panel');
const transactionItemsBody = document.getElementById('transaction-items');
const transactionTotalEl = document.getElementById('transaction-total');
const transactionPaidInput = document.getElementById('transaction-paid-amount');
const transactionChangeEl = document.getElementById('transaction-change');
const transactionPaymentSelect = document.getElementById('transaction-payment-method');

let txItemIndex = 0;

function productOptions(selectedId = null) {
    return ['<option value="">Pilih produk</option>', ...transactionProducts.map(p => {
        const sel = p.id === Number(selectedId) ? 'selected' : '';
        return `<option value="${p.id}" ${sel}>${p.name} (${p.stock}) - ${p.sku || ''}</option>`;
    })].join('');
}

function addTransactionItem(data = {}) {
    const idx = txItemIndex++;
    const productId = data.product_id || '';
    const product = transactionProducts.find(p => p.id === Number(productId));
    const qty = data.quantity ?? 1;
    const price = data.price ?? (product ? product.price : 0);

    const tr = document.createElement('tr');
    tr.id = `tx-item-${idx}`;
    tr.innerHTML = `
        <td class="px-4 py-2 align-top">
            <select name="items[${idx}][product_id]" onchange="onTxProductChange(${idx}, this.value)" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-slate-900" required>
                ${productOptions(productId)}
            </select>
        </td>
        <td class="px-4 py-2 align-top">
            <input type="number" name="items[${idx}][quantity]" value="${qty}" min="1" oninput="updateTxTotals()" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-slate-900" required />
        </td>
        <td class="px-4 py-2 align-top">
            <input type="number" name="items[${idx}][price]" value="${price}" min="0" step="0.01" oninput="updateTxTotals()" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-700 dark:bg-slate-900" required />
        </td>
        <td class="px-4 py-2 align-top">
            <div class="text-sm font-medium">Rp <span class="tx-subtotal">0</span></div>
        </td>
        <td class="px-4 py-2 align-top text-right">
            <button type="button" onclick="removeTxItem(${idx})" class="inline-flex items-center rounded-lg border border-red-600 bg-red-600 px-3 py-1 text-white">Hapus</button>
        </td>
    `;
    transactionItemsBody.appendChild(tr);
    updateTxTotals();
}

function removeTxItem(idx) {
    const el = document.getElementById(`tx-item-${idx}`);
    if (el) el.remove();
    updateTxTotals();
}

function onTxProductChange(idx, productId) {
    const product = transactionProducts.find(p => p.id === Number(productId));
    const row = document.getElementById(`tx-item-${idx}`);
    if (!row) return;
    const priceInput = row.querySelector(`input[name='items[${idx}][price]']`);
    const qtyInput = row.querySelector(`input[name='items[${idx}][quantity]']`);
    if (product) {
        if (priceInput) priceInput.value = product.price;
        if (qtyInput) qtyInput.value = 1;
    }
    updateTxTotals();
}

function updateTxTotals() {
    const rows = transactionItemsBody.querySelectorAll('tr');
    let total = 0;
    rows.forEach(row => {
        const price = parseFloat(row.querySelector('input[name$="[price]"]')?.value) || 0;
        const qty = parseFloat(row.querySelector('input[name$="[quantity]"]')?.value) || 0;
        const subtotal = price * qty;
        total += subtotal;
        const subEl = row.querySelector('.tx-subtotal');
        if (subEl) subEl.textContent = new Intl.NumberFormat('id-ID',{minimumFractionDigits:0}).format(subtotal);
    });
    transactionTotalEl.textContent = new Intl.NumberFormat('id-ID',{minimumFractionDigits:0}).format(total);
    const paid = parseFloat(transactionPaidInput.value) || 0;
    transactionChangeEl.value = new Intl.NumberFormat('id-ID',{minimumFractionDigits:0}).format(Math.max(0, paid - total));
}

function populatePaymentMethods() {
    transactionPaymentSelect.innerHTML = ['<option value="">Pilih metode</option>', ...transactionPaymentMethods.map(m => `<option value="${m.id}">${m.name}</option>`)].join('');
}

function openTransactionCreateModal() {
    populatePaymentMethods();
    // reset form
    document.getElementById('transaction-modal-form').reset();
    transactionItemsBody.innerHTML = '';
    txItemIndex = 0;
    addTransactionItem();
    transactionModalWrapper.classList.remove('hidden');
    transactionModalWrapper.classList.remove('pointer-events-none');
    transactionModalWrapper.classList.add('pointer-events-auto');
    transactionModalBackdrop.classList.remove('opacity-0');
    transactionModalBackdrop.classList.add('opacity-100');
    transactionModalPanel.classList.remove('opacity-0', 'scale-95');
    transactionModalPanel.classList.add('opacity-100', 'scale-100');
}

function closeTransactionModal() {
    transactionModalBackdrop.classList.remove('opacity-100');
    transactionModalBackdrop.classList.add('opacity-0');
    transactionModalPanel.classList.remove('opacity-100', 'scale-100');
    transactionModalPanel.classList.add('opacity-0', 'scale-95');
    setTimeout(() => {
        transactionModalWrapper.classList.add('hidden');
        transactionModalWrapper.classList.add('pointer-events-none');
    }, 200);
}

transactionModalBackdrop.addEventListener('click', closeTransactionModal);
transactionPaidInput.addEventListener('input', updateTxTotals);
window.openTransactionCreateModal = openTransactionCreateModal;

// Kebab menu handling
function closeAllKebabMenus() {
    document.querySelectorAll('.kebab-menu').forEach(el => el.classList.add('hidden'));
}

function toggleKebabMenu(id, event) {
    event.stopPropagation();
    const el = document.getElementById('kebab-menu-' + id);
    if (!el) return;
    const isHidden = el.classList.contains('hidden');
    closeAllKebabMenus();
    if (isHidden) el.classList.remove('hidden');
}

// Close kebab when clicking outside
document.addEventListener('click', function (e) {
    const openMenus = document.querySelectorAll('.kebab-menu:not(.hidden)');
    if (openMenus.length === 0) return;
    // if click is inside menu or its button, ignore
    for (const m of openMenus) {
        if (m.contains(e.target) || (m.previousElementSibling && m.previousElementSibling.contains && m.previousElementSibling.contains(e.target))) {
            return;
        }
    }
    closeAllKebabMenus();
});

// Close kebab on escape
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeAllKebabMenus();
});
</script>
@endsection
