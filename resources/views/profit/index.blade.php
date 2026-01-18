@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
            <div class="min-w-0 flex-1">
                <h1 class="text-3xl md:text-4xl font-black text-gray-900 tracking-tight">Laporan Laba Rugi</h1>
                <p class="mt-2 text-sm md:text-lg text-gray-500">Analisis keuntungan berdasarkan data penjualan.</p>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white shadow-lg rounded-3xl p-5 mb-6 border border-gray-100">
            <form action="{{ route('profit.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label for="start_date"
                        class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Tanggal Mulai</label>
                    <input type="date" name="start_date" id="start_date" value="{{ $startDate }}"
                        class="block w-full rounded-2xl border-gray-200 py-2.5 px-3 text-sm text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>
                <div>
                    <label for="end_date"
                        class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Tanggal Selesai</label>
                    <input type="date" name="end_date" id="end_date" value="{{ $endDate }}"
                        class="block w-full rounded-2xl border-gray-200 py-2.5 px-3 text-sm text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>
                <div>
                    <label for="search" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Cari
                        Invoice/Pelanggan</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        placeholder="Cari..."
                        class="block w-full rounded-2xl border-gray-200 py-2.5 px-3 text-sm text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>
                <div>
                    <button type="submit"
                        class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-2xl shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                        Filter Laporan
                    </button>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-3xl p-6 shadow-xl border border-gray-50 flex items-center gap-4">
                <div class="p-4 rounded-2xl bg-blue-50 text-blue-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Penjualan</p>
                    <h3 class="text-2xl font-black text-gray-900">Rp {{ number_format($totalSales, 0, ',', '.') }}</h3>
                </div>
            </div>
            <div class="bg-white rounded-3xl p-6 shadow-xl border border-gray-50 flex items-center gap-4">
                <div class="p-4 rounded-2xl bg-amber-50 text-amber-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total HPP</p>
                    <h3 class="text-2xl font-black text-gray-900">Rp {{ number_format($totalHpp, 0, ',', '.') }}</h3>
                </div>
            </div>
            <div class="bg-white rounded-3xl p-6 shadow-xl border-indigo-100 border-2 flex items-center gap-4">
                <div class="p-4 rounded-2xl bg-emerald-50 text-emerald-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 01-2 2h2a2 2 0 012-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Keuntungan</p>
                    <h3 class="text-2xl font-black text-emerald-600">Rp {{ number_format($totalProfit, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>

        <!-- Details Table -->
        <div class="bg-white shadow-2xl rounded-3xl overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="py-4 pl-6 pr-3 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                No</th>
                            <th scope="col"
                                class="px-3 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                Invoice</th>
                            <th scope="col"
                                class="px-3 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                Pelanggan</th>
                            <th scope="col"
                                class="px-3 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                Penjualan</th>
                            <th scope="col"
                                class="px-3 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">HPP
                            </th>
                            <th scope="col"
                                class="px-3 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                Keuntungan</th>
                            <th scope="col"
                                class="px-3 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                Margin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse ($invoices as $invoice)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="whitespace-nowrap py-5 pl-6 pr-3 text-sm font-bold text-gray-400">
                                    {{ $loop->iteration }}</td>
                                <td class="whitespace-nowrap px-3 py-5 text-sm font-bold text-gray-900">
                                    <a href="{{ route('invoices.show', $invoice->id) }}"
                                        class="text-indigo-600 hover:underline">
                                        {{ $invoice->invoice_number }}
                                    </a>
                                    <div class="text-xs text-gray-400">
                                        {{ Carbon\Carbon::parse($invoice->date)->format('d M Y') }}</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-5 text-sm text-gray-500 font-medium">
                                    {{ $invoice->customer_name }}</td>
                                <td class="whitespace-nowrap px-3 py-5 text-sm font-bold text-gray-900">Rp
                                    {{ number_format($invoice->sales, 0, ',', '.') }}</td>
                                <td class="whitespace-nowrap px-3 py-5 text-sm text-gray-500">Rp
                                    {{ number_format($invoice->hpp, 0, ',', '.') }}</td>
                                <td
                                    class="whitespace-nowrap px-3 py-5 text-sm font-bold {{ $invoice->profit >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                    Rp {{ number_format($invoice->profit, 0, ',', '.') }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-5">
                                    @php
                                        $margin = $invoice->sales > 0 ? ($invoice->profit / $invoice->sales) * 100 : 0;
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $margin >= 20 ? 'bg-emerald-50 text-emerald-700' : ($margin >= 10 ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-700') }}">
                                        {{ number_format($margin, 1) }}%
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-gray-500 italic">
                                    Tidak ada data dalam rentang waktu ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
