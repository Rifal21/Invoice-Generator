@extends('layouts.app')

@section('title', 'Detail Surat Jalan ' . $deliveryOrder->order_number)

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb & Actions -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
            <div class="min-w-0 flex-1">
                <nav class="flex mb-4" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2">
                        <li><a href="{{ route('delivery-orders.index') }}"
                                class="text-xs md:text-sm font-bold text-gray-400 hover:text-indigo-600">Surat Jalan</a></li>
                        <li><svg class="h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd" />
                            </svg></li>
                        <li class="text-xs md:text-sm font-bold text-indigo-600">Detail</li>
                    </ol>
                </nav>
                <h2 class="text-2xl md:text-3xl font-black text-gray-900 tracking-tight">Detail Surat Jalan</h2>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('delivery-orders.export-pdf', $deliveryOrder->id) }}" target="_blank"
                    class="inline-flex items-center px-5 py-2.5 border border-transparent text-sm font-bold rounded-xl text-white bg-indigo-600 shadow-lg hover:bg-indigo-700 transition-all duration-200">
                    <i class="fas fa-file-pdf mr-2"></i> Cetak PDF
                </a>
                <a href="{{ route('delivery-orders.edit', $deliveryOrder->id) }}"
                    class="inline-flex items-center px-5 py-2.5 border border-gray-200 text-sm font-bold rounded-xl text-gray-700 bg-white shadow-sm hover:bg-gray-50 transition-all duration-200">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
            </div>
        </div>

        <!-- Surat Jalan Card -->
        <div class="bg-white shadow-2xl rounded-3xl overflow-hidden border border-gray-100">
            <!-- Order Header -->
            <div class="p-6 md:p-10 border-b border-gray-50 bg-gray-50/50">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Kepada Yth.</p>
                        <div class="text-lg font-bold text-gray-900 whitespace-pre-line leading-relaxed">
                            {{ $deliveryOrder->customer_name }}
                        </div>
                    </div>
                    <div class="md:text-right">
                        <div class="inline-block bg-white px-6 py-3 rounded-2xl shadow-sm border border-gray-100">
                            <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1">Nomor Surat Jalan</p>
                            <p class="text-xl font-black text-indigo-600">{{ $deliveryOrder->order_number }}</p>
                            <p class="text-xs font-bold text-gray-500 mt-2">
                                {{ \Carbon\Carbon::parse($deliveryOrder->date)->format('d F Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="p-6 md:p-10">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead>
                            <tr>
                                <th
                                    class="px-4 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest bg-gray-50/50 rounded-l-xl">
                                    Banyaknya</th>
                                <th
                                    class="px-4 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest bg-gray-50/50">
                                    Nama Barang</th>
                                <th
                                    class="px-4 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest bg-gray-50/50 rounded-r-xl">
                                    Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-50">
                            @foreach ($deliveryOrder->items as $item)
                                <tr>
                                    <td class="px-4 py-5 text-sm font-bold text-gray-900">{{ $item->quantity_string }}</td>
                                    <td class="px-4 py-5 text-sm font-medium text-gray-600">{{ $item->item_name }}</td>
                                    <td class="px-4 py-5 text-sm text-gray-500 italic">{{ $item->description ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($deliveryOrder->location)
                    <div class="mt-10 p-4 bg-blue-50 rounded-2xl border border-blue-100 flex items-center gap-3">
                        <div class="h-10 w-10 shrink-0 rounded-xl bg-blue-500 flex items-center justify-center text-white">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-blue-400 uppercase tracking-widest">Lokasi Pengiriman</p>
                            <p class="text-sm font-bold text-blue-900">{{ $deliveryOrder->location }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Footer / Signature Spaces -->
            <div class="p-10 border-t border-gray-50 bg-gray-50/30">
                <div class="grid grid-cols-2 gap-10">
                    <div class="text-center">
                        <p class="text-sm font-bold text-gray-400 mb-20">Penerima,</p>
                        <div class="border-b-2 border-gray-200 w-32 mx-auto"></div>
                    </div>
                    <div class="text-center">
                        <p class="text-sm font-bold text-gray-400 mb-20">Hormat Kami,</p>
                        <div class="border-b-2 border-gray-200 w-32 mx-auto"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
