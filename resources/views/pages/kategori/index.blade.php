@extends('layouts.app')

@section('content')
<div class="p-6">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Kategori Produk</h1>
        @perm('kategori.create')
        <a href="{{ route('kategori.create') }}"
           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
            + Tambah Kategori
        </a>
        @endperm
    </div>

    {{-- Flash message --}}
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg dark:bg-green-900 dark:text-green-200">
            {{ session('success') }}
        </div>
    @endif

    {{-- Tabel --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama Kategori</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Deskripsi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total Stok</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($categories as $category)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $category->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $category->description ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $category->products_sum_stock ?? 0 }} pcs</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-medium space-x-2">
                            @perm('kategori.update')
                            <a href="{{ route('kategori.edit', $category->id) }}"
                               class="text-blue-600 hover:text-blue-800 dark:text-blue-400">Edit</a>
                            @endperm
                            @perm('kategori.delete')
                            <form action="{{ route('kategori.destroy', $category->id) }}" method="POST" class="inline" data-confirm-message="Yakin ingin menghapus kategori ini?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400">
                                    Hapus
                                </button>
                            </form>
                            @endperm
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                            Belum ada kategori.
                            @perm('kategori.create')
                            <a href="{{ route('kategori.create') }}" class="text-blue-600 hover:underline">Tambah sekarang</a>
                            @endperm
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        <x-table-footer :paginator="$categories" />
    </div>
</div>
@endsection
