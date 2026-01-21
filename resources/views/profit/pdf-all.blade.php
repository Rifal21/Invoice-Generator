<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Laba Rugi Detail</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
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

        .period {
            font-size: 11px;
            font-weight: bold;
            margin-bottom: 15px;
            text-align: right;
        }

        .summary {
            background-color: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .summary-row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }

        .summary-label {
            display: table-cell;
            font-size: 11px;
            font-weight: bold;
        }

        .summary-value {
            display: table-cell;
            text-align: right;
            font-size: 14px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th {
            background-color: #f3f4f6;
            padding: 8px;
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
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Laporan Laba Rugi Detail</h1>
        <p>Koperasi Jasa Raharja</p>
    </div>

    <div class="period">
        Periode: {{ \Carbon\Carbon::parse($startDate)->isoFormat('D MMMM Y') }} -
        {{ \Carbon\Carbon::parse($endDate)->isoFormat('D MMMM Y') }}
    </div>

    <div class="summary">
        <div class="summary-row">
            <div class="summary-label">Total Penjualan:</div>
            <div class="summary-value">Rp {{ number_format($totalSales, 0, ',', '.') }}</div>
        </div>
        <div class="summary-row">
            <div class="summary-label">Total HPP:</div>
            <div class="summary-value" style="color: #f59e0b;">Rp {{ number_format($totalHpp, 0, ',', '.') }}</div>
        </div>
        <div class="summary-row" style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #ddd;">
            <div class="summary-label" style="font-size: 13px;">TOTAL LABA:</div>
            <div class="summary-value {{ $totalProfit >= 0 ? 'profit' : 'loss' }}" style="font-size: 16px;">Rp
                {{ number_format($totalProfit, 0, ',', '.') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 20%;">No. Invoice</th>
                <th style="width: 20%;">Pelanggan</th>
                <th class="text-right" style="width: 15%;">Penjualan</th>
                <th class="text-right" style="width: 12%;">HPP</th>
                <th class="text-right" style="width: 13%;">Laba</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoices as $index => $invoice)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($invoice->date)->isoFormat('D MMM Y') }}</td>
                    <td class="font-bold">{{ $invoice->invoice_number }}</td>
                    <td>{{ $invoice->customer_name }}</td>
                    <td class="text-right">Rp {{ number_format($invoice->sales, 0, ',', '.') }}</td>
                    <td class="text-right" style="color: #f59e0b;">Rp {{ number_format($invoice->hpp, 0, ',', '.') }}
                    </td>
                    <td class="text-right font-bold {{ $invoice->profit >= 0 ? 'profit' : 'loss' }}">
                        Rp {{ number_format($invoice->profit, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="4" class="text-right">TOTAL</td>
                <td class="text-right">Rp {{ number_format($totalSales, 0, ',', '.') }}</td>
                <td class="text-right" style="color: #f59e0b;">Rp {{ number_format($totalHpp, 0, ',', '.') }}</td>
                <td class="text-right {{ $totalProfit >= 0 ? 'profit' : 'loss' }}">Rp
                    {{ number_format($totalProfit, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 40px; font-size: 9px; color: #999; text-align: center;">
        Dicetak otomatis pada {{ now()->format('d/m/Y H:i') }}
    </div>
</body>

</html>
