@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Metode Pembayaran</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Perbarui data metode pembayaran</p>
        </div>
        <a href="{{ route('payment-method.index') }}"
           class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm font-medium">
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

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Logo Preview --}}
        <div class="space-y-4">
            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 text-center dark:border-gray-700 dark:bg-gray-900">
                <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Logo Saat Ini</span>
                <div class="mt-4 overflow-hidden rounded-xl bg-gray-100 dark:bg-gray-800">
                    @if($payment_method->logo)
                        <img src="{{ asset('storage/' . $payment_method->logo) }}" alt="{{ $payment_method->name }}" class="h-32 w-full object-cover" />
                    @else
                        <div class="flex h-32 items-center justify-center text-gray-400 dark:text-gray-500">
                            Tidak ada logo
                        </div>
                    @endif
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Ganti Logo</h2>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Unggah logo pembayaran baru</p>
                <label class="mt-4 flex cursor-pointer items-center justify-center rounded-lg border border-dashed border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 hover:border-blue-500 hover:text-blue-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:border-blue-500 dark:hover:text-blue-400">
                    Pilih File
                    <input type="file" name="logo_preview" accept="image/*" class="sr-only" />
                </label>
            </div>
        </div>

        {{-- Form --}}
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <form action="{{ route('payment-method.update', $payment_method) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nama Metode Pembayaran</label>
                            <input type="text" name="name" value="{{ old('name', $payment_method->name) }}" placeholder="contoh: Cash, QRIS, Transfer Bank"
                                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                                   required />
                        </div>

                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Jenis Pembayaran</label>
                                <div class="space-y-3">
                                    <label class="flex items-center">
                                        <input type="radio" name="is_cash" value="1" {{ old('is_cash', $payment_method->is_cash) == 1 ? 'checked' : '' }}
                                               class="h-4 w-4 border-gray-300 text-blue-600" />
                                        <span class="ml-3 text-gray-700 dark:text-gray-300">
                                            <span class="font-medium">Cash</span>
                                            <span class="block text-sm text-gray-500 dark:text-gray-400">Uang tunai langsung masuk ke kas</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="is_cash" value="0" {{ old('is_cash', $payment_method->is_cash) == 0 ? 'checked' : '' }}
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
                                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $payment_method->is_active) == 1 ? 'checked' : '' }}
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
                                      class="w-full rounded-lg border border-gray-300 bg-white px-4 py-3 text-gray-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white">{{ old('description', $payment_method->description) }}</textarea>
                        </div>

                        {{-- Info Box --}}
                        <div class="rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                            <div class="flex gap-3">
                                <svg class="h-5 w-5 text-blue-600 dark:text-blue-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                                <div class="text-sm text-blue-800 dark:text-blue-200">
                                    <strong>Catatan:</strong> Jenis pembayaran Cash akan secara otomatis dicatat ke cash flow, sedangkan Non-Cash memerlukan proses verifikasi lebih lanjut.
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <button type="submit"
                                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                                Update Metode Pembayaran
                            </button>
                            <a href="{{ route('payment-method.index') }}"
                               class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                                Batal
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
