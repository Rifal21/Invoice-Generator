<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Laba Rugi - {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }

        .header p {
            margin: 5px 0;
            color: #666;
            font-size: 11px;
        }

        .invoice-info {
            margin-bottom: 20px;
        }

        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }

        .info-label {
            display: table-cell;
            width: 30%;
            font-weight: bold;
            font-size: 11px;
        }

        .info-value {
            display: table-cell;
            font-size: 11px;
        }

        .summary {
            background-color: #f0f9ff;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #3b82f6;
        }

        .summary-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }

        .summary-label {
            display: table-cell;
            font-size: 12px;
            font-weight: bold;
        }

        .summary-value {
            display: table-cell;
            text-align: right;
            font-size: 13px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th {
            background-color: #f3f4f6;
            padding: 10px 8px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            border-bottom: 2px solid #ddd;
        }

        td {
            padding: 8px;
            border-bottom: 1px solid #eee;
            font-size: 11px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: bold;
        }

        .profit {
            color: #10b981;
        }

        .loss {
            color: #ef4444;
        }

        .total-row {
            background-color: #f9fafb;
            font-weight: bold;
            border-top: 2px solid #333;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
            text-transform: uppercase;
            color: #333;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Laporan Laba Rugi Invoice</h1>
        <p>Koperasi Jasa Raharja</p>
    </div>

    <div class="invoice-info">
        <div class="info-row">
            <div class="info-label">No. Invoice:</div>
            <div class="info-value font-bold">{{ $invoice->invoice_number }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Tanggal:</div>
            <div class="info-value">{{ \Carbon\Carbon::parse($invoice->date)->isoFormat('D MMMM Y') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Pelanggan:</div>
            <div class="info-value">{{ $invoice->customer_name }}</div>
        </div>
    </div>

    <div class="section-title">Detail Item</div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 30%;">Produk</th>
                <th class="text-center" style="width: 10%;">Qty</th>
                <th class="text-right" style="width: 13%;">Harga Jual</th>
                <th class="text-right" style="width: 13%;">Subtotal</th>
                <th class="text-right" style="width: 13%;">HPP/Unit</th>
                <th class="text-right" style="width: 13%;">Total HPP</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->product_name }}</td>
                    <td class="text-center">{{ $item->quantity }} {{ $item->unit }}</td>
                    <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="text-right font-bold">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                    <td class="text-right" style="color: #f59e0b;">Rp
                        {{ number_format($item->hpp_per_unit, 0, ',', '.') }}</td>
                    <td class="text-right font-bold" style="color: #f59e0b;">Rp
                        {{ number_format($item->total_hpp, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4" class="text-right">TOTAL</td>
                <td class="text-right">Rp {{ number_format($invoice->sales, 0, ',', '.') }}</td>
                <td></td>
                <td class="text-right" style="color: #f59e0b;">Rp {{ number_format($invoice->hpp, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="summary">
        <div class="summary-row">
            <div class="summary-label">Total Penjualan:</div>
            <div class="summary-value">Rp {{ number_format($invoice->sales, 0, ',', '.') }}</div>
        </div>
        <div class="summary-row">
            <div class="summary-label">Total HPP (Modal):</div>
            <div class="summary-value" style="color: #f59e0b;">- Rp {{ number_format($invoice->hpp, 0, ',', '.') }}
            </div>
        </div>
        <div class="summary-row" style="margin-top: 10px; padding-top: 10px; border-top: 2px solid #3b82f6;">
            <div class="summary-label" style="font-size: 14px;">LABA KOTOR:</div>
            <div class="summary-value {{ $invoice->profit >= 0 ? 'profit' : 'loss' }}" style="font-size: 18px;">
                Rp {{ number_format($invoice->profit, 0, ',', '.') }}
            </div>
        </div>
        @if ($invoice->sales > 0)
            <div class="summary-row" style="margin-top: 5px;">
                <div class="summary-label" style="font-size: 10px; color: #666;">Margin:</div>
                <div class="summary-value" style="font-size: 11px; color: #666;">
                    {{ number_format(($invoice->profit / $invoice->sales) * 100, 1) }}%</div>
            </div>
        @endif
    </div>

    <div style="margin-top: 50px; font-size: 9px; color: #999; text-align: center;">
        Dicetak otomatis pada {{ now()->format('d/m/Y H:i') }}
    </div>
</body>

</html>
