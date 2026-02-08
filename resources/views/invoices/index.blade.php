@extends('layouts.app')

@section('title', 'Daftar Invoice')

@push('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
@endpush

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-end md:justify-between mb-8 gap-6">
            <div class="min-w-0 flex-1">
                <h1 class="text-3xl md:text-5xl font-black text-gray-900 tracking-tight">Daftar Invoice</h1>
                <p class="mt-3 text-base md:text-lg text-gray-500 font-medium tracking-tight">Kelola dan pantau semua invoice
                    transaksi dalam satu dasbor terpusat.</p>
            </div>
            <div class="flex shrink-0">
                <a href="{{ route('invoices.create') }}"
                    class="group w-full md:w-auto inline-flex items-center justify-center px-8 py-4 border border-transparent text-lg font-black rounded-[2rem] text-white bg-indigo-600 shadow-2xl shadow-indigo-200 hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/50 transition-all duration-300 transform hover:-translate-y-1">
                    <i class="fas fa-plus-circle mr-3 text-xl group-hover:rotate-12 transition-transform"></i>
                    Buat Invoice Baru
                </a>
            </div>
        </div>

        <!-- Desktop & Tablet View (Side-by-Side Grid) -->
        <div class="hidden sm:grid grid-cols-1 sm:grid-cols-3 gap-6 mb-10">
            <!-- Total Invoice Card -->
            <div
                class="bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-[2.5rem] p-8 text-white relative overflow-hidden group">
                <div
                    class="absolute right-0 top-0 opacity-10 transform translate-x-4 -translate-y-4 transition-transform group-hover:scale-110">
                    <i class="fas fa-file-invoice text-[10rem]"></i>
                </div>
                <div class="relative z-10">
                    <p class="text-xs font-black text-indigo-100 uppercase tracking-[0.2em] mb-2">Total Invoice</p>
                    <h3 class="text-4xl font-black">{{ number_format($stats['total_count']) }} <span
                            class="text-lg font-medium">Data</span></h3>
                    <div
                        class="mt-4 inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 text-[10px] font-bold">
                        <i class="fas fa-database"></i> Database Real-time
                    </div>
                </div>
            </div>

            <!-- Total Omzet Card -->
            <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 relative overflow-hidden group">
                <div
                    class="hidden md:block absolute right-6 top-6 bg-emerald-100 rounded-2xl p-4 text-emerald-600 transition-transform group-hover:rotate-6">
                    <i class="fas fa-money-bill-wave text-2xl"></i>
                </div>
                <div>
                    <p class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Total Omzet</p>
                    <h3 class="text-3xl font-black text-gray-900">Rp
                        {{ number_format($stats['total_amount'], 0, ',', '.') }}</h3>
                    <p class="text-xs text-emerald-600 mt-4 font-bold flex items-center gap-1">
                        <i class="fas fa-chart-line"></i> Akumulasi Penjualan
                    </p>
                </div>
            </div>

            <!-- Today's Invoice Card -->
            <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 relative overflow-hidden group">
                <div
                    class="absolute right-6 top-6 bg-amber-100 rounded-2xl p-4 text-amber-600 transition-transform group-hover:-rotate-6">
                    <i class="fas fa-calendar-day text-2xl"></i>
                </div>
                <div>
                    <p class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Invoice Hari Ini</p>
                    <h3 class="text-3xl font-black text-gray-900">{{ number_format($stats['today_count']) }} <span
                            class="text-lg font-medium text-gray-400">Order</span></h3>
                    <p class="text-xs text-amber-600 mt-4 font-bold flex items-center gap-1">
                        <i class="fas fa-history"></i> {{ now()->format('d F Y') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Mobile View (Swiper Cards Effect) -->
        <div class="sm:hidden mb-10 px-4">
            <div class="swiper statsSwiper !overflow-visible">
                <div class="swiper-wrapper">
                    <!-- Total Invoice Card -->
                    <div class="swiper-slide !h-auto">
                        <div
                            class="h-full bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-[2.5rem] p-8 text-white relative overflow-hidden">
                            <div class="absolute right-0 top-0 opacity-10 transform translate-x-4 -translate-y-4">
                                <i class="fas fa-file-invoice text-[10rem]"></i>
                            </div>
                            <div class="relative z-10 text-left">
                                <p class="text-xs font-black text-indigo-100 uppercase tracking-[0.2em] mb-2">Total Invoice
                                </p>
                                <h3 class="text-4xl font-black">{{ number_format($stats['total_count']) }} <span
                                        class="text-lg font-medium">Data</span></h3>
                                <div
                                    class="mt-4 inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 text-[10px] font-bold">
                                    <i class="fas fa-database"></i> Database Real-time
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Omzet Card -->
                    <div class="swiper-slide !h-auto">
                        <div class="h-full bg-white rounded-[2.5rem] p-8 border border-gray-100 relative overflow-hidden">
                            <div class="text-left">
                                <p class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Total Omzet</p>
                                <h3 class="text-3xl font-black text-gray-900">Rp
                                    {{ number_format($stats['total_amount'], 0, ',', '.') }}</h3>
                                <p class="text-xs text-emerald-600 mt-4 font-bold flex items-center gap-1">
                                    <i class="fas fa-chart-line"></i> Akumulasi Penjualan
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Today's Invoice Card -->
                    <div class="swiper-slide !h-auto">
                        <div class="h-full bg-white rounded-[2.5rem] p-8 border border-gray-100 relative overflow-hidden">
                            <div class="text-left">
                                <p class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Invoice Hari Ini
                                </p>
                                <h3 class="text-3xl font-black text-gray-900">{{ number_format($stats['today_count']) }}
                                    <span class="text-lg font-medium text-gray-400">Order</span></h3>
                                <p class="text-xs text-amber-600 mt-4 font-bold flex items-center gap-1">
                                    <i class="fas fa-history"></i> {{ now()->format('d F Y') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search & Filter Card -->
        <div class="bg-white shadow-2xl rounded-[2.5rem] p-2 mb-10 border border-gray-100 relative z-10">
            <div class="p-6">
                <form id="filter-form" action="{{ route('invoices.index') }}" method="GET"
                    class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
                    <div class="md:col-span-4">
                        <label for="search"
                            class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2">Cari
                            Invoice</label>
                        <div class="relative group">
                            <i
                                class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 transition-colors group-focus-within:text-indigo-500"></i>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                placeholder="Nomor invoice atau pelanggan..."
                                class="block w-full rounded-[1.5rem] border-gray-100 bg-gray-50/50 py-4 pl-14 pr-5 text-sm font-medium text-gray-900 shadow-inner focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                        </div>
                    </div>
                    <div class="md:col-span-3">
                        <label for="customer"
                            class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2">Pilih
                            Pelanggan</label>
                        <div class="relative">
                            <select name="customer" id="customer"
                                class="block w-full rounded-[1.5rem] border-gray-100 bg-gray-50/50 py-4 px-5 text-sm font-medium text-gray-900 shadow-inner focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all appearance-none cursor-pointer">
                                <option value="">Semua Pelanggan</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer }}"
                                        {{ request('customer') == $customer ? 'selected' : '' }}>{{ $customer }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                <i class="fas fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                    </div>
                    <div class="md:col-span-3">
                        <label for="date"
                            class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2">Pilih
                            Tanggal</label>
                        <input type="date" name="date" id="date" value="{{ request('date') }}"
                            class="block w-full rounded-[1.5rem] border-gray-100 bg-gray-50/50 py-4 px-5 text-sm font-medium text-gray-900 shadow-inner focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                    </div>
                    <div class="md:col-span-2 flex gap-2">
                        <button type="submit"
                            class="flex-1 flex items-center justify-center py-4 border border-transparent rounded-[1.5rem] shadow-xl text-sm font-black text-white bg-gray-900 hover:bg-black focus:outline-none focus:ring-4 focus:ring-gray-300 transition-all transform active:scale-95">
                            <i class="fas fa-filter mr-2"></i> Terapkan
                        </button>
                        <a href="{{ route('invoices.index') }}"
                            class="flex items-center justify-center py-4 px-4 rounded-[1.5rem] bg-gray-100 text-gray-400 hover:text-gray-900 transition-all">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </form>

                <!-- Bulk Actions Toolbar -->
                <div class="mt-8 pt-6 border-t border-gray-50 flex flex-wrap gap-3">
                    <button type="button" onclick="submitBulkExport()" id="bulk-export-btn" disabled
                        class="inline-flex items-center px-6 py-3 rounded-2xl bg-rose-50 text-rose-700 text-xs font-black uppercase tracking-widest hover:bg-rose-100 disabled:opacity-30 disabled:cursor-not-allowed transition-all border border-rose-100 shadow-sm">
                        <i class="fas fa-file-pdf mr-2 text-sm"></i> Laporan
                    </button>
                    <button type="button" onclick="submitMultiPrint()" id="multi-print-btn" disabled
                        class="inline-flex items-center px-6 py-3 rounded-2xl bg-indigo-50 text-indigo-700 text-xs font-black uppercase tracking-widest hover:bg-indigo-100 disabled:opacity-30 disabled:cursor-not-allowed transition-all border border-indigo-100 shadow-sm">
                        <i class="fas fa-print mr-2 text-sm"></i> Cetak Massal
                    </button>

                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open" @click.away="open = false" id="report-dropdown-btn"
                            disabled
                            class="inline-flex items-center px-6 py-3 rounded-2xl bg-emerald-50 text-emerald-700 text-xs font-black uppercase tracking-widest hover:bg-emerald-100 disabled:opacity-30 disabled:cursor-not-allowed transition-all border border-emerald-100 shadow-sm">
                            <i class="fas fa-paper-plane mr-2 text-sm"></i> <span>Kirim Laporan</span>
                            <i class="fas fa-chevron-down ml-2 text-[10px] transform transition-transform"
                                :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-cloak
                            class="absolute left-0 mt-2 w-64 rounded-3xl shadow-2xl bg-white border border-gray-100 z-50 overflow-hidden py-2"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0">
                            <div
                                class="px-4 py-2 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50 mb-1 text-center">
                                Pilih Platform</div>
                            <button type="button" onclick="submitTelegram()"
                                class="w-full flex items-center gap-3 px-4 py-3 text-xs font-black text-gray-700 hover:bg-sky-50 transition-colors">
                                <i class="fab fa-telegram text-sky-500 text-lg"></i> <span id="telegram-btn-text">Telegram
                                    Admin</span>
                            </button>
                            <button type="button" onclick="submitTelegramCustomer()"
                                class="w-full flex items-center gap-3 px-4 py-3 text-xs font-black text-gray-700 hover:bg-indigo-50 transition-colors">
                                <i class="fas fa-user-circle text-indigo-500 text-lg"></i> <span
                                    id="telegram-customer-btn-text">Telegram Personal</span>
                            </button>
                            <div class="h-px bg-gray-50 my-1"></div>
                            <button type="button" onclick="submitWhatsApp()"
                                class="w-full flex items-center gap-3 px-4 py-3 text-xs font-black text-gray-700 hover:bg-green-50 transition-colors">
                                <i class="fab fa-whatsapp text-green-500 text-lg"></i> <span
                                    id="whatsapp-btn-text">WhatsApp WAHA</span>
                            </button>
                            <button type="button" onclick="submitWhapi()"
                                class="w-full flex items-center gap-3 px-4 py-3 text-xs font-black text-gray-700 hover:bg-green-50 transition-colors">
                                <i class="fab fa-whatsapp-square text-green-600 text-lg"></i> <span
                                    id="whapi-btn-text">Whapi Cloud</span>
                            </button>
                        </div>
                    </div>

                    <button type="button" onclick="submitBulkDelete()" id="bulk-delete-btn" disabled
                        class="inline-flex items-center px-6 py-3 rounded-2xl bg-gray-900 text-white text-xs font-black uppercase tracking-widest hover:bg-black disabled:opacity-30 disabled:cursor-not-allowed transition-all shadow-sm">
                        <i class="fas fa-trash-alt mr-2 text-sm"></i> Hapus terpilih
                    </button>
                </div>
            </div>
        </div>

        <!-- Sort & Pagination Controls -->
        <div
            class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4 bg-white p-4 rounded-3xl shadow-sm border border-gray-50">
            <div class="flex items-center gap-3 w-full md:w-auto">
                <span class="text-xs font-black text-gray-400 uppercase tracking-widest whitespace-nowrap">Show:</span>
                <select name="per_page" onchange="document.getElementById('filter-form').submit()" form="filter-form"
                    class="w-full md:w-auto rounded-xl border-gray-200 text-sm font-bold focus:ring-indigo-500 focus:border-indigo-500 py-2 pl-3 pr-8 shadow-sm">
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    <option value="all" {{ request('per_page') == 'all' ? 'selected' : '' }}>All</option>
                </select>
            </div>

            <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto">
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <select name="sort_by" onchange="document.getElementById('filter-form').submit()" form="filter-form"
                        class="w-full sm:auto rounded-xl border-gray-200 text-sm font-bold focus:ring-indigo-500 focus:border-indigo-500 py-2 pl-3 pr-8 shadow-sm">
                        <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Terbaru
                        </option>
                        <option value="date" {{ request('sort_by') == 'date' ? 'selected' : '' }}>Tanggal</option>
                        <option value="invoice_number" {{ request('sort_by') == 'invoice_number' ? 'selected' : '' }}>No.
                            Invoice</option>
                        <option value="customer_name" {{ request('sort_by') == 'customer_name' ? 'selected' : '' }}>Nama
                            Pelanggan</option>
                        <option value="total_amount" {{ request('sort_by') == 'total_amount' ? 'selected' : '' }}>Nominal
                        </option>
                    </select>
                </div>
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <select name="sort_order" onchange="document.getElementById('filter-form').submit()"
                        form="filter-form"
                        class="w-full sm:w-auto rounded-xl border-gray-200 text-sm font-bold focus:ring-indigo-500 focus:border-indigo-500 py-2 pl-3 pr-8 shadow-sm">
                        <option value="desc" {{ request('sort_order', 'desc') == 'desc' ? 'selected' : '' }}>Menurun
                        </option>
                        <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Menaik</option>
                    </select>
                </div>
            </div>
        </div>

        <form id="bulk-export-form" action="{{ route('invoices.bulk-export-pdf') }}" method="POST" target="_blank">
            @csrf

            <!-- Desktop View: High Fidelity Table -->
            <div class="hidden lg:block bg-white shadow-2xl rounded-[3rem] overflow-hidden border border-gray-100 mb-10">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-50">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th scope="col" class="py-6 pl-8 pr-4 text-left w-10">
                                    <input type="checkbox" id="select-all"
                                        class="h-6 w-6 rounded-lg border-gray-300 text-indigo-600 focus:ring-indigo-500 transition-all cursor-pointer">
                                </th>
                                <th scope="col"
                                    class="px-4 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">
                                    Dokumen</th>
                                <th scope="col"
                                    class="px-4 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">
                                    Pelanggan</th>
                                <th scope="col"
                                    class="px-4 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">
                                    Tipe</th>
                                <th scope="col"
                                    class="px-4 py-6 text-right text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">
                                    Total Tagihan</th>
                                <th scope="col"
                                    class="px-4 py-6 text-center text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">
                                    Status</th>
                                <th scope="col"
                                    class="py-6 pl-4 pr-8 text-right text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">
                                    Manajemen</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 bg-white">
                            @forelse($invoices as $invoice)
                                <tr class="hover:bg-indigo-50/30 transition-all duration-200 group cursor-pointer"
                                    onclick="toggleDetails('details-{{ $invoice->id }}')">
                                    <td class="py-6 pl-8 pr-4" onclick="event.stopPropagation()">
                                        <input type="checkbox" name="invoice_ids[]" value="{{ $invoice->id }}"
                                            class="invoice-checkbox h-6 w-6 rounded-lg border-gray-300 text-indigo-600 focus:ring-indigo-500 transition-all cursor-pointer">
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-6">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="h-14 w-14 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300 shadow-sm border border-indigo-100">
                                                <i class="fas fa-file-invoice text-xl"
                                                    id="icon-details-{{ $invoice->id }}"></i>
                                            </div>
                                            <div>
                                                <div
                                                    class="text-sm font-black text-gray-900 leading-none mb-1 group-hover:text-indigo-600 transition-colors uppercase tracking-tight flex items-center gap-2">
                                                    {{ $invoice->invoice_number }}
                                                    <i class="fas fa-chevron-right text-[10px] text-gray-300 transition-transform duration-300"
                                                        id="chevron-details-{{ $invoice->id }}"></i>
                                                </div>
                                                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                                    <i class="far fa-calendar-alt mr-1"></i>
                                                    {{ \Carbon\Carbon::parse($invoice->date)->format('d M Y') }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-6">
                                        <div class="text-sm font-black text-gray-900 leading-tight">
                                            {{ $invoice->customer_name }}</div>
                                        <div
                                            class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1 italic">
                                            Client Partner</div>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-6">
                                        @php
                                            $type = 'LAINNYA';
                                            $badgeClass = 'bg-gray-100 text-gray-700';
                                            if (str_contains($invoice->invoice_number, 'BSH')) {
                                                $type = 'BASAHAN';
                                                $badgeClass = 'bg-indigo-100 text-indigo-700';
                                            } elseif (str_contains($invoice->invoice_number, 'KRBSBM')) {
                                                $type = 'BUMIL';
                                                $badgeClass = 'bg-rose-100 text-rose-700';
                                            } elseif (str_contains($invoice->invoice_number, 'KR')) {
                                                $type = 'KERINGAN';
                                                $badgeClass = 'bg-amber-100 text-amber-700';
                                            } elseif (str_contains($invoice->invoice_number, 'OPR')) {
                                                $type = 'OPERASIONAL';
                                                $badgeClass = 'bg-emerald-100 text-emerald-700';
                                            }
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-4 py-1.5 rounded-full text-[10px] font-black {{ $badgeClass }} uppercase tracking-widest border border-current border-opacity-10">
                                            {{ $type }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-6 text-right">
                                        <div class="text-sm font-black text-gray-900 italic tracking-tight">Rp
                                            {{ number_format($invoice->total_amount, 0, ',', '.') }}</div>
                                        @if ($invoice->discount > 0)
                                            <div
                                                class="text-[10px] font-bold text-rose-500 mt-1 uppercase tracking-widest">
                                                Disc: Rp {{ number_format($invoice->discount, 0, ',', '.') }}</div>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-6 text-center">
                                        <div class="flex justify-center gap-1.5">
                                            @if ($invoice->whatsapp_sent_at)
                                                <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-sm shadow-emerald-200"
                                                    title="WA Terkirim"></div>
                                            @endif
                                            @if ($invoice->telegram_sent_at)
                                                <div class="w-2.5 h-2.5 rounded-full bg-sky-500 shadow-sm shadow-sky-200"
                                                    title="Telegram Terkirim"></div>
                                            @endif
                                            @if (!$invoice->whatsapp_sent_at && !$invoice->telegram_sent_at)
                                                <div class="w-2.5 h-2.5 rounded-full bg-gray-200" title="Belum Dikirim">
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap py-6 pl-4 pr-8 text-right text-sm font-medium"
                                        onclick="event.stopPropagation()">
                                        <div class="flex justify-end gap-2 transition-all duration-300">
                                            <a href="{{ route('invoices.show', $invoice->id) }}"
                                                class="h-12 w-12 flex items-center justify-center rounded-2xl bg-gray-900 text-white hover:bg-black transition-all shadow-xl shadow-gray-200 active:scale-95">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('invoices.export-pdf', $invoice->id) }}" target="_blank"
                                                class="h-12 w-12 flex items-center justify-center rounded-2xl bg-rose-600 text-white hover:bg-rose-700 transition-all shadow-xl shadow-rose-200 active:scale-95">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                            <a href="{{ route('invoices.edit', ['invoice' => $invoice->id] + request()->query()) }}"
                                                class="h-12 w-12 flex items-center justify-center rounded-2xl bg-indigo-600 text-white hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-200 active:scale-95">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" onclick="deleteInvoice({{ $invoice->id }})"
                                                class="h-12 w-12 flex items-center justify-center rounded-2xl bg-white border border-gray-100 text-gray-400 hover:text-rose-600 hover:border-rose-100 transition-all shadow-sm active:scale-95">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Desktop Details Row -->
                                <tr id="details-{{ $invoice->id }}" class="hidden bg-gray-50/50">
                                    <td colspan="7" class="p-8">
                                        <div
                                            class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden">
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 p-8 border-b border-gray-50">
                                                <div class="col-span-2">
                                                    <h4
                                                        class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">
                                                        Rincian Item Invoice</h4>
                                                    <div class="overflow-hidden rounded-2xl border border-gray-50">
                                                        <table class="min-w-full divide-y divide-gray-50">
                                                            <thead class="bg-gray-50/30">
                                                                <tr>
                                                                    <th
                                                                        class="px-4 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                                                        No</th>
                                                                    <th
                                                                        class="px-4 py-3 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                                                        Produk</th>
                                                                    <th
                                                                        class="px-4 py-3 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
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
                                                                @foreach ($invoice->items as $item)
                                                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                                                        <td
                                                                            class="px-4 py-3 text-start text-xs font-bold text-gray-600">
                                                                            {{ $loop->iteration }}
                                                                        </td>
                                                                        <td class="px-4 py-3">
                                                                            <div class="text-xs font-bold text-gray-900">
                                                                                {{ $item->product_name }}</div>
                                                                            @if ($item->description)
                                                                                <div
                                                                                    class="text-[10px] text-gray-400 italic mt-1">
                                                                                    {{ $item->description }}</div>
                                                                            @endif
                                                                        </td>
                                                                        <td
                                                                            class="px-4 py-3 text-center text-xs font-bold text-gray-600">
                                                                            {{ rtrim(rtrim(number_format($item->quantity, 2, ',', '.'), '0'), ',') }}
                                                                            {{ $item->unit }}
                                                                        </td>
                                                                        <td
                                                                            class="px-4 py-3 text-right text-xs font-bold text-gray-600">
                                                                            Rp
                                                                            {{ number_format($item->price, 0, ',', '.') }}
                                                                        </td>
                                                                        <td
                                                                            class="px-4 py-3 text-right text-xs font-black text-indigo-600 italic">
                                                                            Rp
                                                                            {{ number_format($item->total, 0, ',', '.') }}
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="space-y-6">
                                                    <div>
                                                        <h4
                                                            class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">
                                                            Ringkasan Biaya</h4>
                                                        <div class="bg-indigo-50/50 rounded-2xl p-6 space-y-3">
                                                            <div
                                                                class="flex justify-between items-center text-xs font-bold text-gray-500">
                                                                <span>Subtotal</span>
                                                                @php
                                                                    $subtotal = $invoice->items->sum('total');
                                                                @endphp
                                                                <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                                                            </div>
                                                            <div
                                                                class="flex justify-between items-center text-xs font-bold text-rose-500">
                                                                <span>Potongan (Diskon)</span>
                                                                <span>- Rp
                                                                    {{ number_format($invoice->discount, 0, ',', '.') }}</span>
                                                            </div>
                                                            <div
                                                                class="pt-3 border-t border-indigo-100 flex justify-between items-center">
                                                                <span
                                                                    class="text-xs font-black text-gray-900 uppercase">Total
                                                                    Tagihan</span>
                                                                <span class="text-xl font-black text-indigo-600 italic">Rp
                                                                    {{ number_format($invoice->total_amount, 0, ',', '.') }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <h4
                                                            class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">
                                                            Shortcut</h4>
                                                        <div class="grid grid-cols-2 gap-2">
                                                            <a href="{{ route('invoices.export-pdf', $invoice->id) }}"
                                                                target="_blank"
                                                                class="flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-rose-50 text-rose-700 text-[10px] font-black uppercase tracking-widest hover:bg-rose-100 transition-all shadow-sm">
                                                                <i class="fas fa-print"></i> PDF
                                                            </a>
                                                            <a href="{{ route('invoices.edit', $invoice->id) }}"
                                                                class="flex items-center justify-center gap-2 py-3 px-4 rounded-xl bg-indigo-50 text-indigo-700 text-[10px] font-black uppercase tracking-widest hover:bg-indigo-100 transition-all shadow-sm">
                                                                <i class="fas fa-edit"></i> Edit
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="px-8 py-4 bg-gray-50/50 flex justify-between items-center">
                                                <div
                                                    class="flex items-center gap-2 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                                    <i class="fas fa-info-circle text-indigo-400"></i>
                                                    Klik kembali untuk menutup rincian
                                                </div>
                                                <a href="{{ route('invoices.show', $invoice->id) }}"
                                                    class="text-[10px] font-black text-indigo-600 uppercase tracking-widest hover:underline flex items-center gap-1">
                                                    Detail Penuh <i class="fas fa-arrow-right"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-8 py-20 text-center">
                                        <div class="flex flex-col items-center">
                                            <div
                                                class="h-24 w-24 bg-gray-50 rounded-[2rem] flex items-center justify-center text-gray-200 mb-6 tracking-tighter">
                                                <i class="fas fa-folder-open text-5xl"></i>
                                            </div>
                                            <h3 class="text-xl font-black text-gray-900 mb-2">Tidak Ada Invoice</h3>
                                            <p class="text-gray-500 font-medium max-w-xs mx-auto">Mulailah dengan membuat
                                                invoice baru untuk melihat data transaksi di sini.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Desktop Summary Footer -->
                @if (count($invoices) > 0)
                    <div class="bg-gray-50/50 p-8 border-t border-gray-100">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                            <div class="text-gray-400 text-xs font-black uppercase tracking-[0.2em]">Data Analytics
                                Ringkasan</div>
                            <div class="flex flex-col md:flex-row justify-end gap-6 md:gap-12">
                                <div class="flex flex-col items-end">
                                    <span
                                        class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Subtotal
                                        Halaman</span>
                                    <span class="text-xl font-black text-gray-900 tracking-tight">Rp
                                        {{ number_format($stats['total_amount_page'], 0, ',', '.') }}</span>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span
                                        class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-1">Total
                                        Filter</span>
                                    <span class="text-2xl font-black text-indigo-600 tracking-tight">Rp
                                        {{ number_format($stats['total_amount_filtered'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Mobile View: Modern Cards with Infinite Scroll -->
            <div id="mobile-invoices-list" class="lg:hidden space-y-6 mb-10">
                <div
                    class="bg-indigo-50/50 rounded-3xl p-5 flex items-center justify-between border border-indigo-100/50 mb-2">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" id="select-all-mobile"
                            class="h-6 w-6 rounded-lg border-gray-300 text-indigo-600 focus:ring-indigo-500 transition-all cursor-pointer">
                        <span class="text-sm font-black text-indigo-900 uppercase tracking-widest">Pilih Semua</span>
                    </div>
                </div>

                @forelse($invoices as $invoice)
                    <div
                        class="invoice-card-mobile bg-white rounded-[2.5rem] p-6 shadow-xl border border-gray-100 relative group overflow-hidden transition-all active:scale-[0.98]">
                        <!-- Selection Checkbox -->
                        <div class="absolute top-6 right-6 z-10">
                            <input type="checkbox" name="invoice_ids[]" value="{{ $invoice->id }}"
                                class="invoice-checkbox-mobile h-6 w-6 rounded-lg border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                        </div>

                        <div class="flex items-start gap-4 mb-6">
                            <div
                                class="h-14 w-14 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 border border-indigo-100">
                                <i class="fas fa-file-invoice text-xl"></i>
                            </div>
                            <div class="pr-10">
                                <h3 class="text-xl font-black text-gray-900 leading-tight mb-1">
                                    {{ $invoice->invoice_number }}</h3>
                                <div
                                    class="inline-flex items-center gap-2 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    <i class="far fa-calendar-alt"></i>
                                    {{ \Carbon\Carbon::parse($invoice->date)->format('d M Y') }}
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-3xl p-5 mb-6 space-y-4">
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Pelanggan
                                </p>
                                <p class="text-base font-black text-gray-900 leading-tight">{{ $invoice->customer_name }}
                                </p>
                            </div>
                            <div class="pt-4 border-t border-gray-100 flex justify-between items-end">
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Total
                                        Tagihan</p>
                                    <p class="text-2xl font-black text-indigo-600 italic">Rp
                                        {{ number_format($invoice->total_amount, 0, ',', '.') }}</p>
                                </div>
                                <div class="flex gap-1.5 pb-1">
                                    @if ($invoice->whatsapp_sent_at)
                                        <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-sm shadow-emerald-200">
                                        </div>
                                    @endif
                                    @if ($invoice->telegram_sent_at)
                                        <div class="w-2.5 h-2.5 rounded-full bg-sky-500 shadow-sm shadow-sky-200"></div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-5 gap-2">
                            <button type="button" onclick="toggleDetails('details-mobile-{{ $invoice->id }}')"
                                class="h-12 flex items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition-all shadow-sm active:scale-95">
                                <i class="fas fa-list-ul"></i>
                            </button>
                            <a href="{{ route('invoices.show', $invoice->id) }}"
                                class="h-12 flex items-center justify-center rounded-2xl bg-gray-900 text-white hover:bg-black transition-all shadow-lg active:scale-95">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('invoices.export-pdf', $invoice->id) }}" target="_blank"
                                class="h-12 flex items-center justify-center rounded-2xl bg-rose-600 text-white hover:bg-rose-700 transition-all shadow-lg active:scale-95">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                            <a href="{{ route('invoices.edit', ['invoice' => $invoice->id] + request()->query()) }}"
                                class="h-12 flex items-center justify-center rounded-2xl bg-indigo-600 text-white hover:bg-indigo-700 transition-all shadow-lg active:scale-95">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" onclick="deleteInvoice({{ $invoice->id }})"
                                class="h-12 flex items-center justify-center rounded-2xl bg-white border border-gray-100 text-gray-400 hover:text-rose-600 hover:border-rose-100 transition-all shadow-sm active:scale-95">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>

                        <!-- Mobile Details Section -->
                        <div id="details-mobile-{{ $invoice->id }}"
                            class="hidden mt-6 pt-6 border-t border-gray-100 space-y-4">
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Rincian Item</h4>
                            <div class="space-y-3">
                                @foreach ($invoice->items as $item)
                                    <div class="bg-gray-50 rounded-2xl p-4">
                                        <div class="flex justify-between items-start gap-3">
                                            <div class="flex-1">
                                                <p class="text-sm font-black text-gray-900">{{ $item->product_name }}</p>
                                                @if ($item->description)
                                                    <p class="text-[10px] text-gray-500 mt-1">{{ $item->description }}</p>
                                                @endif
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-black text-indigo-600">Rp
                                                    {{ number_format($item->total, 0, ',', '.') }}</p>
                                                <p class="text-[10px] font-bold text-gray-400">
                                                    {{ rtrim(rtrim(number_format($item->quantity, 2, ',', '.'), '0'), ',') }}
                                                    {{ $item->unit }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if ($invoice->discount > 0)
                                <div class="flex justify-between items-center px-4 py-3 bg-rose-50 rounded-2xl">
                                    <span
                                        class="text-[10px] font-black text-rose-700 uppercase tracking-widest">Diskon</span>
                                    <span class="text-sm font-black text-rose-700">- Rp
                                        {{ number_format($invoice->discount, 0, ',', '.') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-[3rem] p-16 text-center border border-gray-100 shadow-xl">
                        <div
                            class="h-24 w-24 bg-gray-50 rounded-[2rem] flex items-center justify-center text-gray-200 mx-auto mb-6">
                            <i class="fas fa-inbox text-4xl"></i>
                        </div>
                        <h3 class="text-xl font-black text-gray-900">Belum ada invoice</h3>
                        <p class="text-sm text-gray-500 font-medium mt-2 max-w-[200px] mx-auto leading-relaxed">Gunakan
                            filter atau buat baru untuk mulai melihat data.</p>
                    </div>
                @endforelse

                <!-- Loading Indicator for Infinite Scroll -->
                <div id="infinite-scroll-loader" class="py-12 transition-opacity duration-300" style="opacity: 0;">
                    <div class="flex flex-col items-center gap-4">
                        <div
                            class="w-12 h-12 border-4 border-indigo-100 border-t-indigo-600 rounded-full animate-spin shadow-sm">
                        </div>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">Memasok data
                            baru...</span>
                    </div>
                </div>
            </div>

            <!-- Mobile Summary Bottom -->
            @if (count($invoices) > 0)
                <div
                    class="lg:hidden bg-gray-900 text-white rounded-[2.5rem] p-8 shadow-2xl space-y-6 mb-10 overflow-hidden relative">
                    <div class="absolute right-0 top-0 opacity-10 transform translate-x-4 -translate-y-4">
                        <i class="fas fa-chart-pie text-8xl"></i>
                    </div>
                    <div class="relative z-10">
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-black text-gray-400 uppercase tracking-widest">Subtotal
                                Halaman</span>
                            <span class="text-xl font-black tracking-tight italic">Rp
                                {{ number_format($stats['total_amount_page'], 0, ',', '.') }}</span>
                        </div>
                        <div class="h-px bg-white/10 my-6"></div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-black text-indigo-400 uppercase tracking-widest">Total Filter</span>
                            <span class="text-3xl font-black text-indigo-400 tracking-tighter italic">Rp
                                {{ number_format($stats['total_amount_filtered'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <div id="pagination-container" class="mt-8 lg:block {{ request('per_page') == 'all' ? 'hidden' : '' }}">
                @if ($invoices instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="bg-white p-4 rounded-3xl shadow-sm border border-gray-50">
                        {{ $invoices->links() }}
                    </div>
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
            const checkedCount = document.querySelectorAll('.invoice-checkbox:checked, .invoice-checkbox-mobile:checked')
                .length;

            bulkExportBtn.disabled = checkedCount === 0;
            multiPrintBtn.disabled = checkedCount === 0;
            reportDropdownBtn.disabled = checkedCount === 0;
            bulkDeleteBtn.disabled = checkedCount === 0;

            if (checkedCount > 0) {
                bulkExportBtn.innerHTML = `<i class="fas fa-file-pdf mr-2"></i> Laporan (${checkedCount})`;
                multiPrintBtn.innerHTML = `<i class="fas fa-print mr-2"></i> Cetak (${checkedCount})`;
                bulkDeleteBtn.innerHTML = `<i class="fas fa-trash-alt mr-2"></i> Hapus (${checkedCount})`;
                reportDropdownBtn.querySelector('span').innerText = `Kirim Laporan (${checkedCount})`;
                telegramBtnText.innerText = `Telegram Admin (${checkedCount})`;
                telegramCustomerBtnText.innerText = `Telegram Personal (${checkedCount})`;
                whatsappBtnText.innerText = `WhatsApp WAHA (${checkedCount})`;
                whapiBtnText.innerText = `Whapi Cloud (${checkedCount})`;
            } else {
                bulkExportBtn.innerHTML = `<i class="fas fa-file-pdf mr-2"></i> Laporan`;
                multiPrintBtn.innerHTML = `<i class="fas fa-print mr-2"></i> Cetak Massal`;
                bulkDeleteBtn.innerHTML = `<i class="fas fa-trash-alt mr-2"></i> Hapus terpilih`;
                reportDropdownBtn.querySelector('span').innerText = `Kirim Laporan`;
                telegramBtnText.innerText = `Telegram Admin`;
                telegramCustomerBtnText.innerText = `Telegram Personal`;
                whatsappBtnText.innerText = `WhatsApp WAHA`;
                whapiBtnText.innerText = `Whapi Cloud`;
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

        if (window.innerWidth < 1024 && mobileList && loader && paginationContainer) {
            if ('IntersectionObserver' in window) {
                paginationContainer.style.display = 'none';
                const observer = new IntersectionObserver((entries) => {
                    if (entries[0].isIntersecting && !isLoading) {
                        loadMore();
                    }
                }, {
                    rootMargin: '400px',
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

                const newCards = doc.querySelectorAll('.invoice-card-mobile');
                newCards.forEach(card => {
                    const idInput = card.querySelector('input[type="checkbox"]');
                    if (idInput) {
                        const id = idInput.value;
                        if (!mobileList.querySelector(`input[value="${id}"]`)) {
                            mobileList.insertBefore(card, loader);
                        }
                    }
                });

                const newPagination = doc.getElementById('pagination-container');
                if (newPagination) paginationContainer.innerHTML = newPagination.innerHTML;

                if (!paginationContainer.querySelector('a[rel="next"]')) {
                    loader.innerHTML =
                        '<p class="text-center text-gray-300 font-bold uppercase tracking-[0.2em] py-8">Tuntas. Semua invoice telah dimuat.</p>';
                } else {
                    loader.style.opacity = '0';
                }

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
                title: 'Telegram Admin?',
                text: "Kirim invoice dan laporan Laba Rugi ke Group Telegram Admin.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Kirim Sekarang',
                cancelButtonText: 'Batalkan',
                customClass: {
                    popup: 'rounded-[2rem]',
                    container: 'backdrop-blur-sm'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Proses...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                    bulkForm.target = "_self";
                    bulkForm.action = "{{ route('invoices.send-telegram') }}";
                    bulkForm.submit();
                }
            })
        }

        function submitTelegramCustomer() {
            Swal.fire({
                title: 'Telegram Personal?',
                text: "Kirim invoice ke Telegram Pribadi pelanggan masing-masing.",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Kirim Sekarang',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-[2rem]'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Mengirim...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                    bulkForm.target = "_self";
                    bulkForm.action = "{{ route('invoices.send-customer') }}";
                    bulkForm.submit();
                }
            })
        }

        function submitWhatsApp() {
            Swal.fire({
                title: 'WhatsApp (WAHA)?',
                text: "Kirim invoice ke nomor WhatsApp pelanggan via server WAHA.",
                icon: 'success',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Kirim WhatsApp',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-[2rem]'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Antrean WA...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                    bulkForm.target = "_self";
                    bulkForm.action = "{{ route('invoices.send-whatsapp') }}";
                    bulkForm.submit();
                }
            })
        }

        function submitWhapi() {
            Swal.fire({
                title: 'Whapi Cloud?',
                text: "Kirim invoice via Whapi.cloud API ke pelanggan.",
                icon: 'success',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Kirim via Whapi',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-[2rem]'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Sedang Proses...',
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });
                    bulkForm.target = "_self";
                    bulkForm.action = "{{ route('invoices.send-whapi') }}";
                    bulkForm.submit();
                }
            })
        }

        function submitBulkDelete() {
            Swal.fire({
                title: 'Hapus Massal?',
                text: "Semua invoice terpilih akan dihapus permanen dari sistem!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Hapus Data',
                cancelButtonText: 'Jangan Hapus',
                customClass: {
                    popup: 'rounded-[2rem]'
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
                title: 'Hapus Invoice?',
                text: "Data invoice ini akan hilang secara permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4f46e5',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Hapus Permanen',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-[2rem]'
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
            const chevron = document.getElementById('chevron-' + id);

            if (row.classList.contains('hidden')) {
                row.classList.remove('hidden');
                if (icon) icon.classList.replace('fa-file-invoice', 'fa-folder-open');
                if (chevron) chevron.style.transform = 'rotate(90deg)';
                row.style.opacity = '0';
                setTimeout(() => {
                    row.style.opacity = '1';
                }, 10);
            } else {
                row.classList.add('hidden');
                if (icon) icon.classList.replace('fa-folder-open', 'fa-file-invoice');
                if (chevron) chevron.style.transform = 'rotate(0deg)';
            }
        }

        // Initialize Swiper for Stats on Mobile (Phone only)
        document.addEventListener('DOMContentLoaded', function() {
            if (window.innerWidth < 640) {
                const swiper = new Swiper('.statsSwiper', {
                    effect: 'cards',
                    cardsEffect: {
                        slideShadows: false,
                        rotate: true,
                        perSlideRotate: 2,
                        perSlideOffset: 8,
                    },
                    grabCursor: true,
                    loop: true,
                    autoplay: {
                        delay: 3000,
                        disableOnInteraction: false,
                    },
                });
            }
        });
    </script>
@endsection
