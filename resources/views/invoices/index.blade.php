@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-10">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
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
            class="bg-white shadow-lg rounded-3xl p-5 mb-8 border border-gray-100">
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
                    <select name="sort_order" onchange="document.getElementById('filter-form').submit()" form="filter-form"
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
                                    <td class="whitespace-nowrap px-3 py-5">
                                        <div class="flex items-center">
                                            <div
                                                class="h-10 w-10 flex-shrink-0 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600">
                                                <svg class="h-6 w-6" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-gray-900">
                                                    {{ $invoice->invoice_number }}</div>
                                                <div class="text-xs text-gray-400">ID: #{{ $invoice->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-5 text-sm text-gray-600 font-medium">
                                        {{ \Carbon\Carbon::parse($invoice->date)->format('d M Y') }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-5">
                                        <div class="text-sm font-bold text-gray-900">{{ $invoice->customer_name }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-5">
                                        <div class="text-sm font-black text-indigo-600">Rp
                                            {{ number_format($invoice->total_amount, 2, ',', '.') }}</div>
                                    </td>
                                    <td
                                        class="relative whitespace-nowrap py-5 pl-3 pr-6 text-right text-sm font-bold space-x-2">
                                        <a href="{{ route('invoices.show', $invoice->id) }}"
                                            class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1.5 rounded-lg transition-colors inline-block">Lihat</a>
                                        <a href="{{ route('invoices.edit', $invoice->id) }}"
                                            class="text-amber-600 hover:text-amber-900 bg-amber-50 px-3 py-1.5 rounded-lg transition-colors inline-block">Edit</a>
                                        <button type="button" onclick="deleteInvoice({{ $invoice->id }})"
                                            class="text-red-600 hover:text-red-900 bg-red-50 px-3 py-1.5 rounded-lg transition-colors inline-block">Hapus</button>
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
            <div class="md:hidden space-y-4">
                <div class="flex items-center gap-3 mb-4 bg-indigo-50 p-3 rounded-xl">
                    <input type="checkbox" id="select-all-mobile"
                        class="h-5 w-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="select-all-mobile" class="text-sm font-bold text-indigo-900">Pilih Semua Invoice</label>
                </div>

                @forelse($invoices as $invoice)
                    <div class="bg-white rounded-3xl p-5 shadow-lg border border-gray-100 relative overflow-hidden">
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
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-2">
                            <a href="{{ route('invoices.show', $invoice->id) }}"
                                class="flex items-center justify-center py-2 px-3 rounded-xl bg-indigo-50 text-indigo-700 font-bold text-sm">
                                Detail
                            </a>
                            <a href="{{ route('invoices.edit', $invoice->id) }}"
                                class="flex items-center justify-center py-2 px-3 rounded-xl bg-amber-50 text-amber-700 font-bold text-sm">
                                Edit
                            </a>
                            <button type="button" onclick="deleteInvoice({{ $invoice->id }})"
                                class="flex items-center justify-center py-2 px-3 rounded-xl bg-red-50 text-red-700 font-bold text-sm">
                                Hapus
                            </button>
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

            @if ($invoices instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="mt-8">
                    {{ $invoices->links() }}
                </div>
            @endif
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
        const checkboxesMobile = document.querySelectorAll('.invoice-checkbox-mobile');

        const bulkExportBtn = document.getElementById('bulk-export-btn');
        const multiPrintBtn = document.getElementById('multi-print-btn');
        const bulkForm = document.getElementById('bulk-export-form');

        function updateExportButton() {
            // Check both desktop and mobile checkboxes
            const checkedCount = document.querySelectorAll('.invoice-checkbox:checked, .invoice-checkbox-mobile:checked')
                .length;

            bulkExportBtn.disabled = checkedCount === 0;
            multiPrintBtn.disabled = checkedCount === 0;

            if (checkedCount > 0) {
                bulkExportBtn.innerHTML = `
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Buat (${checkedCount}) Laporan
                `;
                multiPrintBtn.innerHTML = `
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Cetak (${checkedCount}) Invoice
                `;
            } else {
                bulkExportBtn.innerHTML = `
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Buat Laporan (PDF)
                `;
                multiPrintBtn.innerHTML = `
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Cetak Invoice (PDF)
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
                checkboxesMobile.forEach(cb => cb.checked = this.checked);
                updateExportButton();
            });
        }

        const allCheckboxes = [...checkboxes, ...checkboxesMobile];
        allCheckboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                updateExportButton();
                // Simple sync logic could be added here if needed, but since mobile/desktop are mutually exclusive view-wise, it's fine.
            });
        });

        function submitBulkExport() {
            bulkForm.action = "{{ route('invoices.bulk-export-pdf') }}";
            bulkForm.submit();
        }

        function submitMultiPrint() {
            bulkForm.action = "{{ route('invoices.print-multi-pdf') }}";
            bulkForm.submit();
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
    </script>
@endsection
