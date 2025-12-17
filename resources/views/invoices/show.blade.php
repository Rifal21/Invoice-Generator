@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="md:flex md:items-center md:justify-between mb-8">
            <div class="min-w-0 flex-1">
                <h2 class="text-3xl font-bold leading-7 text-gray-900 sm:truncate sm:tracking-tight">Invoice Details</h2>
            </div>
            <div class="mt-4 flex md:ml-4 md:mt-0">
                <a href="{{ route('invoices.edit', $invoice->id) }}"
                    class="ml-3 inline-flex items-center rounded-md bg-yellow-600 text-white px-3 py-2 text-sm font-semibold shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-yellow-500">
                    Edit Invoice
                </a>
                <a href="{{ route('invoices.export-pdf', $invoice->id) }}"
                    class="ml-3 inline-flex items-center rounded-md bg-red-600 text-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    Export PDF
                </a>
                <a href="{{ route('invoices.export-excel', $invoice->id) }}"
                    class="ml-3 inline-flex items-center rounded-md bg-green-600 text-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    Export Excel
                </a>
                <a href="{{ route('invoices.index') }}"
                    class="ml-3 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Back
                </a>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="p-8">
                <!-- Header -->
                <div class="flex justify-between items-start border-b border-gray-200 pb-8">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">KOPERASI JEMBAR RAHAYU SEJAHTERA</h1>
                        <p class="mt-2 text-gray-600">JL. Moch. Bagowi Kp. Bojong RT003 / RW002</p>
                        <p class="text-gray-600">Telepon : +6281546527513</p>
                        <p class="text-gray-600">Email : koperasikonsumenjembarrahayu@gmail.com</p>
                    </div>
                    <div class="text-right">
                        <h2 class="text-3xl font-bold text-gray-900">INVOICE</h2>
                        <p class="mt-2 text-gray-600">No : <span class="font-semibold">{{ $invoice->invoice_number }}</span>
                        </p>
                        <p class="text-gray-600">Tanggal : <span
                                class="font-semibold">{{ \Carbon\Carbon::parse($invoice->date)->format('d F Y') }}</span>
                        </p>
                    </div>
                </div>

                <!-- Customer & Payment Info -->
                <div class="flex justify-between items-start mt-8 mb-8">
                    <div>
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Kepada Yth.</h3>
                        <p class="mt-2 text-lg font-medium text-gray-900">{{ $invoice->customer_name }}</p>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 rounded-lg border border-gray-200 text-center">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Jumlah Yang Harus Di Bayar
                        </h3>
                        <p class="mt-2 text-2xl font-bold text-indigo-600">
                            Rp{{ number_format($invoice->total_amount, 2, ',', '.') }}</p>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="ring-1 ring-gray-200 rounded-lg overflow-hidden mb-8">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">No.</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Deskripsi</th>
                                <th scope="col" class="px-3 py-3.5 text-center text-sm font-semibold text-gray-900">Qty
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-center text-sm font-semibold text-gray-900">
                                    Volume</th>
                                <th scope="col" class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900">Harga
                                    Satuan</th>
                                <th scope="col" class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900">Total
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach ($invoice->items as $index => $item)
                                <tr>
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-900 sm:pl-6">
                                        {{ $index + 1 }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">{{ $item->product_name }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 text-center">
                                        {{ $item->quantity }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 text-center">
                                        {{ $item->unit }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 text-right">
                                        Rp{{ number_format($item->price, 2, ',', '.') }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900 text-right font-medium">
                                        Rp{{ number_format($item->total, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="5"
                                    class="py-3.5 pl-4 pr-3 text-right text-sm font-semibold text-gray-900 sm:pl-6">Sub
                                    total</td>
                                <td class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900">
                                    Rp{{ number_format($invoice->total_amount, 2, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td colspan="5"
                                    class="py-3.5 pl-4 pr-3 text-right text-sm font-semibold text-gray-900 sm:pl-6">Diskon
                                </td>
                                <td class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900">-</td>
                            </tr>
                            <tr>
                                <td colspan="5"
                                    class="py-3.5 pl-4 pr-3 text-right text-sm font-semibold text-gray-900 sm:pl-6">Pajak
                                </td>
                                <td class="px-3 py-3.5 text-right text-sm font-semibold text-gray-900">-</td>
                            </tr>
                            <tr class="bg-gray-100">
                                <td colspan="5"
                                    class="py-3.5 pl-4 pr-3 text-right text-base font-bold text-gray-900 sm:pl-6">Total</td>
                                <td class="px-3 py-3.5 text-right text-base font-bold text-indigo-600">
                                    Rp{{ number_format($invoice->total_amount, 2, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Footer Info -->
                <div class="flex justify-between items-end pt-8 border-t border-gray-200">
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900">Transfer ke No. Rek :</h4>
                        <p class="mt-1 text-sm text-gray-600">8155688615 BNI</p>
                        <p class="text-sm text-gray-600">a/n KOPERASI JEMBAR RAHAYU SEJAHTERA</p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-600 mb-16">Hormat Kami,</p>
                        <p class="text-sm font-bold text-gray-900 border-b border-gray-900 inline-block pb-1">Rizki Ichsan
                            Al-Fath</p>
                        <p class="text-xs text-gray-500 mt-1">Ketua Pengurus</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
