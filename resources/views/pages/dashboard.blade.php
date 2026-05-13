@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <div class="rounded-xl bg-white p-6 shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-gray-800">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Dasbor</h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Ringkasan performa toko dan laporan cepat.</p>
            </div>
            <form method="GET" action="{{ route('dashboard') }}" class="w-full max-w-sm">
                <label class="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-300">Rentang Waktu</label>
                <select name="range" onchange="this.form.submit()"
                        class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 shadow-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    <option value="today" {{ $range === 'today' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="week" {{ $range === 'week' ? 'selected' : '' }}>Minggu Ini</option>
                    <option value="month" {{ $range === 'month' ? 'selected' : '' }}>Bulan Ini</option>
                    <option value="year" {{ $range === 'year' ? 'selected' : '' }}>Tahun Ini</option>
                </select>
            </form>
        </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-3">
        <div class="rounded-3xl bg-white p-6 shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-gray-800">
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Penjualan</p>
            <p class="mt-3 text-3xl font-semibold text-slate-900 dark:text-white">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</p>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Omset</p>
        </div>

        <div class="rounded-3xl bg-white p-6 shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-gray-800">
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Uang Masuk</p>
            <p class="mt-3 text-3xl font-semibold text-slate-900 dark:text-white">Rp {{ number_format($cashInflow, 0, ',', '.') }}</p>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Cash Inflow</p>
        </div>

        <div class="rounded-3xl bg-white p-6 shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-gray-800">
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Uang Keluar</p>
            <p class="mt-3 text-3xl font-semibold text-slate-900 dark:text-white">Rp {{ number_format($cashOutflow, 0, ',', '.') }}</p>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Cash Outflow</p>
        </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-3">
        <div class="rounded-3xl bg-white p-6 shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-gray-800">
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Uang Masuk</p>
            <p class="mt-3 text-2xl font-semibold text-slate-900 dark:text-white">Rp {{ number_format($totalUangMasuk, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-3xl bg-white p-6 shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-gray-800">
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Uang Keluar</p>
            <p class="mt-3 text-2xl font-semibold text-slate-900 dark:text-white">Rp {{ number_format($totalUangKeluar, 0, ',', '.') }}</p>
        </div>
        <div class="rounded-3xl bg-white p-6 shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-gray-800">
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Uang Toko</p>
            <p class="mt-3 text-2xl font-semibold text-slate-900 dark:text-white">Rp {{ number_format($totalUangToko, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="grid gap-4 xl:grid-cols-2">
        <div class="rounded-3xl bg-white p-6 shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Produk Terlaris</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Total jumlah pembelian per produk</p>
                </div>
                <div class="relative w-40">
                    <input id="produkTerlarisSearch" type="text" placeholder="Cari"
                           class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm text-slate-900 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
                </div>
            </div>
            <div class="mt-6 overflow-hidden rounded-3xl border border-slate-200 dark:border-gray-700">
                <table id="produkTerlarisTable" class="w-full text-left text-sm text-slate-700 dark:text-slate-300">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 dark:bg-gray-800 dark:text-slate-400">
                        <tr>
                            <th class="px-4 py-3">Gambar</th>
                            <th class="px-4 py-3">Nama Produk</th>
                            <th class="px-4 py-3">Jumlah Pembelian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white dark:divide-gray-700 dark:bg-gray-900">
                        @forelse($produkTerlaris as $product)
                            <tr>
                                <td class="px-4 py-4 align-middle text-sm text-slate-500 dark:text-slate-400">-
                                </td>
                                <td class="px-4 py-4 font-medium text-slate-900 dark:text-white">{{ $product->name }}</td>
                                <td class="px-4 py-4 text-slate-700 dark:text-slate-300">{{ $product->total_quantity }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">Tidak ada data produk terlaris</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-3xl bg-white p-6 shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Status Stok Produk</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Periksa status persediaan produk</p>
                </div>
                <div class="relative w-40">
                    <input id="stockStatusSearch" type="text" placeholder="Cari"
                           class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm text-slate-900 outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
                </div>
            </div>
            <div class="mt-6 overflow-hidden rounded-3xl border border-slate-200 dark:border-gray-700">
                <table id="stockStatusTable" class="w-full text-left text-sm text-slate-700 dark:text-slate-300">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-500 dark:bg-gray-800 dark:text-slate-400">
                        <tr>
                            <th class="px-4 py-3">Produk</th>
                            <th class="px-4 py-3">Stok</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white dark:divide-gray-700 dark:bg-gray-900">
                        @forelse($stockStatus as $product)
                            <tr>
                                <td class="px-4 py-4 font-medium text-slate-900 dark:text-white">{{ $product->name }}</td>
                                <td class="px-4 py-4 text-slate-700 dark:text-slate-300">{{ $product->stock }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">Tidak ada data stok</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="grid gap-4 xl:grid-cols-2">
        <div class="rounded-3xl bg-white p-6 shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Transaksi per Metode Pembayaran</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Total jumlah transaksi per metode pembayaran</p>
                </div>
            </div>
            <div class="mt-8">
                <div id="chartPaymentMethods" class="h-72"></div>
            </div>
            <div class="mt-8 space-y-3">
                @forelse($transactionsByMethod as $method)
                    <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3 text-sm dark:bg-gray-800">
                        <span class="text-slate-700 dark:text-slate-300">{{ $method['name'] }}</span>
                        <span class="font-semibold text-slate-900 dark:text-white">{{ $method['total'] }}</span>
                    </div>
                @empty
                    <div class="rounded-2xl bg-slate-50 px-4 py-4 text-center text-sm text-slate-500 dark:bg-gray-800 dark:text-slate-400">
                        Belum ada data metode pembayaran
                    </div>
                @endforelse
            </div>
        </div>

        <div class="rounded-3xl bg-white p-6 shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-gray-800">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Arus Kas</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Total jumlah pergerakan alur kas</p>
                </div>
            </div>
            <div class="mt-8">
                <div id="chartCashFlow" class="h-72"></div>
            </div>
            <div class="mt-8 space-y-3">
                <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-700 dark:bg-gray-800 dark:text-slate-300">
                    Cash in: Rp {{ number_format($cashInflow, 0, ',', '.') }}
                </div>
                <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-700 dark:bg-gray-800 dark:text-slate-300">
                    Cash out: Rp {{ number_format($cashOutflow, 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        window.dashboardChartData = {
            paymentMethods: @json($transactionsByMethod->pluck('total')),
            paymentMethodLabels: @json($transactionsByMethod->pluck('name')),
            cashFlowSeries: @json([$cashInflow, $cashOutflow]),
            cashFlowLabels: @json(['Cash Inflow', 'Cash Outflow']),
        };
    </script>
    <script>
        // Client-side table filters for the small dashboard tables
        function simpleFilter(inputId, tableSelector, columnIndex = 1) {
            const input = document.getElementById(inputId);
            if (!input) return;
            input.addEventListener('input', () => {
                const q = input.value.trim().toLowerCase();
                document.querySelectorAll(tableSelector + ' tbody tr').forEach(tr => {
                    const cells = tr.querySelectorAll('td');
                    const text = (cells[columnIndex] && cells[columnIndex].textContent || '').toLowerCase();
                    tr.style.display = text.includes(q) ? '' : 'none';
                });
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            simpleFilter('produkTerlarisSearch', '#produkTerlarisTable', 1);
            simpleFilter('stockStatusSearch', '#stockStatusTable', 0);
        });
    </script>
@endpush
@endsection
