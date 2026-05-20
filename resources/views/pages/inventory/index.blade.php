@extends('layouts.app')

@section('title', 'Inventory')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Inventory</h1>
        </div>
        @perm('inventory.create')
        <a href="{{ route('inventory.create') }}" onclick="event.preventDefault(); openInventoryCreateModal()" class="btn btn-primary btn-lg">
            Buat
        </a>
        @endperm
    </div>

    <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-6 dark:bg-slate-900 dark:border-slate-700">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
            <div class="relative w-full md:w-80">
                <form method="GET" action="{{ route('inventory.index') }}">
                    @foreach(request()->except('search') as $k => $v)
                        @if(is_array($v))
                            @foreach($v as $vv)
                                <input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">
                            @endforeach
                        @else
                            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                        @endif
                    @endforeach
                    <input name="search" type="search" placeholder="Cari" value="{{ $search ?? request('search') }}" class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:border-gray-700 dark:bg-slate-800 dark:text-white" />
                </form>
            </div>
            <div class="text-sm text-slate-500 dark:text-slate-400">Menampilkan 1 sampai {{ $inventories->count() }} dari {{ $inventories->count() }} hasil</div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                    <tr>
                        <th class="px-6 py-4">No.Referensi</th>
                        <th class="px-6 py-4">Tipe</th>
                        <th class="px-6 py-4">Sumber</th>
                        <th class="px-6 py-4">Total</th>
                        <th class="px-6 py-4">Notes</th>
                        <th class="px-6 py-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white dark:divide-slate-700 dark:bg-slate-900">
                    @forelse($inventories as $inventory)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900 dark:text-white">{{ $inventory->reference_no }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700 dark:text-slate-300">
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold
                                @if($inventory->type === 'in') bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200
                                @elseif($inventory->type === 'out') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                @else bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-200 @endif">
                                {{ $inventory->type === 'in' ? 'Masuk' : ($inventory->type === 'out' ? 'Keluar' : 'Penyesuaian') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700 dark:text-slate-300">{{ $inventory->source ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700 dark:text-slate-300">Rp {{ number_format($inventory->total_modal, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700 dark:text-slate-300">{{ $inventory->notes ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700 dark:text-slate-300">
                            @perm('inventory.update')
                            @php
                                $inventoryData = [
                                    'id' => $inventory->id,
                                    'type' => $inventory->type,
                                    'source' => $inventory->source,
                                    'notes' => $inventory->notes,
                                    'inventory_date' => $inventory->inventory_date?->format('Y-m-d'),
                                    'items' => $inventory->inventoryItems->map(function ($item) {
                                        return [
                                            'product_id' => $item->product_id,
                                            'quantity' => $item->quantity,
                                            'cost_price' => $item->cost_price,
                                        ];
                                    }),
                                ];
                            @endphp
                            <a href="{{ route('inventory.edit', $inventory->id) }}"
                               data-inventory='@json($inventoryData)'
                               onclick="event.preventDefault(); openInventoryEditModal(this.dataset.inventory)"
                               class="btn btn-secondary btn-xs btn-pill">
                                <span>Ubah</span>
                            </a>
                            @endperm
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-500 dark:text-slate-400">Belum ada data inventory</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            <x-table-footer :paginator="$inventories" />
        </div>
    </div>
</div>

<div id="inventory-modal-wrapper" class="hidden pointer-events-none fixed inset-0 z-50 flex items-center justify-center p-4">
    <div id="inventory-modal-backdrop" class="fixed inset-0 z-40 bg-slate-900/30 backdrop-blur-sm opacity-0 transition-opacity duration-200 dark:bg-black/60"></div>
    <div id="inventory-modal-panel" class="relative z-50 w-full max-w-4xl transform rounded-2xl border border-gray-200 bg-white shadow-xl opacity-0 scale-95 transition-all duration-200 dark:border-gray-800 dark:bg-slate-900">
        <form id="inventory-modal-form" action="{{ route('inventory.store') }}" method="POST" class="space-y-6 p-6">
            @csrf
            <input type="hidden" name="_method" id="inventory-modal-method" value="">
            <input type="hidden" name="type" id="inventory-modal-type" value="in">
            <div class="flex items-start justify-between gap-4 border-b border-slate-200 pb-4 dark:border-slate-700">
                <div>
                    <h2 id="inventory-modal-title" class="text-xl font-semibold text-slate-900 dark:text-white">Buat Inventory</h2>
                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Kelola inventory langsung dari halaman ini.</p>
                </div>
                <button type="button" onclick="closeInventoryModal()" class="inline-flex h-10 w-10 items-center justify-center rounded-full text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 dark:hover:bg-slate-800 dark:hover:text-slate-200" aria-label="Tutup">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l8 8M14 6l-8 8" />
                    </svg>
                </button>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-slate-950">
                <div class="grid gap-6 lg:grid-cols-[1.5fr_1fr]">
                    <div class="space-y-6">
                        <div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tipe Stok</p>
                            <div class="grid grid-cols-3 gap-3">
                                <button type="button" id="inventory-type-in" onclick="selectInventoryType('in')" class="btn btn-secondary btn-md">Masuk</button>
                                <button type="button" id="inventory-type-out" onclick="selectInventoryType('out')" class="btn btn-secondary btn-md">Keluar</button>
                                <button type="button" id="inventory-type-adjustment" onclick="selectInventoryType('adjustment')" class="btn btn-secondary btn-md">Penyesuaian</button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sumber *</label>
                            <select name="source" id="inventory-modal-source" required class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-teal-500 focus:ring-teal-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500">
                                <option value="">Pilih salah satu opsi</option>
                                <option value="Penambahan Stock">Penambahan Stock</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Total Modal *</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-500 dark:text-gray-400">Rp</span>
                                <input type="text" id="inventory-modal-total" value="0" readonly class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 pl-10 text-sm text-gray-900 placeholder-gray-400 focus:border-teal-500 focus:ring-teal-500 dark:border-gray-700 dark:bg-slate-800 dark:text-white dark:placeholder-gray-500" />
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tanggal Inventory *</label>
                            <input type="date" name="inventory_date" id="inventory-modal-date" value="{{ date('Y-m-d') }}" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-teal-500 focus:ring-teal-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500" required />
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-slate-950">
                <div class="flex items-center justify-between gap-4 mb-6">
                    <div>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Pemilihan Produk</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Pilih produk, atur harga modal dan jumlah.</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[720px] border-separate border-spacing-y-3">
                        <thead>
                            <tr class="text-left text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                <th class="px-4 py-3">Produk *</th>
                                <th class="px-4 py-3">Harga Modal *</th>
                                <th class="px-4 py-3">Stok Saat Ini *</th>
                                <th class="px-4 py-3">Jumlah *</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody id="inventory-modal-items" class="space-y-3"></tbody>
                    </table>
                </div>
                <button type="button" onclick="addInventoryItem()" class="btn btn-primary btn-md mt-4">
                    Tambahkan inventory item
                </button>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-slate-950">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Catatan</label>
                <textarea name="notes" id="inventory-modal-notes" rows="4" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-teal-500 focus:ring-teal-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500" placeholder="Masukkan catatan tambahan"></textarea>
            </div>

            <div class="flex flex-wrap justify-end gap-3">
                <button type="button" onclick="closeInventoryModal()" class="btn btn-secondary btn-md">
                    Batal
                </button>
                <button type="submit" id="inventory-modal-save" class="btn btn-primary btn-md">
                    Buat
                </button>
                <button type="submit" id="inventory-modal-save-another" name="create_another" value="1" class="btn btn-primary btn-md">
                    Buat & buat lagi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
@php
    $_inventoryProductsForJs = [];
    foreach ($products as $product) {
        $_inventoryProductsForJs[] = [
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'stock' => $product->stock,
            'cost_price' => $product->cost_price ?? 0,
        ];
    }
@endphp

const inventoryProducts = @json($_inventoryProductsForJs);

const inventoryModalWrapper = document.getElementById('inventory-modal-wrapper');
const inventoryModalBackdrop = document.getElementById('inventory-modal-backdrop');
const inventoryModalPanel = document.getElementById('inventory-modal-panel');
const inventoryModalForm = document.getElementById('inventory-modal-form');
const inventoryModalMethod = document.getElementById('inventory-modal-method');
const inventoryModalType = document.getElementById('inventory-modal-type');
const inventoryModalTitle = document.getElementById('inventory-modal-title');
const inventoryModalTotal = document.getElementById('inventory-modal-total');
const inventoryModalDate = document.getElementById('inventory-modal-date');
const inventoryModalSource = document.getElementById('inventory-modal-source');
const inventoryModalNotes = document.getElementById('inventory-modal-notes');
const inventoryModalItems = document.getElementById('inventory-modal-items');
const inventoryModalSaveButton = document.getElementById('inventory-modal-save');
const inventoryModalSaveAnotherButton = document.getElementById('inventory-modal-save-another');

let inventoryItemIndex = 0;
let inventoryModalMode = 'create';

function formatCurrency(value) {
    return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(value);
}

function inventoryProductOptions(selectedId = null) {
    return ['<option value="">Pilih salah satu opsi</option>', ...inventoryProducts.map(product => {
        const selected = product.id === Number(selectedId) ? 'selected' : '';
        return `<option value="${product.id}" ${selected}>${product.name} (${product.stock}) - ${product.sku}</option>`;
    })].join('');
}

function createInventoryItemRow(data = {}) {
    const index = inventoryItemIndex++;
    const productId = data.product_id || '';
    const product = inventoryProducts.find(p => p.id === Number(productId));
    const stockValue = product ? product.stock : '';
    const costPriceValue = data.cost_price ?? (product ? product.cost_price : 0);
    const quantityValue = data.quantity ?? 1;

    const row = document.createElement('tr');
    row.id = `inventory-item-${index}`;
    row.className = 'bg-slate-50 dark:bg-slate-800 rounded-3xl';

    row.innerHTML = `
        <td class="px-4 py-4 align-top">
            <select name="items[${index}][product_id]" onchange="onInventoryProductChange(${index}, this.value)" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-teal-500 focus:ring-teal-500 dark:border-gray-700 dark:bg-slate-900 dark:text-white dark:placeholder-gray-500" required>
                ${inventoryProductOptions(productId)}
            </select>
        </td>
        <td class="px-4 py-4 align-top">
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-500 dark:text-gray-400">Rp</span>
                <input type="number" name="items[${index}][cost_price]" value="${costPriceValue}" min="0" step="0.01" oninput="updateInventoryTotals()" class="w-full rounded-lg border border-gray-300 bg-white px-10 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-teal-500 focus:ring-teal-500 dark:border-gray-700 dark:bg-slate-900 dark:text-white dark:placeholder-gray-500" required />
            </div>
        </td>
        <td class="px-4 py-4 align-top">
            <input type="text" id="inventory-stock-${index}" value="${stockValue}" readonly class="w-full rounded-lg border border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-500 dark:border-gray-700 dark:bg-slate-800 dark:text-gray-400" />
        </td>
        <td class="px-4 py-4 align-top">
            <input type="number" name="items[${index}][quantity]" value="${quantityValue}" min="1" oninput="updateInventoryTotals()" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-teal-500 focus:ring-teal-500 dark:border-gray-700 dark:bg-slate-900 dark:text-white dark:placeholder-gray-500" required />
        </td>
        <td class="px-4 py-4 align-top text-right">
            <button type="button" onclick="removeInventoryItem(${index})" class="inline-flex items-center justify-center rounded-lg border border-red-600 bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Hapus</button>
        </td>
    `;

    inventoryModalItems.appendChild(row);
}

function addInventoryItem(data = {}) {
    createInventoryItemRow(data);
    updateInventoryTotals();
}

function removeInventoryItem(index) {
    const row = document.getElementById(`inventory-item-${index}`);
    if (row) {
        row.remove();
        updateInventoryTotals();
    }
}

function onInventoryProductChange(index, productId) {
    const product = inventoryProducts.find(p => p.id === Number(productId));
    const stockInput = document.getElementById(`inventory-stock-${index}`);
    if (stockInput) {
        stockInput.value = product ? product.stock : '';
    }
    updateInventoryTotals();
}

function selectInventoryType(type) {
    inventoryModalType.value = type;
    const mapping = {
        in: 'inventory-type-in',
        out: 'inventory-type-out',
        adjustment: 'inventory-type-adjustment'
    };
    const activeId = mapping[type] || 'inventory-type-in';
    ['inventory-type-in', 'inventory-type-out', 'inventory-type-adjustment'].forEach(id => {
        const button = document.getElementById(id);
        if (!button) return;
        if (id === activeId) {
            button.classList.add('bg-teal-600', 'text-white', 'border-teal-600');
            button.classList.remove('bg-white', 'text-gray-900', 'dark:bg-slate-800');
        } else {
            button.classList.remove('bg-teal-600', 'text-white', 'border-teal-600');
            button.classList.add('bg-white', 'text-gray-900', 'dark:bg-slate-800');
        }
    });
}

function setInventoryMode(mode) {
    inventoryModalMode = mode;
    if (mode === 'create') {
        inventoryModalTitle.textContent = 'Buat Inventory';
        inventoryModalForm.action = '{{ route('inventory.store') }}';
        inventoryModalMethod.value = '';
        inventoryModalSaveButton.textContent = 'Buat';
        inventoryModalSaveAnotherButton.classList.remove('hidden');
    } else {
        inventoryModalTitle.textContent = 'Ubah Inventory';
        inventoryModalSaveButton.textContent = 'Simpan';
        inventoryModalSaveAnotherButton.classList.add('hidden');
    }
}

function resetInventoryModal() {
    selectInventoryType('in');
    inventoryModalSource.value = '';
    inventoryModalNotes.value = '';
    inventoryModalDate.value = new Date().toISOString().slice(0, 10);
    inventoryModalItems.innerHTML = '';
    inventoryItemIndex = 0;
    addInventoryItem();
    updateInventoryTotals();
}

function openInventoryModal() {
    inventoryModalWrapper.classList.remove('hidden');
    inventoryModalWrapper.classList.remove('pointer-events-none');
    inventoryModalWrapper.classList.add('pointer-events-auto');
    inventoryModalBackdrop.classList.remove('opacity-0');
    inventoryModalBackdrop.classList.add('opacity-100');
    inventoryModalPanel.classList.remove('opacity-0', 'scale-95');
    inventoryModalPanel.classList.add('opacity-100', 'scale-100');
}

function closeInventoryModal() {
    inventoryModalBackdrop.classList.remove('opacity-100');
    inventoryModalBackdrop.classList.add('opacity-0');
    inventoryModalPanel.classList.remove('opacity-100', 'scale-100');
    inventoryModalPanel.classList.add('opacity-0', 'scale-95');
    setTimeout(() => {
        inventoryModalWrapper.classList.add('hidden');
        inventoryModalWrapper.classList.add('pointer-events-none');
    }, 200);
}

function openInventoryCreateModal() {
    setInventoryMode('create');
    resetInventoryModal();
    openInventoryModal();
}

function openInventoryEditModal(inventoryData) {
    let data = inventoryData;
    if (typeof inventoryData === 'string') {
        try { data = JSON.parse(inventoryData); } catch (error) { data = null; }
    }
    if (!data || !data.id) return;

    setInventoryMode('edit');
    inventoryModalForm.action = '{{ url('inventory') }}/' + data.id;
    inventoryModalMethod.value = 'PUT';
    inventoryModalSource.value = data.source || '';
    inventoryModalNotes.value = data.notes || '';
    inventoryModalDate.value = data.inventory_date || new Date().toISOString().slice(0, 10);
    selectInventoryType(data.type || 'in');
    inventoryModalItems.innerHTML = '';
    inventoryItemIndex = 0;
    (data.items || []).forEach(item => addInventoryItem(item));
    updateInventoryTotals();
    openInventoryModal();
}

function updateInventoryTotals() {
    const rows = inventoryModalItems.querySelectorAll('tr');
    let total = 0;
    rows.forEach(row => {
        const costInput = row.querySelector('input[name$="[cost_price]"]');
        const qtyInput = row.querySelector('input[name$="[quantity]"]');
        const cost = parseFloat(costInput?.value) || 0;
        const qty = parseFloat(qtyInput?.value) || 0;
        total += cost * qty;
    });
    inventoryModalTotal.value = formatCurrency(total);
}

inventoryModalBackdrop.addEventListener('click', closeInventoryModal);
window.addEventListener('keydown', function(event) {
    if (event.key === 'Escape' && !inventoryModalWrapper.classList.contains('hidden')) {
        closeInventoryModal();
    }
});

window.openInventoryCreateModal = openInventoryCreateModal;
window.openInventoryEditModal = openInventoryEditModal;
</script>
@endsection
