@extends('layouts.app')
@section('content')
<div class="p-6">
  {{-- Breadcrumb --}}
  <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Setting</h1>

  @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg dark:bg-green-900 dark:text-green-200">
      {{ session('success') }}
    </div>
  @endif

  {{-- Tabel --}}
  <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
    {{-- Toolbar --}}
    <div class="flex items-center justify-end p-4 border-b border-gray-200 dark:border-gray-700">
      <div class="flex items-center gap-2">
        <div class="relative">
          <input type="text" placeholder="Cari"
                 class="pl-8 pr-3 py-1.5 text-sm border border-gray-300 rounded-lg
                        focus:outline-none focus:ring-2 focus:ring-blue-500
                        dark:bg-gray-700 dark:border-gray-600 dark:text-white">
          <svg class="absolute left-2 top-2 w-4 h-4 text-gray-400" fill="none"
               stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
          </svg>
        </div>
        {{-- Column toggle icon --}}
        <button class="p-1.5 border border-gray-300 rounded-lg hover:bg-gray-50
                       dark:border-gray-600 dark:hover:bg-gray-700">
          <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
      </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
          <tr>
            <th class="w-8 px-4 py-3">
              <input type="checkbox" class="rounded border-gray-300">
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500
                       dark:text-gray-300 uppercase tracking-wider">Logo Toko</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500
                       dark:text-gray-300 uppercase tracking-wider">Nama Toko</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500
                       dark:text-gray-300 uppercase tracking-wider">Alamat Toko</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500
                       dark:text-gray-300 uppercase tracking-wider">Nomor Telepon</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500
                       dark:text-gray-300 uppercase tracking-wider">Print Via Bluetooth</th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500
                       dark:text-gray-300 uppercase tracking-wider"></th>
          </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
          @if($setting)
          <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
            <td class="px-4 py-4">
              <input type="checkbox" class="rounded border-gray-300">
            </td>
            <td class="px-6 py-4">
              @if($setting->store_logo)
                <img src="{{ asset('storage/' . $setting->store_logo) }}"
                     alt="Logo" class="w-10 h-10 object-contain rounded">
              @else
                <div class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded
                            flex items-center justify-center">
                  <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                       viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2
                             0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0
                             00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                </div>
              @endif
            </td>
            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">
              {{ $setting->store_name }}
            </td>
            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400 max-w-xs">
              {{ $setting->store_address }}
            </td>
            <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
              {{ $setting->store_phone }}
            </td>
            <td class="px-6 py-4">
              @if($setting->print_type === 'bluetooth')
                <svg class="w-6 h-6 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0"/>
                </svg>
              @else
                <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0"/>
                </svg>
              @endif
            </td>
            <td class="px-6 py-4 text-right">
              <button onclick="openSettingModal()"
                      class="inline-flex items-center gap-1.5 text-sm text-teal-600
                             hover:text-teal-800 dark:text-teal-400 font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5
                           m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Ubah
              </button>
            </td>
          </tr>
          @else
          <tr>
            <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
              Belum ada setting.
              <button onclick="openSettingModal()"
                      class="text-blue-600 hover:underline">Buat sekarang</button>
            </td>
          </tr>
          @endif
        </tbody>
      </table>
    </div>

    <div class="px-6">
      <x-table-footer :paginator="isset(
          // if single setting exists, pass as collection for summary
          $setting) ? collect([$setting]) : collect([])" />
    </div>
  </div>
</div>

