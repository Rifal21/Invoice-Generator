<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan</title>
    <style>
        body {
            font-family: sans-serif;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            text-transform: uppercase;
        }

        .header p {
            margin: 5px 0;
            color: #666;
            font-size: 14px;
        }

        .period {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: right;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            border-bottom: 2px solid #333;
            padding-bottom: 5px;
            margin-top: 20px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }

        .label {
            display: table-cell;
            text-align: left;
            font-size: 12px;
        }

        .value {
            display: table-cell;
            text-align: right;
            font-weight: bold;
            font-size: 12px;
        }

        .sub-value {
            color: #666;
            font-size: 10px;
        }

        .total-row {
            display: table;
            width: 100%;
            margin-top: 10px;
            border-top: 1px dotted #ccc;
            padding-top: 5px;
        }

        .total-label {
            display: table-cell;
            text-align: left;
            font-weight: bold;
            font-size: 14px;
        }

        .total-value {
            display: table-cell;
            text-align: right;
            font-weight: bold;
            font-size: 14px;
        }

        .highlight {
            background-color: #f0f0f0;
            padding: 10px;
            border-radius: 5px;
        }

        .profit {
            color: #10b981;
        }

        .loss {
            color: #ef4444;
        }

        .expense {
            color: #6b7280;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Laporan Laba Rugi</h1>
        <p>Koperasi Jasa Raharja</p>
    </div>

    <div class="period">
        Periode: {{ \Carbon\Carbon::parse($startDate)->isoFormat('D MMMM Y') }} -
        {{ \Carbon\Carbon::parse($endDate)->isoFormat('D MMMM Y') }}
    </div>

    <!-- 1. PENDAPATAN -->
    <div class="section-title">1. Pendapatan Usaha</div>
    <div class="row">
        <div class="label">Penjualan Kotor (Omset)</div>
        <div class="value">Rp{{ number_format($totalSales, 0, ',', '.') }}</div>
    </div>
    <div class="row">
        <div class="label" style="padding-left: 20px;">Harga Pokok Penjualan (HPP)</div>
        <div class="value expense">(Rp{{ number_format($totalHpp, 0, ',', '.') }})</div>
    </div>

    <div class="total-row" style="margin-bottom: 20px;">
        <div class="total-label">Laba Kotor</div>
        <div class="total-value profit">Rp{{ number_format($grossProfit, 0, ',', '.') }}</div>
    </div>

    <!-- 2. BEBAN OPERASIONAL -->
    <div class="section-title">2. Beban Operasional</div>
    <div class="row">
        <div class="label">Gaji Pegawai</div>
        <div class="value expense">Rp{{ number_format($totalSalaries, 0, ',', '.') }}</div>
    </div>

    @foreach ($expensesByCategory as $exp)
        <div class="row">
            <div class="label">{{ $exp->category }}</div>
            <div class="value expense">Rp{{ number_format($exp->total, 0, ',', '.') }}</div>
        </div>
    @endforeach

    <div class="total-row" style="margin-bottom: 20px;">
        <div class="total-label">Total Beban Operasional</div>
        <div class="total-value expense">
            (Rp{{ number_format($totalOperationalExpenses + $totalSalaries, 0, ',', '.') }})</div>
    </div>

    <!-- 3. LABA BERSIH -->
    <div class="highlight total-row" style="border-top: 2px solid #000; padding: 15px 10px;">
        <div class="total-label" style="font-size: 16px; text-transform: uppercase;">Laba Bersih (Net Profit)</div>
        <div class="total-value {{ $netProfit >= 0 ? 'profit' : 'loss' }}" style="font-size: 18px;">
            Rp{{ number_format($netProfit, 0, ',', '.') }}
        </div>
    </div>

    <!-- FOOTER -->
    <div style="margin-top: 50px; font-size: 10px; color: #999; text-align: center;">
        Dicetak otomatis oleh Sistem Invoice Generator pada {{ now()->format('d/m/Y H:i') }}
    </div>
</body>

</html>
