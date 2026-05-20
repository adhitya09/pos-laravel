<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Keuangan {{ $month ?? 1 }} {{ $year ?? now()->year }}</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: Arial, sans-serif; font-size: 10px; color: #111; }
    .header { text-align: center; margin-bottom: 16px; border-bottom: 2px solid #0D9488; pb: 10px; }
    .header h1 { font-size: 16px; font-weight: bold; color: #0D9488; }
    .header p  { font-size: 10px; color: #666; margin-top: 2px; }
    .section-title {
      font-size: 12px; font-weight: bold; color: #fff;
      background: #0D9488; padding: 6px 10px; margin: 14px 0 6px;
    }
    .summary-grid {
      display: flex; gap: 10px; margin-bottom: 14px;
    }
    .summary-card {
      flex: 1; border: 1px solid #e5e7eb; border-radius: 6px;
      padding: 8px 10px; background: #f9fafb;
    }
    .summary-card .label { font-size: 9px; color: #6b7280; margin-bottom: 2px; }
    .summary-card .value { font-size: 13px; font-weight: bold; color: #111; }
    .summary-card.green .value { color: #0D9488; }
    .summary-card.red   .value { color: #dc2626; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
    th {
      background: #0D9488; color: #fff; padding: 5px 6px;
      text-align: left; font-size: 9px; font-weight: bold;
    }
    td { padding: 4px 6px; font-size: 9px; border-bottom: 1px solid #e5e7eb; }
    tr:nth-child(even) td { background: #f9fafb; }
    .badge-in  { background:#d1fae5; color:#065f46; padding:1px 5px; border-radius:8px; font-size:8px; }
    .badge-out { background:#fee2e2; color:#991b1b; padding:1px 5px; border-radius:8px; font-size:8px; }
    .badge-ok  { background:#dbeafe; color:#1e40af; padding:1px 5px; border-radius:8px; font-size:8px; }
    .footer {
      margin-top: 20px; border-top: 1px solid #e5e7eb;
      padding-top: 8px; text-align: center; font-size: 9px; color: #9ca3af;
    }
    .text-right { text-align: right; }
    .font-bold  { font-weight: bold; }
  </style>
</head>
<body>

  <div class="header">
    <h1>LAPORAN KEUANGAN</h1>
    <p>{{ $setting->store_name ?? 'POS System' }}</p>
    <p>Periode: {{ $periode ?? \Carbon\Carbon::create($year ?? now()->year, $month ?? now()->month)->translatedFormat('F Y') }}</p>
  {{-- Ringkasan --}}
  <div class="section-title">RINGKASAN</div>
  <table>
    <tr>
      <td style="width:50%; vertical-align:top; padding-right:8px;">
        <table>
          <tr><th colspan="2">Penjualan</th></tr>
          <tr>
            <td>Total Transaksi</td>
            <td class="text-right font-bold">{{ $totalTransaksi }}</td>
          </tr>
          <tr>
            <td>Total Penjualan</td>
            <td class="text-right font-bold" style="color:#0D9488">
              Rp{{ number_format($totalPenjualan, 0, ',', '.') }}
            </td>
          </tr>
          <tr>
            <td>Produk Terjual</td>
            <td class="text-right">{{ $produkTerjual }} item</td>
          </tr>
        </table>
      </td>
      <td style="width:50%; vertical-align:top; padding-left:8px;">
        <table>
          <tr><th colspan="2">Kas</th></tr>
          <tr>
            <td>Uang Masuk</td>
            <td class="text-right" style="color:#0D9488; font-weight:bold">
              Rp{{ number_format($cashFlowIn, 0, ',', '.') }}
            </td>
          </tr>
          <tr>
            <td>Uang Keluar</td>
            <td class="text-right" style="color:#dc2626; font-weight:bold">
              Rp{{ number_format($cashFlowOut, 0, ',', '.') }}
            </td>
          </tr>
          <tr>
            <td>Saldo Kas</td>
            <td class="text-right font-bold">
              Rp{{ number_format($cashFlowIn - $cashFlowOut, 0, ',', '.') }}
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>

  {{-- Tabel Transaksi --}}
  <div class="section-title">DAFTAR TRANSAKSI</div>
  <table>
    <thead>
      <tr>
        <th>Invoice</th>
        <th>Tanggal</th>
        <th>Pelanggan</th>
        <th>Metode</th>
        <th class="text-right">Total</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      @forelse($recentTransactions as $trx)
        <tr>
          <td>{{ $trx->invoice_no }}</td>
          <td>{{ $trx->created_at->format('d/m/Y H:i') }}</td>
          <td>{{ $trx->customer_name ?: 'Umum' }}</td>
          <td>{{ $trx->paymentMethod->name ?? '-' }}</td>
          <td class="text-right font-bold">
            Rp{{ number_format($trx->total_amount, 0, ',', '.') }}
          </td>
          <td>
            <span class="badge-ok">{{ ucfirst($trx->status) }}</span>
          </td>
        </tr>
      @empty
        <tr><td colspan="6" style="text-align:center;color:#9ca3af">Belum ada transaksi</td></tr>
      @endforelse
    </tbody>
  </table>

  {{-- Tabel Cash Flow --}}
  <div class="section-title">ARUS KAS</div>
  <table>
    <thead>
      <tr>
        <th>Tanggal</th>
        <th>Tipe</th>
        <th>Sumber</th>
        <th class="text-right">Nominal</th>
        <th>Catatan</th>
      </tr>
    </thead>
    <tbody>
      @forelse($cashFlows as $cf)
        <tr>
          <td>{{ \Carbon\Carbon::parse($cf->date)->format('d/m/Y') }}</td>
          <td>
            @if($cf->type === 'in')
              <span class="badge-in">Masuk</span>
            @else
              <span class="badge-out">Keluar</span>
            @endif
          </td>
          <td>{{ $cf->source->name ?? '-' }}</td>
          <td class="text-right font-bold"
              style="{{ $cf->type==='in' ? 'color:#0D9488' : 'color:#dc2626' }}">
            Rp{{ number_format($cf->amount, 0, ',', '.') }}
          </td>
          <td>{{ $cf->notes ?: '-' }}</td>
        </tr>
      @empty
        <tr><td colspan="5" style="text-align:center;color:#9ca3af">Belum ada data kas</td></tr>
      @endforelse
    </tbody>
  </table>

  <div class="footer">
    Dokumen ini dibuat otomatis oleh sistem POS —
    {{ $setting->store_name ?? 'POS System' }} —
    {{ now()->format('d/m/Y H:i') }}
  </div>

</body>
</html>
