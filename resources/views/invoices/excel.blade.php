<table>
    <thead>
        <tr>
            <th colspan="8"
                style="font-size: 20px; font-weight: bold; text-align: right; color: #ffffff; background-color: #203764; border: 1px solid #000000;">
                INVOICE</th>
        </tr>
        <tr>
            <th rowspan="4" style="background-color: #203764; border: 1px solid #000000; width: 100px;"></th>
            <!-- Logo Placeholder -->
            <th colspan="4"
                style="background-color: #203764; color: #ffffff; font-weight: bold; border: 1px solid #000000; vertical-align: top;">
                KOPERASI KONSUMEN JEMBAR RAHAYU SEJAHTERA</th>
            <th colspan="3"
                style="background-color: #203764; color: #ffffff; text-align: right; border: 1px solid #000000; vertical-align: top;">
                No : {{ $invoice->invoice_number }}</th>
        </tr>
        <tr>
            <th colspan="4"
                style="background-color: #203764; color: #ffffff; border: 1px solid #000000; vertical-align: top;">JL.
                Moch. Bagowi Kp. Bojong RT003 / RW002</th>
            <th colspan="3"
                style="background-color: #203764; color: #ffffff; text-align: right; border: 1px solid #000000; vertical-align: top;">
                Tanggal : {{ \Carbon\Carbon::parse($invoice->date)->format('d/m/Y') }}</th>
        </tr>
        <tr>
            <th colspan="4"
                style="background-color: #203764; color: #ffffff; border: 1px solid #000000; vertical-align: top;">
                Telepon : +6281546527513</th>
            <th colspan="3" style="background-color: #203764; border: 1px solid #000000;"></th>
        </tr>
        <tr>
            <th colspan="4"
                style="background-color: #203764; color: #ffffff; border: 1px solid #000000; vertical-align: top;">Email
                : koperasikonsumenjembarrahayu@gmail.com</th>
            <th colspan="3" style="background-color: #203764; border: 1px solid #000000;"></th>
        </tr>
        <tr>
            <td colspan="8" style="border: 1px solid #000000;"></td>
        </tr>
        <tr>
            <td colspan="5" style="border: 1px solid #000000;">Kepada Yth.</td>
            <td colspan="3" style="border: 1px solid #000000; font-weight: bold; text-align: center;">Jumlah Yang
                Harus Di Bayar</td>
        </tr>
        <tr>
            <td colspan="5" style="border: 1px solid #000000; font-weight: bold; font-size: 14px;">
                {{ $invoice->customer_name }}</td>
            <td colspan="3"
                style="border: 1px solid #000000; font-weight: bold; font-size: 18px; text-align: center;">
                Rp{{ number_format((float) $invoice->total_amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td colspan="8" style="border: 1px solid #000000;"></td>
        </tr>
        <tr>
            <th
                style="background-color: #203764; color: #ffffff; border: 1px solid #000000; font-weight: bold; text-align: center;">
                No</th>
            <th colspan="3"
                style="background-color: #203764; color: #ffffff; border: 1px solid #000000; font-weight: bold; text-align: center;">
                Deskripsi</th>
            <th
                style="background-color: #203764; color: #ffffff; border: 1px solid #000000; font-weight: bold; text-align: center;">
                Jumlah</th>
            <th
                style="background-color: #203764; color: #ffffff; border: 1px solid #000000; font-weight: bold; text-align: center;">
                Volume</th>
            <th
                style="background-color: #203764; color: #ffffff; border: 1px solid #000000; font-weight: bold; text-align: center;">
                Harga Satuan</th>
            <th
                style="background-color: #203764; color: #ffffff; border: 1px solid #000000; font-weight: bold; text-align: center;">
                Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($invoice->items as $index => $item)
            <tr>
                <td style="border: 1px solid #000000; text-align: center;">{{ $index + 1 }}</td>
                <td colspan="3" style="border: 1px solid #000000;">{{ $item->product_name }}</td>
                <td style="border: 1px solid #000000; text-align: center;">{{ $item->quantity }}</td>
                <td style="border: 1px solid #000000; text-align: center;">{{ $item->unit }}</td>
                <td style="border: 1px solid #000000; text-align: right;">{{ (float) $item->price }}</td>
                <td style="border: 1px solid #000000; text-align: right;">{{ (float) $item->total }}</td>
            </tr>
        @endforeach

        <!-- Combined Bank Info and Totals Section -->
        <tr>
            <td colspan="5" style="text-align: center;">Transfer ke No. Rek :</td>
            <td colspan="2" style="border: 1px solid #000000; font-weight: bold; text-align: right;">Sub total</td>
            <td style="border: 1px solid #000000; font-weight: bold; text-align: right;">
                {{ (float) $invoice->total_amount }}</td>
        </tr>
        <tr>
            <td colspan="5" style="text-align: center; font-weight: bold;">8155688615 BNI a/n KOPERASI KONSUMEN
                JEMBAR RAHAYU
                SEJAHTERA</td>
            <td colspan="2" style="border: 1px solid #000000; font-weight: bold; text-align: right;">Diskon</td>
            <td style="border: 1px solid #000000; font-weight: bold; text-align: right;">-</td>
        </tr>
        <tr>
            <td colspan="5" style="text-align: center; font-weight: bold;">Terima Kasih</td>
            <td colspan="2" style="border: 1px solid #000000; font-weight: bold; text-align: right;">Pajak</td>
            <td style="border: 1px solid #000000; font-weight: bold; text-align: right;">-</td>
        </tr>
        <tr>
            <td colspan="5"></td>
            <td colspan="2"
                style="background-color: #203764; color: #ffffff; border: 1px solid #000000; font-weight: bold; text-align: right;">
                Total</td>
            <td
                style="background-color: #203764; color: #ffffff; border: 1px solid #000000; font-weight: bold; text-align: right;">
                {{ (float) $invoice->total_amount }}</td>
        </tr>

        <tr>
            <td colspan="8"></td>
        </tr>

        <!-- Signature Section -->
        <tr>
            <td colspan="5"></td>
            <td colspan="3" style="text-align: center;">Hormat Kami,</td>
        </tr>
        <tr>
            <td colspan="5"></td>
            <td colspan="3" style="height: 60px;"></td>
        </tr>
        <tr>
            <td colspan="5"></td>
            <td colspan="3" style="text-align: center; font-weight: bold; text-decoration: underline;">Rizki Ichsan
                Al-Fath</td>
        </tr>
        <tr>
            <td colspan="5"></td>
            <td colspan="3" style="text-align: center;">Ketua Pengurus</td>
        </tr>
    </tbody>
</table>
