@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Detail Transaksi</h1>
        <div class="flex space-x-3">
            <a href="{{ route('pos.resi', $transaksi->id) }}"
               target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white
                      rounded-lg text-sm hover:bg-blue-700 font-medium">
              🖨 Cetak Resi
            </a>
            <a href="{{ route('transaksi.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                Kembali
            </a>
        </div>
    </div>

    <!-- Transaction Header -->
    <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">No. Transaksi</label>
                <p class="text-gray-900 dark:text-white font-medium">{{ $transaksi->invoice_no }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal</label>
                <p class="text-gray-900 dark:text-white font-medium">{{ $transaksi->transaction_date->format('d M Y, H:i') }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Metode Pembayaran</label>
                <div class="flex items-center gap-3 mt-2">
                    @if($transaksi->paymentMethod && $transaksi->paymentMethod->logo)
                        <img src="{{ asset('storage/' . $transaksi->paymentMethod->logo) }}" alt="{{ $transaksi->paymentMethod->name }}" class="h-8 w-8 rounded object-cover">
                    @else
                        <div class="h-8 w-8 rounded bg-gray-200 dark:bg-gray-700"></div>
                    @endif
                    <p class="text-gray-900 dark:text-white font-medium">{{ $transaksi->paymentMethod->name ?? 'N/A' }}</p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                    @if($transaksi->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                    @elseif($transaksi->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                    @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300 @endif">
                    {{ ucfirst($transaksi->status) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Transaction Items -->
    <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Detail Item</h3>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-3 px-4 font-semibold text-gray-900 dark:text-white">Produk</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-900 dark:text-white">Harga</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-900 dark:text-white">Jumlah</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-900 dark:text-white">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksi->transactionItems as $item)
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <td class="py-3 px-4 text-gray-900 dark:text-white">{{ $item->product->name }}</td>
                            <td class="py-3 px-4 text-gray-900 dark:text-white">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-gray-900 dark:text-white">{{ $item->quantity }}</td>
                            <td class="py-3 px-4 text-gray-900 dark:text-white">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-8 px-4 text-center text-gray-500 dark:text-gray-400">
                                Tidak ada item
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="border-t border-gray-200 dark:border-gray-700">
                            <td colspan="3" class="py-3 px-4 text-right font-semibold text-gray-900 dark:text-white">Total:</td>
                            <td class="py-3 px-4 font-semibold text-gray-900 dark:text-white">Rp {{ number_format($transaksi->total_amount, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Transaction Summary -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Payment Info -->
        <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Pembayaran</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Metode Pembayaran:</span>
                    <div class="flex items-center gap-2">
                        @if($transaksi->paymentMethod && $transaksi->paymentMethod->logo)
                            <img src="{{ asset('storage/' . $transaksi->paymentMethod->logo) }}" alt="{{ $transaksi->paymentMethod->name }}" class="h-6 w-6 rounded object-cover">
                        @endif
                        <span class="text-gray-900 dark:text-white font-medium">{{ $transaksi->paymentMethod->name ?? 'N/A' }}</span>
                    </div>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Jenis Pembayaran:</span>
                    @if($transaksi->paymentMethod)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $transaksi->paymentMethod->is_cash ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' }}">
                            {{ $transaksi->paymentMethod->is_cash ? 'Cash (Tunai Langsung)' : 'Non-Cash (Perlu Verifikasi)' }}
                        </span>
                    @else
                        <span class="text-gray-500">-</span>
                    @endif
                </div>
                @if($transaksi->paymentMethod && !$transaksi->paymentMethod->is_cash)
                    <div class="mt-3 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded text-sm text-blue-800 dark:text-blue-200">
                        ⓘ Metode non-cash memerlukan proses verifikasi sebelum dicatat ke laporan kas. Pastikan ada bukti pembayaran (screenshot transfer, bukti QRIS, dll).
                    </div>
                @elseif($transaksi->paymentMethod && $transaksi->paymentMethod->is_cash)
                    <div class="mt-3 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded text-sm text-green-800 dark:text-green-200">
                        ✓ Transaksi cash otomatis dicatat ke laporan kas fisik.
                    </div>
                @endif
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Total Bayar:</span>
                    <span class="text-gray-900 dark:text-white font-medium">Rp {{ number_format($transaksi->total_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Status:</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($transaksi->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                        @elseif($transaksi->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                        @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300 @endif">
                        {{ ucfirst($transaksi->status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Transaction Info -->
        <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Transaksi</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Dibuat:</span>
                    <span class="text-gray-900 dark:text-white font-medium">{{ $transaksi->created_at->format('d M Y, H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Diupdate:</span>
                    <span class="text-gray-900 dark:text-white font-medium">{{ $transaksi->updated_at->format('d M Y, H:i') }}</span>
                </div>
                @if($transaksi->status === 'completed')
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <form action="{{ route('transaksi.destroy', $transaksi->id) }}" method="POST" data-confirm-message="Apakah Anda yakin ingin membatalkan transaksi ini?">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                            Batalkan Transaksi
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print { display: none !important; }
    body { background: white !important; }
    .bg-white { background: white !important; }
    .dark\:bg-gray-900 { background: white !important; }
    .border { border: 1px solid #000 !important; }
    .text-gray-900 { color: #000 !important; }
    .dark\:text-white { color: #000 !important; }
}
</style>
@endsection
