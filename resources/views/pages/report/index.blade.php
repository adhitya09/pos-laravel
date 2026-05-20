@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Laporan & Analitik</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">Kelola laporan tersimpan, unduh PDF, dan perbarui periode laporan.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            {{-- <a href="{{ route('report.export.pdf', ['month' => now()->month, 'year' => now()->year]) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export PDF
            </a> --}}
            <button type="button"
                    onclick="openReportCreateModal()"
                    class="btn btn-primary btn-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Laporan Baru
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-800 dark:bg-green-900/20 dark:text-green-100">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 p-6">
        <div class="flex items-center justify-between mb-4 gap-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Daftar Laporan</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Laporan tersimpan dengan periode dan tipe.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">Nama/Kode Laporan</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">Tipe Laporan</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">Dari Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">Sampai Tanggal</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
                    @forelse($reports as $report)
                        @php
                            $reportPayload = [
                                'id' => $report->id,
                                'type' => $report->type,
                                'from_date' => $report->from_date ? \Carbon\Carbon::parse($report->from_date)->format('Y-m-d') : null,
                                'to_date' => $report->to_date ? \Carbon\Carbon::parse($report->to_date)->format('Y-m-d') : null,
                                'code' => $report->code ?? null,
                                'name' => $report->name ?? null,
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-6 py-4 whitespace-normal text-sm text-gray-900 dark:text-gray-100">
                                <div class="font-semibold">{{ $report->name }}</div>
                                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $report->code }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $report->type_label }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $report->from_date->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $report->to_date->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <button type="button"
                                        data-report='@json($reportPayload)'
                                        onclick="openReportEditModalFromButton(this)"
                                        class="btn btn-secondary btn-xs">
                                    Edit
                                </button>
                                <a href="{{ route('report.export.pdf', ['from' => $report->from_date->format('Y-m-d'), 'to' => $report->to_date->format('Y-m-d')]) }}"
                                   class="btn btn-danger btn-xs">
                                    PDF
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                Belum ada laporan tersimpan. Gunakan tombol "Laporan Baru" untuk membuat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="report-modal" class="hidden fixed inset-0 z-40 items-center justify-center bg-slate-900/30 backdrop-blur-sm dark:bg-black/60 px-4 py-6">
    <div class="absolute inset-0" data-modal-backdrop></div>
    <div class="relative z-10 w-full max-w-2xl overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl shadow-slate-900/10 dark:border-slate-700 dark:bg-slate-900">
        <form id="report-modal-form" action="{{ route('report.store') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="_method" id="report-modal-method" value="POST" />
            <input type="hidden" name="form_mode" id="report-modal-form-mode" value="create" />
            <input type="hidden" name="report_id" id="report-edit-id" value="" />

            <div class="border-b border-slate-200 px-6 py-4 dark:border-slate-700">
                <h3 id="report-modal-title" class="text-lg font-semibold text-slate-900 dark:text-white">Buat Laporan Baru</h3>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Pilih tipe laporan dan periode tanggal.</p>
            </div>

            <div class="px-6 py-6">
                <div class="space-y-6">
                    <div>
                        <label class="mb-3 block text-sm font-medium text-slate-700 dark:text-slate-300">Tipe Laporan</label>
                        <div class="grid grid-cols-3 gap-2">
                            @php
                                $typeOptions = [
                                    ['value' => 'in', 'label' => 'Uang Masuk'],
                                    ['value' => 'out', 'label' => 'Uang Keluar'],
                                    ['value' => 'sales', 'label' => 'Penjualan'],
                                ];
                            @endphp
                            @foreach($typeOptions as $option)
                                <label class="cursor-pointer rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-center text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-100 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:border-slate-600 dark:hover:bg-slate-700">
                                    <input type="radio" name="type" value="{{ $option['value'] }}" class="peer sr-only" />
                                    <span class="block peer-checked:bg-slate-900 peer-checked:text-white peer-checked:border-slate-900 dark:peer-checked:bg-slate-700">{{ $option['label'] }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('type')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label for="report_from_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Dari Tanggal<span class="text-red-500">*</span></label>
                            <input id="report_from_date" name="from_date" type="date" required value="{{ old('from_date') }}"
                                   onclick="this.showPicker && this.showPicker()"
                                   class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-teal-500 focus:ring-teal-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white [color-scheme:light] dark:[color-scheme:dark] date-picker-input" />
                            @error('from_date')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="report_to_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sampai Tanggal<span class="text-red-500">*</span></label>
                            <input id="report_to_date" name="to_date" type="date" required value="{{ old('to_date') }}"
                                   onclick="this.showPicker && this.showPicker()"
                                   class="mt-2 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:border-teal-500 focus:ring-teal-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white [color-scheme:light] dark:[color-scheme:dark] date-picker-input" />
                            @error('to_date')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
                        <button type="button" onclick="closeReportModal()"
                                class="btn btn-secondary btn-md">
                            Cancel
                        </button>
                        <button id="report-modal-save-button" type="submit"
                                class="btn btn-primary btn-md hidden">
                            Save changes
                        </button>
                        <button id="report-modal-create-another-button" type="submit" name="create_another" value="1"
                                class="btn btn-secondary btn-md">
                            Create & create another
                        </button>
                        <button id="report-modal-create-button" type="submit"
                                class="btn btn-primary btn-md">
                            Create
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@if(session('open_create_modal') || ($errors->any() && old('form_mode') === 'create'))
    <script>
        window.reportOpenOnLoad = { mode: 'create' };
    </script>
@endif

@if($errors->any() && old('form_mode') === 'edit')
    <script>
        window.reportOpenOnLoad = {
            mode: 'edit',
            values: {
                id: '{{ old('report_id') }}',
                type: '{{ old('type') }}',
                from_date: '{{ old('from_date') }}',
                to_date: '{{ old('to_date') }}'
            }
        };
    </script>
@endif

<script>
    function openReportEditModalFromButton(button) {
        try {
            const report = JSON.parse(button.dataset.report);
            openReportEditModal(report.id, report);
        } catch (error) {
            console.error('Invalid report payload:', error);
            alert('Data laporan tidak valid.');
        }
    }
</script>
@endsection
