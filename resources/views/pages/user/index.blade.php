@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Manajemen User</h1>
        @perm('user.create')
        <a href="{{ route('user.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
            Tambah User
        </a>
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
                                    <a href="{{ route('user.edit', $user->id) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
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
    <div class="mt-4 px-6">
        <x-table-footer :paginator="$users" />
    </div>
</div>
@endsection
