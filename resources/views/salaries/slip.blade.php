<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Slip Gaji - {{ $salary->user->name }}</title>
    <style>
        @page {
            margin: 15px;
        }

        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            font-size: 10px;
        }

        .container {
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }

        /* Header Section */
        .header {
            border-bottom: 3px solid #4f46e5;
            padding-bottom: 10px;
            margin-bottom: 15px;
            display: table;
            width: 100%;
        }

        .logo-section {
            display: table-cell;
            width: 90px;
            vertical-align: middle;
        }

        .logo {
            width: 75px;
            height: 75px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .logo img {
            max-width: 100%;
            max-height: 100%;
            width: auto;
            height: auto;
            object-fit: contain;
        }

        .company-info {
            display: table-cell;
            vertical-align: middle;
            padding-left: 12px;
        }

        .company-name {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
            margin: 0 0 3px 0;
        }

        .company-details {
            font-size: 9px;
            color: #6b7280;
            line-height: 1.3;
        }

        /* Slip Title */
        .slip-title {
            text-align: center;
            margin: 12px 0;
        }

        .slip-title h1 {
            margin: 0;
            font-size: 18px;
            color: #1f2937;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        .slip-title .period {
            font-size: 10px;
            color: #6b7280;
            margin-top: 3px;
        }

        /* Employee Info */
        .employee-info {
            background-color: #f9fafb;
            padding: 10px 12px;
            border-radius: 8px;
            margin-bottom: 12px;
            border-left: 3px solid #4f46e5;
        }

        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 4px;
        }

        .info-label {
            display: table-cell;
            width: 35%;
            font-weight: bold;
            color: #374151;
            font-size: 9px;
        }

        .info-value {
            display: table-cell;
            color: #1f2937;
            font-size: 9px;
        }

        /* Salary Details */
        .salary-section {
            margin: 12px 0;
        }

        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #374151;
            text-transform: uppercase;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 4px;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .detail-row {
            border-bottom: 1px solid #f3f4f6;
        }

        .detail-row td {
            padding: 6px 0;
            font-size: 9px;
        }

        .detail-label {
            color: #6b7280;
        }

        .detail-value {
            text-align: right;
            font-weight: bold;
            color: #1f2937;
        }

        .detail-value.positive {
            color: #10b981;
        }

        .detail-value.negative {
            color: #ef4444;
        }

        /* Total Section */
        .total-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 15px;
            border-radius: 8px;
            margin: 12px 0;
        }

        .total-row {
            display: table;
            width: 100%;
        }

        .total-label {
            display: table-cell;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .total-value {
            display: table-cell;
            text-align: right;
            font-size: 16px;
            font-weight: bold;
        }

        /* Signature Section */
        .signature-section {
            margin-top: 20px;
            display: table;
            width: 100%;
        }

        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 10px;
        }

        .signature-label {
            font-size: 9px;
            color: #6b7280;
            margin-bottom: 35px;
            display: block;
        }

        .signature-name {
            font-weight: bold;
            color: #1f2937;
            border-top: 2px solid #1f2937;
            padding-top: 5px;
            display: inline-block;
            min-width: 150px;
            font-size: 9px;
        }

        /* Notes */
        .notes {
            background-color: #fef3c7;
            border-left: 3px solid #f59e0b;
            padding: 8px 10px;
            border-radius: 6px;
            margin: 10px 0;
        }

        .notes-title {
            font-weight: bold;
            color: #92400e;
            margin-bottom: 3px;
            font-size: 9px;
        }

        .notes-content {
            color: #78350f;
            font-size: 8px;
            line-height: 1.3;
        }

        /* Footer */
        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 7px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 8px;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="logo-section">
                <div class="logo">
                    <img src="{{ public_path('images/kopinvoice.png') }}" alt="Logo">
                </div>
            </div>
            <div class="company-info">
                <div class="company-name">Koperasi Konsumen Jembar Rahayu Sejahtera</div>
                <div class="company-details">
                    JL. Moch. Bagowi Kp. Bojong RT003 / RW002<br>
                    Telepon : +6281546527513 | Email : koperasikonsumenjembarrahayu@gmail.com
                </div>
            </div>
        </div>

        <!-- Slip Title -->
        <div class="slip-title">
            <h1>Slip Gaji Karyawan</h1>
            <div class="period">
                Periode:
                @if ($salary->start_date && $salary->end_date)
                    {{ $salary->start_date->isoFormat('D MMMM Y') }} - {{ $salary->end_date->isoFormat('D MMMM Y') }}
                @else
                    {{ $salary->period->isoFormat('MMMM Y') }}
                @endif
            </div>
        </div>

        <!-- Employee Information -->
        <div class="employee-info">
            <div class="info-row">
                <div class="info-label">Nama Karyawan:</div>
                <div class="info-value">{{ $salary->user->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Posisi:</div>
                <div class="info-value">{{ ucfirst($salary->user->role) }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value">{{ $salary->user->email }}</div>
            </div>
            @if ($salary->working_days)
                <div class="info-row">
                    <div class="info-label">Hari Kerja:</div>
                    <div class="info-value">{{ $salary->working_days }} Hari</div>
                </div>
            @endif
        </div>

        <!-- Salary Details -->
        <div class="salary-section">
            <div class="section-title">Rincian Gaji</div>
            <table>
                <tr class="detail-row">
                    <td class="detail-label">Gaji Harian</td>
                    <td class="detail-value">Rp {{ number_format($salary->daily_salary, 0, ',', '.') }}</td>
                </tr>
                @if ($salary->working_days)
                    <tr class="detail-row">
                        <td class="detail-label">Jumlah Hari Kerja</td>
                        <td class="detail-value">{{ $salary->working_days }} Hari</td>
                    </tr>
                @endif
                <tr class="detail-row">
                    <td class="detail-label"><strong>Gaji Pokok</strong></td>
                    <td class="detail-value"><strong>Rp {{ number_format($salary->base_salary, 0, ',', '.') }}</strong>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Bonus & Deductions -->
        <div class="salary-section">
            <div class="section-title">Tunjangan & Potongan</div>
            <table>
                @if ($salary->bonus > 0)
                    <tr class="detail-row">
                        <td class="detail-label">Bonus / Tunjangan</td>
                        <td class="detail-value positive">+ Rp {{ number_format($salary->bonus, 0, ',', '.') }}</td>
                    </tr>
                @endif
                @if ($salary->deductions > 0)
                    <tr class="detail-row">
                        <td class="detail-label">Potongan</td>
                        <td class="detail-value negative">- Rp {{ number_format($salary->deductions, 0, ',', '.') }}
                        </td>
                    </tr>
                @endif
            </table>
        </div>

        <!-- Total Net Salary -->
        <div class="total-section">
            <div class="total-row">
                <div class="total-label">Total Gaji Bersih:</div>
                <div class="total-value">Rp {{ number_format($salary->net_salary, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Notes -->
        @if ($salary->notes)
            <div class="notes">
                <div class="notes-title">Catatan:</div>
                <div class="notes-content">{{ $salary->notes }}</div>
            </div>
        @endif

        <!-- Payment Status -->
        <div style="text-align: center; margin: 20px 0;">
            @if ($salary->status === 'paid')
                <span
                    style="display: inline-block; padding: 8px 20px; background-color: #d1fae5; color: #065f46; border-radius: 20px; font-weight: bold; font-size: 11px;">
                    ✓ SUDAH DIBAYAR - {{ $salary->paid_at->isoFormat('D MMMM Y') }}
                </span>
            @else
                <span
                    style="display: inline-block; padding: 8px 20px; background-color: #fef3c7; color: #92400e; border-radius: 20px; font-weight: bold; font-size: 11px;">
                    ⏳ BELUM DIBAYAR
                </span>
            @endif
        </div>

        <!-- Signatures -->
        <div class="signature-section">
            <div class="signature-box">
                <span class="signature-label">Diterima Oleh,</span><br>
                <span class="signature-name">{{ $salary->user->name }}</span>
            </div>
            <div class="signature-box">
                <span class="signature-label">Dibayar Oleh,</span><br>
                <span class="signature-name">Finance Manager</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            Dokumen ini dicetak secara otomatis pada {{ now()->isoFormat('D MMMM Y, HH:mm') }} WIB<br>
            Slip gaji ini sah tanpa tanda tangan basah
        </div>
    </div>
</body>

</html>
