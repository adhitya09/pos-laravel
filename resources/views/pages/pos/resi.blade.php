<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Resi {{ $transaction->invoice_no }}</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    font-family: 'Courier New', Courier, monospace;
    font-size: 10px;
    line-height: 1.3;
    color: #000;
    background: #fff;
    width: 72mm;
    margin: 0 auto;
    padding: 4px 4px;
  }

  .store-name {
    font-size: 13px; font-weight: bold;
    text-align: center;
    letter-spacing: 0.5px;
  }
  .store-sub {
    font-size: 9px; text-align: center;
    color: #333; margin-top: 1px;
    word-break: break-word;
  }
  .center { text-align: center; }
  .bold { font-weight: bold; }

  .divider {
    border: none;
    border-top: 1px dashed #555;
    margin: 4px 0;
  }
  .divider-solid {
    border: none;
    border-top: 1px solid #000;
    margin: 4px 0;
  }

  .row {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1px;
    gap: 4px;
  }
  .row .label {
    color: #333;
    flex-shrink: 0;
    font-size: 9px;
  }
  .row .value {
    font-weight: 500;
    text-align: right;
    font-size: 9px;
    word-break: break-word;
  }

  .item-row { margin-bottom: 3px; }
  .item-name {
    font-weight: bold;
    word-break: break-word;
    font-size: 9.5px;
  }
  .item-detail {
    display: flex;
    justify-content: space-between;
    color: #333;
    font-size: 9px;
  }

  .total-row {
    display: flex;
    justify-content: space-between;
    font-weight: bold;
    font-size: 11px;
    margin: 2px 0;
  }

  .footer {
    text-align: center;
    font-size: 8.5px;
    color: #444;
    margin-top: 5px;
    line-height: 1.4;
  }

  /* PRINT-SPECIFIC: hide browser chrome, set exact paper */
  @media print {
    html, body {
      width: 72mm;
      margin: 0 !important;
      padding: 4px !important;
    }
    @page {
      size: 72mm auto;
      margin: 0mm;
    }
  }
</style>
</head>
<body>

  {{-- Header Toko --}}
  <div class="store-name">{{ $setting->store_name ?? 'POS SYSTEM' }}</div>
  @if($setting && $setting->store_address)
    <div class="store-sub">{{ $setting->store_address }}</div>
  @endif
  @if($setting && $setting->store_phone)
    <div class="store-sub">Telp: {{ $setting->store_phone }}</div>
  @endif

  <hr class="divider-solid">

  {{-- Info Transaksi --}}
  <div class="row">
    <span class="label">No. Invoice</span>
    <span class="value">{{ $transaction->invoice_no }}</span>
  </div>
  <div class="row">
    <span class="label">Tanggal</span>
    <span class="value">{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
  </div>
  @if($transaction->customer_name)
  <div class="row">
    <span class="label">Pelanggan</span>
    <span class="value">{{ $transaction->customer_name }}</span>
  </div>
  @endif
  <div class="row">
    <span class="label">Pembayaran</span>
    <span class="value">{{ $transaction->paymentMethod->name ?? '-' }}</span>
  </div>

  <hr class="divider">

  {{-- Item List --}}
  @foreach($transaction->transactionItems as $item)
    <div class="item-row">
      <div class="item-name">{{ $item->product->name ?? '[Produk dihapus]' }}</div>
      <div class="item-detail">
        <span>{{ $item->quantity }} x Rp{{ number_format($item->price, 0, ',', '.') }}</span>
        <span>Rp{{ number_format($item->subtotal, 0, ',', '.') }}</span>
      </div>
    </div>
  @endforeach

  <hr class="divider">

  {{-- Total --}}
  <div class="row">
    <span class="label">Subtotal</span>
    <span class="value">Rp{{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
  </div>
  <hr class="divider">
  <div class="total-row">
    <span>TOTAL</span>
    <span>Rp{{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
  </div>
  <div class="row" style="margin-top:4px">
    <span class="label">Bayar</span>
    <span class="value">Rp{{ number_format($transaction->paid_amount, 0, ',', '.') }}</span>
  </div>
  <div class="row">
    <span class="label">Kembali</span>
    <span class="value bold">Rp{{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
  </div>

  <hr class="divider-solid">

  {{-- Footer --}}
  <div class="footer">
    <p>Terima kasih atas kunjungan Anda!</p>
    <p>Barang yang sudah dibeli tidak dapat ditukar/dikembalikan.</p>
    @if($setting && $setting->store_name)
      <p style="margin-top:4px">— {{ $setting->store_name }} —</p>
    @endif
  </div>

  <script>
  window.addEventListener('load', function() {
    setTimeout(function() { window.print(); }, 300);
  });
  </script>
</body>
</html>
