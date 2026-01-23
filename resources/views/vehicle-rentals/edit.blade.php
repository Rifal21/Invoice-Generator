@extends('layouts.app')

@section('title', 'Edit Invoice Sewa Kendaraan')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
            <div class="min-w-0 flex-1">
                <nav class="flex mb-4" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2">
                        <li><a href="{{ route('vehicle-rentals.index') }}"
                                class="text-xs md:text-sm font-bold text-gray-400 hover:text-indigo-600">Sewa Kendaraan</a>
                        </li>
                        <li><svg class="h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd" />
                            </svg></li>
                        <li class="text-xs md:text-sm font-bold text-indigo-600">Edit</li>
                    </ol>
                </nav>
                <div class="flex items-center gap-4">
                    <h2 class="text-2xl md:text-3xl font-extrabold leading-7 text-gray-900 sm:truncate sm:tracking-tight">
                        Edit Invoice Sewa Kendaraan</h2>
                    <span
                        class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-bold bg-amber-100 text-amber-800">
                        {{ $vehicleRental->invoice_number }}
                    </span>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-2xl rounded-3xl overflow-hidden border-2 border-gray-100">
            <form action="{{ route('vehicle-rentals.update', $vehicleRental) }}" method="POST"
                class="p-5 sm:p-10 space-y-8 md:space-y-10">
                @csrf
                @method('PUT')

                <!-- Invoice Details Section -->
                <div class="grid grid-cols-1 gap-y-6 gap-x-6 md:grid-cols-3 border-b border-gray-100 pb-8 md:pb-10">
                    <div>
                        <label for="date" class="block text-sm font-bold text-gray-700 mb-2">Tanggal Invoice</label>
                        <div class="relative">
                            <input type="date" name="date" id="date"
                                value="{{ old('date', $vehicleRental->date) }}" required
                                class="block w-full rounded-2xl border-2 border-gray-200 py-3 px-3 text-gray-900 shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all duration-200">
                        </div>
                    </div>

                    <div>
                        <label for="hari" class="block text-sm font-bold text-gray-700 mb-2">Hari</label>
                        <div class="relative">
                            <input type="text" id="hari" readonly
                                class="block w-full rounded-2xl border-2 border-gray-100 bg-gray-50 py-3 px-3 text-gray-500 shadow-sm focus:outline-none transition-all duration-200 font-bold"
                                placeholder="Otomatis...">
                        </div>
                    </div>

                    <div>
                        <label for="customer_name" class="block text-sm font-bold text-gray-700 mb-2">Kepada
                            (Manual)</label>
                        <textarea name="customer_name" id="customer_name" required rows="2"
                            class="block w-full rounded-2xl border-2 border-gray-200 py-3 px-3 text-gray-900 shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all duration-200 placeholder-gray-400"
                            placeholder="Contoh:&#10;SPPG Dapur Kabungah Cipondok&#10;Kabupaten Tasikmalaya">{{ old('customer_name', $vehicleRental->customer_name) }}</textarea>
                    </div>
                </div>

                <!-- Items Section -->
                <div>
                    <div
                        class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 pb-2 border-b-2 border-gray-50 gap-2">
                        <h3 class="text-xl font-bold text-gray-900">Item Sewa</h3>
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-widest">Ubah rincian sewa
                            kendaraan</span>
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
                        Tambah Item Sewa
                    </button>
                </div>

                <!-- Totals Section -->
                <div class="bg-gray-50 rounded-3xl p-6 md:p-8 border border-gray-100">
                    <div class="flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0">
                        <div class="text-center sm:text-left">
                            <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Total Tagihan</p>
                        </div>
                        <div class="text-3xl font-black text-indigo-600">
                            Rp <span id="grand-total">0</span>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-4 pt-6">
                    <a href="{{ route('vehicle-rentals.index') }}"
                        class="w-full sm:w-auto text-center px-6 py-3 text-sm font-bold text-gray-500 hover:text-gray-700 transition-colors duration-200">
                        Batal
                    </a>
                    <button type="submit"
                        class="w-full sm:w-auto px-10 py-4 border border-transparent text-lg font-black rounded-2xl text-white bg-indigo-600 shadow-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:scale-105">
                        Perbarui Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let itemIndex = 0;
        const existingItems = @json(old('items', $vehicleRental->items));

        function formatCurrency(num) {
            if (num === '' || num === null || num === undefined) return '0';
            return new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(num);
        }

        function updateHari() {
            const dateInput = document.getElementById('date').value;
            if (!dateInput) return;

            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const d = new Date(dateInput);
            document.getElementById('hari').value = days[d.getDay()];
        }

        function addItem(existingItem = null) {
            const container = document.getElementById('items-container');
            const itemDiv = document.createElement('div');
            itemDiv.className =
                "item-card bg-white p-4 md:p-6 rounded-3xl border-2 border-gray-100 shadow-sm hover:shadow-md transition-all duration-200 relative group";

            const description = existingItem ? existingItem.description : '';
            const startDate = existingItem ? existingItem.start_date : '';
            const endDate = existingItem ? existingItem.end_date : '';
            const quantity = existingItem ? existingItem.quantity : 0;
            const unit = existingItem ? existingItem.unit : 'Hari';
            const price = existingItem ? existingItem.price : 0;
            const total = price * quantity;

            itemDiv.innerHTML = `
                <button type="button" onclick="removeItem(this)" 
                    class="absolute -top-3 -right-3 bg-red-50 text-red-500 p-2 rounded-full shadow-sm hover:bg-red-500 hover:text-white transition-all duration-200 focus:outline-none z-10">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-12 md:col-span-12">
                         <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Deskripsi Sewa</label>
                         <textarea name="items[${itemIndex}][description]" rows="2" 
                            class="block w-full rounded-xl border-2 border-gray-200 py-2.5 px-3 text-gray-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-sm font-bold"
                            placeholder="Contoh: Sewa Mobil Daihatsu GrandMax Z 8030 NM">${description}</textarea>
                    </div>

                    <div class="col-span-12 md:col-span-4">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Tanggal Mulai</label>
                        <input type="date" name="items[${itemIndex}][start_date]" value="${startDate}"
                            class="start-date-input block w-full rounded-xl border-2 border-gray-200 py-2.5 px-3 text-gray-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-sm font-bold" 
                            onchange="calculateDays(this)">
                    </div>

                    <div class="col-span-12 md:col-span-4">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Tanggal Selesai</label>
                        <input type="date" name="items[${itemIndex}][end_date]" value="${endDate}"
                            class="end-date-input block w-full rounded-xl border-2 border-gray-200 py-2.5 px-3 text-gray-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-sm font-bold" 
                            onchange="calculateDays(this)">
                    </div>

                    <div class="col-span-4 md:col-span-4 lg:col-span-4">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Harga Sewa / Hari</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-400 text-xs font-bold">Rp</span>
                            </div>
                            <input type="number" name="items[${itemIndex}][price]" value="${price}"
                                class="price-input block w-full rounded-xl border-2 border-gray-200 py-2.5 pl-8 text-gray-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all text-sm font-bold" 
                                step="any" onchange="updateTotal(this)">
                        </div>
                    </div>

                    <div class="col-span-8 md:col-span-2">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Durasi</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="items[${itemIndex}][quantity]" value="${quantity}" readonly
                                class="quantity-input block w-full rounded-xl border-transparent bg-gray-50 py-2.5 px-3 text-gray-900 font-bold text-center" 
                                required>
                            <input type="hidden" name="items[${itemIndex}][unit]" value="${unit}">
                        </div>
                    </div>

                    <div class="col-span-12 md:col-span-10 lg:col-span-10">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Subtotal</label>
                        <div class="relative">
                             <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-400 text-xs font-bold">Rp</span>
                            </div>
                            <input type="text" value="${formatCurrency(total)}"
                                class="total-input block w-full rounded-xl border-transparent py-2.5 pl-8 text-gray-900 font-black bg-indigo-50 text-base" 
                                readonly>
                        </div>
                    </div>
                </div>
            `;

            container.appendChild(itemDiv);
            itemIndex++;
            calculateGrandTotal();
        }

        function calculateDays(element) {
            const card = element.closest('.item-card');
            const startInput = card.querySelector('.start-date-input').value;
            const endInput = card.querySelector('.end-date-input').value;
            const quantityInput = card.querySelector('.quantity-input');

            if (startInput && endInput) {
                const start = new Date(startInput);
                const end = new Date(endInput);
                const diffTime = end - start;
                const diffDays = Math.max(0, Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1);
                quantityInput.value = diffDays;
            } else {
                quantityInput.value = 0;
            }
            updateTotal(element);
        }

        function updateTotal(element) {
            const card = element.closest('.item-card');
            const price = parseFloat(card.querySelector('.price-input').value) || 0;
            const quantity = parseFloat(card.querySelector('.quantity-input').value) || 0;
            const total = price * quantity;
            card.querySelector('.total-input').value = formatCurrency(total);
            calculateGrandTotal();
        }

        function removeItem(button) {
            button.closest('.item-card').remove();
            calculateGrandTotal();
        }

        function calculateGrandTotal() {
            let grandTotal = 0;
            document.querySelectorAll('.item-card').forEach(card => {
                const price = parseFloat(card.querySelector('.price-input').value) || 0;
                const quantity = parseFloat(card.querySelector('.quantity-input').value) || 0;
                grandTotal += (price * quantity);
            });
            document.getElementById('grand-total').innerText = formatCurrency(grandTotal);
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('date').addEventListener('change', updateHari);
            updateHari();

            if (existingItems.length > 0) {
                existingItems.forEach(item => addItem(item));
            } else {
                addItem();
            }
        });
    </script>
@endsection
