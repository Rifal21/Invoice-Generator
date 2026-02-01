<!DOCTYPE html>
<html>

<head>
    <title>Invoice {{ $kitchenIncentive->invoice_number }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 14px;
            margin: 0;
            padding: 20px;
        }

        .header {
            margin-bottom: 40px;
        }

        .invoice-title-section {
            float: right;
            text-align: right;
        }

        .invoice-title-text {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #000;
            letter-spacing: 1px;
        }

        .invoice-details {
            font-size: 12px;
            line-height: 1.5;
        }

        .recipient-section {
            float: left;
            margin-top: 20px;
            font-size: 12px;
            line-height: 1.5;
        }

        .recipient-label {
            margin-bottom: 5px;
            font-weight: bold;
        }

        .clear {
            clear: both;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            border-top: 2px solid #000;
        }

        .items-table th {
            text-align: left;
            padding: 8px 5px;
            border-bottom: 1px solid #000;
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
        }

        .items-table td {
            padding: 15px 5px;
            vertical-align: top;
            font-size: 12px;
        }

        .items-table th.right,
        .items-table td.right {
            text-align: right;
        }

        .items-table th.center,
        .items-table td.center {
            text-align: center;
        }

        .totals-section {
            float: right;
            width: 40%;
            text-align: right;
            font-size: 12px;
        }

        .totals-row {
            margin-bottom: 5px;
        }

        .totals-label {
            display: inline-block;
            width: 100px;
            text-align: right;
            margin-right: 10px;
        }

        .totals-value {
            display: inline-block;
            width: 120px;
            text-align: right;
            font-weight: bold;
        }

        .footer-wrapper {
            margin-top: 50px;
            width: 100%;
        }

        .footer_info {
            font-size: 12px;
            line-height: 1.5;
            float: left;
            width: 60%;
        }

        .signature {
            float: right;
            text-align: center;
            width: 30%;
            font-size: 12px;
            margin-top: 100px;
        }

        .pic-label {
            margin-bottom: 50px;
            /* Space for signature */
        }

        .sig-name {
            font-weight: bold;
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="invoice-title-section">
            <div class="invoice-title-text">#INVOICE</div>
            <div class="invoice-details">
                INVOICE NO : {{ $kitchenIncentive->invoice_number }}<br>
                TANGGAL : {{ \Carbon\Carbon::parse($kitchenIncentive->date)->format('d/m/Y') }}
            </div>
        </div>
        <div class="recipient-section">
            <div class="recipient-label">KEPADA :</div>
            {{ $kitchenIncentive->customer->name ?? $kitchenIncentive->recipient_name }}<br>
            {!! nl2br(e($kitchenIncentive->customer->address ?? '')) !!}
        </div>
        <div class="clear"></div>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 40%;">NO DESKRIPSI</th>
                <th style="width: 20%;">JUMLAH</th>
                <th style="width: 20%;" class="right">HARGA UNIT</th>
                <th style="width: 20%;" class="right">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($kitchenIncentive->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}. {{ $item->description }}</td>
                    <td>
                        {{ $item->quantity + 0 }} {{ $item->unit }}<br>
                        @if ($item->duration_text)
                            <span style="display: block; margin-top: 2px;">{{ $item->duration_text }}</span>
                        @endif
                    </td>
                    <td class="right">Rp.{{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="right">Rp{{ number_format($item->total_price, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals-section">
        <div class="totals-row">
            <span class="totals-label">SUBTOTAL :</span>
            <span class="totals-value">Rp{{ number_format($kitchenIncentive->total_amount, 0, ',', '.') }}</span>
        </div>
        <div class="totals-row">
            <span class="totals-label">TOTAL AKHIR :</span>
            <span class="totals-value">Rp{{ number_format($kitchenIncentive->total_amount, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="footer-wrapper">
        <div class="footer_info">
            Pembayaran Via Transfer ke Rekening :<br>
            @if ($kitchenIncentive->customer && $kitchenIncentive->customer->bank_account_info)
                {!! nl2br(e($kitchenIncentive->customer->bank_account_info)) !!}
            @else
                <strong>BNI</strong><br>
                1991361018 a/n Iip Said Ahmad Ramadan<br>
                <strong>BCA</strong><br>
                0710122270 a/n Iip Said Ahmad Ramadan
            @endif
        </div>

        <div class="signature">
            <div class="pic-label">PIC,</div>
            <div class="sig-space"></div>
            <div class="sig-name">{{ $kitchenIncentive->customer->pic ?? 'Iip Said Ahmad Ramadan' }}</div>
        </div>
        <div class="clear"></div>
    </div>
</body>

</html>
