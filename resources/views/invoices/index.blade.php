@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="sm:flex sm:items-center sm:justify-between mb-10">
            <div class="sm:flex-auto">
                <h1 class="text-4xl font-black text-gray-900 tracking-tight">Daftar Invoice</h1>
                <p class="mt-2 text-lg text-gray-500">Kelola dan pantau semua invoice yang telah dibuat dalam satu tempat.
                </p>
            </div>
            <div class="mt-6 sm:ml-16 sm:mt-0">
                <a href="{{ route('invoices.create') }}"
                    class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-bold rounded-2xl text-white bg-indigo-600 shadow-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:-translate-y-1">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Buat Invoice Baru
                </a>
            </div>
        </div>

        <!-- Filter Section -->
        <form action="{{ route('invoices.index') }}" method="GET"
            class="bg-white shadow-xl rounded-3xl p-6 mb-8 border border-gray-100">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
                <div class="md:col-span-2">
                    <label for="search" class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Cari
                        Invoice / Pelanggan</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        placeholder="Nomor atau nama..."
                        class="block w-full rounded-2xl border-gray-200 py-3 px-3 text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>
                <div class="md:col-span-2">
                    <label for="customer"
                        class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Filter
                        Pelanggan</label>
                    <select name="customer" id="customer"
                        class="block w-full rounded-2xl border-gray-200 py-3 px-3 text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                        <option value="">Semua Pelanggan</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer }}" {{ request('customer') == $customer ? 'selected' : '' }}>
                                {{ $customer }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label for="date"
                        class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Filter
                        Tanggal</label>
                    <input type="date" name="date" id="date" value="{{ request('date') }}"
                        class="block w-full rounded-2xl border-gray-200 py-3 px-3 text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                </div>
                <div class="md:col-span-2 flex gap-2">
                    <button type="submit"
                        class="flex-1 bg-indigo-600 text-white font-bold py-3 px-6 rounded-2xl hover:bg-indigo-700 transition-all shadow-lg">
                        Filter
                    </button>
                    <a href="{{ route('invoices.index') }}"
                        class="bg-gray-100 text-gray-600 font-bold py-3 px-6 rounded-2xl hover:bg-gray-200 transition-all text-center">
                        Reset
                    </a>
                </div>
                <div class="md:col-span-4 flex gap-2">
                    <button type="button" onclick="submitBulkExport()" id="bulk-export-btn" disabled
                        class="flex-1 bg-red-50 text-red-700 font-bold py-3 px-4 rounded-2xl hover:bg-red-100 transition-all border border-red-100 disabled:opacity-50 disabled:cursor-not-allowed text-xs flex items-center justify-center">
                        Buat Laporan (PDF)
                    </button>
                    <button type="button" onclick="submitMultiPrint()" id="multi-print-btn" disabled
                        class="flex-1 bg-indigo-50 text-indigo-700 font-bold py-3 px-4 rounded-2xl hover:bg-indigo-100 transition-all border border-indigo-100 disabled:opacity-50 disabled:cursor-not-allowed text-xs flex items-center justify-center">
                        Cetak Invoice (PDF)
                    </button>
                </div>
            </div>
        </form>

        <form id="bulk-export-form" action="{{ route('invoices.bulk-export-pdf') }}" method="POST" target="_blank">
            @csrf

            <div class="bg-white shadow-2xl rounded-3xl overflow-hidden border border-gray-100">
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
                                    Nomor Invoice</th>
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
                                                    {{ $invoice->invoice_number }}
                                                </div>
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
                                        class="relative whitespace-nowrap py-5 pl-3 pr-6 text-right text-sm font-bold space-x-3">
                                        <a href="{{ route('invoices.show', $invoice->id) }}"
                                            class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1.5 rounded-lg transition-colors">Lihat</a>
                                        <a href="{{ route('invoices.edit', $invoice->id) }}"
                                            class="text-amber-600 hover:text-amber-900 bg-amber-50 px-3 py-1.5 rounded-lg transition-colors">Edit</a>
                                        <button type="button" onclick="deleteInvoice({{ $invoice->id }})"
                                            class="text-red-600 hover:text-red-900 bg-red-50 px-3 py-1.5 rounded-lg transition-colors">Hapus</button>
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
                                            <a href="{{ route('invoices.create') }}"
                                                class="mt-4 text-indigo-600 font-bold hover:underline">Buat invoice pertama
                                                Anda</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </form>

        <form id="delete-form" action="" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>

    <script>
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.invoice-checkbox');
        const bulkExportBtn = document.getElementById('bulk-export-btn');
        const multiPrintBtn = document.getElementById('multi-print-btn');
        const bulkForm = document.getElementById('bulk-export-form');

        function updateExportButton() {
            const checkedCount = document.querySelectorAll('.invoice-checkbox:checked').length;
            bulkExportBtn.disabled = checkedCount === 0;
            multiPrintBtn.disabled = checkedCount === 0;

            if (checkedCount > 0) {
                bulkExportBtn.innerText = `Buat (${checkedCount}) Laporan`;
                multiPrintBtn.innerText = `Cetak (${checkedCount}) Invoice`;
            } else {
                bulkExportBtn.innerText = `Buat Laporan`;
                multiPrintBtn.innerText = `Cetak Invoice`;
            }
        }

        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateExportButton();
        });

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                updateExportButton();
                if (!this.checked) selectAll.checked = false;
                if (document.querySelectorAll('.invoice-checkbox:checked').length === checkboxes.length)
                    selectAll.checked = true;
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
            if (confirm('Apakah Anda yakin ingin menghapus invoice ini?')) {
                const form = document.getElementById('delete-form');
                form.action = `/invoices/${id}`;
                form.submit();
            }
        }
    </script>
@endsection
