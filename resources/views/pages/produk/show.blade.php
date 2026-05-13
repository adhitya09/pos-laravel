@extends('layouts.app')

@section('title', 'Detail Produk')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Detail Produk</h1>
        <div class="flex space-x-3">
            @perm('produk.update')
            <a href="{{ route('produk.edit', $produk->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                Edit Produk
            </a>
            @endperm
            <a href="{{ route('produk.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Product Image -->
        <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Gambar Produk</h3>
            @if($produk->image)
                <img src="{{ asset('storage/' . $produk->image) }}" alt="{{ $produk->name }}" class="w-full h-64 object-cover rounded-lg">
            @else
                <div class="w-full h-64 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            @endif
        </div>

        <!-- Product Details -->
        <div class="lg:col-span-2 bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Produk</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Produk</label>
                    <p class="text-gray-900 dark:text-white font-medium">{{ $produk->name }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">SKU</label>
                    <p class="text-gray-900 dark:text-white font-medium">{{ $produk->sku }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kategori</label>
                    <p class="text-gray-900 dark:text-white font-medium">{{ $produk->category->name ?? 'N/A' }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Harga</label>
                    <p class="text-gray-900 dark:text-white font-medium">Rp {{ number_format($produk->price, 0, ',', '.') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Stok</label>
                    <p class="text-gray-900 dark:text-white font-medium">{{ $produk->stock }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $produk->stock > 0 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' }}">
                        {{ $produk->stock > 0 ? 'Tersedia' : 'Habis' }}
                    </span>
                </div>
            </div>

            @if($produk->description)
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Deskripsi</label>
                <p class="text-gray-600 dark:text-gray-400">{{ $produk->description }}</p>
            </div>
            @endif

            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <div class="flex justify-between text-sm text-gray-500 dark:text-gray-400">
                    <span>Dibuat: {{ $produk->created_at->format('d M Y, H:i') }}</span>
                    <span>Diupdate: {{ $produk->updated_at->format('d M Y, H:i') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
