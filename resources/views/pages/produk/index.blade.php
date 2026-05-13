@extends('layouts.app')

@section('title', 'Product')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <div class="text-sm text-slate-500 dark:text-slate-400 mb-2"></div>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Product</h1>
        </div>
        @perm('produk.create')
        <a href="{{ route('produk.create') }}" class="inline-flex items-center rounded-full bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">
            Buat
        </a>
        @endperm
    </div>

{{-- Toolbar: filter chips + action buttons --}}
<div class="px-6 pt-4 pb-3">
    <!-- ROW 1: Filter chips + search -->
    <div class="flex items-center justify-between px-6 pt-4 pb-3">
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('produk.index') }}"
                 class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium
                                {{ !request('status') ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900'
                                                                            : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300' }}">
                All
                <span class="ml-1.5 text-xs {{ !request('status') ? 'bg-gray-700 text-white dark:bg-gray-300 dark:text-gray-900' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }} rounded-full px-1.5 py-0.5">
                     {{ $stats['all'] ?? $products->count() }}
                </span>
            </a>
            <a href="{{ route('produk.index', ['status' => 'banyak']) }}"
                 class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium
                                {{ request('status') == 'banyak' ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900'
                                                                                                 : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300' }}">
                Stock Banyak
            </a>
            <a href="{{ route('produk.index', ['status' => 'sedikit']) }}"
                 class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium
                                {{ request('status') == 'sedikit' ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900'
                                                                                                    : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300' }}">
                Stock Sedikit
            </a>
            <a href="{{ route('produk.index', ['status' => 'habis']) }}"
                 class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium
                                {{ request('status') == 'habis' ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900'
                                                                                                : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300' }}">
                Stock Habis
            </a>
        </div>

        <div class="relative">
            <form method="GET" action="{{ route('produk.index') }}">
                @foreach(request()->except('search') as $k => $v)
                    @if(is_array($v))
                        @foreach($v as $vv)
                            <input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endif
                @endforeach
                <input name="search" type="text"
                       placeholder="Cari produk..."
                       id="searchInput"
                       value="{{ request('search') }}"
                       class="pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg
                              focus:outline-none focus:ring-2 focus:ring-blue-500 w-56
                              dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none"
                     stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                </svg>
            </form>
        </div>
    </div>

    <!-- ROW 2: Action buttons -->
    <div class="flex items-center px-6 pb-4 gap-2">

        <!-- DEFAULT toolbar (no selection) -->
        <div id="toolbar-default" class="flex items-center gap-2">
            @perm('produk.viewAny')
            <a href="{{ route('produk.cetak-barcode') }}"
               target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white
                      rounded-lg hover:bg-green-700 text-sm font-medium">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
              </svg>
              Cetak Barcode
            </a>
            @endperm
        </div>

        <!-- SELECTION toolbar (visible when checkboxes checked) -->
        <div id="toolbar-selected" class="hidden items-center gap-2">

            <!-- Tindakan dropdown -->
            <div class="relative" id="tindakan-wrapper">
              <button type="button" onclick="toggleTindakanDropdown()"
                      class="inline-flex items-center gap-2 px-4 py-2 bg-white border
                             border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50
                             text-sm font-medium dark:bg-gray-800 dark:border-gray-600
                             dark:text-gray-300">
                Tindakan
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
              </button>
              <div id="tindakan-dropdown"
                   class="hidden absolute left-0 top-full mt-1 w-52 bg-white dark:bg-gray-800
                          border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg z-50">
                @perm('produk.delete')
                <button type="button" onclick="submitBulkDelete()"
                        class="flex items-center gap-2 w-full px-4 py-2.5 text-sm text-red-600
                               hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20">
                  Hapus yang dipilih
                </button>
                @endperm
              </div>
            </div>

            <!-- Cetak Barcode selected -->
            @perm('produk.viewAny')
            <button type="button" onclick="submitCetakBarcodeSelected()"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white
                           rounded-lg hover:bg-green-700 text-sm font-medium">
              Cetak Barcode
            </button>
            @endperm

            <!-- Reset Stok -->
            @perm('produk.viewAny')
            <button type="button" onclick="submitResetStok()"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white
                           rounded-lg hover:bg-blue-700 text-sm font-medium">
              Reset Stok
            </button>
            @endperm

        </div>
    </div>
</div>
            <x-table-footer :paginator="$products" />


    <!-- Hidden Forms for Bulk Actions -->
    <form id="form-cetak-selected" action="{{ route('produk.cetak-barcode-selected') }}" method="POST" class="hidden">
        @csrf
        <div id="cetak-selected-ids"></div>
    </form>

    <form id="form-bulk-delete" action="{{ route('produk.bulk-delete') }}" method="POST" class="hidden">
        @csrf
        <div id="bulk-delete-ids"></div>
    </form>

    <form id="form-reset-stok" action="{{ route('produk.reset-stok') }}" method="POST" class="hidden">
        @csrf
        <div id="reset-stok-ids"></div>
    </form>

    <div class="rounded-3xl bg-white border border-slate-200 p-4 shadow-sm dark:border-gray-700 dark:bg-slate-900">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm text-slate-700 dark:text-slate-300">
                <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                    <tr>
                        <th class="whitespace-nowrap px-4 py-3"><input type="checkbox" id="checkAll" class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" onchange="toggleCheckAll(this)"></th>
                        <th class="whitespace-nowrap px-4 py-3">Nama Produk</th>
                        <th class="whitespace-nowrap px-4 py-3">Gambar</th>
                        <th class="whitespace-nowrap px-4 py-3">Stok</th>
                        <th class="whitespace-nowrap px-4 py-3">Harga Modal</th>
                        <th class="whitespace-nowrap px-4 py-3">Harga Jual</th>
                        <th class="whitespace-nowrap px-4 py-3">No.Barcode</th>
                        <th class="whitespace-nowrap px-4 py-3">SKU</th>
                        <th class="whitespace-nowrap px-4 py-3">Produk Aktif</th>
                        <th class="whitespace-nowrap px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white dark:divide-gray-700 dark:bg-slate-900">
                    @forelse($products as $product)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800">
                            <td class="px-4 py-4"><input type="checkbox" name="product_ids[]" value="{{ $product->id }}" class="product-checkbox h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" onchange="onCheckboxChange()"></td>
                            <td class="px-4 py-4">
                                <div class="font-medium text-slate-900 dark:text-slate-100">{{ $product->name }}</div>
                                <div class="text-xs text-slate-500 dark:text-slate-400">{{ $product->category->name ?? 'Kategori tidak tersedia' }}</div>
                            </td>
                            <td class="px-4 py-4">
                                @if($product->image_url)
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-12 w-12 rounded-lg object-cover">
                                @else
                                    <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-slate-100 text-xs text-slate-500 dark:bg-slate-800 dark:text-slate-400">No Image</div>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-slate-900 dark:text-slate-100">{{ $product->stock }}</td>
                            <td class="px-4 py-4 text-slate-900 dark:text-slate-100">
                                @if($product->cost_price > 0)
                                    Rp {{ number_format($product->cost_price, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-4 text-slate-900 dark:text-slate-100">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                            <td class="px-4 py-4 text-slate-900 dark:text-slate-100">{{ $product->barcode ?? '-' }}</td>
                            <td class="px-4 py-4 text-slate-900 dark:text-slate-100">{{ $product->sku }}</td>
                            <td class="px-4 py-4">
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $product->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-200' : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200' }}">
                                    {{ $product->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex flex-wrap gap-2">
                                    @perm('produk.update')
                                    <button type="button" onclick="resetSingleStock({{ $product->id }})" class="inline-flex items-center rounded-full bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700">Reset stok</button>
                                    <a href="{{ route('produk.edit', $product->id) }}" class="inline-flex items-center rounded-full bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700">Ubah</a>
                                    @endperm
                                    @perm('produk.delete')
                                    <form action="{{ route('produk.destroy', $product->id) }}" method="POST" class="inline" data-confirm-message="Apakah Anda yakin ingin menghapus produk ini?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center rounded-full bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700">Hapus</button>
                                    </form>
                                    @endperm
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">Belum ada produk tersedia</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function toggleCheckAll(source) {
    document.querySelectorAll('.product-checkbox')
            .forEach(cb => cb.checked = source.checked);
    onCheckboxChange();
}

function onCheckboxChange() {
    const checked = document.querySelectorAll('.product-checkbox:checked');
    const toolbarDefault  = document.getElementById('toolbar-default');
    const toolbarSelected = document.getElementById('toolbar-selected');
    const checkAll = document.getElementById('checkAll');

    if (checked.length > 0) {
        toolbarDefault.classList.add('hidden');
        toolbarSelected.classList.remove('hidden');
        toolbarSelected.classList.add('flex');
    } else {
        toolbarDefault.classList.remove('hidden');
        toolbarSelected.classList.add('hidden');
        toolbarSelected.classList.remove('flex');
        document.getElementById('tindakan-dropdown').classList.add('hidden');
        if (checkAll) checkAll.checked = false;
    }
}

function toggleTindakanDropdown() {
    document.getElementById('tindakan-dropdown').classList.toggle('hidden');
}

document.addEventListener('click', function(e) {
    const wrapper = document.getElementById('tindakan-wrapper');
    if (wrapper && !wrapper.contains(e.target)) {
        document.getElementById('tindakan-dropdown').classList.add('hidden');
    }
});

function getSelectedIds() {
    return Array.from(document.querySelectorAll('.product-checkbox:checked'))
                .map(cb => cb.value);
}

function injectIds(containerId, ids) {
    const container = document.getElementById(containerId);
    container.innerHTML = '';
    ids.forEach(id => {
        const input = document.createElement('input');
        input.type  = 'hidden';
        input.name  = 'product_ids[]';
        input.value = id;
        container.appendChild(input);
    });
}

async function submitBulkDelete() {
    const ids = getSelectedIds();
    if (!ids.length) return;
    const ok = await window.showConfirm({ message: 'Yakin hapus ' + ids.length + ' produk?' });
    if (!ok) return;
    injectIds('bulk-delete-ids', ids);
    document.getElementById('form-bulk-delete').submit();
}

function submitCetakBarcodeSelected() {
    const ids = getSelectedIds();
    if (!ids.length) return;
    injectIds('cetak-selected-ids', ids);
    document.getElementById('form-cetak-selected').submit();
}

async function submitResetStok() {
    const ids = getSelectedIds();
    if (!ids.length) return;
    const ok = await window.showConfirm({ message: 'Yakin reset stok ' + ids.length + ' produk ke 0?' });
    if (!ok) return;
    injectIds('reset-stok-ids', ids);
    document.getElementById('form-reset-stok').submit();
}

async function resetSingleStock(id) {
    const ok = await window.showConfirm({ message: 'Yakin reset stok produk ini ke 0?' });
    if (!ok) return;
    injectIds('reset-stok-ids', [id]);
    document.getElementById('form-reset-stok').submit();
}
</script>
@endsection
