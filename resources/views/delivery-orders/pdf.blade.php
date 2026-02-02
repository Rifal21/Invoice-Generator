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
            font-size: 14px;
            color: #000;
            line-height: 1.4;
            margin: 0;
            padding: 10px;
        }

        .header {
            width: 100%;
            margin-bottom: 20px;
            position: relative;
        }

        .logo {
            width: 150px;
            float: left;
            margin-top: -10px;
        }

        .company-info {
            float: left;
            margin-left: 15px;
            width: 60%;
            text-align: center;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
            text-align: center;
        }

        .company-details {
            font-size: 11px;
            font-weight: bold;
            text-align: left;
        }

        .meta-info {
            float: right;
            margin-top: 55px;
            text-align: center;
            width: 30%;
        }

        .meta-date {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 20px;
        }

        .recipient {
            font-size: 12px;
            text-align: center;
        }

        .recipient-label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .recipient-name {
            font-weight: bold;
            font-size: 14px;
            line-height: 1.3;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        .nota-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 25px;
            text-align: center;
            text-transform: uppercase;
            width: 100%;
            text-decoration: underline;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
            margin-bottom: 20px;
        }

        .items-table th {
            border: 1px solid #000;
            padding: 8px;
            text-transform: uppercase;
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 13px;
        }

        .items-table td {
            border: 1px solid #000;
            padding: 8px;
            font-weight: bold;
            vertical-align: middle;
            font-size: 13px;
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
            font-size: 13px;
            margin-bottom: 30px;
            font-style: italic;
        }

        .footer-signatures {
            width: 100%;
            margin-top: 30px;
        }

        .signature-box {
            width: 33.33%;
            float: left;
            text-align: center;
            min-height: 140px;
            position: relative;
        }

        .signature-label {
            font-weight: bold;
            margin-bottom: 100px;
            display: block;
            font-size: 13px;
        }

        .signature-line {
            border-bottom: 2px solid #000;
            width: 80%;
            margin: 0 auto;
            display: block;
        }

        .signature-img {
            position: absolute;
            width: 100px;
            height: 100px;
            top: 25px;
            right: 100px;
            z-index: 1;
            opacity: 0.9;
        }

        .signer-name {
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            font-size: 13px;
            position: relative;
            z-index: 2;
            margin-top: 40px;
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
            @for ($i = count($delivery->items); $i < 3; $i++)
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
