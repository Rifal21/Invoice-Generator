<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Surat Jalan {{ $delivery->order_number }}</title>
    <style>
        @page {
            margin: 0.5cm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #000;
            line-height: 1.4;
            margin: 0;
            padding: 10px;
        }

        .header {
            width: 100%;
            margin-bottom: 10px;
            position: relative;
        }

        .logo {
            width: 90px;
            float: left;
            margin-top: -10px;
        }

        .company-info {
            float: left;
            margin-left: 10px;
            width: 60%;
            text-align: left;
        }

        .company-name {
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 2px;
            text-align: center;
        }

        .company-details {
            font-size: 9px;
            font-weight: bold;
            text-align: center;
        }

        .meta-info {
            float: right;
            text-align: right;
            width: 30%;
        }

        .meta-date {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 15px;
        }

        .recipient {
            font-size: 11px;
            text-align: right;
        }

        .recipient-label {
            font-weight: bold;
            margin-bottom: 2px;
        }

        .recipient-name {
            font-weight: bold;
            font-size: 12px;
            line-height: 1.2;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        .nota-title {
            font-size: 20px;
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 20px;
            text-align: center;
            text-transform: uppercase;
            width: 100%;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
            margin-bottom: 10px;
        }

        .items-table th {
            border: 1px solid #000;
            padding: 5px;
            text-transform: uppercase;
            background-color: #fff;
            font-weight: bold;
            font-size: 12px;
        }

        .items-table td {
            border: 1px solid #000;
            padding: 5px;
            font-weight: bold;
            vertical-align: middle;
            font-size: 11px;
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

        .footer-note {
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 20px;
        }

        .footer-signatures {
            width: 100%;
            margin-top: 20px;
        }

        .signature-box {
            width: 33.33%;
            float: left;
            text-align: center;
            min-height: 120px;
            position: relative;
        }

        .signature-label {
            font-weight: bold;
            margin-bottom: 60px;
            display: block;
        }

        .signature-line {
            border-bottom: 2px solid #000;
            width: 80%;
            margin: 0 auto;
            display: block;
        }

        .signature-img {
            position: absolute;
            height: 80px;
            top: 20px;
            right: 15px;
            z-index: 1;
            opacity: 0.8;
        }

        .signer-name {
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            font-size: 12px;
            position: relative;
            z-index: 2;
        }
    </style>
</head>

<body>
    <div class="header clearfix">
        <div class="logo">
            <img src="{{ public_path('images/KJRS.png') }}" style="width: 100%;" alt="Logo">
        </div>
        <div class="company-info">
            <div class="company-name">KOPERASI KONSUMEN<br>JEMBAR RAHAYU SEJAHTERA</div>
            <div class="company-details">
                Jl. Ahmad Bagowi Kp. Bojong RT. 003 RW. 002<br>
                Sukamantri - Kab. Tasikmalaya<br>
                Telp: 0852 2300 4240<br>
                Email: jr.jembarrahayu@gmail.com
            </div>
        </div>
        <div class="meta-info">
            <div class="meta-date">
                {{ \Carbon\Carbon::parse($delivery->date)->isoFormat('D MMMM Y') }}
            </div>
            <div class="recipient">
                <div class="recipient-label">Kepada Yth,</div>
                <div class="recipient-name">
                    {!! nl2br(e($delivery->customer_name)) !!}
                </div>
            </div>
        </div>
    </div>

    <div class="clearfix"></div>

    <div class="nota-title">
        SURAT JALAN
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">NO</th>
                <th style="width: 15%;">Qty</th>
                <th style="width: 55%;">NAMA BARANG</th>
                <th style="width: 25%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($delivery->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $item->quantity_string }}</td>
                    <td class="text-center">{{ $item->item_name }}</td>
                    <td class="text-center">{{ $item->description ?? '-' }}</td>
                </tr>
            @endforeach
            @for ($i = count($delivery->items); $i < 6; $i++)
                <tr>
                    <td class="text-center" style="color: white; height: 20px;">.</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endfor
        </tbody>
    </table>

    <div class="footer-note">
        Barang sudah diterima dengan baik dan cukup
    </div>

    <div class="footer-signatures clearfix">
        <div class="signature-box">
            <span class="signature-label">Penerima</span>
            <span class="signature-line"></span>
        </div>
        <div class="signature-box">
            <span class="signature-label">Driver</span>
        </div>
        <div class="signature-box">
            <span class="signature-label" style="margin-bottom: 50px;">Mengetahui,</span>
            <img src="{{ public_path('images/ttd rizki ichsan al-fath.png') }}" class="signature-img" alt="Stamp">
            <div class="signer-name">RIZKI ICHSAN AL-FATH</div>
        </div>
    </div>
</body>

</html>
