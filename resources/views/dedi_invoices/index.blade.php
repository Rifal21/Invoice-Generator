@extends('layouts.app')

@section('title', 'Nota Faktur H Dedi')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
            <div class="min-w-0 flex-1">
                <nav class="flex mb-4" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2">
                        <li><a href="{{ route('dedi-invoices.index') }}"
                                class="text-xs md:text-sm font-bold text-gray-400 hover:text-indigo-600">Operasional</a></li>
                        <li><svg class="h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd" />
                            </svg></li>
                        <li class="text-xs md:text-sm font-bold text-indigo-600">Nota H Dedi</li>
                    </ol>
                </nav>
                <h2 class="text-2xl md:text-3xl font-extrabold leading-7 text-gray-900 sm:truncate sm:tracking-tight">Nota
                    Faktur H Dedi</h2>
                <p class="mt-2 text-sm text-gray-500">Kelola Nota Faktur khusus H Dedi.</p>
            </div>
            <div class="flex shrink-0">
                <a href="{{ route('dedi-invoices.create') }}"
                    class="w-full md:w-auto inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-bold rounded-2xl text-white bg-indigo-600 shadow-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:-translate-y-1">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Buat Nota Baru
                </a>
            </div>
        </div>

        <!-- Invoices List -->
        <div class="bg-white shadow-2xl rounded-3xl overflow-hidden border border-gray-100">
            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Nomor
                                Invoice</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                Tanggal</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                Pelanggan</th>
                            <th class="px-6 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">Total
                            </th>
                            <th
                                class="relative px-6 py-4 text-right text-xs font-black text-gray-400 uppercase tracking-widest">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 bg-white">
                        @forelse ($invoices as $invoice)
                            <tr class="hover:bg-indigo-50/30 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900">{{ $invoice->invoice_number }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-600">
                                        {{ \Carbon\Carbon::parse($invoice->date)->format('d M Y') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900">{{ $invoice->customer_name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-black text-indigo-600">Rp
                                        {{ number_format($invoice->total_amount, 2, ',', '.') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-1">
                                    <a href="{{ route('dedi-invoices.export-pdf', $invoice->id) }}" target="_blank"
                                        class="text-red-600 hover:text-red-900 bg-red-50 p-2 rounded-xl inline-flex items-center justify-center"
                                        title="Print PDF">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <a href="{{ route('dedi-invoices.edit', $invoice->id) }}"
                                        class="text-amber-600 hover:text-amber-900 bg-amber-50 p-2 rounded-xl inline-flex items-center justify-center"
                                        title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('dedi-invoices.destroy', $invoice->id) }}" method="POST"
                                        class="inline-block"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-600 hover:text-red-900 bg-red-50 p-2 rounded-xl inline-flex items-center justify-center"
                                            title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500 font-medium">Belum ada nota
                                    yang dibuat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile View -->
            <div class="md:hidden space-y-4 p-4">
                @forelse ($invoices as $invoice)
                    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex flex-col gap-3">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-black text-gray-900">{{ $invoice->invoice_number }}</h3>
                                <p class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($invoice->date)->format('d M Y') }}</p>
                            </div>
                            <span
                                class="px-2 py-1 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-lg">{{ $invoice->customer_name }}</span>
                        </div>
                        <div class="flex justify-between items-end border-t border-gray-100 pt-3">
                            <div>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Total</p>
                                <p class="text-lg font-black text-indigo-600">Rp
                                    {{ number_format($invoice->total_amount, 0, ',', '.') }}</p>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('dedi-invoices.export-pdf', $invoice->id) }}"
                                    class="p-2 bg-red-50 text-red-600 rounded-xl"><i class="fas fa-print"></i></a>
                                <a href="{{ route('dedi-invoices.edit', $invoice->id) }}"
                                    class="p-2 bg-amber-50 text-amber-600 rounded-xl"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('dedi-invoices.destroy', $invoice->id) }}" method="POST"
                                    onsubmit="return confirm('Hapus?');">
                                    @csrf @method('DELETE')
                                    <button class="p-2 bg-red-50 text-red-600 rounded-xl"><i
                                            class="fas fa-trash-alt"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 text-gray-500">Belum ada nota.</div>
                @endforelse
            </div>
            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $invoices->links() }}
            </div>
        </div>
    </div>
@endsection
