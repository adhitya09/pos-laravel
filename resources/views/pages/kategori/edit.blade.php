@extends('layouts.app')

@section('content')
<div class="p-6">
    {{-- Header --}}
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between mb-6">
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">Kategori &gt; Edit</p>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Kategori</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Perbarui data kategori</p>
        </div>
        <div class="flex items-center gap-3 justify-end">
            @perm('kategori.delete')
            <form action="{{ route('kategori.destroy', $kategori->id) }}" method="POST" class="inline"
                  data-confirm-message="Yakin ingin menghapus kategori ini?">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="btn btn-danger btn-sm">
                    Hapus
                </button>
            </form>
            @endperm
            <a href="{{ route('kategori.index') }}"
               class="btn btn-secondary btn-sm">
                ← Kembali
            </a>
        </div>
    </div>

    {{-- Error messages --}}
    @if($errors->any())
        <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg dark:bg-red-900 dark:text-red-200">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Card --}}
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900 p-6 mb-6">
        <form action="{{ route('kategori.update', $kategori->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Kategori</label>
                <input type="text" name="name" value="{{ old('name', $kategori->name) }}" placeholder="Masukkan nama kategori"
                       class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-teal-500 focus:ring-teal-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500"
                       required />
            </div>

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi</label>
                <textarea name="description" rows="4"
                          class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-teal-500 focus:ring-teal-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500"
                          placeholder="Masukkan deskripsi kategori">{{ old('description', $kategori->description) }}</textarea>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-start">
                <button type="submit"
                        class="btn btn-primary btn-md">
                    Save changes
                </button>
                <a href="{{ route('kategori.index') }}"
                   class="btn btn-secondary btn-md">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    {{-- Products Table --}}
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <div class="flex flex-col gap-3 p-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Products</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Produk dalam kategori ini.</p>
            </div>
            <form method="GET" action="{{ route('kategori.edit', $kategori->id) }}" class="flex items-center gap-2">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari produk..."
                       class="w-full max-w-xs rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-teal-500 focus:ring-teal-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500" />
                <button type="submit"
                        class="btn btn-primary btn-sm">
                    Search
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Nama Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Jumlah Stok</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800 bg-white dark:bg-gray-900">
                    @forelse($products as $product)
                        <tr class="border-t border-gray-200 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $product->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $product->stock ?? 0 }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                Belum ada produk pada kategori ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
            <div class="border-t border-gray-200 dark:border-gray-800 p-4">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
