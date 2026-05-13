@extends('layouts.app')

@section('title', 'Cash Flow')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->


    <!-- Header -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Cash Flow</h1>
        </div>
        <button onclick="openCreateModal()" class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">
            Buat
        </button>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
        <!-- Total Modal -->
        <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-6 dark:bg-slate-900 dark:border-slate-700">
            <p class="text-sm text-slate-500 dark:text-slate-400">Total Modal</p>
            <h3 class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">Rp {{ number_format($totalModal, 0, ',', '.') }}</h3>
            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Total Capital</p>
        </div>

        <!-- Total Uang Masuk -->
        <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-6 dark:bg-slate-900 dark:border-slate-700">
            <p class="text-sm text-slate-500 dark:text-slate-400">Total Uang Masuk</p>
            <h3 class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">Rp {{ number_format($totalInflow, 0, ',', '.') }}</h3>
            <p class="mt-2 text-xs text-emerald-600 dark:text-emerald-400">
                <svg class="inline-block w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                    <path fill-rule="evenodd" d="M3.293 9.293a1 1 0 011.414 0L9 14.586l4.293-4.293a1 1 0 111.414 1.414l-5 5a1 1 0 01-1.414 0l-5-5a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
                Cash Inflow
            </p>
        </div>

        <!-- Total Uang Keluar -->
        <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-6 dark:bg-slate-900 dark:border-slate-700">
            <p class="text-sm text-slate-500 dark:text-slate-400">Total Uang Keluar</p>
            <h3 class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">Rp {{ number_format($totalOutflow, 0, ',', '.') }}</h3>
            <p class="mt-2 text-xs text-red-600 dark:text-red-400">
                <svg class="inline-block w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                    <path fill-rule="evenodd" d="M16.707 10.707a1 1 0 01-1.414 0L11 6.414l-4.293 4.293a1 1 0 11-1.414-1.414l5-5a1 1 0 011.414 0l5 5a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
                Cash Outflow
            </p>
        </div>

        <!-- Total Uang Toko -->
        <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-6 dark:bg-slate-900 dark:border-slate-700">
            <p class="text-sm text-slate-500 dark:text-slate-400">Total Uang Toko</p>
            <h3 class="mt-2 text-2xl font-bold text-slate-900 dark:text-white">Rp {{ number_format($totalToko, 0, ',', '.') }}</h3>
            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">masuk : Rp {{ number_format($totalInflow, 0, ',', '.') }} - Keluar : Rp {{ number_format($totalOutflow, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Table Section -->
    <div class="rounded-3xl bg-white border border-slate-200 shadow-sm p-6 dark:bg-slate-900 dark:border-slate-700">
        <!-- Controls -->
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-6">
            <div class="text-sm text-slate-500 dark:text-slate-400">
                Menampilkan 1 sampai {{ $cashFlows->count() }} dari {{ $cashFlows->total() }} hasil
            </div>
            <div class="flex items-center gap-2">
                <button onclick="openFilterModal()" class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-slate-300 text-slate-600 hover:bg-slate-50 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500 dark:bg-slate-800 dark:text-slate-400">
                    <tr>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">Type</th>
                        <th class="px-6 py-4">Source</th>
                        <th class="px-6 py-4">Amount</th>
                        <th class="px-6 py-4">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white dark:divide-slate-700 dark:bg-slate-900">
                    @forelse($cashFlows as $flow)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900 dark:text-white">
                            {{ $flow->date->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            {!! $flow->type_badge_html !!}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700 dark:text-slate-300">
                            {{ $flow->source->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-900 dark:text-white">
                            {{ $flow->formatted_amount }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <form method="POST" action="{{ route('cash-flow.destroy', $flow->id) }}" class="inline" data-confirm-message="Yakin ingin menghapus?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-sm text-slate-500 dark:text-slate-400">
                            Belum ada data cash flow
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            <x-table-footer :paginator="$cashFlows" />
        </div>
    </div>
</div>

<!-- Modal Create Cash Flow -->
<div id="createModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-md rounded-3xl bg-white shadow-xl dark:bg-slate-900">
        <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4 dark:border-slate-700">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Buat cash flow</h2>
            <button onclick="closeCreateModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>

        <form id="createForm" class="p-6 space-y-4">
            @csrf

            <!-- Type Toggle -->
            <div>
                <label class="block text-sm font-semibold text-slate-900 dark:text-white mb-3">Type</label>
                <div class="flex gap-2">
                    <button type="button" onclick="setType('in')" id="typeInBtn" class="flex-1 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition">
                        Masuk
                    </button>
                    <button type="button" onclick="setType('out')" id="typeOutBtn" class="flex-1 rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-800">
                        Keluar
                    </button>
                </div>
                <input type="hidden" name="type" id="typeInput" value="in" required>
            </div>

            <!-- Source Dropdown -->
            <div>
                <label for="sourceId" class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">Source</label>
                <select id="sourceId" name="source_id" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:border-slate-600 dark:bg-slate-800 dark:text-white dark:focus:ring-emerald-900" required>
                    <option value="">Pilih salah satu opsi</option>
                    @foreach($inSources as $source)
                    <option value="{{ $source->id }}">{{ $source->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Amount -->
            <div>
                <label for="amount" class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">Amount</label>
                <div class="relative">
                    <span class="absolute left-3 top-2 text-sm font-medium text-slate-600 dark:text-slate-400">Rp</span>
                    <input type="number" id="amount" name="amount" placeholder="0" min="1" class="w-full rounded-lg border border-slate-300 bg-white pl-10 pr-4 py-2 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:border-slate-600 dark:bg-slate-800 dark:text-white dark:focus:ring-emerald-900" required>
                </div>
            </div>

            <!-- Date -->
            <div>
                <label for="date" class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">Date</label>
                <input type="date" id="date" name="date" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:border-slate-600 dark:bg-slate-800 dark:text-white dark:focus:ring-emerald-900" required>
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">Notes</label>
                <textarea id="notes" name="notes" rows="3" placeholder="Catatan (opsional)" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:border-slate-600 dark:bg-slate-800 dark:text-white dark:focus:ring-emerald-900"></textarea>
            </div>

            <!-- Error Message -->
            <div id="errorMessage" class="hidden rounded-lg bg-red-100 p-3 text-sm text-red-800 dark:bg-red-900 dark:text-red-200"></div>

            <!-- Buttons -->
            <div class="flex gap-2 pt-4">
                <button type="submit" class="flex-1 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">
                    Buat
                </button>
                <button type="button" onclick="closeCreateModal()" class="flex-1 rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-800">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Filter -->
<div id="filterModal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-sm rounded-3xl bg-white shadow-xl dark:bg-slate-900">
        <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4 dark:border-slate-700">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white">Filter</h2>
            <button onclick="closeFilterModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>

        <form id="filterForm" method="GET" action="{{ route('cash-flow.index') }}" class="p-6 space-y-4">
            <!-- From Date -->
            <div>
                <label for="fromDate" class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">Dari Tanggal</label>
                <input type="date" id="fromDate" name="from_date" value="{{ request('from_date') }}" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:border-slate-600 dark:bg-slate-800 dark:text-white dark:focus:ring-emerald-900">
            </div>

            <!-- To Date -->
            <div>
                <label for="toDate" class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">Sampai Tanggal</label>
                <input type="date" id="toDate" name="to_date" value="{{ request('to_date') }}" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:border-slate-600 dark:bg-slate-800 dark:text-white dark:focus:ring-emerald-900">
            </div>

            <!-- Type Filter -->
            <div>
                <label for="filterType" class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">Tipe</label>
                <select id="filterType" name="type" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:border-slate-600 dark:bg-slate-800 dark:text-white dark:focus:ring-emerald-900">
                    <option value="">Semua Tipe</option>
                    <option value="in" {{ request('type') === 'in' ? 'selected' : '' }}>Masuk</option>
                    <option value="out" {{ request('type') === 'out' ? 'selected' : '' }}>Keluar</option>
                </select>
            </div>

            <!-- Source Filter -->
            <div>
                <label for="filterSource" class="block text-sm font-semibold text-slate-900 dark:text-white mb-2">Sumber</label>
                <select id="filterSource" name="source_id" class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 dark:border-slate-600 dark:bg-slate-800 dark:text-white dark:focus:ring-emerald-900">
                    <option value="">Semua Sumber</option>
                    @foreach($allSources as $source)
                    <option value="{{ $source->id }}" {{ request('source_id') == $source->id ? 'selected' : '' }}>{{ $source->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex gap-2 pt-4">
                <a href="{{ route('cash-flow.index') }}" class="flex-1 rounded-lg border border-red-300 px-4 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50 dark:border-red-600 dark:text-red-400 dark:hover:bg-slate-800 text-center">
                    Atur ulang filter
                </a>
                <button type="submit" class="flex-1 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">
                    Tutup
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let currentType = 'in';

// Store sources data
const inSources = {!! json_encode($inSources->map(fn($s) => ['id' => $s->id, 'name' => $s->name])->values()) !!};
const outSources = {!! json_encode($outSources->map(fn($s) => ['id' => $s->id, 'name' => $s->name])->values()) !!};

function setType(type) {
    currentType = type;
    const typeInput = document.getElementById('typeInput');
    if (typeInput) {
        typeInput.value = type;
    }

    // Update button styles
    const inBtn = document.getElementById('typeInBtn');
    const outBtn = document.getElementById('typeOutBtn');

    if (!inBtn || !outBtn) return;

    if (type === 'in') {
        inBtn.className = 'flex-1 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition';
        outBtn.className = 'flex-1 rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-800';
    } else {
        outBtn.className = 'flex-1 rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white transition';
        inBtn.className = 'flex-1 rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-800';
    }

    // Update source dropdown
    updateSourceOptions(type);
}

function updateSourceOptions(type) {
    const sourceSelect = document.getElementById('sourceId');
    if (!sourceSelect) return;

    const sources = type === 'in' ? inSources : outSources;

    // Rebuild source options based on type
    sourceSelect.innerHTML = '<option value="">Pilih salah satu opsi</option>';

    sources.forEach(source => {
        const option = document.createElement('option');
        option.value = source.id;
        option.textContent = source.name;
        sourceSelect.appendChild(option);
    });
}

function openCreateModal() {
    const modal = document.getElementById('createModal');
    if (modal) {
        modal.classList.remove('hidden');
        // Set default date to today
        const dateInput = document.getElementById('date');
        if (dateInput) {
            dateInput.valueAsDate = new Date();
        }
    }
}

function closeCreateModal() {
    const modal = document.getElementById('createModal');
    const form = document.getElementById('createForm');
    const errorMsg = document.getElementById('errorMessage');

    if (modal) modal.classList.add('hidden');
    if (form) form.reset();
    if (errorMsg) errorMsg.classList.add('hidden');

    setType('in');
}

function openFilterModal() {
    const modal = document.getElementById('filterModal');
    if (modal) modal.classList.remove('hidden');
}

function closeFilterModal() {
    const modal = document.getElementById('filterModal');
    if (modal) modal.classList.add('hidden');
}

// Form submission
document.addEventListener('DOMContentLoaded', function() {
    const createForm = document.getElementById('createForm');
    if (createForm) {
        createForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const errorDiv = document.getElementById('errorMessage');

            try {
                const response = await fetch('{{ route("cash-flow.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': formData.get('_token'),
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const data = await response.json();

                if (!response.ok) {
                    if (errorDiv) {
                        errorDiv.textContent = data.message || 'Terjadi kesalahan';
                        errorDiv.classList.remove('hidden');
                    }
                    return;
                }

                // Success
                closeCreateModal();
                location.reload();
            } catch (error) {
                if (errorDiv) {
                    errorDiv.textContent = 'Terjadi kesalahan saat menyimpan data';
                    errorDiv.classList.remove('hidden');
                }
            }
        });
    }

    // Close modals when clicking outside
    const createModal = document.getElementById('createModal');
    const filterModal = document.getElementById('filterModal');

    if (createModal) {
        createModal.addEventListener('click', function(e) {
            if (e.target === this) closeCreateModal();
        });
    }

    if (filterModal) {
        filterModal.addEventListener('click', function(e) {
            if (e.target === this) closeFilterModal();
        });
    }

    // Initialize
    setType('in');
});
</script>
@endsection
