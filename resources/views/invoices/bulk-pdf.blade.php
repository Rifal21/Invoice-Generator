<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Pemeriksaan Bahan Makanan</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo {
            width: 100px;
            margin-bottom: 10px;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 2px 0;
            vertical-align: top;
        }

        .info-label {
            width: 80px;
            font-weight: bold;
        }

        .info-separator {
            width: 10px;
        }

        .type-title {
            font-size: 14px;
            font-weight: bold;
            margin-top: 30px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .data-table th {
            background-color: #BDD7EE;
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            font-weight: bold;
        }

        .data-table td {
            border: 1px solid #000;
            padding: 6px 8px;
        }

        .text-center {
            text-align: center;
        }

        .page-break {
            page-break-after: always;
        }

        /* Optimization for page breaks */
        .table-group {
            page-break-inside: avoid;
            margin-bottom: 20px;
        }

        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: avoid;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="{{ public_path('images/kopinvoice.png') }}" class="logo" alt="Logo">
        <div class="title">LAPORAN PEMERIKSAAN BAHAN MAKANAN</div>
    </div>

    <table class="info-table">
        <tr>
            <td class="info-label">Dari</td>
            <td class="info-separator">:</td>
            <td><strong>KOPERASI KONSUMEN JEMBAR RAHAYU SEJAHTERA</strong></td>
        </tr>
        <tr>
            <td class="info-label">Kepada</td>
            <td class="info-separator">:</td>
            <td><strong>{{ strtoupper($invoices->pluck('customer_name')->unique()->implode(', ')) }}</strong></td>
        </tr>
        <tr>
            <td class="info-label">Hari</td>
            <td class="info-separator">:</td>
            <td>
                @php
                    $firstInvoice = $invoices->first();
                    $date = $firstInvoice ? \Carbon\Carbon::parse($firstInvoice->date) : now();
                    $days = [
                        'Sunday' => 'Minggu',
                        'Monday' => 'Senin',
                        'Tuesday' => 'Selasa',
                        'Wednesday' => 'Rabu',
                        'Thursday' => 'Kamis',
                        'Friday' => 'Jumat',
                        'Saturday' => 'Sabtu',
                    ];
                    $months = [
                        '01' => 'Januari',
                        '02' => 'Februari',
                        '03' => 'Maret',
                        '04' => 'April',
                        '05' => 'Mei',
                        '06' => 'Juni',
                        '07' => 'Juli',
                        '08' => 'Agustus',
                        '09' => 'September',
                        '10' => 'Oktober',
                        '11' => 'November',
                        '12' => 'Desember',
                    ];
                    $dayName = $days[$date->format('l')];
                    $monthName = $months[$date->format('m')];
                @endphp
                <strong>{{ $dayName }}, {{ $date->format('d') }} {{ $monthName }}
                    {{ $date->format('Y') }}</strong>
            </td>
        </tr>
    </table>

    @foreach ($groupedItems as $type => $items)
        <div class="table-group">
            <div class="type-title">{{ $type }}</div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 40px;">No</th>
                        <th>Jenis Bahan</th>
                        <th style="width: 80px;">Quantity</th>
                        <th style="width: 80px;">Volume</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $item->product_name }}</td>
                            <td class="text-center">{{ number_format($item->quantity, 0, ',', '.') }}</td>
                            <td class="text-center">{{ $item->unit }}</td>
                            <td>{{ $item->description }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

</body>

</html>
