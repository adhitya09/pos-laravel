@extends('layouts.app')

@section('content')
<div class="grid grid-cols-12 gap-4 md:gap-6">
  <div class="col-span-12">
    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
      <div class="border-b border-stroke py-4 px-6.5 dark:border-strokedark">
        <h3 class="font-medium text-black dark:text-white">
          Edit User
        </h3>
      </div>
      <form action="{{ route('user.update', $user) }}" method="POST" class="p-6.5">
        @csrf
        @method('PUT')
        @if($errors->any())
          <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg dark:bg-red-900 dark:text-red-200">
            <ul class="list-disc list-inside">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif
        <div class="mb-4.5">
          <label class="mb-2.5 block text-slate-900 dark:text-white">
            Nama <span class="text-meta-1">*</span>
          </label>
          <input type="text" name="name" value="{{ old('name', $user->name) }}" placeholder="Masukkan nama lengkap" class="w-full rounded border-[1.5px] border-slate-300 bg-white dark:bg-slate-800 py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default dark:border-slate-700 dark:focus:border-primary" required />
        </div>
        <div class="mb-4.5">
          <label class="mb-2.5 block text-slate-900 dark:text-white">
            Email <span class="text-meta-1">*</span>
          </label>
          <input type="email" name="email" value="{{ old('email', $user->email) }}" placeholder="Masukkan alamat email" class="w-full rounded border-[1.5px] border-slate-300 bg-white dark:bg-slate-800 py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default dark:border-slate-700 dark:focus:border-primary" required />
        </div>
        <div class="mb-4.5">
          <label class="mb-2.5 block text-slate-900 dark:text-white">
            Password Baru (kosongkan jika tidak ingin mengubah)
          </label>
          <input type="password" name="password" placeholder="Masukkan password baru" class="w-full rounded border-[1.5px] border-slate-300 bg-white dark:bg-slate-800 py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default dark:border-slate-700 dark:focus:border-primary" />
        </div>
        <div class="mb-4.5">
          <label class="mb-2.5 block text-slate-900 dark:text-white">
            Role <span class="text-meta-1">*</span>
          </label>
          <select name="role_id" class="w-full rounded border-[1.5px] border-slate-300 bg-white dark:bg-slate-800 py-3 px-5 font-medium outline-none transition focus:border-primary active:border-primary disabled:cursor-default dark:border-slate-700 dark:focus:border-primary" required>
            <option value="">Pilih Role</option>
            @foreach($roles as $role)
              <option value="{{ $role->id }}" {{ (string) old('role_id', $user->role_id) === (string) $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
            @endforeach
          </select>
        </div>
        <button type="submit" class="btn btn-primary btn-lg w-full">
          Update User
        </button>
      </form>
    </div>
  </div>
</div>
@endsection
