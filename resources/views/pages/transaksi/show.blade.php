@extends('layouts.app')

@section('title', 'Detail Pesanan')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
        <a href="{{ route('transaksi.index') }}" class="hover:text-gray-900 dark:hover:text-gray-200">Transaksi</a>
        <span>/</span>
        <span>Detail Pesanan</span>
    </div>

    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Detail Pesanan</h1>
        <div class="flex space-x-3">
            <a href="{{ route('transaksi.pdf', $transaksi->id) }}" class="btn btn-outline btn-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                Download PDF
            </a>
            <a href="{{ route('pos.resi', $transaksi->id) }}" target="_blank" class="btn btn-success btn-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                Cetak Resi
            </a>
            <a href="{{ route('transaksi.index') }}" class="btn btn-secondary btn-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                Kembali
            </a>
        </div>
    </div>

    <!-- Transaction Header Info -->
    <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">No. Transaksi</label>
                <p class="text-gray-900 dark:text-white font-medium">{{ $transaksi->invoice_no }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Customer</label>
                <p class="text-gray-900 dark:text-white font-medium">{{ $transaksi->customer_name ?? '-' }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Metode Pembayaran</label>
                <div class="flex items-center gap-3 mt-2">
                    @if($transaksi->paymentMethod && $transaksi->paymentMethod->logo)
                        <img src="{{ asset('storage/' . $transaksi->paymentMethod->logo) }}" alt="{{ $transaksi->paymentMethod->name }}" class="h-6 w-6 rounded object-cover">
                    @else
                        <div class="h-6 w-6 rounded bg-gray-200 dark:bg-gray-700"></div>
                    @endif
                    <p class="text-gray-900 dark:text-white font-medium">{{ $transaksi->paymentMethod->name ?? 'N/A' }}</p>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Transaksi</label>
                <p class="text-gray-900 dark:text-white font-medium">{{ $transaksi->transaction_date->format('d M Y, H:i') }}</p>
            </div>
        </div>
    </div>

    <!-- Transaction Items -->
    <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Rincian Item</h3>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="text-left py-3 px-4 font-semibold text-gray-900 dark:text-white text-sm">Gambar</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-900 dark:text-white text-sm">Nama Produk</th>
                            <th class="text-right py-3 px-4 font-semibold text-gray-900 dark:text-white text-sm">Jumlah</th>
                            <th class="text-right py-3 px-4 font-semibold text-gray-900 dark:text-white text-sm">Harga Modal</th>
                            <th class="text-right py-3 px-4 font-semibold text-gray-900 dark:text-white text-sm">Harga Jual</th>
                            <th class="text-right py-3 px-4 font-semibold text-gray-900 dark:text-white text-sm">Profit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksi->transactionItems as $item)
                        @php
                            $costPrice = $item->product->cost_price ?? 0;
                            $profit = ($item->price - $costPrice) * $item->quantity;
                        @endphp
                        <tr class="border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="py-3 px-4">
                                @if($item->product && $item->product->image_url)
                                    <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="h-12 w-12 rounded object-cover">
                                @else
                                    <div class="h-12 w-12 rounded bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-gray-900 dark:text-white">{{ $item->product->name ?? 'Produk Dihapus' }}</td>
                            <td class="py-3 px-4 text-right text-gray-900 dark:text-white">{{ $item->quantity }}</td>
                            <td class="py-3 px-4 text-right text-gray-900 dark:text-white">Rp {{ number_format($costPrice, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right text-gray-900 dark:text-white">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right text-gray-900 dark:text-white font-medium">Rp {{ number_format($profit, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-8 px-4 text-center text-gray-500 dark:text-gray-400">
                                Tidak ada item
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Summary Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Informasi Pembayaran</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Total Amount:</span>
                    <span class="text-gray-900 dark:text-white font-medium">Rp {{ number_format($transaksi->total_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Paid Amount:</span>
                    <span class="text-gray-900 dark:text-white font-medium">Rp {{ number_format($transaksi->paid_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Change:</span>
                    <span class="text-gray-900 dark:text-white font-medium">Rp {{ number_format($transaksi->change_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Jenis Pembayaran:</span>
                    @if($transaksi->paymentMethod)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $transaksi->paymentMethod->is_cash ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' }}">
                            {{ $transaksi->paymentMethod->is_cash ? 'Cash' : 'Non-Cash' }}
                        </span>
                    @else
                        <span class="text-gray-500">-</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Summary</h3>
            <div class="space-y-3">
                @php
                    $totalProfit = $transaksi->transactionItems->sum(function ($item) {
                        $profit = ($item->price - ($item->product->cost_price ?? 0)) * $item->quantity;
                        return $profit;
                    });
                @endphp
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Status:</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($transaksi->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                        @elseif($transaksi->status === 'returned') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                        @elseif($transaksi->status === 'pending') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                        @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300 @endif">
                        {{ ucfirst($transaksi->status) }}
                    </span>
                </div>
                <div class="flex justify-between border-t border-gray-200 dark:border-gray-700 pt-3">
                    <span class="font-semibold text-gray-900 dark:text-white">Total Profit:</span>
                    <span class="font-bold text-green-600 dark:text-green-400 text-lg">Rp {{ number_format($totalProfit, 0, ',', '.') }}</span>
                </div>
                @if($transaksi->status === 'completed')
                <div class="flex gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                    <form action="{{ route('transaksi.return', $transaksi->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Return transaksi ini?')">
                        @csrf
                        <button type="submit" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                            Return Transaksi
                        </button>
                    </form>
                    <form action="{{ route('transaksi.destroy', $transaksi->id) }}" method="POST" class="flex-1" onsubmit="return confirm('Batalkan transaksi ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                            Batalkan
                        </button>
                    </form>
                </div>
                @elseif($transaksi->status === 'returned')
                <div class="pt-3 border-t border-gray-200 dark:border-gray-700 text-center">
                    <p class="text-yellow-600 dark:text-yellow-400 font-medium">Transaksi telah di-return</p>
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
