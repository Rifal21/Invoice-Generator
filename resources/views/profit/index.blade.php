@extends('layouts.app')

@section('title', 'Laporan Laba Rugi')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
            <div class="min-w-0 flex-1">
                <h1 class="text-3xl md:text-4xl font-black text-gray-900 tracking-tight">Laporan Laba Rugi</h1>
                <p class="mt-2 text-sm md:text-lg text-gray-500">Analisis keuntungan berdasarkan data penjualan.</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow-lg rounded-[2rem] p-6 mb-8 border border-gray-100">
            <form action="{{ route('profit.index') }}" method="GET"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                <div>
                    <label for="start_date"
                        class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Tanggal
                        Mulai</label>
                    <input type="date" name="start_date" id="start_date" value="{{ $startDate }}"
                        class="block w-full rounded-xl border-gray-200 py-2.5 px-3 text-sm text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>
                <div>
                    <label for="end_date"
                        class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Tanggal
                        Selesai</label>
                    <input type="date" name="end_date" id="end_date" value="{{ $endDate }}"
                        class="block w-full rounded-xl border-gray-200 py-2.5 px-3 text-sm text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>
                <div class="sm:col-span-2 lg:col-span-1">
                    <label for="search"
                        class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Cari</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        placeholder="Invoice/Pelanggan..."
                        class="block w-full rounded-xl border-gray-200 py-2.5 px-3 text-sm text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                        class="flex-1 flex justify-center py-2.5 px-4 border border-transparent rounded-xl shadow-sm text-xs font-black uppercase tracking-widest text-white bg-indigo-600 hover:bg-indigo-700 transition-all">
                        Filter
                    </button>
                    <a href="{{ route('profit.export-all-pdf', request()->query()) }}" target="_blank"
                        class="flex items-center justify-center p-2.5 border border-transparent rounded-xl shadow-sm text-white bg-red-600 hover:bg-red-700 transition-all">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </a>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 mb-6">
            <div
                class="bg-white rounded-[2rem] p-6 shadow-xl border border-gray-50 flex items-center gap-4 group transition-all hover:translate-y-[-4px]">
                <div class="p-4 rounded-2xl bg-blue-50 text-blue-600 shrink-0">
                    <svg class="w-6 h-6 md:w-8 md:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest truncate">Total Penjualan</p>
                    <h3 class="text-xl md:text-2xl font-black text-gray-900 tracking-tighter truncate">Rp
                        {{ number_format($totalSales, 0, ',', '.') }}</h3>
                </div>
            </div>
            <div
                class="bg-white rounded-[2rem] p-6 shadow-xl border border-gray-50 flex items-center gap-4 group transition-all hover:translate-y-[-4px]">
                <div class="p-4 rounded-2xl bg-amber-50 text-amber-600 shrink-0">
                    <svg class="w-6 h-6 md:w-8 md:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest truncate">Total HPP</p>
                    <h3 class="text-xl md:text-2xl font-black text-gray-900 tracking-tighter truncate">Rp
                        {{ number_format($totalHpp, 0, ',', '.') }}</h3>
                </div>
            </div>
            <div
                class="bg-white rounded-[2rem] p-6 shadow-xl border-indigo-100 border-2 flex items-center gap-4 sm:col-span-2 lg:col-span-1 group transition-all hover:translate-y-[-4px]">
                <div class="p-4 rounded-2xl bg-emerald-50 text-emerald-600 shrink-0">
                    <svg class="w-6 h-6 md:w-8 md:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 01-2 2h2a2 2 0 012-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest truncate">Total Keuntungan</p>
                    <h3 class="text-xl md:text-2xl font-black text-emerald-600 tracking-tighter truncate">Rp
                        {{ number_format($totalProfit, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>

        <!-- Details Table & Mobile view -->
        <div class="bg-white shadow-2xl rounded-[2rem] overflow-hidden border border-gray-100 mb-8">
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="py-4 pl-6 pr-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                No</th>
                            <th scope="col"
                                class="px-3 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Invoice</th>
                            <th scope="col"
                                class="px-3 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Pelanggan</th>
                            <th scope="col"
                                class="px-3 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Penjualan</th>
                            <th scope="col"
                                class="px-3 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                HPP</th>
                            <th scope="col"
                                class="px-3 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Profit</th>
                            <th scope="col"
                                class="px-3 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                Margin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($invoices as $invoice)
                            <tr class="hover:bg-gray-50 transition-colors cursor-pointer"
                                onclick="toggleDetails('details-{{ $invoice->id }}')">
                                <td class="whitespace-nowrap py-5 pl-6 pr-3 text-sm font-bold text-gray-400">
                                    {{ $loop->iteration }}</td>
                                <td class="whitespace-nowrap px-3 py-5 text-sm font-bold text-gray-900">
                                    <div class="flex items-center gap-2">
                                        <svg id="icon-details-{{ $invoice->id }}"
                                            class="w-4 h-4 text-gray-400 transform transition-transform duration-200"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                        <div>
                                            <span
                                                class="text-indigo-600 underline underline-offset-4 decoration-indigo-100 decoration-2 font-black">{{ $invoice->invoice_number }}</span>
                                            <div class="text-[10px] text-gray-400 uppercase font-black">
                                                {{ Carbon\Carbon::parse($invoice->date)->format('d M y') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-5 text-sm text-gray-500 font-medium">
                                    {{ $invoice->customer_name }}</td>
                                <td class="whitespace-nowrap px-3 py-5 text-sm font-black text-gray-900">
                                    Rp{{ number_format($invoice->sales, 0, ',', '.') }}</td>
                                <td class="whitespace-nowrap px-3 py-5 text-sm text-gray-400">
                                    Rp{{ number_format($invoice->hpp, 0, ',', '.') }}</td>
                                <td
                                    class="whitespace-nowrap px-3 py-5 text-sm font-black {{ $invoice->profit >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                    Rp{{ number_format($invoice->profit, 0, ',', '.') }}</td>
                                <td class="whitespace-nowrap px-3 py-5">
                                    @php $margin = $invoice->sales > 0 ? ($invoice->profit / $invoice->sales) * 100 : 0; @endphp
                                    <span
                                        class="px-2 py-1 rounded-lg text-[10px] font-black {{ $margin >= 20 ? 'bg-emerald-50 text-emerald-700' : ($margin >= 10 ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-700') }}">
                                        {{ number_format($margin, 1) }}%
                                    </span>
                                </td>
                            </tr>
                            <tr id="details-{{ $invoice->id }}" class="hidden bg-gray-50 border-t border-gray-100">
                                <td colspan="7" class="px-6 py-4">
                                    <div class="flex items-center justify-between mb-4">
                                        <h4 class="text-xs font-black text-gray-900 uppercase tracking-widest">Detail Item
                                        </h4>
                                        <a href="{{ route('profit.export-invoice-pdf', $invoice->id) }}" target="_blank"
                                            class="inline-flex items-center px-3 py-1.5 rounded-lg bg-indigo-600 text-[10px] font-black text-white uppercase tracking-widest">Export
                                            PDF</a>
                                    </div>
                                    <div class="rounded-2xl border border-gray-200 overflow-hidden bg-white">
                                        <table class="min-w-full divide-y divide-gray-100">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th
                                                        class="px-4 py-3 text-left text-[9px] font-black text-gray-400 uppercase tracking-widest">
                                                        Produk</th>
                                                    <th
                                                        class="px-4 py-3 text-right text-[9px] font-black text-gray-400 uppercase tracking-widest">
                                                        Qty</th>
                                                    <th
                                                        class="px-4 py-3 text-right text-[9px] font-black text-gray-400 uppercase tracking-widest">
                                                        HPP</th>
                                                    <th
                                                        class="px-4 py-3 text-right text-[9px] font-black text-gray-400 uppercase tracking-widest">
                                                        Jual</th>
                                                    <th
                                                        class="px-4 py-3 text-right text-[9px] font-black text-gray-400 uppercase tracking-widest">
                                                        Profit</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-50">
                                                @foreach ($invoice->items as $item)
                                                    @php
                                                        $hppSatuan =
                                                            $item->purchase_price > 0
                                                                ? $item->purchase_price
                                                                : ($item->product
                                                                    ? $item->product->purchase_price
                                                                    : 0);
                                                        $totalHppItem = $hppSatuan * $item->quantity;
                                                        $profitItem = $item->total - $totalHppItem;
                                                    @endphp
                                                    <tr>
                                                        <td class="px-4 py-3 text-xs font-bold text-gray-900 text-left">
                                                            {{ $item->product_name }}</td>
                                                        <td class="px-4 py-3 text-xs text-gray-500 text-right">
                                                            {{ $item->quantity }} {{ $item->unit }}</td>
                                                        <td class="px-4 py-3 text-xs text-gray-400 text-right">
                                                            Rp{{ number_format($hppSatuan, 0, ',', '.') }}</td>
                                                        <td class="px-4 py-3 text-xs font-bold text-gray-900 text-right">
                                                            Rp{{ number_format($item->price, 0, ',', '.') }}</td>
                                                        <td
                                                            class="px-4 py-3 text-xs font-black text-right {{ $profitItem >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                                            Rp{{ number_format($profitItem, 0, ',', '.') }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7"
                                    class="px-6 py-10 text-center text-gray-400 font-bold uppercase text-xs tracking-widest italic">
                                    Belum ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile List (Visible only on mobile) -->
            <div class="md:hidden divide-y divide-gray-100">
                @forelse ($invoices as $invoice)
                    <div class="p-5 space-y-4" onclick="toggleDetails('details-mobile-{{ $invoice->id }}')">
                        <div class="flex justify-between items-start">
                            <div class="min-w-0">
                                <p class="text-xs font-black text-indigo-600">{{ $invoice->invoice_number }}</p>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">
                                    {{ $invoice->customer_name }}</p>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-xs font-black text-gray-900">
                                    Rp{{ number_format($invoice->sales, 0, ',', '.') }}</p>
                                <p class="text-[9px] font-bold text-gray-400">
                                    {{ Carbon\Carbon::parse($invoice->date)->format('d/m/y') }}</p>
                            </div>
                        </div>
                        <div class="flex justify-between items-center bg-gray-50 rounded-xl p-3">
                            <div>
                                <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Profit</p>
                                <p
                                    class="text-xs font-black {{ $invoice->profit >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                                    Rp{{ number_format($invoice->profit, 0, ',', '.') }}</p>
                            </div>
                            @php $margin = $invoice->sales > 0 ? ($invoice->profit / $invoice->sales) * 100 : 0; @endphp
                            <span
                                class="px-2 py-1 rounded-lg text-[9px] font-black {{ $margin >= 20 ? 'bg-emerald-50 text-emerald-700' : ($margin >= 10 ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-700') }}">
                                {{ number_format($margin, 1) }}% Margin
                            </span>
                        </div>

                        <!-- Mobile Detail Content (Collapsible) -->
                        <div id="details-mobile-{{ $invoice->id }}"
                            class="hidden pt-4 space-y-3 border-t border-gray-100">
                            @foreach ($invoice->items as $item)
                                @php
                                    $hppSatuan =
                                        $item->purchase_price > 0
                                            ? $item->purchase_price
                                            : ($item->product
                                                ? $item->product->purchase_price
                                                : 0);
                                    $profitItem = $item->total - $hppSatuan * $item->quantity;
                                @endphp
                                <div class="flex justify-between items-center text-[10px]">
                                    <div class="min-w-0 pr-4">
                                        <p class="font-bold text-gray-900 truncate">{{ $item->product_name }}</p>
                                        <p class="text-gray-400 font-medium">{{ $item->quantity }} {{ $item->unit }} x
                                            Rp{{ number_format($item->price, 0, ',', '.') }}</p>
                                    </div>
                                    <p
                                        class="font-black shrink-0 {{ $profitItem >= 0 ? 'text-emerald-600' : 'text-red-500' }}">
                                        +Rp{{ number_format($profitItem, 0, ',', '.') }}</p>
                                </div>
                            @endforeach
                            <a href="{{ route('profit.export-invoice-pdf', $invoice->id) }}" target="_blank"
                                class="block w-full text-center py-2.5 bg-red-50 text-red-600 rounded-xl text-[9px] font-black uppercase tracking-widest">Detail
                                PDF</a>
                        </div>
                    </div>
                @empty
                    <div class="p-10 text-center text-gray-400 font-bold uppercase text-xs tracking-widest italic">Belum
                        ada data.</div>
                @endforelse
            </div>
            <!-- Pagination Links -->
            <div class="px-8 py-4 border-t border-gray-100">
                {{ $invoices->links() }}
            </div>
        </div>
    </div>
    </div>

    <script>
        function toggleDetails(id) {
            const row = document.getElementById(id);
            const icon = document.getElementById('icon-' + id);

            if (row.classList.contains('hidden')) {
                row.classList.remove('hidden');
                icon.style.transform = 'rotate(90deg)';
                // Fade in effect
                row.style.opacity = '0';
                setTimeout(() => {
                    row.style.opacity = '1';
                }, 10);
            } else {
                row.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
                row.style.opacity = '0';
            }
        }
    </script>
@endsection
