@extends('layouts.app')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tambah Role</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Buat role/jabatan pengguna baru</p>
        </div>
        <a href="{{ route('role.index') }}"
           class="btn btn-secondary btn-sm">
            ← Kembali
        </a>
    </div>

    {{-- Error messages --}}
    @if($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-900 dark:bg-red-900/20">
            <h3 class="text-sm font-medium text-red-800 dark:text-red-200 mb-2">Kesalahan Validasi</h3>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li class="text-sm text-red-700 dark:text-red-300">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('role.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Form Card --}}
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Informasi Role</h2>
            </div>

            <div class="p-6 space-y-6">
                {{-- Nama Role --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nama Role <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="contoh: Admin, Kasir, Supervisor"
                           class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-teal-500 focus:ring-teal-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500"
                           required />
                    @error('name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Deskripsi</label>
                    <textarea name="description" rows="3" placeholder="Masukkan deskripsi role"
                              class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-teal-500 focus:ring-teal-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Permission Card --}}
        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Permissions</h2>
                    <label class="flex items-center gap-2">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Pilih Semua</span>
                        <input id="select-all" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-teal-600 focus:ring-teal-500 dark:border-gray-600 dark:bg-gray-800" />
                    </label>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach($permissionGroups as $group)
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                            <h3 class="mb-3 text-sm font-semibold text-gray-900 dark:text-white">{{ $group['label'] }}</h3>
                            <div class="space-y-3">
                                @foreach($group['actions'] as $actionKey => $label)
                                    @php $permValue = $group['resource'] . '.' . $actionKey; @endphp
                                    <label class="flex items-center gap-3">
                                        <input type="checkbox" name="permissions[]" value="{{ $permValue }}" class="permission-checkbox h-4 w-4 rounded border-gray-300 text-teal-600 focus:ring-teal-500 dark:border-gray-600 dark:bg-gray-700" {{ in_array($permValue, old('permissions', [])) ? 'checked' : '' }} />
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Buttons --}}
                <div class="mt-8 flex items-center gap-3 border-t border-gray-200 pt-6 dark:border-gray-800">
                    <button type="submit"
                            class="btn btn-primary btn-md">
                        Simpan Role
                    </button>
                    <a href="{{ route('role.index') }}"
                       class="btn btn-secondary btn-md">
                        Batal
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('select-all');
        const permissionCheckboxes = document.querySelectorAll('input.permission-checkbox');

        if (!selectAll || !permissionCheckboxes.length) return;

        // Handle "Select All" checkbox
        selectAll.addEventListener('change', function (e) {
            const checked = e.target.checked;
            permissionCheckboxes.forEach(cb => cb.checked = checked);
        });

        // Update "Select All" checkbox state when individual checkboxes change
        permissionCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                const allChecked = Array.from(permissionCheckboxes).every(cb => cb.checked);
                const someChecked = Array.from(permissionCheckboxes).some(cb => cb.checked);
                selectAll.checked = allChecked;
                selectAll.indeterminate = someChecked && !allChecked;
            });
        });
    });
</script>
@endpush
