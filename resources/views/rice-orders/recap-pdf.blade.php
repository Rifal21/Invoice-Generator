<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Rekap Pesanan Beras</title>
    <style>
        @page {
            margin: 1cm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .company-name {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .report-title {
            font-size: 14px;
            font-weight: bold;
            margin-top: 5px;
        }

        .report-date {
            font-size: 10px;
            color: #666;
        }

        .customer-section {
            margin-bottom: 30px;
        }

        .customer-header {
            font-size: 12px;
            font-weight: bold;
            background-color: #f3f4f6;
            padding: 5px 10px;
            border-left: 4px solid #4f46e5;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th {
            background-color: #f9fafb;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            border-bottom: 1px solid #000;
            padding: 8px 5px;
            text-align: left;
        }

        td {
            padding: 8px 5px;
            border-bottom: 1px solid #eee;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .footer {
            margin-top: 50px;
            width: 100%;
        }

        .grand-total {
            margin-top: 10px;
            padding: 10px;
            background-color: #4f46e5;
            color: white;
            font-weight: bold;
            text-align: right;
            border-radius: 5px;
        }

        .summary-table th,
        .summary-table td {
            border: 1px solid #000;
            padding: 5px;
        }

        .summary-table {
            border: 1px solid #000;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="company-name">KOPERASI KONSUMEN JEMBAR RAHAYU SEJAHTERA</div>
        <div class="report-title">LAPORAN REKAP PESANAN BERAS</div>
        <div class="report-date">
            Periode: {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} s/d
            {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}
        </div>
    </div>

    @php
        $grandTotal = 0;
        $customerSummary = [];
    @endphp

    @forelse($customerGroups as $customerName => $items)
        <div class="customer-section">
            <div class="customer-header">PELANGGAN: {{ $customerName }}</div>
            <table>
                <thead>
                    <tr>
                        <th style="width: 20%;">TANGGAL</th>
                        <th style="width: 25%;">NO. INVOICE</th>
                        <th style="width: 40%;">NAMA BARANG</th>
                        <th style="width: 15%;" class="text-right">QTY</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $customerTotal = 0;
                        $customerQty = [];
                    @endphp
                    @foreach ($items as $item)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($item->invoice->date)->format('d/m/Y') }}</td>
                            <td>{{ $item->invoice->invoice_number }}</td>
                            <td>{{ $item->product_name }}</td>
                            <td class="text-right">{{ number_format($item->quantity, 0, ',', '.') }} {{ $item->unit }}
                            </td>
                        </tr>
                        @php
                            $customerTotal += $item->total;
                            $grandTotal += $item->total;
                            $rawUnit = $item->unit ?: 'pcs';
                            $unit = strtolower(trim($rawUnit));
                            if (!isset($customerQty[$unit])) {
                                $customerQty[$unit] = ['sum' => 0, 'display' => $rawUnit];
                            }
                            $customerQty[$unit]['sum'] += $item->quantity;
                        @endphp
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-right" style="font-weight: bold; border-top: 1px solid #000;">
                            TOTAL QTY UNTUK {{ $customerName }}
                        </td>
                        <td class="text-right" style="font-weight: bold; border-top: 1px solid #000;">
                            @foreach ($customerQty as $data)
                                {{ number_format($data['sum'], 0, ',', '.') }} {{ $data['display'] }}@if (!$loop->last)
                                    ,<br>
                                @endif
                            @endforeach
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @php
            $customerSummary[$customerName] = [
                'qtys' => $customerQty,
            ];
        @endphp
        @empty
            <div class="text-center" style="padding: 50px;">Tidak ada data pesanan beras untuk periode ini.</div>
        @endforelse

        @if ($grandTotal > 0)
            @php
                $grandQty = [];
                foreach ($customerGroups as $items) {
                    foreach ($items as $item) {
                        $rawUnit = $item->unit ?: 'pcs';
                        $unit = strtolower(trim($rawUnit));
                        if (!isset($grandQty[$unit])) {
                            $grandQty[$unit] = ['sum' => 0, 'display' => $rawUnit];
                        }
                        $grandQty[$unit]['sum'] += $item->quantity;
                    }
                }
            @endphp
            <div style="margin-top: 20px;">
                <div style="font-weight: bold; font-size: 12px; margin-bottom: 5px; text-transform: uppercase;">Ringkasan
                    Per
                    Pelanggan:</div>
                <table class="summary-table">
                    <thead>
                        <tr>
                            <th style="width: 70%;">NAMA PELANGGAN</th>
                            <th style="width: 30%; text-align: right;">TOTAL QTY</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customerSummary as $name => $data)
                            <tr>
                                <td>{{ $name }}</td>
                                <td class="text-right">
                                    @foreach ($data['qtys'] as $qtyData)
                                        {{ number_format($qtyData['sum'], 0, ',', '.') }} {{ $qtyData['display'] }}
                                        @if (!$loop->last)
                                            ,<br>
                                        @endif
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="grand-total">
                GRAND TOTAL QTY KESELURUHAN:
                @foreach ($grandQty as $data)
                    {{ number_format($data['sum'], 0, ',', '.') }} {{ $data['display'] }}@if (!$loop->last)
                        ,
                    @endif
                @endforeach
            </div>
        @endif

        <div class="footer">
            <table style="width: 100%; border: none;">
                <tr style="border: none;">
                    <td style="width: 70%; border: none;"></td>
                    <td style="width: 30%; text-align: center; border: none;">
                        <div>Tasikmalaya, {{ date('d/m/Y') }}</div>
                        <div style="margin-bottom: 60px;">Hormat Kami,</div>
                        <div style="font-weight: bold; text-decoration: underline;">RIZKI ICHSAN AL-FATH</div>
                    </td>
                </tr>
            </table>
        </div>
    </body>

    </html>
