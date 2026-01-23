@extends('layouts.app')

@section('title', 'Rekap Pesanan Beras')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
            <div class="min-w-0 flex-1">
                <h1 class="text-3xl md:text-4xl font-black text-gray-900 tracking-tight">Rekap Pesanan Beras</h1>
                <p class="mt-2 text-sm md:text-lg text-gray-500">Ringkasan total pesanan beras dari semua invoice.</p>
            </div>
            <div class="flex shrink-0 gap-2">
                <a href="{{ route('rice-order-recap.export-pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                    class="inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 text-sm font-bold rounded-xl text-white shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all">
                    <i class="fas fa-file-pdf mr-2"></i> Cetak PDF
                </a>
                <button onclick="window.print()"
                    class="inline-flex items-center justify-center px-4 py-2.5 border border-gray-200 text-sm font-bold rounded-xl text-gray-700 bg-white shadow-sm hover:bg-gray-50 transition-all">
                    <i class="fas fa-print mr-2"></i> Print Screen
                </button>
            </div>
        </div>

        <!-- Filter Card -->
        <div class="bg-white shadow-xl rounded-3xl p-6 mb-8 border border-gray-100">
            <form action="{{ route('rice-order-recap.index') }}" method="GET"
                class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                <div>
                    <label for="start_date"
                        class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="start_date" value="{{ $startDate }}"
                        class="block w-full rounded-2xl border-2 border-gray-100 py-3 px-4 text-gray-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-bold">
                </div>
                <div>
                    <label for="end_date"
                        class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Tanggal
                        Selesai</label>
                    <input type="date" name="end_date" id="end_date" value="{{ $endDate }}"
                        class="block w-full rounded-2xl border-2 border-gray-100 py-3 px-4 text-gray-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-bold">
                </div>
                <div>
                    <button type="submit"
                        class="w-full bg-indigo-600 text-white font-black py-4 rounded-2xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100 uppercase tracking-widest text-sm">
                        Filter Data
                    </button>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Summary Table -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Product Summary -->
                <div class="bg-white shadow-2xl rounded-3xl overflow-hidden border border-gray-100">
                    <div class="p-6 border-b border-gray-50 bg-gray-50/50">
                        <h3 class="text-lg font-black text-gray-900">Total Per Produk</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @forelse($items as $item)
                                <div
                                    class="flex items-center justify-between p-4 rounded-2xl bg-indigo-50/50 border border-indigo-100/50">
                                    <div>
                                        <p class="text-xs font-bold text-indigo-400 uppercase tracking-widest">
                                            {{ $item->product_name }}</p>
                                        <p class="text-xl font-black text-indigo-900">
                                            {{ number_format($item->total_quantity, 0, ',', '.') }} {{ $item->unit }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Nilai
                                        </p>
                                        <p class="text-sm font-black text-indigo-600">Rp
                                            {{ number_format($item->total_amount, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <p class="text-gray-400 font-bold">Tidak ada data pesanan beras</p>
                                </div>
                            @endforelse
                        </div>

                        @if ($items->count() > 0)
                            <div class="mt-6 pt-6 border-t border-gray-100 space-y-2">
                                <div class="flex items-center justify-between text-gray-500">
                                    <span class="text-[10px] font-black uppercase tracking-widest">Total Qty</span>
                                    <span class="text-sm font-black">
                                        @php
                                            $totalQty = $items
                                                ->groupBy(function ($item) {
                                                    return strtolower(trim($item->unit ?: 'pcs'));
                                                })
                                                ->map(function ($group) {
                                                    return number_format($group->sum('total_quantity'), 0, ',', '.') .
                                                        ' ' .
                                                        $group->first()->unit;
                                                })
                                                ->implode(', ');
                                        @endphp
                                        {{ $totalQty }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-black text-gray-900 uppercase tracking-widest">Grand
                                        Total</span>
                                    <span class="text-xl font-black text-indigo-600">Rp
                                        {{ number_format($items->sum('total_amount'), 0, ',', '.') }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Customer Summary (Dapur) -->
                <div class="bg-white shadow-2xl rounded-3xl overflow-hidden border border-gray-100">
                    <div class="p-6 border-b border-gray-50 bg-gray-50/50">
                        <h3 class="text-lg font-black text-gray-900">Total Per Pelanggan</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @php
                                $customerData = $details
                                    ->groupBy(function ($item) {
                                        return $item->invoice->customer_name;
                                    })
                                    ->map(function ($group) {
                                        return [
                                            'qty' => $group
                                                ->groupBy(function ($item) {
                                                    return strtolower(trim($item->unit ?: 'pcs'));
                                                })
                                                ->map(function ($unitGroup) {
                                                    return [
                                                        'sum' => $unitGroup->sum('quantity'),
                                                        'display_unit' => $unitGroup->first()->unit ?: 'pcs',
                                                    ];
                                                }),
                                            'amount' => $group->sum('total'),
                                        ];
                                    });
                            @endphp
                            @foreach ($customerData as $name => $data)
                                <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100">
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">
                                        {{ $name }}
                                    </p>
                                    <div class="flex justify-between items-end mt-1">
                                        <div class="text-lg font-black text-gray-900">
                                            @foreach ($data['qty'] as $q)
                                                {{ number_format($q['sum'], 0, ',', '.') }} {{ $q['display_unit'] }}<br>
                                            @endforeach
                                        </div>
                                        <p class="text-sm font-black text-indigo-600 text-right">Rp
                                            {{ number_format($data['amount'], 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Transactions -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow-2xl rounded-3xl overflow-hidden border border-gray-100">
                    <div class="p-6 border-b border-gray-50 bg-gray-50/50 flex justify-between items-center">
                        <h3 class="text-lg font-black text-gray-900">Detail Transaksi</h3>
                        <span
                            class="px-3 py-1 bg-white rounded-full text-[10px] font-black text-gray-400 border border-gray-100 uppercase">{{ $details->count() }}
                            Transaksi</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead>
                                <tr class="bg-gray-50/30">
                                    <th
                                        class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        Tanggal</th>
                                    <th
                                        class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        Pelanggan</th>
                                    <th
                                        class="px-6 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        Produk</th>
                                    <th
                                        class="px-6 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        Harga</th>
                                    <th
                                        class="px-6 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        Qty</th>
                                    <th
                                        class="px-6 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($details as $detail)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-600">
                                            {{ \Carbon\Carbon::parse($detail->invoice->date)->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-black text-gray-900">
                                                {{ $detail->invoice->customer_name }}</div>
                                            <div class="text-[10px] font-bold text-indigo-400">
                                                {{ $detail->invoice->invoice_number }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-700">
                                            {{ $detail->product_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-black text-right text-gray-900">
                                            Rp {{ number_format($detail->price, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-black text-right text-gray-900">
                                            {{ number_format($detail->quantity, 0, ',', '.') }} {{ $detail->unit }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm font-black text-right text-indigo-600">
                                            Rp {{ number_format($detail->total, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-20 text-center">
                                            <i class="fas fa-box-open text-4xl text-gray-200 mb-4 block"></i>
                                            <p class="text-gray-400 font-bold">Tidak ada rincian transaksi</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            .max-w-7xl,
            .max-w-7xl * {
                visibility: visible;
            }

            .max-w-7xl {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            button,
            form {
                display: none !important;
            }
        }
    </style>
@endsection
