@extends('layouts.app')

@section('title', 'Detail Invoice Sewa Kendaraan')

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-10">
        <!-- Action Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 md:mb-10 gap-4 md:gap-6">
            <div class="min-w-0 flex-1">
                <nav class="flex mb-4" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2">
                        <li><a href="{{ route('vehicle-rentals.index') }}"
                                class="text-xs md:text-sm font-bold text-indigo-600 hover:text-indigo-500">Sewa Kendaraan</a>
                        </li>
                        <li><svg class="h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd" />
                            </svg></li>
                        <li class="text-xs md:text-sm font-bold text-gray-400">Detail</li>
                    </ol>
                </nav>
                <h2 class="text-3xl md:text-4xl font-black text-gray-900 tracking-tight">Detail Invoice</h2>
            </div>
            <div class="flex flex-wrap gap-2 md:gap-3">
                <a href="{{ route('vehicle-rentals.edit', $vehicleRental->id) }}"
                    class="inline-flex items-center justify-center px-4 py-2.5 rounded-2xl bg-amber-50 text-amber-700 text-sm font-bold shadow-sm hover:bg-amber-100 transition-all">
                    <i class="fas fa-edit mr-2"></i>
                    Edit
                </a>
                <a href="{{ route('vehicle-rentals.export-pdf', $vehicleRental->id) }}" target="_blank"
                    class="inline-flex items-center justify-center px-4 py-2.5 rounded-2xl bg-red-50 text-red-700 text-sm font-bold shadow-sm hover:bg-red-100 transition-all">
                    <i class="fas fa-file-pdf mr-2"></i>
                    PDF
                </a>
                <a href="{{ route('vehicle-rentals.index') }}"
                    class="inline-flex items-center justify-center px-5 py-2.5 rounded-2xl bg-indigo-600 text-white text-sm font-bold shadow-xl hover:bg-indigo-700 transition-all transform hover:-translate-y-0.5">
                    Kembali
                </a>
            </div>
        </div>

        <!-- Invoice Card -->
        <div class="bg-white shadow-2xl rounded-3xl overflow-hidden border border-gray-100">
            <div class="p-6 md:p-12">
                <!-- Header -->
                <div
                    class="flex flex-col md:flex-row justify-between items-start border-b border-gray-100 pb-8 md:pb-10 gap-8">
                    <div class="max-w-sm w-full">
                        <div
                            class="h-12 w-12 md:h-16 md:w-16 bg-amber-500 rounded-2xl flex items-center justify-center text-white mb-6 shadow-lg">
                            <i class="fas fa-car-side text-3xl"></i>
                        </div>
                        <h1 class="text-lg md:text-xl font-black text-gray-900 uppercase tracking-tight leading-snug">
                            JR JEMBARRAHAYU</h1>
                        <div class="mt-4 space-y-1 text-xs md:text-sm text-gray-500 font-medium">
                            <p>Jl. Ahmad Bagowi Kp. Bojong RT. 003 RW. 007</p>
                            <p>Sukamantri - Kab. Tasikmalaya</p>
                            <p>Telp: 0852 2300 4240</p>
                            <p>Email: jr.jembarrahayu@gmail.com</p>
                        </div>
                    </div>
                    <div class="text-left md:text-right w-full md:w-auto">
                        <h2 class="text-4xl md:text-5xl font-black text-indigo-600 tracking-tighter uppercase text-right">
                            INVOICE</h2>
                        <div class="mt-6 flex flex-col items-end gap-y-2">
                            <div>
                                <p class="text-xs md:text-sm font-bold text-gray-400 uppercase tracking-widest text-right">
                                    Nomor Invoice</p>
                                <p class="text-lg md:text-xl font-black text-gray-900 text-right">
                                    INV-{{ $vehicleRental->invoice_number }}</p>
                            </div>
                            <div>
                                <p
                                    class="text-xs md:text-sm font-bold text-gray-400 uppercase tracking-widest text-right mt-4">
                                    Tanggal Terbit</p>
                                <p class="text-base md:text-lg font-bold text-gray-900 text-right">
                                    {{ \Carbon\Carbon::parse($vehicleRental->date)->format('d F Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Customer info -->
                <div class="mt-8 md:mt-12 mb-8 md:mb-12">
                    <div class="bg-gray-50 rounded-3xl p-6 md:p-8 border border-gray-100 max-w-md">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">KEPADA :</h3>
                        <p class="text-lg font-bold text-gray-900 whitespace-pre-wrap">{!! nl2br(e($vehicleRental->customer_name)) !!}</p>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="overflow-hidden rounded-3xl border border-gray-100 shadow-sm mb-12">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th scope="col"
                                    class="py-4 pl-6 pr-3 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                    NO</th>
                                <th scope="col"
                                    class="px-3 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                    DESKRIPSI</th>
                                <th scope="col"
                                    class="px-3 py-4 text-center text-xs font-black text-gray-400 uppercase tracking-widest">
                                    JUMLAH</th>
                                <th scope="col"
                                    class="px-3 py-4 text-right text-xs font-black text-gray-400 uppercase tracking-widest">
                                    HARGA UNIT</th>
                                <th scope="col"
                                    class="px-3 py-4 pr-6 text-right text-xs font-black text-gray-400 uppercase tracking-widest">
                                    TOTAL</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 bg-white">
                            @foreach ($vehicleRental->items as $index => $item)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="whitespace-nowrap py-5 pl-6 pr-3 text-sm font-bold text-gray-400">
                                        {{ $index + 1 }}.</td>
                                    <td class="px-3 py-5 text-sm font-bold text-gray-900">
                                        <div class="font-black">{{ $item->description }}</div>
                                        @if ($item->start_date && $item->end_date)
                                            <div class="text-xs text-gray-400 mt-1">
                                                <i class="fas fa-calendar-alt mr-1"></i>
                                                {{ \Carbon\Carbon::parse($item->start_date)->format('d/m/Y') }} -
                                                {{ \Carbon\Carbon::parse($item->end_date)->format('d/m/Y') }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-5 text-sm font-bold text-gray-600 text-center">
                                        {{ number_format($item->quantity, floor($item->quantity) == $item->quantity ? 0 : 2, ',', '.') }}
                                        {{ $item->unit }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-5 text-sm font-bold text-gray-600 text-right">
                                        Rp {{ number_format($item->price, 0, ',', '.') }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-3 py-5 pr-6 text-sm font-black text-gray-900 text-right">
                                        Rp {{ number_format($item->total, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50/50">
                            <tr>
                                <td colspan="4"
                                    class="py-4 pl-6 pr-3 text-right text-sm font-black text-gray-400 uppercase tracking-widest">
                                    TOTAL AKHIR</td>
                                <td class="px-3 py-4 pr-6 text-right text-xl font-black text-indigo-600">
                                    Rp {{ number_format($vehicleRental->total_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Footer Info -->
                <div
                    class="flex flex-col md:flex-row justify-between items-end pt-8 md:pt-12 border-t border-gray-100 gap-8 md:gap-12">
                    <div class="w-full md:max-w-sm">
                        <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Informasi Pembayaran
                        </h4>
                        <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 space-y-2">
                            <p class="text-sm font-bold text-gray-900">BRI</p>
                            <p class="text-lg font-black text-indigo-600 tracking-tight">143701000476300</p>
                            <p class="text-xs font-medium text-gray-500 uppercase">a/n Jembar Rahayu</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
