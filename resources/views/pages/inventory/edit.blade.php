@extends('layouts.app')

@section('content')
<div class="grid grid-cols-12 gap-4 md:gap-6">
  <div class="col-span-12">
    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
      <div class="border-b border-stroke py-4 px-6.5 dark:border-strokedark">
        <h3 class="font-medium text-black dark:text-white">
          Edit Inventory - {{ $inventory->reference_no }}
        </h3>
      </div>
      <form action="{{ route('inventory.update', $inventory->id) }}" method="POST" class="p-6.5 space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
          <div>
            <label class="mb-2.5 block text-black dark:text-white">Reference No</label>
            <input type="text" name="reference_no" value="{{ $inventory->reference_no }}" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input" required />
          </div>
          <div>
            <label class="mb-2.5 block text-black dark:text-white">Tanggal Inventory</label>
            <input type="date" name="inventory_date" value="{{ $inventory->inventory_date->format('Y-m-d') }}" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input" required />
          </div>
          <div>
            <label class="mb-2.5 block text-black dark:text-white">Tipe</label>
            <select name="type" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input" required>
              <option value="">Pilih Tipe</option>
              <option value="stock_in" {{ $inventory->type === 'stock_in' ? 'selected' : '' }}>Stock In</option>
              <option value="stock_out" {{ $inventory->type === 'stock_out' ? 'selected' : '' }}>Stock Out</option>
            </select>
          </div>
          <div>
            <label class="mb-2.5 block text-black dark:text-white">Notes</label>
            <textarea name="notes" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input">{{ $inventory->notes }}</textarea>
          </div>
        </div>

        <div class="rounded-sm border border-stroke bg-white p-4 dark:border-strokedark dark:bg-boxdark">
          <h4 class="mb-4 font-semibold text-black dark:text-white">Item Inventory Saat Ini</h4>
          <div class="overflow-x-auto">
            <table class="min-w-full text-left">
              <thead>
                <tr>
                  <th class="px-4 py-3 font-semibold text-black dark:text-white">Produk</th>
                  <th class="px-4 py-3 font-semibold text-black dark:text-white">Qty</th>
                  <th class="px-4 py-3 font-semibold text-black dark:text-white">Cost Price</th>
                </tr>
              </thead>
              <tbody>
                @forelse($inventory->inventoryItems as $item)
                <tr class="border-t border-stroke dark:border-strokedark">
                  <td class="px-4 py-3 text-black dark:text-white">{{ $item->product->name ?? '-' }}</td>
                  <td class="px-4 py-3 text-black dark:text-white">{{ $item->qty }}</td>
                  <td class="px-4 py-3 text-black dark:text-white">Rp {{ number_format($item->cost_price, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                  <td colspan="3" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">Belum ada item inventory</td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>

        <div class="rounded-sm border border-stroke bg-white p-4 dark:border-strokedark dark:bg-boxdark">
          <h4 class="mb-4 font-semibold text-black dark:text-white">Tambah Item Baru</h4>
          <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
            <div>
              <label class="mb-2.5 block text-black dark:text-white">Produk</label>
              <select name="new_product_id" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input">
                <option value="">Pilih Produk</option>
                @foreach($products as $product)
                  <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label class="mb-2.5 block text-black dark:text-white">Jumlah</label>
              <input type="number" name="new_qty" min="1" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input" />
            </div>
            <div>
              <label class="mb-2.5 block text-black dark:text-white">Cost Price</label>
              <input type="number" name="new_cost_price" step="0.01" min="0" class="w-full rounded border-[1.5px] border-stroke bg-transparent py-3 px-5 font-medium outline-none transition focus:border-primary dark:border-form-strokedark dark:bg-form-input" />
            </div>
          </div>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-end">
          <a href="{{ route('inventory.index') }}" class="inline-flex justify-center rounded border border-stroke bg-transparent px-5 py-3 text-sm font-medium text-black transition hover:bg-gray-100 dark:text-white dark:hover:bg-gray-800">Batal</a>
          <button type="submit" class="inline-flex justify-center rounded bg-primary px-5 py-3 text-sm font-medium text-white transition hover:bg-primary-hover">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
