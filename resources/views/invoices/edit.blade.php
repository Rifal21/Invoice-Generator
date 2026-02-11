@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8 py-6 md:py-12">
        <!-- Header Section with Premium Touch -->
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between mb-10 gap-6">
            <div class="space-y-2">
                <nav class="flex mb-4" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-xs font-black uppercase tracking-[0.2em]">
                        <li><a href="{{ route('invoices.index', request()->query()) }}"
                                class="text-gray-400 hover:text-indigo-600 transition-colors">Invoice</a></li>
                        <li class="text-gray-300">/</li>
                        <li class="text-indigo-600">Edit</li>
                    </ol>
                </nav>
                <div class="flex flex-wrap items-center gap-4">
                    <h2 class="text-4xl md:text-5xl font-black text-gray-900 tracking-tight">
                        Edit <span
                            class="bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-indigo-800">Invoice</span>
                    </h2>
                    <span id="invoice_number_display"
                        class="inline-flex items-center px-6 py-2 rounded-full text-sm font-black bg-indigo-100 text-indigo-600 shadow-sm border border-indigo-200">
                        {{ $invoice->invoice_number }}
                    </span>
                </div>
                <p class="text-lg text-gray-500 font-medium">Perbarui rincian transaksi dengan presisi.</p>
            </div>

            <div class="flex flex-col sm:flex-row gap-4">
                {{-- Optional: Add specific edit actions here --}}
            </div>
        </div>

        <div class="relative">
            <!-- Decorative Elements -->
            <div class="absolute -top-10 -right-10 w-64 h-64 bg-indigo-100 rounded-full blur-3xl opacity-50 -z-10"></div>
            <div class="absolute -bottom-10 -left-10 w-64 h-64 bg-blue-100 rounded-full blur-3xl opacity-50 -z-10"></div>

            <div
                class="bg-white/80 backdrop-blur-xl shadow-[0_40px_100px_rgba(0,0,0,0.05)] rounded-[2rem] md:rounded-[3rem] border border-white/20">
                <form action="{{ route('invoices.update', $invoice) }}" method="POST"
                    class="p-4 sm:p-12 space-y-8 md:space-y-12">
                    @csrf
                    @method('PUT')

                    <!-- Hidden Invoice Number (Must be inside form) -->
                    <input type="hidden" name="invoice_number" id="invoice_number"
                        value="{{ old('invoice_number', $invoice->invoice_number) }}">

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
                    <div
                        class="grid grid-cols-1 gap-5 md:gap-8 md:grid-cols-3 bg-gray-50/50 p-5 md:p-8 rounded-[1.5rem] md:rounded-[2.5rem] border border-gray-100">
                        <div>
                            <label for="date"
                                class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2">Tanggal
                                Invoice</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i
                                        class="fas fa-calendar-alt text-indigo-400 group-focus-within:text-indigo-600 transition-colors"></i>
                                </div>
                                <input type="date" name="date" id="date"
                                    value="{{ old('date', $invoice->date) }}" required
                                    class="block w-full rounded-2xl border-none bg-white py-4 pl-12 pr-4 text-gray-900 font-bold shadow-sm ring-1 ring-gray-200 focus:ring-4 focus:ring-indigo-500/10 focus:outline-none transition-all duration-300">
                            </div>
                        </div>

                        <div>
                            <label for="tipe"
                                class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2">Tipe
                                Invoice</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none z-10">
                                    <i
                                        class="fas fa-tags text-indigo-400 group-focus-within:text-indigo-600 transition-colors"></i>
                                </div>
                                <select name="tipe" id="tipe" required
                                    class="block w-full rounded-2xl border-none bg-white py-4 pl-12 pr-4 text-gray-900 font-bold shadow-sm ring-1 ring-gray-200 focus:ring-4 focus:ring-indigo-500/10 focus:outline-none transition-all duration-300 appearance-none">
                                    <option value="">Pilih Tipe</option>
                                    <option value="BSH"
                                        {{ old('tipe', str_contains($invoice->invoice_number, '-BSH') ? 'BSH' : '') == 'BSH' ? 'selected' : '' }}>
                                        Basahan (BSH)</option>
                                    <option value="KR"
                                        {{ old('tipe', str_contains($invoice->invoice_number, '-KR') && !str_contains($invoice->invoice_number, '-KRBSBM') ? 'KR' : '') == 'KR' ? 'selected' : '' }}>
                                        Keringan (KR)</option>
                                    <option value="KRBSBM"
                                        {{ old('tipe', str_contains($invoice->invoice_number, '-KRBSBM') ? 'KRBSBM' : '') == 'KRBSBM' ? 'selected' : '' }}>
                                        Keringan Bumil Busui (KRBSBM)</option>
                                    <option value="OPR"
                                        {{ old('tipe', str_contains($invoice->invoice_number, '-OPR') ? 'OPR' : '') == 'OPR' ? 'selected' : '' }}>
                                        Operasional (OPR)</option>
                                    <option value="LMN"
                                        {{ old('tipe', str_contains($invoice->invoice_number, '-LMN') ? 'LMN' : '') == 'LMN' ? 'selected' : '' }}>
                                        Lain-lain (LMN)</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none z-10">
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="customer_name"
                                class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2">Nama
                                Pelanggan</label>
                            <div class="relative group">
                                <select name="customer_name" id="customer_name" required
                                    class="customer-select-input block w-full">
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
                    </div>

                    <!-- Items Section -->
                    <div class="space-y-8">
                        <div class="flex flex-col sm:flex-row justify-between items-end gap-4">
                            <div class="space-y-1">
                                <h3 class="text-2xl font-black text-gray-900 tracking-tight">Item Invoice</h3>
                                <p class="text-sm text-gray-500 font-medium">Ubah produk atau layanan yang tercatat.</p>
                            </div>
                            <div class="hidden lg:grid grid-cols-12 gap-6 w-full max-w-[calc(100%-300px)]">
                                <div
                                    class="col-span-12 lg:col-span-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">
                                    Produk / Layanan</div>
                                <div
                                    class="col-span-6 lg:col-span-2 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">
                                    HPP (Modal)</div>
                                <div
                                    class="col-span-6 lg:col-span-2 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">
                                    Harga Jual</div>
                                <div
                                    class="col-span-6 lg:col-span-2 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">
                                    Jumlah & Satuan</div>
                                <div
                                    class="col-span-6 lg:col-span-2 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-right">
                                    Subtotal</div>
                            </div>
                        </div>

                        <div id="items-container" class="space-y-6">
                            <!-- Items will be added here as cards -->
                        </div>

                        <button type="button" onclick="addItem(null, true)"
                            class="group mt-4 inline-flex items-center justify-center w-full md:w-auto px-6 md:px-10 py-4 md:py-5 bg-white border-2 border-dashed border-indigo-200 rounded-[1.5rem] md:rounded-[2rem] text-base md:text-lg font-black text-indigo-600 hover:border-indigo-500 hover:bg-indigo-50/50 transition-all duration-300">
                            <div
                                class="mr-3 p-1 rounded-full bg-indigo-100 group-hover:bg-indigo-600 group-hover:text-white transition-all">
                                <svg class="h-5 w-5 md:h-6 md:w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </div>
                            Tambah Item Baru
                        </button>
                    </div>

                    <!-- Totals Section -->
                    <div class="flex justify-end">
                        <div
                            class="w-full lg:w-3/5 bg-gradient-to-br from-indigo-50 to-indigo-100/50 rounded-[2rem] md:rounded-[3rem] p-5 md:p-10 space-y-4 md:space-y-6 border border-indigo-100/50">
                            <!-- Subtotal -->
                            <div class="flex justify-between items-center px-4">
                                <span class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Subtotal</span>
                                <span class="text-xl md:text-2xl font-black text-gray-900">Rp <span
                                        id="subtotal">0</span></span>
                            </div>

                            <!-- Discount -->
                            <div
                                class="flex justify-between items-center bg-white p-6 rounded-[2rem] shadow-sm ring-1 ring-indigo-500/10">
                                <label for="discount"
                                    class="text-xs font-black text-indigo-600 uppercase tracking-[0.2em]">Diskon Khusus
                                    (Rp)</label>
                                <div class="relative w-48">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <span class="text-indigo-300 font-black">Rp</span>
                                    </div>
                                    <input type="number" name="discount" id="discount"
                                        value="{{ old('discount', $invoice->discount) }}"
                                        class="block w-full rounded-xl border-none bg-indigo-50/50 py-3 pl-12 pr-4 text-right text-indigo-900 font-extrabold focus:ring-4 focus:ring-indigo-500/20 transition-all"
                                        min="0" step="0.01" oninput="calculateGrandTotal()">
                                </div>
                            </div>

                            <div class="border-t border-indigo-200/50 pt-6">
                                <!-- Grand Total -->
                                <div class="flex justify-between items-center px-4">
                                    <div class="space-y-1">
                                        <p class="text-xs font-black text-indigo-600 uppercase tracking-[0.2em]">Total
                                            Akhir</p>
                                        <p class="text-[10px] text-gray-400 font-bold italic">Sudah termasuk diskon</p>
                                    </div>
                                    <div class="text-4xl sm:text-5xl font-black text-indigo-600 tracking-tight">
                                        Rp <span id="grand-total">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div
                        class="flex flex-col-reverse sm:flex-row items-center justify-end gap-6 pt-10 border-t border-gray-100">
                        <a href="{{ route('invoices.index', request()->query()) }}"
                            class="w-full md:w-auto text-center px-8 md:px-10 py-4 md:py-5 text-base md:text-lg font-black text-gray-400 hover:text-gray-900 bg-gray-50 hover:bg-gray-100 rounded-[1.5rem] md:rounded-[2rem] transition-all duration-300">
                            Batal
                        </a>
                        <button type="submit"
                            class="w-full md:w-auto px-12 md:px-16 py-4 md:py-5 text-lg md:text-xl font-black text-white bg-indigo-600 rounded-[1.5rem] md:rounded-[2rem] shadow-[0_20px_50px_rgba(79,70,229,0.3)] hover:bg-indigo-700 hover:shadow-[0_20px_50px_rgba(79,70,229,0.4)] transition-all duration-300 transform hover:scale-105 active:scale-95">
                            Perbarui Invoice
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .select2-container--default .select2-selection--single {
            border-radius: 1.25rem !important;
            border: 1px solid #e5e7eb !important;
            height: 60px !important;
            display: flex !important;
            align-items: center !important;
            padding-left: 12px !important;
            transition: all 0.3s !important;
            background-color: white !important;
            font-weight: 700 !important;
        }

        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: transparent !important;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1) !important;
            ring: 1px solid #6366f1 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 58px !important;
            color: #111827 !important;
            font-size: 0.875rem !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 58px !important;
        }

        .item-card {
            animation: slideIn 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #c7d2fe;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #818cf8;
        }
    </style>

    <script>
        let itemIndex = 0;
        const products = @json($products);
        const existingItems = @json(old('items', $invoice->items));

        // Format number to Indonesian Currency without decimals
        function formatCurrency(num) {
            if (num === '' || num === null || num === undefined) return '0';
            return new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(num);
        }

        function addItem(existingItem = null, shouldFocus = false) {
            const container = document.getElementById('items-container');
            const itemDiv = document.createElement('div');
            itemDiv.className =
                "item-card bg-white p-4 md:p-8 rounded-[1.5rem] md:rounded-[2.5rem] border border-gray-100 shadow-sm hover:shadow-[0_20px_40px_rgba(0,0,0,0.03)] hover:border-indigo-200 transition-all duration-300 relative group mb-4 md:mb-6";

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

            // Calculate Total
            const total = price * quantity;
            const displayTotal = formatCurrency(total);
            const description = existingItem ? (existingItem.description || '') : '';

            itemDiv.innerHTML = `
                <button type="button" onclick="removeItem(this)" 
                    class="absolute -top-3 -right-3 bg-white text-red-500 p-3 rounded-full shadow-lg border border-red-50 hover:bg-red-500 hover:text-white transition-all duration-300 focus:outline-none z-10">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <div class="grid grid-cols-12 gap-x-3 gap-y-5 md:gap-6 items-center">
                    <!-- Product Selection -->
                    <div class="col-span-12 lg:col-span-4">
                        <label class="block lg:hidden text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Produk / Layanan</label>
                        <select id="${selectId}" name="items[${itemIndex}][product_id]" class="product-select block w-full" required>
                            ${productOptions}
                        </select>
                    </div>

                    <!-- Purchase Price (HPP) -->
                    <div class="col-span-6 lg:col-span-2">
                        <label class="block lg:hidden text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 text-center">HPP (Modal)</label>
                        <div class="relative group/input">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="text-indigo-300 text-xs font-black">Rp</span>
                            </div>
                            <input type="number" name="items[${itemIndex}][purchase_price]" value="${purchase_price || ''}"
                                class="purchase-price-input block w-full rounded-xl md:rounded-2xl border-none bg-gray-50/50 py-3 md:py-4 pl-10 pr-4 text-gray-900 focus:ring-4 focus:ring-indigo-500/10 focus:outline-none transition-all duration-300 text-sm font-bold text-center" 
                                step="any">
                        </div>
                    </div>

                    <!-- Selling Price -->
                    <div class="col-span-6 lg:col-span-2">
                        <label class="block lg:hidden text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 text-center">Harga Jual</label>
                        <div class="relative group/input">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="text-indigo-300 text-xs font-black">Rp</span>
                            </div>
                            <input type="number" name="items[${itemIndex}][price]" value="${price || ''}"
                                class="price-input block w-full rounded-xl md:rounded-2xl border-none bg-gray-50/50 py-3 md:py-4 pl-10 pr-4 text-gray-900 focus:ring-4 focus:ring-indigo-500/10 focus:outline-none transition-all duration-300 text-sm font-bold text-center" 
                                step="any" onchange="updateTotal(this)">
                        </div>
                    </div>

                    <!-- Quantity & Unit -->
                    <div class="col-span-7 lg:col-span-2">
                        <label class="block lg:hidden text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 text-center">Jumlah & Satuan</label>
                        <div class="flex items-center gap-1 md:gap-2 bg-gray-50/80 p-1 md:p-1.5 rounded-xl md:rounded-2xl">
                            <input type="number" name="items[${itemIndex}][quantity]" value="${quantity}"
                                class="quantity-input block w-full bg-white rounded-lg md:rounded-xl border-none py-2 text-center text-gray-900 focus:outline-none text-sm font-black" 
                                min="0.01" step="any" onchange="updateTotal(this)" required>
                            <input type="text" name="items[${itemIndex}][unit]" value="${unit}"
                                class="unit-input block w-14 md:w-20 rounded-lg md:rounded-xl border-none bg-indigo-600/10 py-2 text-center text-[10px] font-black text-indigo-600" 
                                placeholder="STN">
                        </div>
                    </div>

                    <!-- Subtotal -->
                    <div class="col-span-5 lg:col-span-2 text-right">
                        <label class="block lg:hidden text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Subtotal</label>
                        <div class="flex flex-col justify-center h-full">
                            <span class="hidden md:block text-[10px] font-black text-indigo-400 uppercase tracking-widest leading-none">Subtotal</span>
                            <div class="flex items-center justify-end gap-1">
                                <span class="text-xs font-bold text-gray-400">Rp</span>
                                <input type="text" value="${displayTotal}"
                                    class="total-input w-full bg-transparent border-none p-0 text-right text-base md:text-lg font-black text-gray-900 focus:ring-0 cursor-default" 
                                    readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Description (Full width) -->
                    <div class="col-span-12 pt-4 border-t border-gray-50 mt-1 md:mt-2">
                        <div class="flex items-start gap-3">
                            <div class="p-2 rounded-lg bg-gray-50 text-gray-400">
                                <i class="fas fa-edit text-[10px]"></i>
                            </div>
                            <textarea name="items[${itemIndex}][description]" rows="1" 
                                class="block w-full bg-transparent border-none p-0 text-xs md:text-sm text-gray-500 font-medium placeholder-gray-300 focus:ring-0"
                                placeholder="Klik untuk tambah catatan item...">${description}</textarea>
                        </div>
                    </div>
                </div>
            `;

            container.appendChild(itemDiv);

            // Initialize Select2
            $(`#${selectId}`).select2({
                placeholder: "Cari produk...",
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

            if (shouldFocus) {
                setTimeout(() => {
                    $(`#${selectId}`).select2('open');
                }, 100);
            }

            // Handle Select2 events
            $(`#${selectId}`).on('select2:select', function(e) {
                const data = e.params.data;
                const card = this.closest('.item-card');
                const priceInput = card.querySelector('.price-input');
                const unitInput = card.querySelector('.unit-input');

                if (data.newTag) {
                    priceInput.value = '';
                    card.querySelector('.purchase-price-input').value = '';
                    unitInput.value = '';
                    unitInput.placeholder = 'STN';
                } else {
                    const selectedOption = this.options[this.selectedIndex];
                    priceInput.value = selectedOption.getAttribute('data-price');
                    card.querySelector('.purchase-price-input').value = selectedOption.getAttribute(
                        'data-purchase-price');
                    unitInput.value = selectedOption.getAttribute('data-unit');
                }
                updateTotal(this);
            });

            $(`#${selectId}`).on('select2:clear', function() {
                const card = this.closest('.item-card');
                card.querySelector('.price-input').value = '';
                card.querySelector('.unit-input').value = '';
                card.querySelector('.total-input').value = '0';
                calculateGrandTotal();
            });

            itemIndex++;
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
            let subtotal = 0;
            const cards = document.querySelectorAll('.item-card');

            cards.forEach(card => {
                const price = parseFloat(card.querySelector('.price-input').value) || 0;
                const quantity = parseFloat(card.querySelector('.quantity-input').value) || 0;
                subtotal += (price * quantity);
            });

            const discountInput = document.getElementById('discount');
            let discount = parseFloat(discountInput.value) || 0;
            const grandTotal = subtotal - discount;

            document.getElementById('subtotal').innerText = formatCurrency(subtotal);
            document.getElementById('grand-total').innerText = formatCurrency(grandTotal < 0 ? 0 : grandTotal);
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

            // Auto-update Invoice Number Logic
            const invoiceInput = document.getElementById('invoice_number');
            const invoiceDisplay = document.getElementById('invoice_number_display');
            const dateInput = document.getElementById('date');
            const typeSelect = document.getElementById('tipe');

            function updateInvoiceNumber() {
                const currentDate = dateInput.value;
                const currentType = typeSelect.value;
                let currentNumber = invoiceInput.value;

                if (!currentDate || !currentNumber) return;

                let parts = currentNumber.split('-');
                if (parts.length < 3) return;

                const dateComponents = currentDate.split('-');
                if (dateComponents.length === 3) {
                    parts[1] = dateComponents.join('');
                }

                if (currentType) {
                    if (parts.length >= 4) {
                        parts[parts.length - 1] = currentType;
                    }
                }

                const newNumber = parts.join('-');
                invoiceInput.value = newNumber;
                if (invoiceDisplay) invoiceDisplay.textContent = newNumber;
            }

            dateInput.addEventListener('change', updateInvoiceNumber);
            typeSelect.addEventListener('change', updateInvoiceNumber);
        });
    </script>
@endsection
