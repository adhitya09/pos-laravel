@extends('layouts.app')

@section('title', 'Buat Produk')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Buat product</h1>
        </div>
        <a href="{{ route('produk.index') }}" class="inline-flex items-center rounded-full bg-slate-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
            Kembali
        </a>
    </div>

    <div class="rounded-3xl bg-white p-6 shadow-sm border border-slate-200 dark:border-gray-700 dark:bg-slate-900">
        @if($errors->any())
            <div class="mb-6 rounded-3xl bg-red-50 p-4 text-sm text-red-700 shadow-sm dark:bg-red-900 dark:text-red-200">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('produk.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid gap-6 xl:grid-cols-2">
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Nama Produk *</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Nama Produk"
                               class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:border-gray-700 dark:bg-slate-800 dark:text-white" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Harga Modal *</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-500">Rp</span>
                            <input type="number" name="cost_price" value="{{ old('cost_price') }}" step="0.01" placeholder="0" required
                                   class="w-full rounded-2xl border border-slate-300 bg-white px-12 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:border-gray-700 dark:bg-slate-800 dark:text-white">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Gambar Produk</label>
                        <input type="file" name="image" accept="image/*"
                               class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:border-gray-700 dark:bg-slate-800 dark:text-white">
                        <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">jika tidak diisi akan diisi foto default</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">SKU</label>
                        <input type="text" name="sku" value="{{ old('sku') }}" placeholder="SKU produk"
                               class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:border-gray-700 dark:bg-slate-800 dark:text-white">
                        <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">jika tidak diisi akan di generate otomatis</p>
                    </div>

                    <div class="flex items-center gap-3 rounded-3xl border border-slate-200 bg-slate-50 px-4 py-4 dark:border-gray-700 dark:bg-slate-800">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Produk Aktif</label>
                        <input type="checkbox" name="is_active" value="1" class="h-5 w-5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" checked>
                    </div>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Kategori Produk *</label>
                        <select name="category_id" required
                                class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:border-gray-700 dark:bg-slate-800 dark:text-white">
                            <option value="">Pilih salah satu opsi</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Harga Jual *</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-500">Rp</span>
                            <input type="number" name="price" value="{{ old('price') }}" step="0.01" placeholder="0"
                                   class="w-full rounded-2xl border border-slate-300 bg-white px-12 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:border-gray-700 dark:bg-slate-800 dark:text-white" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Stok Produk *</label>
                        <input type="hidden" name="stock" value="0">
                        <input type="number" value="0" readonly
                               class="w-full rounded-2xl border border-slate-300 bg-slate-100 px-4 py-3 text-sm text-slate-500 outline-none dark:border-gray-700 dark:bg-slate-800 dark:text-slate-400" />
                        <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Stok hanya dapat diisi/ditambah pada menejemen inventori</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Kode Barcode</label>
                        <input type="text" name="barcode" value="{{ old('barcode') }}" placeholder="Kode Barcode"
                               class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:border-gray-700 dark:bg-slate-800 dark:text-white">
                        <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">jika tidak diisi akan di generate otomatis</p>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Deskripsi Produk</label>
                <textarea name="description" rows="4"
                          class="w-full rounded-3xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:border-gray-700 dark:bg-slate-800 dark:text-white"
                          placeholder="Deskripsi produk">{{ old('description') }}</textarea>
            </div>

            <div class="flex flex-wrap items-center justify-end gap-3">
                <a href="{{ route('produk.index') }}" class="rounded-2xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-gray-700 dark:text-slate-200 dark:hover:bg-slate-800">
                    Batal
                </a>
                <button type="submit" class="rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">
                    Buat
                </button>
                <button type="submit" name="create_another" value="1" class="rounded-2xl bg-slate-800 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-900">
                    Buat & buat lainnya
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
