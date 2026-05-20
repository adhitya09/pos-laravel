@extends('layouts.app')

@section('title', 'Edit Produk')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <div class="text-sm text-slate-500 dark:text-slate-400 mb-2">Product &gt; Edit</div>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Edit Produk</h1>
        </div>
        <a href="{{ route('produk.index') }}" class="inline-flex items-center rounded-full bg-slate-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
            ← Kembali
        </a>
    </div>

    @if($errors->any())
        <div class="rounded-3xl bg-red-50 p-4 text-sm text-red-700 shadow-sm dark:bg-red-900 dark:text-red-200">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-3xl bg-white p-6 shadow-sm border border-slate-200 dark:border-gray-700 dark:bg-slate-900">
        <div class="grid gap-6 xl:grid-cols-3">
            <div class="space-y-6">
                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5 dark:border-gray-700 dark:bg-slate-800">
                    <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">Preview Gambar Saat Ini</p>
                    <div class="mt-4 rounded-3xl bg-slate-100 p-3 dark:bg-slate-900">
                        @if($produk->image_url)
                            <img src="{{ $produk->image_url }}" alt="{{ $produk->name }}" class="h-56 w-full rounded-3xl object-cover" />
                        @else
                            <div class="flex h-56 items-center justify-center rounded-3xl bg-slate-200 text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                                Tidak ada gambar produk
                            </div>
                        @endif
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5 dark:border-gray-700 dark:bg-slate-800">
                    <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Ganti Gambar</h2>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Unggah file gambar baru untuk produk ini.</p>
                    <label class="mt-4 flex cursor-pointer items-center justify-center rounded-3xl border border-dashed border-slate-300 bg-white px-4 py-3 text-sm font-medium text-slate-700 hover:border-slate-400 hover:text-slate-900 dark:border-gray-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:border-slate-500">
                        Pilih File
                        <input type="file" name="image" accept="image/*" class="sr-only" />
                    </label>
                </div>
            </div>

            <div class="xl:col-span-2">
                <form action="{{ route('produk.update', $produk) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid gap-6 lg:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Nama Produk</label>
                            <input type="text" name="name" value="{{ old('name', $produk->name) }}" placeholder="Nama Produk"
                                   class="w-full rounded-3xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:border-gray-700 dark:bg-slate-800 dark:text-white" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Kategori Produk</label>
                            <select name="category_id" required
                                    class="w-full rounded-3xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:border-gray-700 dark:bg-slate-800 dark:text-white">
                                <option value="">Pilih salah satu opsi</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $produk->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid gap-6 lg:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Harga Modal</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-500">Rp</span>
                                <input type="number" name="cost_price" value="{{ old('cost_price', $produk->cost_price) }}" step="0.01" placeholder="0" required
                                       class="w-full rounded-3xl border border-slate-300 bg-white px-12 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:border-gray-700 dark:bg-slate-800 dark:text-white">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Harga Jual</label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-500">Rp</span>
                                <input type="number" name="price" value="{{ old('price', $produk->price) }}" step="0.01" placeholder="0"
                                       class="w-full rounded-3xl border border-slate-300 bg-white px-12 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:border-gray-700 dark:bg-slate-800 dark:text-white" required>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-6 lg:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">SKU</label>
                            <input type="text" name="sku" value="{{ old('sku', $produk->sku) }}" placeholder="SKU produk"
                                   class="w-full rounded-3xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:border-gray-700 dark:bg-slate-800 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Kode Barcode</label>
                            <input type="text" name="barcode" value="{{ old('barcode', $produk->barcode) }}" placeholder="Kode Barcode"
                                   class="w-full rounded-3xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:border-gray-700 dark:bg-slate-800 dark:text-white">
                        </div>
                    </div>

                    <div class="grid gap-6 lg:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Stok Produk</label>
                            <input type="number" name="stock" value="{{ old('stock', $produk->stock) }}" placeholder="0"
                                   class="w-full rounded-3xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:border-gray-700 dark:bg-slate-800 dark:text-white" required>
                            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Stok hanya dapat diisi/ditambah pada menejemen inventori</p>
                        </div>
                        <div class="flex items-center gap-3 rounded-3xl border border-slate-200 bg-slate-50 px-4 py-4 dark:border-gray-700 dark:bg-slate-800">
                            <input type="checkbox" name="is_active" value="1" class="h-5 w-5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" {{ old('is_active', $produk->is_active) ? 'checked' : '' }}>
                            <div>
                                <p class="text-sm font-medium text-slate-700 dark:text-slate-200">Produk Aktif</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Tandai jika produk tersedia</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Deskripsi Produk</label>
                        <textarea name="description" rows="4"
                                  class="w-full rounded-3xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:border-gray-700 dark:bg-slate-800 dark:text-white"
                                  placeholder="Deskripsi produk">{{ old('description', $produk->description) }}</textarea>
                    </div>

                    <div class="flex flex-wrap justify-end gap-3">
                        <a href="{{ route('produk.index') }}" class="btn btn-secondary btn-md">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary btn-md">
                            Update Produk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
