@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tambah Metode Pembayaran</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Buat metode pembayaran baru ke dalam sistem</p>
        </div>
        <a href="{{ route('payment-method.index') }}"
           class="btn btn-secondary btn-sm">
            ← Kembali
        </a>
    </div>

    @if($errors->any())
        <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg dark:bg-red-900 dark:text-red-200">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form action="{{ route('payment-method.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nama Metode Pembayaran</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="contoh: Cash, QRIS, Transfer Bank"
                           class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                           required />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Logo/Ikon Pembayaran</label>
                    <div class="mt-2">
                        <label class="flex cursor-pointer items-center justify-center rounded-lg border border-dashed border-gray-300 bg-white px-6 py-8 text-sm font-medium text-gray-700 hover:border-blue-500 hover:text-blue-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:border-blue-500 dark:hover:text-blue-400">
                            <div class="text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20a4 4 0 004 4h24a4 4 0 004-4V20a4 4 0 00-4-4h-8l-4-4z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <p class="mt-2">Klik untuk unggah atau seret file</p>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF, WEBP (max 2MB)</p>
                            </div>
                            <input type="file" name="logo" accept="image/*" class="sr-only" />
                        </label>
                    </div>
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Jenis Pembayaran</label>
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="radio" name="is_cash" value="1" checked
                                       class="h-4 w-4 border-gray-300 text-blue-600" />
                                <span class="ml-3 text-gray-700 dark:text-gray-300">
                                    <span class="font-medium">Cash</span>
                                    <span class="block text-sm text-gray-500 dark:text-gray-400">Uang tunai langsung masuk ke kas</span>
                                </span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="is_cash" value="0"
                                       class="h-4 w-4 border-gray-300 text-blue-600" />
                                <span class="ml-3 text-gray-700 dark:text-gray-300">
                                    <span class="font-medium">Non-Cash</span>
                                    <span class="block text-sm text-gray-500 dark:text-gray-400">QRIS, Transfer, Kartu (perlu verifikasi)</span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Status Metode</label>
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1" checked
                                       class="h-4 w-4 rounded border-gray-300 text-blue-600" />
                                <span class="ml-2 text-gray-700 dark:text-gray-300">
                                    Aktif (dapat digunakan di transaksi)
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Deskripsi</label>
                    <textarea name="description" rows="4" placeholder="Masukkan deskripsi atau catatan tentang metode pembayaran ini"
                              class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white"></textarea>
                </div>

                <div class="flex items-center gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="submit"
                            class="btn btn-primary btn-md">
                        Simpan Metode Pembayaran
                    </button>
                    <a href="{{ route('payment-method.index') }}"
                       class="btn btn-secondary btn-md">
                        Batal
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
