@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Metode Pembayaran</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kelola semua metode pembayaran dalam sistem</p>
        </div>
        @perm('payment-method.create')
        <a href="{{ route('payment-method.create') }}"
           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
            + Tambah Metode
        </a>
        @endperm
    </div>

    @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg dark:bg-green-900 dark:text-green-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Logo</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Nama Metode</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Tipe</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Deskripsi</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700 dark:text-gray-200">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($payment_methods as $method)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 {{ $method->trashed() ? 'opacity-50' : '' }}">
                            <td class="px-6 py-4">
                                @if($method->logo)
                                    <img src="{{ asset('storage/' . $method->logo) }}" alt="{{ $method->name }}" class="h-10 w-10 rounded object-cover">
                                @else
                                    <div class="h-10 w-10 rounded bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                        <span class="text-xs text-gray-500">No Logo</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $method->name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $method->is_cash ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200' : 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200' }}">
                                    {{ $method->is_cash ? 'Cash' : 'Non-Cash' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($method->trashed())
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-700 dark:bg-gray-900 dark:text-gray-200">
                                        Dihapus
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $method->is_active ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200' }}">
                                        {{ $method->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ Str::limit($method->description, 50) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    @perm('payment-method.update')
                                    <a href="{{ route('payment-method.edit', $method) }}"
                                       class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        Edit
                                    </a>
                                    @endperm
                                    @if($method->trashed())
                                        @perm('payment-method.restore')
                                        <form action="{{ route('payment-method.restore', $method->id) }}" method="POST" class="inline" data-confirm-message="Pulihkan metode pembayaran ini?">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-800 text-sm font-medium">
                                                Pulihkan
                                            </button>
                                        </form>
                                        @endperm
                                    @else
                                        @perm('payment-method.delete')
                                        <form action="{{ route('payment-method.destroy', $method) }}" method="POST" class="inline" data-confirm-message="Hapus metode pembayaran ini?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                                Hapus
                                            </button>
                                        </form>
                                        @endperm
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                Tidak ada metode pembayaran
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        <x-table-footer :paginator="$payment_methods" />
    </div>
</div>
@endsection
