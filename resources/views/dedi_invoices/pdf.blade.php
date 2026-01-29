<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>{{ $dedi_invoice->invoice_number }} - {{ $dedi_invoice->customer_name }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }

        .header {
            width: 100%;
            margin-bottom: 20px;
        }

        .header-table {
            width: 100%;
            border: none;
        }

        .header-table td {
            vertical-align: top;
            padding: 0;
        }

        .logo {
            width: 100px;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .company-address {
            font-size: 11px;
            line-height: 1.3;
        }

        .invoice-date {
            text-align: right;
            font-weight: bold;
        }

        .customer-info {
            text-align: right;
            margin-top: 10px;
        }

        .invoice-title {
            font-weight: bold;
            font-size: 14px;
            border-bottom: 2px solid #000;
            width: 100%;
            margin-bottom: 0px;
            padding-bottom: 2px;
            display: inline-block;
        }

        .main-line {
            border-bottom: 3px solid #000;
            margin-bottom: 5px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: middle;
        }

        .items-table th {
            text-align: center;
            font-weight: bold;
            background-color: #fff;
            text-transform: uppercase;
        }

        .col-no {
            width: 30px;
            text-align: center;
        }

        .col-qty {
            width: 80px;
            text-align: center;
        }

        .col-item {}

        .col-price {
            width: 100px;
            text-align: right;
        }

        .col-total {
            width: 120px;
            text-align: right;
        }

        .footer-section {
            margin-top: 5px;
            width: 100%;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer-table td {
            vertical-align: top;
        }

        .bank-details {
            font-size: 11px;
            padding-top: 10px;
        }

        .total-label {
            font-weight: bold;
            text-align: right;
            padding: 5px;
            border: 1px solid #000;
            border-top: none;
        }

        .total-value {
            font-weight: bold;
            text-align: right;
            padding: 5px;
            border: 1px solid #000;
            border-top: none;
        }

        .signature {
            text-align: center;
            float: right;
            width: 200px;
            margin-top: 20px;
        }

        .signature-name {
            font-weight: bold;
            margin-top: 60px;
            text-decoration: none;
        }

        .signature-img {
            position: absolute;
            width: 100px;
            margin-top: -50px;
            margin-left: 50px;
            z-index: -1;
            /* Placeholder for signature image if exists */
        }

        /* Specific to match the image uploaded by user */
        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <table class="header-table">
        <tr>
            <td style="width: 120px;">
                <!-- Logo JR -->
                <img src="{{ public_path('images/LOGO JR.png') }}" class="logo" alt="Logo">
            </td>
            <td style="text-align: center;">
                <div class="company-name">CV. JEMBAR RAHAYU</div>
                <div class="company-address">
                    Jl. Ahmad Bagowi Kp. Bojong RT. 003 RW. 007<br>
                    Sukamantri - Kab. Tasikmalaya<br>
                    Telp: 0852 2300 4240<br>
                    Email: jr.jembarrahayu@gmail.com
                </div>
            </td>
            <td style="width: 150px; text-align: right;">
                <div class="invoice-date">Ciawi, {{ \Carbon\Carbon::parse($dedi_invoice->date)->format('d-m-Y') }}</div>
                <div class="customer-info">
                    <strong>Kepada Yth,</strong><br>
                    {{ $dedi_invoice->customer_name }}
                </div>
            </td>
        </tr>
    </table>

    <div class="main-line" style="margin-top: 10px;"></div>

    <div style="font-weight: bold; margin-bottom: 5px;">{{ $dedi_invoice->invoice_number }}</div>

    <table class="items-table">
        <thead>
            <tr>
                <th class="col-no">NO</th>
                <th class="col-qty">BANYAKNYA</th>
                <th class="col-item">NAMA BARANG</th>
                <th class="col-price">HARGA</th>
                <th class="col-total">JUMLAH</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dedi_invoice->items as $index => $item)
                <tr>
                    <td class="col-no">{{ $index + 1 }}</td>
                    <td class="col-qty">{{ (float) $item->quantity }} {{ $item->unit }}</td>
                    <td class="col-item">{{ $item->item_name }}</td>
                    <td class="col-price">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="col-total">Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <!-- Add empty rows if needed to fill space? Image shows 4 rows but only 1 filled. -->
            @for ($i = count($dedi_invoice->items); $i < 4; $i++)
                <tr>
                    <td>&nbsp;</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endfor
            <tr>
                <td colspan="3" style="border: none; text-align: center; vertical-align: top; padding-top: 10px;">
                    Transfer ke No. Rek. :
                </td>
                <td class="total-label" style="border-top:1px solid #000;">Jumlah</td>
                <td class="total-value" style="border-top:1px solid #000;">Rp
                    {{ number_format($dedi_invoice->total_amount, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <table class="footer-table" style="margin-top: 0px;">
        <tr>
            <td style="width: 60%; padding-left: 20px;">
                <div class="bank-details" style="font-size: 11px; line-height: 1.4;">
                    0952237630 BNI a/n DEDI KURNIAWAN<br>
                    143701000005565 BRI a/n DEDI KURNIAWAN<br>
                    2080094500 BCA a/n DEDI KURNIAWAN<br>
                    1770011342083 MANDIRI a/n DEDI KURNIAWAN
                </div>
            </td>
            <td style="width: 40%; text-align: center;">
                <div class="signature">
                    <p style="margin-bottom: 50px; font-weight: bold;">Hormat Kami,</p>
                    <!-- Simulated signature path or image if available -->
                    <img src="{{ public_path('images/TTD H DEDI.png') }}"
                        style="width: 80px; position: absolute; margin-left: 50px; margin-top: -40px;">
                    <p class="signature-name">(H. DEDI KURNIAWAN)</p>
                </div>
            </td>
        </tr>
    </table>

</body>

</html>
