<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #000;
            margin: 0;
            padding: 0;
        }

        .header {
            background-color: #203764;
            color: white;
            padding: 20px 40px;
            height: 100px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }

        .header-table td {
            vertical-align: top;
        }

        .logo-placeholder {
            width: 80px;
            height: 80px;
            /* background-color: white; */
            /* Removed as image will fill */
            margin-right: 20px;
        }

        .logo-placeholder img {
            max-width: 100%;
            max-height: 100%;
        }

        .company-info {
            font-size: 12px;
            line-height: 1.4;
        }

        .company-name {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .invoice-title {
            text-align: right;
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .invoice-details {
            text-align: right;
            font-size: 12px;
        }

        .invoice-details table {
            float: right;
            border-collapse: collapse;
        }

        .invoice-details td {
            padding: 2px 5px;
            text-align: right;
        }

        .invoice-details .label {
            text-align: right;
        }

        .invoice-details .value {
            text-align: center;
            border-bottom: 1px solid white;
            min-width: 100px;
        }

        .content {
            padding: 20px 40px;
            border: 1px solid #000;
        }

        .customer-section {
            width: 100%;
            margin-bottom: 20px;
            display: table;
        }

        .customer-info {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }

        .payment-box-container {
            display: table-cell;
            width: 40%;
            vertical-align: top;
        }

        .payment-box {
            border: 2px solid #000;
            text-align: center;
        }

        .payment-box-header {
            border-bottom: 1px solid #000;
            padding: 5px;
            font-weight: bold;
            font-size: 12px;
        }

        .payment-box-amount {
            padding: 10px;
            font-size: 20px;
            font-weight: bold;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table th {
            background-color: #203764;
            color: white;
            padding: 8px;
            text-align: center;
            border: 1px solid #000;
            font-weight: normal;
        }

        .items-table td {
            border: 1px solid #000;
            padding: 5px 8px;
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
            display: table;
        }

        .bank-info {
            display: table-cell;
            width: 60%;
            vertical-align: top;
            padding-top: 10px;
        }

        .totals-table-container {
            display: table-cell;
            width: 40%;
            vertical-align: top;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 5px 8px;
            border: 1px solid #000;
        }

        .totals-table .label {
            background-color: #203764;
            color: white;
            text-align: left;
        }

        .totals-table .value {
            text-align: right;
            color: white;
            /* Default text color for totals rows, overridden below */
        }

        .totals-table tr:not(:last-child) .value {
            color: black;
            background-color: transparent;
        }

        .totals-table tr:last-child .value {
            background-color: #203764;
            color: white;
            font-weight: bold;
        }

        .signature-section {
            margin-top: 40px;
            text-align: right;
            padding-right: 40px;
        }

        .signature-box {
            display: inline-block;
            text-align: left;
            width: 200px;
        }

        .signature-image {
            height: 60px;
            margin: 10px 0;
            text-align: center;
        }

        .signature-image img {
            max-height: 100%;
            max-width: 100%;
        }

        .footer-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 20px;
            background-color: #203764;
        }

        .thank-you {
            margin-top: 20px;
            font-weight: bold;
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="container" style="border: 2px solid #000; margin: 0 auto;">
        <div class="header">
            <table class="header-table">
                <tr>
                    <td style="width: 100px;">
                        <div class="logo-placeholder">
                            <img src="{{ public_path('images/kopinvoice.png') }}" alt="Logo">
                        </div>
                    </td>
                    <td>
                        <div class="company-info">
                            <div class="company-name">KOPERASI JEMBAR RAHAYU SEJAHTERA</div>
                            <div>JL. Moch. Bagowi Kp. Bojong RT003 / RW002</div>
                            <div>Telepon : +6281546527513</div>
                            <div>Email : koperasikonsumenjembarrahayu@gmail.com</div>
                        </div>
                    </td>
                    <td style="width: 250px; vertical-align: top;">
                        <div class="invoice-title">INVOICE</div>
                        <div class="invoice-details">
                            <table>
                                <tr>
                                    <td class="label">No :</td>
                                    <td class="value">{{ $invoice->invoice_number }}</td>
                                </tr>
                                <tr>
                                    <td class="label">Tanggal :</td>
                                    <td class="value">{{ \Carbon\Carbon::parse($invoice->date)->format('d/m/Y') }}</td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="content" style="border: none;">
            <div class="customer-section">
                <div class="customer-info">
                    <div style="border: 1px solid #000; padding: 5px; width: 300px;">
                        <div style="font-size: 10px; color: #666;">Kepada Yth.</div>
                        <div style="font-weight: bold; font-size: 14px;">{{ $invoice->customer_name }}</div>
                    </div>
                </div>
                <div class="payment-box-container">
                    <div class="payment-box">
                        <div class="payment-box-header">Jumlah Yang Harus Di Bayar</div>
                        <div class="payment-box-amount">
                            Rp{{ number_format((float) $invoice->total_amount, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>

            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">No.</th>
                        <th style="width: 40%;">Deskripsi</th>
                        <th style="width: 10%;">Jumlah</th>
                        <th style="width: 10%;">Volume</th>
                        <th colspan="2" style="width: 15%;">Harga Satuan</th>
                        <th colspan="2" style="width: 20%;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoice->items as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $item->product_name }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-center">{{ $item->unit }}</td>
                            <td class="text-left" style="border-right: none;">Rp</td>
                            <td class="text-right" style="border-left: none;">
                                {{ number_format((float) $item->price, 0, ',', '.') }}</td>
                            <td class="text-left" style="border-right: none;">Rp</td>
                            <td class="text-right" style="border-left: none;">
                                {{ number_format((float) $item->total, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="totals-section">
                <div class="bank-info">
                    <div style="text-align: center; margin-bottom: 5px;">Transfer ke No. Rek :</div>
                    <div style="text-align: center; font-weight: bold;">8155688615 BNI a/n KOPERASI JEMBAR RAHAYU
                        SEJAHTERA
                    </div>

                    <div class="thank-you">
                        Terima Kasih
                    </div>
                </div>
                <div class="totals-table-container">
                    <table class="totals-table">
                        <tr>
                            <td class="label">Sub total</td>
                            <td class="value" style="text-align: right; color: black;">
                                Rp{{ number_format((float) $invoice->total_amount, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="label">Diskon</td>
                            <td class="value" style="text-align: right; color: black;">-</td>
                        </tr>
                        <tr>
                            <td class="label">Pajak</td>
                            <td class="value" style="text-align: right; color: black;">-</td>
                        </tr>
                        <tr>
                            <td class="label" style="font-weight: bold;">Total</td>
                            <td class="value" style="text-align: right;">
                                Rp{{ number_format((float) $invoice->total_amount, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="signature-section">
                <div class="signature-box">
                    <div>Hormat Kami,</div>
                    <div class="signature-image">
                        <img src="{{ public_path('images/ttd.png') }}" alt="Signature">
                    </div>
                    <div style="border-bottom: 1px solid #000; display: inline-block; width: 100%;">Rizki Ichsan Al-Fath
                    </div>
                    <div>Ketua Pengurus</div>
                </div>
            </div>
        </div>

        <div class="footer-bar" style="position: static; width: 100%; height: 20px; background-color: #203764;"></div>
    </div>

</body>

</html>
