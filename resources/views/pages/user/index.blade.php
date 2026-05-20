@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Manajemen User</h1>
        @perm('user.create')
        <button type="button" onclick="openUserCreateModal()" class="bg-teal-600 hover:bg-teal-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
            Tambah User
        </button>
        @endperm
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-lg border border-gray-200 dark:border-gray-800">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Daftar User</h2>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-slate-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">Dibuat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 dark:text-slate-300 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-slate-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($users as $user)
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 dark:text-white font-medium">{{ $user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 dark:text-white">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 dark:text-white">{{ $user->role->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 dark:text-white">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($user->email_verified_at) bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                    @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 @endif">
                                    {{ $user->email_verified_at ? 'Aktif' : 'Belum Verifikasi' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-600 dark:text-slate-400">{{ $user->created_at->format('d M Y') }}</td>
                            <td class="py-3 px-4">
                                <div class="flex space-x-2">
                                    @perm('user.update')
                                    @php
                                        $userPayload = [
                                            'id' => $user->id,
                                            'name' => $user->name,
                                            'email' => $user->email,
                                            'role_id' => $user->role_id ?? optional($user->role)->id,
                                        ];
                                    @endphp
                                    <button type="button"
                                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                        data-user='@json($userPayload)'
                                        onclick="openUserEditModalFromButton(this)"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    @endperm
                                    @if(!$user->email_verified_at)
                                    @perm('user.update')
                                    <form action="{{ route('user.verify', $user->id) }}" method="POST" class="inline" data-confirm-message="Tandai user ini sebagai terverifikasi?">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>
                                    </form>
                                    @endperm
                                    @endif
                                    @if($user->id !== auth()->id())
                                    @perm('user.delete')
                                    <form action="{{ route('user.destroy', $user->id) }}" method="POST" class="inline" data-confirm-message="Apakah Anda yakin ingin menghapus user ini?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                    @endperm
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-8 px-4 text-center text-gray-500 dark:text-gray-400">
                                Belum ada user
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="user-modal" class="hidden fixed inset-0 z-40 items-center justify-center overflow-y-auto px-4 py-10">
        <div class="fixed inset-0 bg-slate-900/30 backdrop-blur-sm dark:bg-black/60" data-modal-backdrop="user" onclick="closeUserModal()"></div>
        <div class="relative z-50 w-full max-w-3xl rounded-2xl border border-gray-200 bg-white shadow-xl dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                <div>
                    <h2 id="user-modal-title" class="text-lg font-semibold text-slate-900 dark:text-white">Buat User</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Isi data pengguna</p>
                </div>
                <button type="button" onclick="closeUserModal()" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                    Batal
                </button>
            </div>
            <div class="p-6 space-y-6">
                <form id="user-modal-form-create" action="{{ route('user.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="_modal_form" value="create">

                    @if($errors->any() && old('_modal_form') === 'create')
                        <div class="rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700 dark:bg-red-900 dark:text-red-200">
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Nama</label>
                            <input type="text" name="name" value="{{ old('name') }}" required placeholder="Nama lengkap" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-teal-500 focus:ring-teal-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500" />
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" required placeholder="email@example.com" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-teal-500 focus:ring-teal-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500" />
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                            <input type="password" name="password" required placeholder="Minimal 8 karakter" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-teal-500 focus:ring-teal-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500" />
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" required placeholder="Konfirmasi password" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-teal-500 focus:ring-teal-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500" />
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Peran / Role</label>
                        <select name="role_id" required class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-teal-500 focus:ring-teal-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500">
                            <option value="">-- Pilih --</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700">
                            Create
                        </button>
                        <button type="submit" name="create_another" value="1" class="inline-flex items-center justify-center rounded-lg bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700">
                            Create & create another
                        </button>
                        <button type="button" onclick="closeUserModal()" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                            Cancel
                        </button>
                    </div>
                </form>

                <form id="user-modal-form-edit" action="#" method="POST" class="hidden space-y-6" data-route-template="{{ route('user.update', ['user' => '__ID__']) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="_modal_form" value="edit">
                    <input type="hidden" name="user_id" value="{{ old('user_id') }}" id="user-modal-edit-user-id">

                    @if($errors->any() && old('_modal_form') === 'edit')
                        <div class="rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700 dark:bg-red-900 dark:text-red-200">
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Nama</label>
                            <input type="text" name="name" value="{{ old('name') }}" required placeholder="Nama lengkap" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-teal-500 focus:ring-teal-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500" />
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" required placeholder="email@example.com" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-teal-500 focus:ring-teal-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500" />
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Password Baru (kosongkan jika tidak ingin mengubah)</label>
                            <input type="password" name="password" placeholder="Masukkan password baru" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-teal-500 focus:ring-teal-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500" />
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Peran / Role</label>
                            <select name="role_id" required class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-teal-500 focus:ring-teal-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500">
                                <option value="">-- Pilih --</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ (string) old('role_id') === (string) $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700">
                            Save changes
                        </button>
                        <button type="button" onclick="closeUserModal()" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="mt-4 px-6">
        <x-table-footer :paginator="$users" />
    </div>
</div>

@push('scripts')
<script>
    window.addEventListener('DOMContentLoaded', function () {
        @if(session('open_create_modal'))
            openUserCreateModal();
        @endif

        @if(old('_modal_form') === 'create')
            openUserCreateModal(true);
        @endif

        @if(old('_modal_form') === 'edit' && old('user_id'))
            openUserEditModalFromData({
                id: {!! json_encode(old('user_id')) !!},
                name: {!! json_encode(old('name', '')) !!},
                email: {!! json_encode(old('email', '')) !!},
                role_id: {!! json_encode(old('role_id', '')) !!},
            });
        @endif
    });
</script>
@endpush
@endsection