{{-- MODAL UBAH SETTING --}}
<div id="setting-modal" class="fixed inset-0 z-50 hidden items-center justify-center px-4 py-6">
  <div class="fixed inset-0 z-40 bg-slate-900/30 backdrop-blur-sm dark:bg-black/60"
       onclick="closeSettingModal()"
       aria-hidden="true"></div>

  <div class="relative z-50 w-full max-w-4xl max-h-[90vh] overflow-y-auto rounded-2xl border border-gray-200 bg-white shadow-xl dark:border-gray-800 dark:bg-gray-900">

      <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-800">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Ubah setting</h2>
        <button type="button" onclick="closeSettingModal()"
                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <form action="{{ route('setting.update') }}" method="POST"
            enctype="multipart/form-data" class="p-6">
        @csrf @method('PUT')

        <div class="mb-6">
          <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 pb-2
                     border-b border-gray-200 dark:border-gray-700">Profil Toko</h3>

          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Nama Toko <span class="text-red-500">*</span>
            </label>
            <input type="text" name="store_name"
                   value="{{ old('store_name', $setting->store_name ?? '') }}"
                   required
                   class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-teal-500 focus:ring-teal-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500">
          </div>

          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Alamat Toko <span class="text-red-500">*</span>
            </label>
            <input type="text" name="store_address"
                   value="{{ old('store_address', $setting->store_address ?? '') }}"
                   required
                   class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-teal-500 focus:ring-teal-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500">
          </div>

          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Nomor Telepon <span class="text-red-500">*</span>
            </label>
            <input type="text" name="store_phone"
                   value="{{ old('store_phone', $setting->store_phone ?? '') }}"
                   required placeholder="+62 ..."
                   class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-teal-500 focus:ring-teal-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500">
          </div>
        </div>

        <div class="mb-6">
          <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4 pb-2
                     border-b border-gray-200 dark:border-gray-700">Setting Printer</h3>

          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Tipe Print <span class="text-red-500">*</span>
            </label>
              <div class="flex flex-wrap gap-2" id="printTypeToggle">
              <button type="button" onclick="setPrintType('kabel')"
                      id="btnKabel"
                      class="px-4 py-2 rounded-lg text-sm font-medium border transition-colors bg-white text-gray-700 border-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">
                Kabel (Server Local)
              </button>
              <button type="button" onclick="setPrintType('bluetooth')"
                      id="btnBluetooth"
                      class="px-4 py-2 rounded-lg text-sm font-medium border transition-colors bg-white text-gray-700 border-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600">
                Bluetooth
              </button>
            </div>
            <input type="hidden" name="print_type" id="printTypeValue"
                   value="{{ old('print_type', $setting->print_type ?? 'kabel') }}">
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
              Pastikan setiap masuk halaman kasir sambungkan bluetooth terlebih dahulu
            </p>
          </div>

          <div class="mb-4" id="printerNameSection">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Nama Printer (Khusus untuk kabel)
            </label>
            <input type="text" name="printer_name"
                   value="{{ old('printer_name', $setting->printer_name ?? '') }}"
                   placeholder="Contoh: Epson T20"
                   class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-teal-500 focus:ring-teal-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500">
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
              Samakan dengan nama printer yang anda gunakan dan sudah terdaftar
              atau terhubung di server yang sama.
            </p>
          </div>

          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
              Logo Toko
            </label>
            @if(isset($setting) && $setting->store_logo)
              <div class="mb-2 flex items-center gap-2 p-2 bg-gray-700 rounded-lg">
                <button type="button" onclick="clearLogo()"
                        class="w-5 h-5 bg-red-500 text-white rounded-full text-xs
                               flex items-center justify-center flex-shrink-0">
                  ×
                </button>
                <span class="text-sm text-gray-300 truncate" id="logoFileName">
                  {{ basename($setting->store_logo) }}
                </span>
                <span class="text-xs text-gray-400 ml-auto" id="logoFileSize"></span>
              </div>
            @endif
            <div id="logoUploadArea"
                 class="{{ (isset($setting) && $setting->store_logo) ? 'hidden' : '' }} rounded-xl border border-gray-300 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
              <input type="file" name="store_logo" id="storeLogoInput"
                     accept="image/*"
                     onchange="handleLogoChange(this)"
                     class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-teal-500 focus:ring-teal-500 file:mr-3 file:py-1 file:px-3 file:border-0 file:text-sm file:font-medium file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100 dark:border-gray-700 dark:bg-gray-800 dark:text-white dark:placeholder-gray-500">
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
              Pastikan format gambar adalah PNG
            </p>
          </div>
        </div>

        <div class="mt-6 flex flex-wrap items-center gap-3">
          <button type="submit"
                  class="btn btn-primary btn-md">
            Simpan
          </button>
          <button type="button" onclick="closeSettingModal()"
                  class="btn btn-secondary btn-md">
            Batal
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
window.openSettingModal = function () {
  const modal = document.getElementById('setting-modal');
  if (!modal) return;
  modal.classList.remove('hidden');
  modal.classList.add('flex');
  document.body.classList.add('overflow-hidden');
  initPrintTypeToggle();
};

window.closeSettingModal = function () {
  const modal = document.getElementById('setting-modal');
  if (!modal) return;
  modal.classList.add('hidden');
  modal.classList.remove('flex');
  document.body.classList.remove('overflow-hidden');
};

window.setPrintType = function (type) {
  const printTypeValue = document.getElementById('printTypeValue');
  const btnKabel = document.getElementById('btnKabel');
  const btnBluetooth = document.getElementById('btnBluetooth');
  const printerSection = document.getElementById('printerNameSection');

  if (printTypeValue) {
    printTypeValue.value = type;
  }

  const activeClass = 'px-4 py-2 rounded-lg text-sm font-medium border transition-colors bg-teal-600 text-white border-teal-600';
  const inactiveClass = 'px-4 py-2 rounded-lg text-sm font-medium border transition-colors bg-white text-gray-700 border-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600';

  if (btnKabel) {
    btnKabel.className = type === 'kabel' ? activeClass : inactiveClass;
  }
  if (btnBluetooth) {
    btnBluetooth.className = type === 'bluetooth' ? activeClass : inactiveClass;
  }

  if (printerSection) {
    printerSection.classList.toggle('hidden', type !== 'kabel');
  }
};

window.initPrintTypeToggle = function () {
  const currentType = document.getElementById('printTypeValue')?.value || 'kabel';
  window.setPrintType(currentType);
};

function handleLogoChange(input) {
  if (input.files && input.files[0]) {
    const file = input.files[0];
    console.log('Logo selected:', file.name);
  }
}

function clearLogo() {
  const input = document.getElementById('storeLogoInput');
  const uploadArea = document.getElementById('logoUploadArea');
  if (input) input.value = '';
  if (uploadArea) uploadArea.classList.remove('hidden');
}
</script>
@endsection
