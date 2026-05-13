@extends('layouts.app')

@section('title', 'Inventory')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Inventory</h1>
        </div>
        @perm('inventory.create')
        <a href="{{ route('inventory.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">
            Buat
        </a>
        @endperm
    </div>

    <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-6 dark:bg-slate-900 dark:border-slate-700">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
            <div class="relative w-full md:w-80">
                <form method="GET" action="{{ route('inventory.index') }}">
                    @foreach(request()->except('search') as $k => $v)
                        @if(is_array($v))
                            @foreach($v as $vv)
                                <input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">
                            @endforeach
                        @else
                            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                        @endif
                    @endforeach
                    <input name="search" type="search" placeholder="Cari" value="{{ $search ?? request('search') }}" class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:border-gray-700 dark:bg-slate-800 dark:text-white" />
                </form>
            </div>
            <div class="text-sm text-slate-500 dark:text-slate-400">Menampilkan 1 sampai {{ $inventories->count() }} dari {{ $inventories->count() }} hasil</div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                    <tr>
                        <th class="px-6 py-4">No.Referensi</th>
                        <th class="px-6 py-4">Tipe</th>
                        <th class="px-6 py-4">Sumber</th>
                        <th class="px-6 py-4">Total</th>
                        <th class="px-6 py-4">Notes</th>
                        <th class="px-6 py-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white dark:divide-slate-700 dark:bg-slate-900">
                    @forelse($inventories as $inventory)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900 dark:text-white">{{ $inventory->reference_no }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700 dark:text-slate-300">
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold
                                @if($inventory->type === 'in') bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200
                                @elseif($inventory->type === 'out') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                @else bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-200 @endif">
                                {{ $inventory->type === 'in' ? 'Masuk' : ($inventory->type === 'out' ? 'Keluar' : 'Penyesuaian') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700 dark:text-slate-300">{{ $inventory->source ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700 dark:text-slate-300">Rp {{ number_format($inventory->total_modal, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700 dark:text-slate-300">{{ $inventory->notes ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700 dark:text-slate-300">
                            @perm('inventory.update')
                            <a href="{{ route('inventory.edit', $inventory->id) }}" class="inline-flex items-center gap-2 rounded-full border border-emerald-600 px-3 py-1 text-emerald-600 transition hover:bg-emerald-50 dark:border-emerald-500 dark:text-emerald-300 dark:hover:bg-slate-800">
                                <span>Ubah</span>
                            </a>
                            @endperm
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-500 dark:text-slate-400">Belum ada data inventory</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            <x-table-footer :paginator="$inventories" />
        </div>
    </div>
</div>
@endsection
