@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
            <div class="min-w-0 flex-1">
                <nav class="flex mb-4" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2">
                        <li><a href="{{ route('invoices.index', request()->query()) }}"
                                class="text-xs md:text-sm font-bold text-gray-400 hover:text-indigo-600">Invoice</a></li>
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
                        Edit Invoice</h2>
                    <span
                        class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-bold bg-indigo-100 text-indigo-800">
                        {{ $invoice->invoice_number }}
                    </span>
                </div>
                <p class="mt-2 text-sm text-gray-500">Perbarui data invoice Anda.</p>
            </div>
        </div>

        <div class="bg-white shadow-2xl rounded-3xl overflow-hidden border-2 border-gray-100">
            <form action="{{ route('invoices.update', $invoice) }}" method="POST"
                class="p-5 sm:p-10 space-y-8 md:space-y-10">
                @csrf
                @method('PUT')

                {{-- Preserve Filters --}}
                @foreach (request()->query() as $key => $value)
                    @if (is_array($value))
                        @foreach ($value as $k => $v)
                            <input type="hidden" name="filters[{{ $key }}][{{ $k }}]"
                                value="{{ $v }}">
                        @endforeach
                    @else
                        <input type="hidden" name="filters[{{ $key }}]" value="{{ $value }}">
                    @endif
                @endforeach

                <!-- Invoice Details Section -->
                <div class="grid grid-cols-1 gap-y-6 gap-x-6 md:grid-cols-3 border-b border-gray-100 pb-8 md:pb-10">
                    <div>
                        <label for="date" class="block text-sm font-bold text-gray-700 mb-2">Tanggal Invoice</label>
                        <div class="relative">
                            <input type="date" name="date" id="date" value="{{ old('date', $invoice->date) }}"
                                required
                                class="block w-full rounded-2xl border-2 border-gray-200 py-3 px-3 text-gray-900 shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all duration-200">
                        </div>
                    </div>

                    <div>
                        <label for="tipe" class="block text-sm font-bold text-gray-700 mb-2">Tipe Invoice</label>
                        <select name="tipe" id="tipe" required
                            class="block w-full rounded-2xl border-2 border-gray-200 py-3 px-3 text-gray-900 shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all duration-200">
                            <option value="">Pilih Tipe</option>
                            <option value="BSH"
                                {{ old('tipe', str_contains($invoice->invoice_number, 'BSH') ? 'BSH' : '') == 'BSH' ? 'selected' : '' }}>
                                Basahan</option>
                            <option value="KR"
                                {{ old('tipe', str_contains($invoice->invoice_number, 'KR') ? 'KR' : '') == 'KR' ? 'selected' : '' }}>
                                Keringan</option>
                            <option value="OPR"
                                {{ old('tipe', str_contains($invoice->invoice_number, 'OPR') ? 'OPR' : '') == 'OPR' ? 'selected' : '' }}>
                                Operasional</option>
                        </select>
                    </div>

                    <div>
                        <label for="customer_name" class="block text-sm font-bold text-gray-700 mb-2">Nama Pelanggan</label>
                        <select name="customer_name" id="customer_name" required
                            class="block w-full rounded-2xl border-2 border-gray-200 py-3 px-3 text-gray-900 shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all duration-200">
                            <option value="">Pilih Pelanggan</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->name }}"
                                    {{ old('customer_name', $invoice->customer_name) == $customer->name ? 'selected' : '' }}>
                                    {{ $customer->name }} {{ $customer->phone ? ' - ' . $customer->phone : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Items Section -->
                <div>
                    <div
                        class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 pb-2 border-b-2 border-gray-50 gap-2">
                        <h3 class="text-xl font-bold text-gray-900">Item Invoice</h3>
                        <span class="text-xs font-medium text-gray-400 uppercase tracking-widest">Ubah produk atau
                            layanan</span>
                    </div>

                    <!-- Desktop Header (Visible only on medium screens and up) -->
                    <div class="hidden md:grid grid-cols-12 gap-6 mb-2 px-6">
                        <div class="col-span-12 md:col-span-4 text-xs font-black text-gray-400 uppercase tracking-widest">
                            Produk / Layanan</div>
                        <div class="col-span-6 md:col-span-2 text-xs font-black text-gray-400 uppercase tracking-widest">
                            HPP</div>
                        <div class="col-span-6 md:col-span-2 text-xs font-black text-gray-400 uppercase tracking-widest">
                            Harga Jual</div>
                        <div class="col-span-6 md:col-span-2 text-xs font-black text-gray-400 uppercase tracking-widest">
                            Jumlah</div>
                        <div class="col-span-12 md:col-span-2 text-xs font-black text-gray-400 uppercase tracking-widest">
                            Subtotal</div>
                    </div>

                    <div id="items-container" class="space-y-4">
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
                <div class="bg-gray-50 rounded-3xl p-6 md:p-8 border border-gray-100">
                    <div class="flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0">
                        <div class="text-center sm:text-left">
                            <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Total Keseluruhan</p>
                            <p class="text-xs text-gray-500 mt-1">Sudah termasuk semua item</p>
                        </div>
                        <div class="text-3xl font-black text-indigo-600">
                            Rp <span id="grand-total">0</span>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-4 pt-6">
                    <a href="{{ route('invoices.index', request()->query()) }}"
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

    <style>
        .select2-container--default .select2-selection--single {
            border-radius: 1rem;
            border: 2px solid #e5e7eb;
            height: 52px;
            display: flex;
            align-items: center;
            padding-left: 8px;
            transition: all 0.2s;
        }

        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
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
        const existingItems = @json(old('items', $invoice->items));

        // Format Currency Helper
        function formatCurrency(num) {
            if (num === '' || num === null || num === undefined) return '';
            return new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(num);
        }

        function addItem(existingItem = null) {
            const container = document.getElementById('items-container');
            const itemDiv = document.createElement('div');
            itemDiv.className =
                "item-card bg-white p-4 md:p-6 rounded-3xl border-2 border-gray-100 shadow-sm hover:shadow-md hover:border-indigo-100 transition-all duration-200 relative group";

            let productOptions = '<option value="">Pilih Produk</option>';
            let isCustomProduct = existingItem && existingItem.product_id && !products.find(p => p.id == existingItem
                .product_id);

            products.forEach(product => {
                const selected = existingItem && existingItem.product_id == product.id ? 'selected' : '';
                const supplierName = product.supplier ? product.supplier.name : '-';
                const formattedPrice = formatCurrency(product.price);
                const label = `${product.name} | Rp ${formattedPrice} | ${product.unit} | ${supplierName}`;

                productOptions +=
                    `<option value="${product.id}" data-price="${product.price}" data-purchase-price="${product.purchase_price}" data-unit="${product.unit}" ${selected}>${label}</option>`;
            });

            if (isCustomProduct) {
                productOptions += `<option value="${existingItem.product_id}" selected>${existingItem.product_id}</option>`;
            }

            const selectId = `product-select-${itemIndex}`;
            const quantity = existingItem ? parseFloat(existingItem.quantity) : 1;
            const price = existingItem ? parseFloat(existingItem.price) : 0;
            const purchase_price = existingItem ? parseFloat(existingItem.purchase_price) : 0;
            const unit = existingItem ? existingItem.unit : '';

            // Calc Total
            const total = price * quantity;
            // Format Display
            const displayTotal = (price > 0 || existingItem) ? formatCurrency(total) : '';

            const description = existingItem ? (existingItem.description || '') : '';
            const unitReadOnly = '';
            const unitClass = '';

            itemDiv.innerHTML = `
                <button type="button" onclick="removeItem(this)" 
                    class="absolute -top-3 -right-3 bg-red-50 text-red-500 p-2 rounded-full shadow-sm hover:bg-red-500 hover:text-white transition-all duration-200 focus:outline-none z-10">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <div class="grid grid-cols-12 gap-4 md:gap-6">
                    <!-- Product Selection -->
                    <div class="col-span-12 md:col-span-4">
                        <label class="block md:hidden text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Produk / Layanan</label>
                        <select id="${selectId}" name="items[${itemIndex}][product_id]" class="product-select block w-full" required>
                            ${productOptions}
                        </select>
                    </div>

                    <!-- Purchase Price (HPP) -->
                    <div class="col-span-6 md:col-span-2">
                        <label class="block md:hidden text-xs font-black text-gray-400 uppercase tracking-widest mb-2">HPP</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-400 text-xs font-bold">Rp</span>
                            </div>
                            <input type="number" name="items[${itemIndex}][purchase_price]" value="${purchase_price || ''}"
                                class="purchase-price-input block w-full rounded-xl border-2 border-gray-100 py-2.5 pl-8 text-gray-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all duration-200 text-sm font-bold bg-gray-50/50" 
                                step="0.01">
                        </div>
                    </div>

                    <!-- Selling Price -->
                    <div class="col-span-6 md:col-span-2">
                        <label class="block md:hidden text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Harga Jual</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-400 text-xs font-bold">Rp</span>
                            </div>
                            <input type="number" name="items[${itemIndex}][price]" value="${price || ''}"
                                class="price-input block w-full rounded-xl border-2 border-gray-200 py-2.5 pl-8 text-gray-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all duration-200 text-sm font-bold" 
                                step="0.01" onchange="updateTotal(this)">
                        </div>
                    </div>

                    <!-- Quantity & Unit -->
                    <div class="col-span-6 md:col-span-2">
                        <label class="block md:hidden text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Jumlah</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="items[${itemIndex}][quantity]" value="${quantity}"
                                class="quantity-input block w-full rounded-xl border-2 border-gray-200 py-2.5 text-center text-gray-900 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all duration-200 text-sm font-bold" 
                                min="0.01" step="any" onchange="updateTotal(this)" required>
                            <input type="text" name="items[${itemIndex}][unit]" value="${unit}"
                                class="unit-input block w-16 rounded-xl border-2 border-gray-200 py-2.5 text-center text-xs font-extrabold text-indigo-600 ${unitClass}" 
                                placeholder="Stn">
                        </div>
                    </div>

                    <!-- Subtotal -->
                    <div class="col-span-6 md:col-span-2">
                        <label class="block md:hidden text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Subtotal</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-400 text-xs font-bold">Rp</span>
                            </div>
                            <!-- CHANGED TO TEXT INPUT -->
                            <input type="text" value="${displayTotal}"
                                class="total-input block w-full rounded-xl border-transparent py-2.5 pl-8 text-gray-900 font-black bg-indigo-50 transition-all duration-200 text-sm cursor-default" 
                                readonly>
                        </div>
                    </div>

                    <!-- Description (Full width) -->
                    <div class="col-span-12">
                        <label class="block md:hidden text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Keterangan (Opsional)</label>
                        <textarea name="items[${itemIndex}][description]" rows="1" 
                            class="block w-full rounded-xl border-2 border-indigo-100 py-2.5 px-3 text-gray-900 shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all duration-200 bg-indigo-50/30 text-sm"
                            placeholder="tambah keterangan disini...">${description}</textarea>
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
                    card.querySelector('.purchase-price-input').value = '';
                    unitInput.value = '';
                    unitInput.readOnly = false;
                    unitInput.classList.remove('bg-gray-50');
                    unitInput.placeholder = 'Satuan';
                } else {
                    const selectedOption = this.options[this.selectedIndex];
                    priceInput.value = selectedOption.getAttribute('data-price');
                    card.querySelector('.purchase-price-input').value = selectedOption.getAttribute(
                        'data-purchase-price');
                    unitInput.value = selectedOption.getAttribute('data-unit');
                    unitInput.readOnly = false;
                    unitInput.classList.remove('bg-gray-50');
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
            // Format to Currency
            card.querySelector('.total-input').value = formatCurrency(total);
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
            document.querySelectorAll('.item-card').forEach(card => {
                const price = parseFloat(card.querySelector('.price-input').value) || 0;
                const quantity = parseFloat(card.querySelector('.quantity-input').value) || 0;
                grandTotal += (price * quantity);
            });
            document.getElementById('grand-total').innerText = formatCurrency(grandTotal);
        }

        $(document).ready(function() {
            if (existingItems.length > 0) {
                existingItems.forEach(item => {
                    addItem(item);
                });
                calculateGrandTotal();
            } else {
                addItem();
            }

            // Initialize Customer Select2
            $('#customer_name').select2({
                placeholder: "Pilih atau ketik nama pelanggan...",
                tags: true,
                allowClear: true,
                width: '100%'
            });
        });
    </script>
@endsection
