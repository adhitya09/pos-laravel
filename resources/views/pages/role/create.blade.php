@extends('layouts.app')

@section('content')
<div class="p-6">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tambah Role</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Buat role/jabatan pengguna baru</p>
        </div>
        <a href="{{ route('role.index') }}"
           class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm font-medium">
            ← Kembali
        </a>
    </div>

    {{-- Error messages --}}
    @if($errors->any())
        <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg dark:bg-red-900 dark:text-red-200">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form action="{{ route('role.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Role</label>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="contoh: Admin, Kasir"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                       required />
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi</label>
                <textarea name="description" rows="4"
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                          placeholder="Masukkan deskripsi role">{{ old('description') }}</textarea>
            </div>

            {{-- Permission Matrix --}}
            <div class="mb-4">
                <div class="flex items-center justify-between mb-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Permissions</label>
                    <div class="flex items-center space-x-2">
                        <label class="text-sm text-gray-600 dark:text-gray-300">Pilih Semua</label>
                        <input id="select-all" type="checkbox" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($permissionGroups as $group)
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                            <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-2">{{ $group['label'] }}</h3>
                            <div class="space-y-2">
                                @foreach($group['actions'] as $actionKey => $label)
                                    @php $permValue = $group['resource'] . '.' . $actionKey; @endphp
                                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                        <input type="checkbox" name="permissions[]" value="{{ $permValue }}" class="permission-checkbox h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600" {{ in_array($permValue, old('permissions', [])) ? 'checked' : '' }} />
                                        <span>{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Tombol --}}
            <div class="flex items-center gap-3 mt-6">
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    Simpan Role
                </button>
                <a href="{{ route('role.index') }}"
                   class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('select-all');
        if (!selectAll) return;
        selectAll.addEventListener('change', function (e) {
            const checked = e.target.checked;
            document.querySelectorAll('input.permission-checkbox').forEach(cb => cb.checked = checked);
        });
    });
</script>
@endpush
