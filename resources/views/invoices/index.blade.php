@extends('layouts.app')

@section('title', 'Daftar Invoice')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
            <div class="min-w-0 flex-1">
                <h1 class="text-3xl md:text-4xl font-black text-gray-900 tracking-tight">Daftar Invoice</h1>
                <p class="mt-2 text-sm md:text-lg text-gray-500">Kelola dan pantau semua invoice Anda.</p>
            </div>
            <div class="flex shrink-0">
                <a href="{{ route('invoices.create') }}"
                    class="w-full md:w-auto inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-bold rounded-2xl text-white bg-indigo-600 shadow-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:-translate-y-1">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Buat Invoice
                </a>
            </div>
        </div>

        <!-- Filter Section -->
        <form id="filter-form" action="{{ route('invoices.index') }}" method="GET"
            class="bg-white shadow-lg rounded-3xl p-5 mb-6 border border-gray-100">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-y-4 gap-x-6 items-end">
                <!-- Search -->
                <div class="md:col-span-3">
                    <label for="search"
                        class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Cari</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        placeholder="No. Invoice / Nama..."
                        class="block w-full rounded-2xl border-gray-200 py-2.5 px-3 text-sm text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>

                <!-- Customer Filter -->
                <div class="md:col-span-3">
                    <label for="customer"
                        class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Pelanggan</label>
                    <select name="customer" id="customer"
                        class="block w-full rounded-2xl border-gray-200 py-2.5 px-3 text-sm text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                        <option value="">Semua Pelanggan</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer }}" {{ request('customer') == $customer ? 'selected' : '' }}>
                                {{ $customer }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Filter -->
                <div class="md:col-span-2">
                    <label for="date"
                        class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Tanggal</label>
                    <input type="date" name="date" id="date" value="{{ request('date') }}"
                        class="block w-full rounded-2xl border-gray-200 py-2.5 px-3 text-sm text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>

                <!-- Action Buttons -->
                <div class="md:col-span-4 flex flex-col sm:flex-row gap-2">
                    <div class="flex flex-1 gap-2">
                        <button type="submit"
                            class="flex-1 bg-indigo-600 text-white font-bold py-2.5 px-4 rounded-2xl hover:bg-indigo-700 transition-all shadow-md text-sm">
                            Filter
                        </button>
                        <a href="{{ route('invoices.index') }}"
                            class="bg-gray-100 text-gray-600 font-bold py-2.5 px-4 rounded-2xl hover:bg-gray-200 transition-all text-center text-sm flex items-center justify-center">
                            Reset
                        </a>
                    </div>
                </div>
            </div>

            <!-- Bulk Actions (Mobile: Stacked, Desktop: Row) -->
            <div class="mt-4 pt-4 border-t border-gray-100 flex flex-col md:flex-row gap-3">
                <button type="button" onclick="submitBulkExport()" id="bulk-export-btn" disabled
                    class="flex-1 md:flex-none bg-red-50 text-red-700 font-bold py-2.5 px-4 rounded-2xl hover:bg-red-100 transition-all border border-red-100 disabled:opacity-50 disabled:cursor-not-allowed text-xs flex items-center justify-center gap-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Buat Laporan (PDF)
                </button>
                <button type="button" onclick="submitMultiPrint()" id="multi-print-btn" disabled
                    class="flex-1 md:flex-none bg-indigo-50 text-indigo-700 font-bold py-2.5 px-4 rounded-2xl hover:bg-indigo-100 transition-all border border-indigo-100 disabled:opacity-50 disabled:cursor-not-allowed text-xs flex items-center justify-center gap-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak Invoice (PDF)
                </button>
                <!-- Dropdown Kirim -->
                <div class="relative flex-1 md:flex-none" x-data="{ open: false }">
                    <button type="button" @click="open = !open" @click.away="open = false" id="report-dropdown-btn"
                        disabled
                        class="w-full md:w-auto bg-indigo-50 text-indigo-700 font-bold py-2.5 px-6 rounded-2xl hover:bg-indigo-100 transition-all border border-indigo-100 disabled:opacity-50 disabled:cursor-not-allowed text-xs flex items-center justify-center gap-2">
                        <i class="fas fa-paper-plane"></i>
                        <span>Kirim Laporan</span>
                        <svg class="h-4 w-4 transform transition-transform" :class="open ? 'rotate-180' : ''" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        class="absolute left-0 mt-2 w-64 rounded-2xl shadow-2xl bg-white ring-1 ring-black ring-opacity-5 z-50 overflow-hidden border border-gray-100">
                        <div class="py-1">
                            <div
                                class="px-4 py-2 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50">
                                Telegram</div>
                            <button type="button" onclick="submitTelegram()" id="telegram-btn"
                                class="flex items-center gap-3 px-4 py-3 text-sm font-bold text-gray-700 hover:bg-sky-50 hover:text-sky-700 w-full text-left transition-colors">
                                <i class="fab fa-telegram text-sky-600 text-lg"></i>
                                <span id="telegram-btn-text">Ke Group Admin</span>
                            </button>
                            <button type="button" onclick="submitTelegramCustomer()" id="telegram-customer-btn"
                                class="flex items-center gap-3 px-4 py-3 text-sm font-bold text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 w-full text-left transition-colors border-t border-gray-50">
                                <i class="fas fa-user-check text-indigo-600 text-lg"></i>
                                <span id="telegram-customer-btn-text">Ke Client (Personal)</span>
                            </button>

                            <div
                                class="px-4 py-2 text-[10px] font-black text-gray-400 uppercase tracking-widest border-y border-gray-50 mt-1">
                                WhatsApp (WAHA)</div>
                            <button type="button" onclick="submitWhatsApp()" id="whatsapp-btn"
                                class="flex items-center gap-3 px-4 py-3 text-sm font-bold text-gray-700 hover:bg-green-50 hover:text-green-700 w-full text-left transition-colors">
                                <i class="fab fa-whatsapp text-green-600 text-xl"></i>
                                <span id="whatsapp-btn-text">Kirim ke WhatsApp (WAHA)</span>
                            </button>
                            <button type="button" onclick="submitWhapi()" id="whapi-btn"
                                class="flex items-center gap-3 px-4 py-3 text-sm font-bold text-gray-700 hover:bg-green-50 hover:text-green-700 w-full text-left transition-colors border-t border-gray-50">
                                <i class="fab fa-whatsapp text-green-500 text-xl"></i>
                                <span id="whapi-btn-text">Kirim ke WhatsApp (Whapi Cloud)</span>
                            </button>
                        </div>
                    </div>
                </div>
                <button type="button" onclick="submitBulkDelete()" id="bulk-delete-btn" disabled
                    class="flex-1 md:flex-none bg-red-600 text-white font-bold py-2.5 px-4 rounded-2xl hover:bg-red-700 transition-all shadow-md disabled:opacity-50 disabled:cursor-not-allowed text-xs flex items-center justify-center gap-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Hapus Selected
                </button>
            </div>
        </form>

        <!-- Sort & Pagination Controls -->
        <div
            class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4 bg-white p-4 rounded-3xl shadow-sm border border-gray-50">
            <div class="flex items-center gap-3 w-full md:w-auto">
                <span class="text-xs font-black text-gray-400 uppercase tracking-widest whitespace-nowrap">Show:</span>
                <select name="per_page" onchange="document.getElementById('filter-form').submit()" form="filter-form"
                    class="w-full md:w-auto rounded-xl border-gray-200 text-sm font-bold focus:ring-indigo-500 focus:border-indigo-500 py-2 pl-3 pr-8">
                    <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>All</option>
                </select>
            </div>

            <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <select name="sort_by" onchange="document.getElementById('filter-form').submit()" form="filter-form"
                        class="w-full sm:w-auto rounded-xl border-gray-200 text-sm font-bold focus:ring-indigo-500 focus:border-indigo-500 py-2 pl-3 pr-8">
                        <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Terbaru
                        </option>
                        <option value="date" {{ request('sort_by') == 'date' ? 'selected' : '' }}>Tanggal</option>
                        <option value="invoice_number" {{ request('sort_by') == 'invoice_number' ? 'selected' : '' }}>No.
                            Invoice</option>
                        <option value="customer_name" {{ request('sort_by') == 'customer_name' ? 'selected' : '' }}>Nama
                        </option>
                        <option value="total_amount" {{ request('sort_by') == 'total_amount' ? 'selected' : '' }}>Nominal
                        </option>
                    </select>
                </div>
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <select name="sort_order" onchange="document.getElementById('filter-form').submit()"
                        form="filter-form"
                        class="w-full sm:w-auto rounded-xl border-gray-200 text-sm font-bold focus:ring-indigo-500 focus:border-indigo-500 py-2 pl-3 pr-8">
                        <option value="desc" {{ request('sort_order', 'desc') == 'desc' ? 'selected' : '' }}>Menurun
                        </option>
                        <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Menaik</option>
                    </select>
                </div>
            </div>
        </div>

        <form id="bulk-export-form" action="{{ route('invoices.bulk-export-pdf') }}" method="POST" target="_blank">
            @csrf

            <!-- Desktop Table View -->
            <div class="hidden md:block bg-white shadow-2xl rounded-3xl overflow-hidden border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th scope="col" class="py-5 pl-6 pr-3 text-left w-10">
                                    <input type="checkbox" id="select-all"
                                        class="h-5 w-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                </th>
                                <th scope="col"
                                    class="px-3 py-5 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                    Nomor</th>
                                <th scope="col"
                                    class="px-3 py-5 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                    Tanggal</th>
                                <th scope="col"
                                    class="px-3 py-5 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                    Pelanggan</th>
                                <th scope="col"
                                    class="px-3 py-5 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                    Tipe</th>
                                <th scope="col"
                                    class="px-3 py-5 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                    Diskon</th>
                                <th scope="col"
                                    class="px-3 py-5 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                    Total</th>
                                <th scope="col"
                                    class="relative py-5 pl-3 pr-6 text-right text-xs font-black text-gray-400 uppercase tracking-widest">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 bg-white">
                            @forelse ($invoices as $invoice)
                                <tr class="hover:bg-indigo-50/30 transition-colors duration-150 group">
                                    <td class="py-5 pl-6 pr-3">
                                        <input type="checkbox" name="invoice_ids[]" value="{{ $invoice->id }}"
                                            class="invoice-checkbox h-5 w-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-5 cursor-pointer"
                                        onclick="toggleDetails('details-{{ $invoice->id }}')">
                                        <div class="flex items-center">
                                            <div
                                                class="h-10 w-10 flex-shrink-0 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600">
                                                <svg id="icon-details-{{ $invoice->id }}"
                                                    class="h-6 w-6 transform transition-transform duration-200"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5l7 7-7 7" />
                                                </svg>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-black text-gray-900 flex items-center gap-2">
                                                    {{ $invoice->invoice_number }}
                                                    <div class="flex gap-1">
                                                        @if ($invoice->whatsapp_sent_at)
                                                            <i class="fab fa-whatsapp text-green-500 text-[10px]"
                                                                title="WA Terkirim: {{ \Carbon\Carbon::parse($invoice->whatsapp_sent_at)->format('d/m H:i') }}"></i>
                                                        @endif
                                                        @if ($invoice->telegram_sent_at)
                                                            <i class="fab fa-telegram text-sky-500 text-[10px]"
                                                                title="TG Terkirim: {{ \Carbon\Carbon::parse($invoice->telegram_sent_at)->format('d/m H:i') }}"></i>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="text-xs font-bold text-gray-400">Invoice Tagihan</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-5 text-sm text-gray-600 font-medium cursor-pointer"
                                        onclick="toggleDetails('details-{{ $invoice->id }}')">
                                        {{ \Carbon\Carbon::parse($invoice->date)->format('d M Y') }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-5 cursor-pointer"
                                        onclick="toggleDetails('details-{{ $invoice->id }}')">
                                        <div class="text-sm font-bold text-gray-900">{{ $invoice->customer_name }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-5 cursor-pointer"
                                        onclick="toggleDetails('details-{{ $invoice->id }}')">
                                        @php
                                            $type = 'UNKNOWN';
                                            if (str_contains($invoice->invoice_number, 'BSH')) {
                                                $type = 'Basahan Siswa';
                                            } elseif (str_contains($invoice->invoice_number, 'KRBSBM')) {
                                                $type = 'Keringan B3';
                                            } elseif (str_contains($invoice->invoice_number, 'KR')) {
                                                $type = 'Keringan Siswa';
                                            } elseif (str_contains($invoice->invoice_number, 'OPR')) {
                                                $type = 'Operasional';
                                            } elseif (str_contains($invoice->invoice_number, 'LMN')) {
                                                $type = 'Lain-lain';
                                            }
                                        @endphp
                                        <div class="text-sm font-bold text-gray-900">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ $type }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-5 cursor-pointer"
                                        onclick="toggleDetails('details-{{ $invoice->id }}')">
                                        <div class="text-sm font-bold text-red-500">
                                            {{ $invoice->discount > 0 ? 'Rp ' . number_format($invoice->discount, 0, ',', '.') : '-' }}
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-5 cursor-pointer"
                                        onclick="toggleDetails('details-{{ $invoice->id }}')">
                                        <div class="text-sm font-black text-indigo-600">Rp
                                            {{ number_format($invoice->total_amount, 2, ',', '.') }}</div>
                                    </td>
                                    <td
                                        class="relative whitespace-nowrap py-5 pl-3 pr-6 text-right text-sm font-bold space-x-1">
                                        <button type="button" onclick="toggleDetails('details-{{ $invoice->id }}')"
                                            class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 p-2 rounded-xl transition-colors inline-flex items-center justify-center"
                                            title="Lihat Detail">
                                            <i class="fas fa-eye text-lg"></i>
                                        </button>
                                        <a href="{{ route('invoices.export-pdf', $invoice->id) }}" target="_blank"
                                            class="text-red-600 hover:text-red-900 bg-red-50 p-2 rounded-xl transition-colors inline-flex items-center justify-center"
                                            title="Cetak PDF">
                                            <i class="fas fa-print text-lg"></i>
                                        </a>
                                        <a href="{{ route('invoices.edit', ['invoice' => $invoice->id] + request()->query()) }}"
                                            class="text-amber-600 hover:text-amber-900 bg-amber-50 p-2 rounded-xl transition-colors inline-flex items-center justify-center"
                                            title="Edit Invoice">
                                            <i class="fas fa-edit text-lg"></i>
                                        </a>
                                        <button type="button" onclick="deleteInvoice({{ $invoice->id }})"
                                            class="text-red-600 hover:text-red-900 bg-red-50 p-2 rounded-xl transition-colors inline-flex items-center justify-center"
                                            title="Hapus Invoice">
                                            <i class="fas fa-trash-alt text-lg"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Expandable Detail Row -->
                                <tr id="details-{{ $invoice->id }}"
                                    class="hidden bg-gray-50/50 border-t border-gray-100 transition-all duration-300">
                                    <td colspan="6" class="px-6 py-6">
                                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                                            <div
                                                class="bg-gray-50/50 px-4 py-3 border-b border-gray-100 flex justify-between items-center">
                                                <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest">
                                                    Detail Item</h4>
                                                <div class="flex items-center gap-3">
                                                    <a href="{{ route('invoices.show', $invoice->id) }}"
                                                        class="text-[10px] font-black uppercase text-gray-400 hover:text-indigo-600 transition-colors flex items-center gap-1">
                                                        <i class="fas fa-external-link-alt"></i> Halaman Detail
                                                    </a>
                                                    <div class="h-4 w-px bg-gray-200"></div>
                                                    <a href="{{ route('invoices.export-pdf', $invoice->id) }}"
                                                        target="_blank"
                                                        class="inline-flex items-center gap-2 px-6 py-3 bg-red-600 hover:bg-red-700 text-white text-xs font-black uppercase tracking-widest rounded-2xl transition-all shadow-xl shadow-red-200 active:scale-95 group/print">
                                                        <i
                                                            class="fas fa-file-pdf text-xl group-hover/print:scale-110 transition-transform"></i>
                                                        CETAK / DOWNLOAD INVOICE (PDF)
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="overflow-x-auto">
                                                <table class="min-w-full divide-y divide-gray-100">
                                                    <thead class="bg-gray-50/30">
                                                        <tr>
                                                            <th
                                                                class="px-4 py-3 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest w-12">
                                                                No</th>
                                                            <th
                                                                class="px-4 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                                                Barang</th>
                                                            <th
                                                                class="px-4 py-3 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                                                Qty</th>
                                                            <th
                                                                class="px-4 py-3 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                                                Harga</th>
                                                            <th
                                                                class="px-4 py-3 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                                                Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-gray-50">
                                                        @foreach ($invoice->items as $index => $item)
                                                            <tr>
                                                                <td
                                                                    class="px-4 py-3 text-center text-sm font-bold text-gray-500">
                                                                    {{ $index + 1 }}
                                                                </td>
                                                                <td class="px-4 py-3">
                                                                    <div class="text-sm font-bold text-gray-900">
                                                                        {{ $item->product_name }}</div>
                                                                    @if ($item->description)
                                                                        <div class="text-[10px] text-gray-400 font-medium">
                                                                            {{ $item->description }}</div>
                                                                    @endif
                                                                </td>
                                                                <td
                                                                    class="px-4 py-3 text-right text-sm font-medium text-gray-600">
                                                                    {{ rtrim(rtrim(number_format($item->quantity, 2, ',', '.'), '0'), ',') }}
                                                                    {{ $item->unit }}
                                                                </td>
                                                                <td
                                                                    class="px-4 py-3 text-right text-sm font-medium text-gray-600">
                                                                    Rp {{ number_format($item->price, 0, ',', '.') }}
                                                                </td>
                                                                <td
                                                                    class="px-4 py-3 text-right text-sm font-black text-indigo-600">
                                                                    Rp {{ number_format($item->total, 0, ',', '.') }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-20 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="h-16 w-16 text-gray-200 mb-4" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <p class="text-gray-500 text-lg font-medium">Belum ada invoice</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-gray-50/50">
                            <tr>
                                <td colspan="4"
                                    class="px-3 py-5 text-right text-xs font-black text-gray-400 uppercase tracking-widest">
                                    Subtotal Halaman Ini</td>
                                <td class="px-3 py-5 text-left">
                                    <div class="text-sm font-black text-indigo-600">Rp
                                        {{ number_format($totalAmountPage, 2, ',', '.') }}</div>
                                </td>
                                <td></td>
                            </tr>
                            <tr class="bg-indigo-50/30">
                                <td colspan="4"
                                    class="px-3 py-5 text-right text-xs font-black text-indigo-600 uppercase tracking-widest">
                                    Total Filter</td>
                                <td class="px-3 py-5 text-left">
                                    <div class="text-base font-black text-indigo-700">Rp
                                        {{ number_format($totalAmountFiltered, 2, ',', '.') }}</div>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Mobile Card View -->
            <div id="mobile-invoices-list" class="md:hidden space-y-4">
                <div class="flex items-center gap-3 mb-4 bg-indigo-50 p-3 rounded-xl">
                    <input type="checkbox" id="select-all-mobile"
                        class="h-5 w-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="select-all-mobile" class="text-sm font-bold text-indigo-900">Pilih Semua Invoice</label>
                </div>

                @forelse($invoices as $invoice)
                    <div
                        class="invoice-card-mobile bg-white rounded-3xl p-5 shadow-lg border border-gray-100 relative overflow-hidden">
                        <!-- Selection Checkbox -->
                        <div class="absolute top-5 right-5">
                            <input type="checkbox" name="invoice_ids[]" value="{{ $invoice->id }}"
                                class="invoice-checkbox-mobile h-6 w-6 rounded-lg border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        </div>

                        <div class="flex items-start gap-4 mb-4">
                            <div
                                class="h-12 w-12 shrink-0 rounded-2xl bg-indigo-100 flex items-center justify-center text-indigo-600">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="pr-8">
                                <h3 class="text-lg font-black text-gray-900">{{ $invoice->invoice_number }}</h3>
                                <p class="text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($invoice->date)->format('d M Y') }}</p>
                            </div>
                        </div>

                        <div class="space-y-3 mb-6">
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Pelanggan</p>
                                <p class="text-base font-bold text-gray-900">{{ $invoice->customer_name }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Nominal</p>
                                <p class="text-xl font-black text-indigo-600">Rp
                                    {{ number_format($invoice->total_amount, 2, ',', '.') }}</p>
                                @if ($invoice->discount > 0)
                                    <p class="text-xs font-bold text-red-400 mt-1">Diskon: Rp
                                        {{ number_format($invoice->discount, 0, ',', '.') }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-4 gap-2">
                            <button type="button" onclick="toggleDetails('details-mobile-{{ $invoice->id }}')"
                                class="flex items-center justify-center py-3 px-3 rounded-2xl bg-indigo-50 text-indigo-700 font-bold transition-all active:scale-90">
                                <i class="fas fa-eye text-lg"></i>
                            </button>
                            <a href="{{ route('invoices.export-pdf', $invoice->id) }}" target="_blank"
                                class="flex items-center justify-center py-3 px-3 rounded-2xl bg-red-50 text-red-700 font-bold transition-all active:scale-90">
                                <i class="fas fa-print text-lg"></i>
                            </a>
                            <a href="{{ route('invoices.edit', ['invoice' => $invoice->id] + request()->query()) }}"
                                class="flex items-center justify-center py-3 px-3 rounded-2xl bg-amber-50 text-amber-700 font-bold transition-all active:scale-90">
                                <i class="fas fa-edit text-lg"></i>
                            </a>
                            <button type="button" onclick="deleteInvoice({{ $invoice->id }})"
                                class="flex items-center justify-center py-3 px-3 rounded-2xl bg-red-50 text-red-700 font-bold transition-all active:scale-90">
                                <i class="fas fa-trash-alt text-lg"></i>
                            </button>
                        </div>

                        <!-- Mobile Expandable Detail -->
                        <div id="details-mobile-{{ $invoice->id }}"
                            class="hidden mt-4 pt-4 border-t border-gray-100 space-y-3 transition-all duration-300">
                            <div class="bg-gray-50 rounded-2xl p-4">
                                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Rincian
                                    Item</h4>
                                <div class="space-y-3">
                                    @foreach ($invoice->items as $index => $item)
                                        <div class="flex justify-between items-start gap-3">
                                            <div
                                                class="flex-shrink-0 w-6 h-6 rounded-full bg-indigo-100 flex items-center justify-center">
                                                <span
                                                    class="text-[10px] font-black text-indigo-600">{{ $index + 1 }}</span>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-sm font-bold text-gray-900 leading-tight">
                                                    {{ $item->product_name }}</p>
                                                @if ($item->description)
                                                    <p class="text-[10px] text-gray-500 mt-0.5">{{ $item->description }}
                                                    </p>
                                                @endif
                                            </div>
                                            <div class="text-right whitespace-nowrap">
                                                <p class="text-sm font-black text-indigo-600">Rp
                                                    {{ number_format($item->total, 0, ',', '.') }}</p>
                                                <p class="text-[10px] text-gray-400 font-bold">
                                                    {{ rtrim(rtrim(number_format($item->quantity, 2, ',', '.'), '0'), ',') }}
                                                    {{ $item->unit }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-4 pt-3 border-t border-gray-200 flex justify-between items-center">
                                    <a href="{{ route('invoices.show', $invoice->id) }}"
                                        class="text-[10px] font-black text-indigo-600 uppercase">Detail Lengkap ↗</a>
                                    <a href="{{ route('invoices.export-pdf', $invoice->id) }}" target="_blank"
                                        class="text-[10px] font-black text-red-600 uppercase">Cetak PDF</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 bg-white rounded-3xl border-2 border-dashed border-gray-200">
                        <svg class="h-12 w-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-gray-500 font-medium">Belum ada invoice</p>
                    </div>
                @endforelse

                <!-- Loading Indicator for Infinite Scroll -->
                <div id="infinite-scroll-loader" class="py-10 transition-opacity duration-300" style="opacity: 0;">
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-10 h-10 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin">
                        </div>
                        <span class="text-xs font-black text-gray-400 uppercase tracking-widest">Memuat data...</span>
                    </div>
                </div>

                <!-- Mobile Summary -->
                @if (count($invoices) > 0)
                    <div class="bg-gray-900 text-white rounded-3xl p-6 shadow-xl space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-400">Subtotal Halaman Ini</span>
                            <span class="text-lg font-bold">Rp {{ number_format($totalAmountPage, 2, ',', '.') }}</span>
                        </div>
                        <div class="h-px bg-gray-700"></div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-indigo-300">Total Filter</span>
                            <span class="text-xl font-black text-indigo-400">Rp
                                {{ number_format($totalAmountFiltered, 2, ',', '.') }}</span>
                        </div>
                    </div>
                @endif
            </div>

            <div id="pagination-container" class="mt-8 lg:block {{ request('per_page') == 'all' ? 'hidden' : '' }}">
                @if ($invoices instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    {{ $invoices->links() }}
                @endif
            </div>
        </form>

        <form id="delete-form" action="" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>

    <script>
        // Desktop Select All
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.invoice-checkbox');

        // Mobile Select All
        const selectAllMobile = document.getElementById('select-all-mobile');
        let checkboxesMobile = document.querySelectorAll('.invoice-checkbox-mobile');

        const bulkExportBtn = document.getElementById('bulk-export-btn');
        const multiPrintBtn = document.getElementById('multi-print-btn');
        const reportDropdownBtn = document.getElementById('report-dropdown-btn');
        const telegramBtnText = document.getElementById('telegram-btn-text');
        const telegramCustomerBtnText = document.getElementById('telegram-customer-btn-text');
        const whatsappBtnText = document.getElementById('whatsapp-btn-text');
        const whapiBtnText = document.getElementById('whapi-btn-text');
        const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
        const bulkForm = document.getElementById('bulk-export-form');

        function updateExportButton() {
            // Check both desktop and mobile checkboxes
            const checkedCount = document.querySelectorAll('.invoice-checkbox:checked, .invoice-checkbox-mobile:checked')
                .length;

            bulkExportBtn.disabled = checkedCount === 0;
            multiPrintBtn.disabled = checkedCount === 0;
            reportDropdownBtn.disabled = checkedCount === 0;
            bulkDeleteBtn.disabled = checkedCount === 0;

            if (checkedCount > 0) {
                bulkExportBtn.innerHTML = `
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Laporan (${checkedCount})
                `;
                multiPrintBtn.innerHTML = `
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Print (${checkedCount})
                `;
                bulkDeleteBtn.innerHTML = `
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Hapus (${checkedCount})
                `;
                reportDropdownBtn.querySelector('span').innerText = `Kirim Laporan (${checkedCount})`;
                telegramBtnText.innerText = `Ke Group Admin (${checkedCount})`;
                telegramCustomerBtnText.innerText = `Ke Client Personal (${checkedCount})`;
                whatsappBtnText.innerText = `WhatsApp WAHA (${checkedCount})`;
                whapiBtnText.innerText = `WhatsApp Whapi Cloud (${checkedCount})`;
            } else {
                bulkExportBtn.innerHTML = `
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Buat Laporan (PDF)
                `;
                multiPrintBtn.innerHTML = `
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Cetak Invoice (PDF)
                `;
                reportDropdownBtn.querySelector('span').innerText = `Kirim Laporan`;
                telegramBtnText.innerText = `Ke Group Admin`;
                telegramCustomerBtnText.innerText = `Ke Client Personal`;
                whatsappBtnText.innerText = `WhatsApp WAHA`;
                whapiBtnText.innerText = `WhatsApp Whapi Cloud`;
                bulkDeleteBtn.innerHTML = `
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Hapus Selected
                `;
            }
        }

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateExportButton();
            });
        }

        if (selectAllMobile) {
            selectAllMobile.addEventListener('change', function() {
                document.querySelectorAll('.invoice-checkbox-mobile').forEach(cb => cb.checked = this.checked);
                updateExportButton();
            });
        }

        // Use event delegation for checkboxes since they are added dynamically
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('invoice-checkbox') || e.target.classList.contains(
                    'invoice-checkbox-mobile')) {
                updateExportButton();
            }
        });

        // INFINITE SCROLL LOGIC
        let isLoading = false;
        const mobileList = document.getElementById('mobile-invoices-list');
        const loader = document.getElementById('infinite-scroll-loader');
        const paginationContainer = document.getElementById('pagination-container');

        // Initial hide of loader visually
        loader.style.opacity = '0';

        // Wider range for mobile/tablet screens
        if (window.innerWidth < 1024) {
            if ('IntersectionObserver' in window) {
                // Hide pagination container on mobile/tablet but keep in DOM
                paginationContainer.style.display = 'none';

                const observer = new IntersectionObserver((entries) => {
                    if (entries[0].isIntersecting && !isLoading) {
                        loadMore();
                    }
                }, {
                    rootMargin: '400px', // Trigger earlier
                    threshold: 0
                });

                observer.observe(loader);
            }
        }

        async function loadMore() {
            const nextLink = paginationContainer.querySelector('a[rel="next"]');
            if (!nextLink) {
                loader.style.display = 'none';
                return;
            }

            isLoading = true;
            loader.style.opacity = '1';
            let url = nextLink.href;
            // Force HTTPS if the current page is HTTPS to avoid Mixed Content errors
            if (window.location.protocol === 'https:' && url.startsWith('http:')) {
                url = url.replace('http:', 'https:');
            }

            try {
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const html = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                // Extract mobile cards
                const newCards = doc.querySelectorAll('.invoice-card-mobile');
                newCards.forEach(card => {
                    // Check if already exists to avoid duplicates
                    const idInput = card.querySelector('input[type="checkbox"]');
                    if (idInput) {
                        const id = idInput.value;
                        if (!mobileList.querySelector(`input[value="${id}"]`)) {
                            // Append before the loader
                            mobileList.insertBefore(card, loader);
                        }
                    }
                });

                // Update pagination container with new next link
                const newPagination = doc.getElementById('pagination-container');
                if (newPagination) {
                    paginationContainer.innerHTML = newPagination.innerHTML;
                }

                // If no more pages, update loader message
                if (!paginationContainer.querySelector('a[rel="next"]')) {
                    loader.innerHTML =
                        '<p class="text-center text-gray-300 font-bold uppercase tracking-widest text-[10px] py-4">Semua data telah dimuat</p>';
                    loader.style.opacity = '1';
                } else {
                    loader.style.opacity = '0';
                }

                // Update select all state if checked
                if (selectAllMobile.checked) {
                    document.querySelectorAll('.invoice-checkbox-mobile').forEach(cb => cb.checked = true);
                }

            } catch (error) {
                console.error('Error loading more invoices:', error);
                loader.style.opacity = '0';
            } finally {
                isLoading = false;
            }
        }

        function submitBulkExport() {
            bulkForm.target = "_blank";
            bulkForm.action = "{{ route('invoices.bulk-export-pdf') }}";
            bulkForm.submit();
        }

        function submitMultiPrint() {
            bulkForm.target = "_blank";
            bulkForm.action = "{{ route('invoices.print-multi-pdf') }}";
            bulkForm.submit();
        }

        function submitTelegram() {
            Swal.fire({
                title: 'Kirim ke Group Telegram?',
                text: "Setiap invoice beserta laporan Laba Rugi akan dikirim ke Group Telegram Admin.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0ea5e9',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Kirim ke Group',
                cancelButtonText: 'Batal',
                customClass: {
                    container: 'rounded-3xl',
                    popup: 'rounded-3xl',
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Sedang Mengirim...',
                        text: 'Mengirim ke Group Admin',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });
                    bulkForm.target = "_self";
                    bulkForm.action = "{{ route('invoices.send-telegram') }}";
                    bulkForm.submit();
                }
            })
        }

        function submitTelegramCustomer() {
            Swal.fire({
                title: 'Kirim ke Pelanggan?',
                text: "Setiap invoice akan dikirim ke Telegram Pribadi pelanggan (jika Chat ID terdaftar).",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Kirim ke Pelanggan',
                cancelButtonText: 'Batal',
                customClass: {
                    container: 'rounded-3xl',
                    popup: 'rounded-3xl',
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Sedang Mengirim...',
                        text: 'Mengirim ke Telegram Client',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });
                    bulkForm.target = "_self";
                    bulkForm.action = "{{ route('invoices.send-customer') }}";
                    bulkForm.submit();
                }
            })
        }

        function submitWhatsApp() {
            Swal.fire({
                title: 'Kirim via WAHA?',
                text: "Setiap invoice akan dikirim ke nomor WhatsApp pelanggan (pastikan server WAHA aktif).",
                icon: 'success',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Kirim WhatsApp',
                cancelButtonText: 'Batal',
                customClass: {
                    container: 'rounded-3xl',
                    popup: 'rounded-3xl',
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Sedang Mengirim...',
                        text: 'Mengirim via WAHA Core',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });
                    bulkForm.target = "_self";
                    bulkForm.action = "{{ route('invoices.send-whatsapp') }}";
                    bulkForm.submit();
                }
            })
        }

        function submitWhapi() {
            Swal.fire({
                title: 'Kirim via Whapi Cloud?',
                text: "Setiap invoice akan dikirim ke WhatsApp pelanggan menggunakan Whapi.cloud API.",
                icon: 'success',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Kirim Whapi',
                cancelButtonText: 'Batal',
                customClass: {
                    container: 'rounded-3xl',
                    popup: 'rounded-3xl',
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Sedang Mengirim...',
                        text: 'Mengirim via Whapi.cloud',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });
                    bulkForm.target = "_self";
                    bulkForm.action = "{{ route('invoices.send-whapi') }}";
                    bulkForm.submit();
                }
            })
        }

        function submitBulkDelete() {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Invoice yang dipilih akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    container: 'rounded-3xl',
                    popup: 'rounded-3xl',
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    bulkForm.target = "_self";
                    bulkForm.action = "{{ route('invoices.bulk-delete') }}";
                    bulkForm.submit();
                }
            })
        }

        function deleteInvoice(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Invoice yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    container: 'rounded-3xl',
                    popup: 'rounded-3xl',
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('delete-form');
                    form.action = `/invoices/${id}`;
                    form.submit();
                }
            })
        }

        function toggleDetails(id) {
            const row = document.getElementById(id);
            const icon = document.getElementById('icon-' + id);

            if (row.classList.contains('hidden')) {
                row.classList.remove('hidden');
                if (icon) icon.style.transform = 'rotate(90deg)';
                // Fade in effect
                row.style.opacity = '0';
                setTimeout(() => {
                    row.style.opacity = '1';
                }, 10);
            } else {
                row.classList.add('hidden');
                if (icon) icon.style.transform = 'rotate(0deg)';
                row.style.opacity = '0';
            }
        }
    </script>
@endsection
