<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $transaksi->invoice_no }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .info-grid {
            margin-bottom: 30px;
        }
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        .info-label {
            width: 30%;
            font-weight: bold;
        }
        .info-value {
            width: 70%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        thead {
            background-color: #f5f5f5;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            font-weight: bold;
            background-color: #f5f5f5;
        }
        .text-right {
            text-align: right;
        }
        .summary {
            margin-top: 20px;
            text-align: right;
        }
        .summary-row {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 10px;
        }
        .summary-label {
            width: 150px;
            font-weight: bold;
        }
        .summary-value {
            width: 120px;
            text-align: right;
        }
        .total-row {
            border-top: 2px solid #000;
            font-weight: bold;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>TRANSAKSI</h1>
        <p style="margin: 0;">{{ $transaksi->invoice_no }}</p>
    </div>

    <div class="info-grid">
        <div class="info-row">
            <div class="info-label">No. Transaksi:</div>
            <div class="info-value">{{ $transaksi->invoice_no }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Tanggal:</div>
            <div class="info-value">{{ $transaksi->transaction_date->format('d M Y, H:i') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Nama Customer:</div>
            <div class="info-value">{{ $transaksi->customer_name ?? '-' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Metode Pembayaran:</div>
            <div class="info-value">{{ $transaksi->paymentMethod->name ?? '-' }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Status:</div>
            <div class="info-value">{{ ucfirst($transaksi->status) }}</div>
        </div>
    </div>

    <h3>Detail Item</h3>
    <table>
        <thead>
            <tr>
                <th>Nama Produk</th>
                <th class="text-right">Jumlah</th>
                <th class="text-right">Harga Modal</th>
                <th class="text-right">Harga Jual</th>
                <th class="text-right">Profit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaksi->transactionItems as $item)
            @php
                $profit = ($item->price - ($item->product->cost_price ?? 0)) * $item->quantity;
            @endphp
            <tr>
                <td>{{ $item->product->name ?? 'Produk Dihapus' }}</td>
                <td class="text-right">{{ $item->quantity }}</td>
                <td class="text-right">Rp {{ number_format($item->product->cost_price ?? 0, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($profit, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <div class="summary-row">
            <div class="summary-label">Total Amount:</div>
            <div class="summary-value">Rp {{ number_format($transaksi->total_amount, 0, ',', '.') }}</div>
        </div>
        <div class="summary-row">
            <div class="summary-label">Paid Amount:</div>
            <div class="summary-value">Rp {{ number_format($transaksi->paid_amount, 0, ',', '.') }}</div>
        </div>
        <div class="summary-row">
            <div class="summary-label">Change:</div>
            <div class="summary-value">Rp {{ number_format($transaksi->change_amount, 0, ',', '.') }}</div>
        </div>
        <div class="summary-row total-row">
            <div class="summary-label">Total Profit:</div>
            <div class="summary-value">Rp {{ number_format($totalProfit, 0, ',', '.') }}</div>
        </div>
    </div>
</body>
</html>
