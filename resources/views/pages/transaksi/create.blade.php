@extends('layouts.app')

@section('title', 'Buat Transaksi')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Buat Transaksi</h1>
        </div>
        <a href="{{ route('transaksi.index') }}" class="inline-flex items-center rounded-2xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Kembali</a>
    </div>
    <form action="{{ route('transaksi.store') }}" method="POST" class="space-y-6">
        @csrf
        <div class="grid gap-6 lg:grid-cols-2">
            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Nama</label>
                <input type="text" name="customer_name" value="{{ old('customer_name') }}" class="w-full rounded-lg border px-3 py-2" />
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Metode Pembayaran</label>
                <select name="payment_method_id" class="w-full rounded-lg border px-3 py-2">
                    <option value="">Pilih</option>
                    @foreach($paymentMethods as $m)
                        <option value="{{ $m->id }}">{{ $m->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <h3 class="text-sm font-medium">Items</h3>
            <div id="page-items"></div>
            <button type="button" onclick="addPageItem()" class="btn btn-primary btn-sm mt-2">Tambah Item</button>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('transaksi.index') }}" class="btn btn-secondary btn-md">Batal</a>
            <button type="submit" class="btn btn-primary btn-md">Buat</button>
        </div>
    </form>
</div>

@php
    $_productsForJs = [];
    foreach ($products as $p) {
        $_productsForJs[] = ['id' => $p->id, 'name' => $p->name, 'price' => $p->price ?? 0, 'stock' => $p->stock ?? 0];
    }
@endphp

<script>
const pageProducts = @json($_productsForJs);
let pageItemIndex = 0;
function productOptions(selected=null){ return ['<option value="">Pilih</option>', ...pageProducts.map(p=>`<option value="${p.id}" ${p.id==selected? 'selected':''}>${p.name} (${p.stock})</option>`)].join(''); }
function addPageItem(){ const idx=pageItemIndex++; const container=document.getElementById('page-items'); const div=document.createElement('div'); div.className='grid grid-cols-4 gap-2 items-center my-2'; div.innerHTML=`<select name="items[${idx}][product_id]" class="rounded border px-2 py-1">${productOptions()}</select><input type="number" name="items[${idx}][quantity]" value="1" min="1" class="rounded border px-2 py-1" /><input type="number" name="items[${idx}][price]" value="0" step="0.01" class="rounded border px-2 py-1" /><button type="button" onclick="this.parentElement.remove()" class="text-red-600">Hapus</button>`; container.appendChild(div); }
</script>
@endsection
