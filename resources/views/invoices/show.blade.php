@extends('layouts.app')

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <!-- Action Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-10 gap-6">
            <div class="min-w-0 flex-1">
                <nav class="flex mb-4" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2">
                        <li><a href="{{ route('invoices.index') }}"
                                class="text-sm font-bold text-indigo-600 hover:text-indigo-500">Invoice</a></li>
                        <li><svg class="h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd" />
                            </svg></li>
                        <li class="text-sm font-bold text-gray-400">Detail</li>
                    </ol>
                </nav>
                <h2 class="text-4xl font-black text-gray-900 tracking-tight">Detail Invoice</h2>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('invoices.edit', $invoice->id) }}"
                    class="inline-flex items-center px-5 py-2.5 rounded-2xl bg-amber-50 text-amber-700 text-sm font-bold shadow-sm hover:bg-amber-100 transition-all">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </a>
                <a href="{{ route('invoices.export-pdf', $invoice->id) }}" target="_blank"
                    class="inline-flex items-center px-5 py-2.5 rounded-2xl bg-red-50 text-red-700 text-sm font-bold shadow-sm hover:bg-red-100 transition-all">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 9h1.5m1.5 0H15m-6 4h6m-6 4h3" />
                    </svg>
                    PDF
                </a>
                <a href="{{ route('invoices.export-excel', $invoice->id) }}"
                    class="inline-flex items-center px-5 py-2.5 rounded-2xl bg-green-50 text-green-700 text-sm font-bold shadow-sm hover:bg-green-100 transition-all">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Excel
                </a>
                <a href="{{ route('invoices.index') }}"
                    class="inline-flex items-center px-5 py-2.5 rounded-2xl bg-indigo-600 text-white text-sm font-bold shadow-xl hover:bg-indigo-700 transition-all transform hover:-translate-y-0.5">
                    Kembali ke Daftar
                </a>
            </div>
        </div>

        <!-- Invoice Card -->
        <div class="bg-white shadow-2xl rounded-3xl overflow-hidden border border-gray-100">
            <div class="p-8 sm:p-12">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between items-start border-b border-gray-100 pb-10 gap-8">
                    <div class="max-w-sm">
                        <div
                            class="h-16 w-16 bg-indigo-600 rounded-2xl flex items-center justify-center text-white mb-6 shadow-lg">
                            <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h1 class="text-xl font-black text-gray-900 uppercase tracking-tight">KOPERASI KONSUMEN JEMBAR
                            RAHAYU
                            SEJAHTERA</h1>
                        <div class="mt-4 space-y-1 text-sm text-gray-500 font-medium">
                            <p>JL. Moch. Bagowi Kp. Bojong RT003 / RW002</p>
                            <p>Telepon : +6281546527513</p>
                            <p>Email : koperasikonsumenjembarrahayu@gmail.com</p>
                        </div>
                    </div>
                    <div class="text-left md:text-right">
                        <h2 class="text-5xl font-black text-indigo-600 tracking-tighter uppercase">INVOICE</h2>
                        <div class="mt-6 space-y-2">
                            <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Nomor Invoice</p>
                            <p class="text-xl font-black text-gray-900">{{ $invoice->invoice_number }}</p>

                            <p class="text-sm font-bold text-gray-400 uppercase tracking-widest mt-4">Tanggal Terbit</p>
                            <p class="text-lg font-bold text-gray-900">
                                {{ \Carbon\Carbon::parse($invoice->date)->format('d F Y') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Customer & Summary -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mt-12 mb-12">
                    <div class="bg-gray-50 rounded-3xl p-8 border border-gray-100">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Ditagihkan Kepada</h3>
                        <p class="text-2xl font-black text-gray-900">{{ $invoice->customer_name }}</p>
                        <p class="mt-2 text-sm text-gray-500 font-medium italic">Pelanggan Setia</p>
                    </div>
                    <div class="bg-indigo-600 rounded-3xl p-8 text-white shadow-xl transform md:rotate-1">
                        <h3 class="text-xs font-black text-indigo-200 uppercase tracking-widest mb-4">Total Tagihan</h3>
                        <p class="text-4xl font-black">Rp {{ number_format($invoice->total_amount, 2, ',', '.') }}</p>
                        <div
                            class="mt-4 inline-flex items-center px-3 py-1 rounded-full bg-indigo-500 text-xs font-bold uppercase tracking-widest">
                            Status: Menunggu Pembayaran
                        </div>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="overflow-hidden rounded-3xl border border-gray-100 shadow-sm mb-12">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th scope="col"
                                    class="py-4 pl-6 pr-3 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                    No.</th>
                                <th scope="col"
                                    class="px-3 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                    Deskripsi</th>
                                <th scope="col"
                                    class="px-3 py-4 text-center text-xs font-black text-gray-400 uppercase tracking-widest">
                                    Jumlah</th>
                                <th scope="col"
                                    class="px-3 py-4 text-center text-xs font-black text-gray-400 uppercase tracking-widest">
                                    Satuan</th>
                                <th scope="col"
                                    class="px-3 py-4 text-right text-xs font-black text-gray-400 uppercase tracking-widest">
                                    Harga Satuan</th>
                                <th scope="col"
                                    class="px-3 py-4 pr-6 text-right text-xs font-black text-gray-400 uppercase tracking-widest">
                                    Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 bg-white">
                            @foreach ($invoice->items as $index => $item)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="whitespace-nowrap py-5 pl-6 pr-3 text-sm font-bold text-gray-400">
                                        {{ $index + 1 }}</td>
                                    <td class="whitespace-nowrap px-3 py-5 text-sm font-bold text-gray-900">
                                        {{ $item->product_name }}</td>
                                    <td class="whitespace-nowrap px-3 py-5 text-sm font-bold text-gray-600 text-center">
                                        {{ $item->quantity }}</td>
                                    <td class="whitespace-nowrap px-3 py-5 text-sm font-medium text-gray-400 text-center">
                                        <span
                                            class="bg-gray-100 px-2 py-1 rounded-lg text-xs font-black uppercase">{{ $item->unit }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-5 text-sm font-bold text-gray-600 text-right">Rp
                                        {{ number_format($item->price, 2, ',', '.') }}</td>
                                    <td
                                        class="whitespace-nowrap px-3 py-5 pr-6 text-sm font-black text-gray-900 text-right">
                                        Rp {{ number_format($item->total, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50/50">
                            <tr>
                                <td colspan="5"
                                    class="py-4 pl-6 pr-3 text-right text-sm font-bold text-gray-500 uppercase tracking-widest">
                                    Subtotal</td>
                                <td class="px-3 py-4 pr-6 text-right text-sm font-bold text-gray-900">Rp
                                    {{ number_format($invoice->total_amount, 2, ',', '.') }}</td>
                            </tr>
                            <tr class="bg-indigo-50/50">
                                <td colspan="5"
                                    class="py-6 pl-6 pr-3 text-right text-lg font-black text-indigo-600 uppercase tracking-tighter">
                                    Total Akhir</td>
                                <td class="px-3 py-6 pr-6 text-right text-2xl font-black text-indigo-600 tracking-tighter">
                                    Rp {{ number_format($invoice->total_amount, 2, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Footer Info -->
                <div class="flex flex-col md:flex-row justify-between items-end pt-12 border-t border-gray-100 gap-12">
                    <div class="w-full md:max-w-sm">
                        <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Informasi Pembayaran
                        </h4>
                        <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 space-y-2">
                            <p class="text-sm font-bold text-gray-900">Bank BNI</p>
                            <p class="text-lg font-black text-indigo-600 tracking-tight">8155688615</p>
                            <p class="text-xs font-medium text-gray-500 uppercase">a/n KOPERASI KONSUMEN JEMBAR RAHAYU
                                SEJAHTERA</p>
                        </div>
                    </div>
                    <div class="text-center w-full md:w-auto">
                        <p class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-12">Tanda Tangan Sah</p>
                        <div class="inline-block border-b-2 border-gray-900 pb-2 px-8">
                            <p class="text-xl font-black text-gray-900">Rizki Ichsan Al-Fath</p>
                        </div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-2">Ketua Pengurus</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-10 text-center">
            <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Terima kasih atas kepercayaan Anda!</p>
        </div>
    </div>
@endsection
