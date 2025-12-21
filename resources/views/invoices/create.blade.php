@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="md:flex md:items-center md:justify-between mb-8">
            <div class="min-w-0 flex-1">
                <h2 class="text-3xl font-extrabold leading-7 text-gray-900 sm:truncate sm:tracking-tight">Buat Invoice Baru
                </h2>
                <p class="mt-2 text-sm text-gray-500">Buat invoice profesional dengan mudah dan cepat.</p>
            </div>
        </div>

        <div class="bg-white shadow-2xl rounded-3xl overflow-hidden border border-gray-100">
            <form action="{{ route('invoices.store') }}" method="POST" class="p-6 sm:p-10 space-y-10">
                @csrf

                @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded-r-2xl">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-bold text-red-800">Terdapat kesalahan pada input Anda:</h3>
                                <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Invoice Details Section -->
                <div class="grid grid-cols-1 gap-y-8 gap-x-6 sm:grid-cols-6 border-b border-gray-100 pb-10">
                    <div class="sm:col-span-2">
                        <label for="date" class="block text-sm font-bold text-gray-700 mb-2">Tanggal Invoice</label>
                        <div class="relative">
                            <input type="date" name="date" id="date" value="{{ old('date', date('Y-m-d')) }}"
                                required
                                class="block w-full rounded-2xl border-gray-200 py-3 text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                        </div>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="tipe" class="block text-sm font-bold text-gray-700 mb-2">Tipe Invoice</label>
                        <select name="tipe" id="tipe" required
                            class="block w-full rounded-2xl border-gray-200 py-3 text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                            <option value="">Pilih Tipe</option>
                            <option value="BSH" {{ old('tipe') == 'BSH' ? 'selected' : '' }}>Basahan Siswa</option>
                            <option value="KR" {{ old('tipe') == 'KR' ? 'selected' : '' }}>Keringan Siswa</option>
                            <option value="OPR" {{ old('tipe') == 'OPR' ? 'selected' : '' }}>Operasional</option>
                            <option value="KRBSBM" {{ old('tipe') == 'KRBSBM' ? 'selected' : '' }}>Keringan Bumil Busui
                            </option>
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label for="customer_name" class="block text-sm font-bold text-gray-700 mb-2">Nama Pelanggan</label>
                        <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name') }}"
                            required placeholder="Contoh: Budi Santoso"
                            class="block w-full rounded-2xl border-gray-200 py-3 text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                    </div>
                </div>

                <!-- Items Section -->
                <div>
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Item Invoice</h3>
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-widest">Tambah produk atau
                            layanan</span>
                    </div>

                    <div id="items-container" class="space-y-6">
                        <!-- Items will be added here as cards -->
                    </div>

                    <button type="button" onclick="addItem()"
                        class="mt-8 inline-flex items-center justify-center w-full sm:w-auto px-6 py-3 border border-transparent text-base font-bold rounded-2xl text-white bg-indigo-600 shadow-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:-translate-y-1">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Tambah Item Baru
                    </button>
                </div>

                <!-- Totals Section -->
                <div class="bg-gray-50 rounded-3xl p-8 border border-gray-100">
                    <div class="flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0">
                        <div class="text-center sm:text-left">
                            <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Total Keseluruhan</p>
                            <p class="text-xs text-gray-500 mt-1">Sudah termasuk semua item</p>
                        </div>
                        <div class="text-3xl font-black text-indigo-600">
                            Rp <span id="grand-total">0.00</span>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex flex-col sm:flex-row items-center justify-end gap-4 pt-6">
                    <a href="{{ route('invoices.index') }}"
                        class="w-full sm:w-auto text-center px-6 py-3 text-sm font-bold text-gray-500 hover:text-gray-700 transition-colors duration-200">
                        Batal
                    </a>
                    <button type="submit"
                        class="w-full sm:w-auto px-10 py-4 border border-transparent text-lg font-black rounded-2xl text-white bg-indigo-600 shadow-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:scale-105">
                        Simpan Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .select2-container--default .select2-selection--single {
            border-radius: 1rem;
            border-color: #e5e7eb;
            height: 50px;
            display: flex;
            align-items: center;
            padding-left: 8px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 50px;
            color: #111827;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 48px;
        }

        .item-card {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <script>
        let itemIndex = 0;
        const products = @json($products);
        const oldItems = @json(old('items', []));

        function addItem(existingItem = null) {
            const container = document.getElementById('items-container');
            const itemDiv = document.createElement('div');
            itemDiv.className =
                "item-card bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition-all duration-200 relative group";

            let productOptions = '<option value="">Pilih Produk</option>';
            let isCustomProduct = existingItem && existingItem.product_id && !products.find(p => p.id == existingItem
                .product_id);

            products.forEach(product => {
                const selected = existingItem && existingItem.product_id == product.id ? 'selected' : '';
                productOptions +=
                    `<option value="${product.id}" data-price="${product.price}" data-unit="${product.unit}" ${selected}>${product.name}</option>`;
            });

            if (isCustomProduct) {
                productOptions += `<option value="${existingItem.product_id}" selected>${existingItem.product_id}</option>`;
            }

            const selectId = `product-select-${itemIndex}`;
            const quantity = existingItem ? existingItem.quantity : 1;
            const price = existingItem ? existingItem.price : '';
            const unit = existingItem ? existingItem.unit : '';
            const total = (parseFloat(price) * parseFloat(quantity)).toFixed(2);
            const displayTotal = isNaN(total) ? '0.00' : total;
            const description = existingItem ? (existingItem.description || '') : '';
            const unitReadOnly = isCustomProduct ? '' : 'readonly';
            const unitClass = isCustomProduct ? '' : 'bg-gray-50';

            itemDiv.innerHTML = `
                <button type="button" onclick="removeItem(this)" 
                    class="absolute -top-3 -right-3 bg-red-50 text-red-500 p-2 rounded-full shadow-sm hover:bg-red-500 hover:text-white transition-all duration-200 focus:outline-none">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                    <!-- Product Selection -->
                    <div class="md:col-span-5">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Produk / Layanan</label>
                        <select id="${selectId}" name="items[${itemIndex}][product_id]" class="product-select block w-full" required>
                            ${productOptions}
                        </select>
                    </div>

                    <!-- Price -->
                    <div class="md:col-span-3">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Harga</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="text-gray-400 text-sm font-bold">Rp</span>
                            </div>
                            <input type="number" name="items[${itemIndex}][price]" value="${price}"
                                class="price-input block w-full rounded-2xl border-gray-200 py-3 pl-12 text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200" 
                                step="0.01" onchange="updateTotal(this)">
                        </div>
                    </div>

                    <!-- Quantity & Unit -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Jumlah</label>
                        <div class="flex items-center space-x-2">
                            <input type="number" name="items[${itemIndex}][quantity]" value="${quantity}"
                                class="quantity-input block w-full rounded-2xl border-gray-200 py-3 text-center text-gray-900 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200" 
                                min="0.01" step="any" onchange="updateTotal(this)" required>
                            <input type="text" name="items[${itemIndex}][unit]" value="${unit}"
                                class="unit-input block w-20 rounded-2xl border-gray-200 py-3 text-center text-xs font-bold text-gray-500 ${unitClass}" 
                                ${unitReadOnly} placeholder="Satuan">
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Subtotal</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="text-gray-400 text-sm font-bold">Rp</span>
                            </div>
                            <input type="number" value="${displayTotal}"
                                class="total-input block w-full rounded-2xl border-transparent py-3 pl-12 text-gray-900 font-black bg-indigo-50 transition-all duration-200" 
                                readonly>
                        </div>
                    </div>

                    <!-- Description (Keterangan) -->
                    <div class="md:col-span-12">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Keterangan (Hanya untuk Laporan Pemeriksaan)</label>
                        <textarea name="items[${itemIndex}][description]" rows="2" 
                            class="block w-full rounded-2xl border-gray-200 py-3 text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 border-indigo-50 border-2"
                            placeholder="Contoh: Rasa Plain, Cokelat, dll">${description}</textarea>
                    </div>
                </div>
            `;

            container.appendChild(itemDiv);

            // Initialize Select2
            $(`#${selectId}`).select2({
                placeholder: "Cari atau ketik produk...",
                allowClear: true,
                tags: true,
                width: '100%',
                createTag: function(params) {
                    var term = $.trim(params.term);
                    if (term === '') return null;
                    return {
                        id: term,
                        text: term,
                        newTag: true
                    }
                }
            });

            // Handle Select2 events
            $(`#${selectId}`).on('select2:select', function(e) {
                const data = e.params.data;
                const card = this.closest('.item-card');
                const priceInput = card.querySelector('.price-input');
                const unitInput = card.querySelector('.unit-input');

                if (data.newTag) {
                    priceInput.value = '';
                    priceInput.readOnly = false;
                    priceInput.classList.remove('bg-gray-50');
                    unitInput.value = '';
                    unitInput.readOnly = false;
                    unitInput.classList.remove('bg-gray-50');
                    unitInput.placeholder = 'Satuan';
                } else {
                    const selectedOption = this.options[this.selectedIndex];
                    priceInput.value = selectedOption.getAttribute('data-price');
                    unitInput.value = selectedOption.getAttribute('data-unit');
                    unitInput.readOnly = true;
                    unitInput.classList.add('bg-gray-50');
                }
                updateTotal(this);
            });

            $(`#${selectId}`).on('select2:clear', function() {
                const card = this.closest('.item-card');
                card.querySelector('.price-input').value = '';
                card.querySelector('.unit-input').value = '';
                card.querySelector('.total-input').value = '';
                calculateGrandTotal();
            });

            itemIndex++;
        }

        function updateTotal(element) {
            const card = element.closest('.item-card');
            const price = parseFloat(card.querySelector('.price-input').value) || 0;
            const quantity = parseFloat(card.querySelector('.quantity-input').value) || 0;
            const total = price * quantity;
            card.querySelector('.total-input').value = total.toFixed(2);
            calculateGrandTotal();
        }

        function removeItem(button) {
            const card = button.closest('.item-card');
            const select = card.querySelector('.product-select');
            if ($(select).data('select2')) $(select).select2('destroy');

            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            setTimeout(() => {
                card.remove();
                calculateGrandTotal();
            }, 300);
        }

        function calculateGrandTotal() {
            let grandTotal = 0;
            document.querySelectorAll('.total-input').forEach(input => {
                grandTotal += parseFloat(input.value) || 0;
            });
            document.getElementById('grand-total').innerText = grandTotal.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        $(document).ready(function() {
            if (oldItems.length > 0) {
                oldItems.forEach(item => {
                    addItem(item);
                });
                calculateGrandTotal();
            } else {
                addItem();
            }
        });
    </script>
@endsection
