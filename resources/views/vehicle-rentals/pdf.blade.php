<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .header-container {
            width: 100%;
            margin-bottom: 20px;
        }

        .logo-section {
            width: 50%;
            float: left;
        }

        .invoice-section {
            width: 50%;
            float: right;
            text-align: right;
        }

        .company-logo {
            width: 100px;
            margin-bottom: 10px;
        }

        .invoice-title {
            font-size: 20px;
            font-weight: bold;
            color: #777;
            margin-bottom: 5px;
        }

        .company-address {
            clear: both;
            margin-bottom: 20px;
            line-height: 1.4;
            font-size: 12px;
        }

        .recipient-section {
            margin-bottom: 30px;
            font-size: 12px;
        }

        .recipient-title {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table th {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 8px 5px;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
        }

        .items-table td {
            padding: 8px 5px;
            vertical-align: top;
        }

        .border-bottom {
            border-bottom: 1px solid #000;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .totals-section {
            width: 100%;
            margin-top: 10px;
        }

        .totals-table {
            float: right;
            width: 250px;
        }

        .totals-table td {
            padding: 3px 5px;
            font-size: 12px;
        }

        .payment-info {
            margin-top: 40px;
            font-size: 11px;
            line-height: 1.5;
        }

        .payment-title {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>

<body>
    <div class="header-container clearfix">
        <div class="logo-section">
            <img src="{{ public_path('images/LOGO JR.png') }}" class="company-logo" alt="Logo">
        </div>
        <div class="invoice-section">
            <div class="invoice-title">#INVOICE</div>
            <div>INVOICE NO : INV-{{ $invoice->invoice_number }}</div>
            <div>TANGGAL : {{ \Carbon\Carbon::parse($invoice->date)->format('d/m/Y') }}</div>
        </div>
    </div>

    <div class="company-address">
        Jl. Ahmad Bagowi Kp. Bojong RT. 003 RW. 007<br>
        Sukamantri - Kab. Tasikmalaya<br>
        Telp: 0852 2300 4240<br>
        Email: jr.jembarrahayu@gmail.com
    </div>

    <div class="recipient-section">
        <div class="recipient-title">KEPADA :</div>
        <div>{!! nl2br(e($invoice->customer_name)) !!}</div>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">NO</th>
                <th style="width: 45%; text-align: left;">DESKRIPSI</th>
                <th style="width: 15%;">JUMLAH</th>
                <th style="width: 15%;">HARGA UNIT</th>
                <th style="width: 20%;">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items as $index => $item)
                <tr class="{{ $loop->last ? 'border-bottom' : '' }}">
                    <td class="text-center">{{ $index + 1 }}.</td>
                    <td>
                        <div style="font-weight: bold;">{{ $item->description }}</div>
                        @if ($item->start_date && $item->end_date)
                            <div style="font-size: 10px; color: #666; margin-top: 3px;">
                                {{ \Carbon\Carbon::parse($item->start_date)->format('d/m/Y') }} -
                                {{ \Carbon\Carbon::parse($item->end_date)->format('d/m/Y') }}
                            </div>
                        @endif
                    </td>
                    <td class="text-center">
                        {{ number_format($item->quantity, floor($item->quantity) == $item->quantity ? 0 : 2, ',', '.') }}
                        {{ $item->unit }}
                    </td>
                    <td class="text-right">
                        Rp{{ number_format($item->price, 0, ',', '.') }}
                    </td>
                    <td class="text-right">
                        Rp{{ number_format($item->total, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="clearfix">
        <div class="totals-table">
            <table width="100%">
                <tr>
                    <td class="text-right" style="font-weight: bold;">SUBTOTAL :</td>
                    <td class="text-right">Rp{{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="text-right" style="font-weight: bold;">TOTAL AKHIR :</td>
                    <td class="text-right" style="font-weight: bold;">
                        Rp{{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="payment-info">
        <div class="payment-title">Pembayaran Via Transfer ke Rekening :</div>
        <div>BRI</div>
        <div style="font-weight: bold;">143701000476300 a/n Jembar Rahayu</div>
    </div>
</body>

</html>
