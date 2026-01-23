@extends('layouts.app')

@section('title', 'Detail Nota Pengiriman Beras')

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-10">
        <!-- Action Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 md:mb-10 gap-4 md:gap-6">
            <div class="min-w-0 flex-1">
                <nav class="flex mb-4" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2">
                        <li><a href="{{ route('rice-deliveries.index') }}"
                                class="text-xs md:text-sm font-bold text-indigo-600 hover:text-indigo-500">Nota Beras</a>
                        </li>
                        <li><svg class="h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd" />
                            </svg></li>
                        <li class="text-xs md:text-sm font-bold text-gray-400">Detail</li>
                    </ol>
                </nav>
                <h2 class="text-3xl md:text-4xl font-black text-gray-900 tracking-tight">Detail Nota</h2>
            </div>
            <div class="flex flex-wrap gap-2 md:gap-3">
                <a href="{{ route('rice-deliveries.edit', $riceDelivery->id) }}"
                    class="inline-flex items-center justify-center px-4 py-2.5 rounded-2xl bg-amber-50 text-amber-700 text-sm font-bold shadow-sm hover:bg-amber-100 transition-all">
                    <i class="fas fa-edit mr-2"></i>
                    Edit
                </a>
                <a href="{{ route('rice-deliveries.export-pdf', $riceDelivery->id) }}" target="_blank"
                    class="inline-flex items-center justify-center px-4 py-2.5 rounded-2xl bg-red-50 text-red-700 text-sm font-bold shadow-sm hover:bg-red-100 transition-all">
                    <i class="fas fa-file-pdf mr-2"></i>
                    PDF
                </a>
                <a href="{{ route('rice-deliveries.index') }}"
                    class="inline-flex items-center justify-center px-5 py-2.5 rounded-2xl bg-indigo-600 text-white text-sm font-bold shadow-xl hover:bg-indigo-700 transition-all transform hover:-translate-y-0.5">
                    Kembali
                </a>
            </div>
        </div>

        <!-- Nota Card -->
        <div class="bg-white shadow-2xl rounded-3xl overflow-hidden border border-gray-100">
            <div class="p-6 md:p-12">
                <!-- Header Template -->
                <div
                    class="flex flex-col lg:flex-row justify-between items-start border-b-4 border-black pb-8 md:pb-10 gap-8">
                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4 sm:gap-6 w-full lg:w-auto">
                        <img src="{{ asset('images/KJRS.png') }}" class="h-16 md:h-20 w-auto" alt="Logo">
                        <div class="text-center sm:text-left">
                            <h1 class="text-xl md:text-2xl font-black text-gray-900 leading-tight uppercase">
                                Koperasi Konsumen<br class="hidden sm:block">Jembar Rahayu Sejahtera</h1>
                            <div class="mt-2 text-[10px] md:text-sm text-gray-600 font-bold space-y-0.5">
                                <p>Jl. Ahmad Bagowi Kp. Bojong RT. 003 RW. 002</p>
                                <p>Sukamantri - Kab. Tasikmalaya</p>
                                <p>Telp: 0852 2300 4240</p>
                                <p>Email: jr.jembarrahayu@gmail.com</p>
                            </div>
                        </div>
                    </div>
                    <div class="text-left lg:text-right w-full lg:w-auto">
                        <p class="text-base md:text-lg font-black text-gray-900">{{ $riceDelivery->location }},
                            {{ \Carbon\Carbon::parse($riceDelivery->date)->format('d-m-Y') }}</p>
                        <div class="mt-4 bg-blue-50 p-4 rounded-2xl border border-blue-100">
                            <h3 class="text-xs font-black text-blue-400 uppercase tracking-widest mb-1">Kepada Yth,</h3>
                            <p
                                class="text-base font-bold text-gray-900 whitespace-pre-wrap lowercase first-letter:uppercase">
                                {!! nl2br(e($riceDelivery->customer_name)) !!}</p>
                        </div>
                    </div>
                </div>

                <div class="py-6">
                    <h2 class="text-2xl font-black text-gray-900">NOTA {{ $riceDelivery->nota_number }}</h2>
                </div>

                <!-- Items Table -->
                <div class="overflow-hidden rounded-3xl border-2 border-gray-900 mb-8">
                    <table class="min-w-full divide-y-2 divide-gray-900">
                        <thead>
                            <tr class="bg-gray-100">
                                <th scope="col"
                                    class="py-4 px-4 text-center text-xs font-black text-gray-900 uppercase border-r-2 border-gray-900 w-12">
                                    NO</th>
                                <th scope="col"
                                    class="px-4 py-4 text-center text-xs font-black text-gray-900 uppercase border-r-2 border-gray-900">
                                    BANYAKNYA</th>
                                <th scope="col"
                                    class="px-4 py-4 text-center text-xs font-black text-gray-900 uppercase border-r-2 border-gray-900">
                                    NAMA BARANG</th>
                                <th scope="col"
                                    class="px-4 py-4 text-center text-xs font-black text-gray-900 uppercase border-r-2 border-gray-900">
                                    HARGA</th>
                                <th scope="col" class="px-4 py-4 text-center text-xs font-black text-gray-900 uppercase">
                                    JUMLAH</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-2 divide-gray-900 bg-white">
                            @foreach ($riceDelivery->items as $index => $item)
                                <tr>
                                    <td
                                        class="whitespace-nowrap py-4 px-4 text-center text-sm font-bold text-gray-900 border-r-2 border-gray-900">
                                        {{ $index + 1 }}</td>
                                    <td
                                        class="px-4 py-4 text-center text-sm font-bold text-gray-900 border-r-2 border-gray-900">
                                        {{ $item->quantity_string }}</td>
                                    <td
                                        class="px-4 py-4 text-left text-sm font-bold text-gray-900 border-r-2 border-gray-900">
                                        {{ $item->description }}</td>
                                    <td
                                        class="whitespace-nowrap px-4 py-4 text-right text-sm font-bold text-gray-900 border-r-2 border-gray-900">
                                        <div class="flex justify-between w-full">
                                            <span>Rp</span>
                                            <span>{{ number_format($item->price, 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4 text-right text-sm font-black text-gray-900">
                                        <div class="flex justify-between w-full">
                                            <span>Rp</span>
                                            <span>{{ number_format($item->total, 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            <!-- Fill empty rows to match template feel if desired, or just footer -->
                        </tbody>
                        <tfoot class="border-t-2 border-gray-900 bg-gray-50">
                            <tr>
                                <td colspan="4"
                                    class="py-4 px-4 text-right text-sm font-black text-gray-900 uppercase border-r-2 border-gray-900">
                                    JUMLAH Total</td>
                                <td class="px-4 py-4 text-right text-lg font-black text-indigo-600">
                                    <div class="flex justify-between w-full">
                                        <span>Rp</span>
                                        <span>{{ number_format($riceDelivery->total_amount, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Footer Info -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-12 mt-10">
                    <div class="text-[10px] md:text-sm space-y-1 font-bold text-gray-600 order-2 sm:order-1">
                        <p class="font-black text-gray-900 mb-2">Transfer ke No. Rek. :</p>
                        <p>0952237630 BNI a/n DEDI KURNIAWAN</p>
                        <p>143701000005565 BRI a/n DEDI KURNIAWAN</p>
                        <p>2080094500 BCA a/n DEDI KURNIAWAN</p>
                        <p>1770011342083 MANDIRI a/n DEDI KURNIAWAN</p>
                    </div>
                    <div class="text-center w-full sm:w-64 relative order-1 sm:order-2 pb-10 sm:pb-0">
                        <p class="font-black text-gray-900 mb-16 sm:mb-20">Hormat Kami,</p>
                        <img src="{{ asset('images/ttd rizki ichsan al-fath.png') }}"
                            class="absolute bottom-6 sm:bottom-6 left-1/2 transform -translate-x-1/2 h-20 md:h-24 w-auto pointer-events-none opacity-90"
                            alt="Signature">
                        <p class="font-black text-gray-900 uppercase border-b-2 border-black inline-block">RIZKI ICHSAN
                            AL-FATH</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
