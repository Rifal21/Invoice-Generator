<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Nota {{ $delivery->nota_number }}</title>
    <style>
        @page {
            margin: 0.5cm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 14px;
            color: #000;
            line-height: 1.3;
            margin: 0;
            padding: 10px;
        }

        .header {
            width: 100%;
            margin-bottom: 25px;
            position: relative;
        }

        .logo {
            width: 90px;
            float: left;
        }

        .company-info {
            float: left;
            margin-left: 20px;
            text-align: center;
            width: 60%;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .company-details {
            font-size: 12px;
            font-weight: bold;
            margin-top: 5px;
        }

        .meta-info {
            float: right;
            text-align: right;
            width: 25%;
        }

        .meta-date {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 12px;
        }

        .recipient {
            font-size: 14px;
            font-weight: bold;
            text-align: left;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        .nota-title {
            font-size: 16px;
            font-weight: bold;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #000;
            margin-top: 10px;
        }

        .items-table th {
            border: 1.5px solid #000;
            padding: 8px;
            text-transform: uppercase;
            background-color: #fff;
            font-weight: bold;
            font-size: 12px;
        }

        .items-table td {
            border: 1.5px solid #000;
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

        .footer {
            margin-top: 30px;
            width: 100%;
        }

        .payment-info {
            float: left;
            width: 60%;
            font-size: 12px;
            font-weight: bold;
            line-height: 1.5;
            padding-top: 10px;
        }

        .signature-area {
            float: right;
            width: 30%;
            text-align: center;
            position: relative;
        }

        .signature-img {
            height: 70px;
            margin: -25px 0;
            opacity: 0.9;
        }

        .signer-name {
            text-decoration: underline;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 40px;
            font-size: 13px;
        }

        .total-row {
            background-color: #fff;
        }

        .total-cell {
            font-size: 14px;
            font-weight: 900;
        }

        .price-col {
            width: 100px;
        }

        .amount-col {
            width: 120px;
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
            <div class="meta-date">{{ $delivery->location }},
                {{ \Carbon\Carbon::parse($delivery->date)->format('d-m-Y') }}</div>
            <div class="recipient">
                Kepada Yth,<br>
                {!! nl2br(e($delivery->customer_name)) !!}
            </div>
        </div>
    </div>

    <div class="nota-title">NOTA {{ $delivery->nota_number }}</div>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">NO</th>
                <th style="width: 20%;">BANYAKNYA</th>
                <th style="width: 35%;">NAMA BARANG</th>
                <th style="width: 20%;">HARGA</th>
                <th style="width: 20%;">JUMLAH</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($delivery->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">{{ $item->quantity_string }}</td>
                    <td class="text-left">{{ $item->description }}</td>
                    <td class="text-right">
                        <div style="width:100%; position:relative;">
                            <span style="float:left;">Rp</span>
                            <span>{{ number_format($item->price, 0, ',', '.') }}</span>
                        </div>
                    </td>
                    <td class="text-right">
                        <div style="width:100%; position:relative;">
                            <span style="float:left;">Rp</span>
                            <span>{{ number_format($item->total, 0, ',', '.') }}</span>
                        </div>
                    </td>
                </tr>
            @endforeach
            @for ($i = count($delivery->items); $i < 4; $i++)
                <tr>
                    <td class="text-center" style="color: white;">.</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endfor
            <tr class="total-row">
                <td colspan="4" class="text-right total-cell" style="padding: 8px;">JUMLAH</td>
                <td class="text-right total-cell" style="padding: 8px;">
                    <div style="width:100%; position:relative;">
                        <span style="float:left;">Rp</span>
                        <span>{{ number_format($delivery->total_amount, 0, ',', '.') }}</span>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="footer clearfix">
        <div class="payment-info">
            Transfer ke No. Rek. :<br>
            0952237630 BNI a/n DEDI KURNIAWAN<br>
            143701000005565 BRI a/n DEDI KURNIAWAN<br>
            2080094500 BCA a/n DEDI KURNIAWAN<br>
            1770011342083 MANDIRI a/n DEDI KURNIAWAN
        </div>
        <div class="signature-area">
            <div>Hormat Kami,</div>
            <div style="margin-top: 30px; margin-bottom: 10px;">
                <img src="{{ public_path('images/ttd rizki ichsan al-fath.png') }}" class="signature-img"
                    alt="Signature">
            </div>
            <div class="signer-name">RIZKI ICHSAN AL-FATH</div>
        </div>
    </div>
</body>

</html>
