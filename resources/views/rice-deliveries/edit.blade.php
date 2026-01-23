@extends('layouts.app')

@section('title', 'Edit Nota Pengiriman Beras')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
            <div class="min-w-0 flex-1">
                <nav class="flex mb-4" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2">
                        <li><a href="{{ route('rice-deliveries.index') }}"
                                class="text-xs md:text-sm font-bold text-gray-400 hover:text-indigo-600">Nota Beras</a></li>
                        <li><svg class="h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd" />
                            </svg></li>
                        <li class="text-xs md:text-sm font-bold text-indigo-600">Edit NOTA {{ $riceDelivery->nota_number }}
                        </li>
                    </ol>
                </nav>
                <h2 class="text-2xl md:text-3xl font-extrabold leading-7 text-gray-900 sm:truncate sm:tracking-tight">Edit
                    Nota Pengiriman Beras</h2>
            </div>
        </div>

        <div class="bg-white shadow-2xl rounded-3xl overflow-hidden border-2 border-gray-100">
            <form action="{{ route('rice-deliveries.update', $riceDelivery) }}" method="POST"
                class="p-5 sm:p-10 space-y-8 md:space-y-10">
                @csrf
                @method('PUT')

                <!-- Nota Details Section -->
                <div class="grid grid-cols-1 gap-y-6 gap-x-6 md:grid-cols-4 border-b border-gray-100 pb-8 md:pb-10">
                    <div>
                        <label for="nota_number" class="block text-sm font-bold text-gray-700 mb-2">Nomor Nota</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-400 text-xs font-bold font-mono">NOTA</span>
                            </div>
                            <input type="text" name="nota_number" id="nota_number"
                                value="{{ old('nota_number', $riceDelivery->nota_number) }}" required
                                class="block w-full rounded-2xl border-2 border-gray-200 py-3 pl-14 pr-3 text-gray-900 shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all duration-200 font-mono font-bold"
                                placeholder="001">
                        </div>
                    </div>

                    <div>
                        <label for="location" class="block text-sm font-bold text-gray-700 mb-2">Lokasi</label>
                        <input type="text" name="location" id="location"
                            value="{{ old('location', $riceDelivery->location) }}" required
                            class="block w-full rounded-2xl border-2 border-gray-200 py-3 px-3 text-gray-900 shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all duration-200 font-bold">
                    </div>

                    <div>
                        <label for="date" class="block text-sm font-bold text-gray-700 mb-2">Tanggal</label>
                        <input type="date" name="date" id="date" value="{{ old('date', $riceDelivery->date) }}"
                            required
                            class="block w-full rounded-2xl border-2 border-gray-200 py-3 px-3 text-gray-900 shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all duration-200 font-bold">
                    </div>

                    <div>
                        <label for="customer_name" class="block text-sm font-bold text-gray-700 mb-2">Kepada Yth.</label>
                        <textarea name="customer_name" id="customer_name" required rows="2"
                            class="block w-full rounded-2xl border-2 border-gray-200 py-3 px-3 text-gray-900 shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all duration-200 placeholder-gray-400 font-bold"
                            placeholder="SPPG Maju Bersama&#10;Pagerageung">{{ old('customer_name', $riceDelivery->customer_name) }}</textarea>
                    </div>
                </div>

                <!-- Items Section -->
                <div>
                    <div
                        class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 pb-2 border-b-2 border-gray-50 gap-2">
                        <h3 class="text-xl font-bold text-gray-900">Rincian Barang</h3>
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-widest">Input rincian pengiriman
                            beras</span>
                    </div>

                    <div id="items-container" class="space-y-4">
                        <!-- Items will be added here -->
                    </div>

                    <button type="button" onclick="addItem()"
                        class="mt-8 inline-flex items-center justify-center w-full sm:w-auto px-6 py-3 border border-transparent text-base font-bold rounded-2xl text-white bg-indigo-600 shadow-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:-translate-y-1">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Tambah Barang
                    </button>
                </div>

                <!-- Totals Section -->
                <div class="bg-gray-50 rounded-3xl p-6 md:p-8 border border-gray-100">
                    <div class="flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0">
                        <div class="text-center sm:text-left">
                            <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Total Nota</p>
                        </div>
                        <div class="text-3xl font-black text-indigo-600">
                            Rp <span id="grand-total">0</span>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-4 pt-6">
                    <a href="{{ route('rice-deliveries.index') }}"
                        class="w-full sm:w-auto text-center px-6 py-3 text-sm font-bold text-gray-500 hover:text-gray-700 transition-colors duration-200">
                        Batal
                    </a>
                    <button type="submit"
                        class="w-full sm:w-auto px-10 py-4 border border-transparent text-lg font-black rounded-2xl text-white bg-indigo-600 shadow-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:scale-105">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let itemIndex = 0;
        const existingItems = @json($riceDelivery->items);

        function formatCurrency(num) {
            if (num === '' || num === null || num === undefined) return '0';
            return new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(num);
        }

        function addItem(existingItem = null) {
            const container = document.getElementById('items-container');
            const itemDiv = document.createElement('div');
            itemDiv.className =
                "item-card bg-white p-4 md:p-6 rounded-3xl border-2 border-gray-100 shadow-sm hover:shadow-md transition-all duration-200 relative group";

            const quantityString = existingItem ? existingItem.quantity_string : '';
            const description = existingItem ? existingItem.description : '';
            const price = existingItem ? existingItem.price : 0;
            const total = existingItem ? existingItem.total : 0;

            itemDiv.innerHTML = `
                <button type="button" onclick="removeItem(this)" 
                    class="absolute -top-3 -right-3 bg-red-50 text-red-500 p-2 rounded-full shadow-sm hover:bg-red-500 hover:text-white transition-all duration-200 focus:outline-none z-10">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-12 md:col-span-3">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Banyaknya</label>
                        <input type="text" name="items[${itemIndex}][quantity_string]" value="${quantityString}"
                            class="qty-string-input block w-full rounded-xl border-2 border-gray-200 py-2.5 px-3 text-gray-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-sm font-bold" 
                            placeholder="800 kg" onchange="updateRowTotal(this)">
                    </div>

                    <div class="col-span-12 md:col-span-4">
                         <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Nama Barang</label>
                         <input type="text" name="items[${itemIndex}][description]" value="${description}"
                            class="block w-full rounded-xl border-2 border-gray-200 py-2.5 px-3 text-gray-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-sm font-bold"
                            placeholder="Beras JR Spesial 25kg">
                    </div>

                    <div class="col-span-6 md:col-span-2">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Harga</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-400 text-xs font-bold">Rp</span>
                            </div>
                            <input type="number" name="items[${itemIndex}][price]" value="${price}"
                                class="price-input block w-full rounded-xl border-2 border-gray-200 py-2.5 pl-8 text-gray-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-sm font-bold text-right" 
                                step="any" onchange="updateRowTotal(this)">
                        </div>
                    </div>

                    <div class="col-span-6 md:col-span-3">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Jumlah</label>
                        <div class="relative">
                             <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-400 text-xs font-bold">Rp</span>
                            </div>
                            <input type="text" value="${formatCurrency(total)}"
                                class="row-total-input block w-full rounded-xl border-transparent py-2.5 pl-8 text-gray-900 font-black bg-indigo-50 text-right text-sm" 
                                readonly>
                        </div>
                    </div>
                </div>
            `;

            container.appendChild(itemDiv);
            itemIndex++;
            calculateGrandTotal();
        }

        function extractNumeric(str) {
            if (!str) return 0;
            const matches = str.match(/[\d\.]+/);
            return matches ? parseFloat(matches[0]) : 0;
        }

        function updateRowTotal(element) {
            const card = element.closest('.item-card');
            const qtyStr = card.querySelector('.qty-string-input').value;
            const price = parseFloat(card.querySelector('.price-input').value) || 0;

            const qtyNum = extractNumeric(qtyStr);
            const total = qtyNum * price;

            card.querySelector('.row-total-input').value = formatCurrency(total);
            calculateGrandTotal();
        }

        function removeItem(button) {
            button.closest('.item-card').remove();
            calculateGrandTotal();
        }

        function calculateGrandTotal() {
            let grandTotal = 0;
            document.querySelectorAll('.item-card').forEach(card => {
                const qtyStr = card.querySelector('.qty-string-input').value;
                const price = parseFloat(card.querySelector('.price-input').value) || 0;
                const qtyNum = extractNumeric(qtyStr);
                grandTotal += (qtyNum * price);
            });
            document.getElementById('grand-total').innerText = formatCurrency(grandTotal);
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (existingItems.length > 0) {
                existingItems.forEach(item => addItem(item));
            } else {
                addItem();
            }
        });
    </script>
@endsection
