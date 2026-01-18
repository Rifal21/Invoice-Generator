@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto lg:h-[calc(100vh-10rem)] pb-24 lg:pb-0">
        <!-- Header & Tab Switcher (Mobile Only) -->
        <div class="lg:hidden flex flex-col gap-4 mb-6 px-4 pt-2">
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-black text-gray-900">Kasir Pintar</h1>
                <div class="bg-indigo-600 px-3 py-1 rounded-full">
                    <span id="mobileCount" class="text-white text-xs font-black">0 Item</span>
                </div>
            </div>

            <!-- Segmented Control for Mobile -->
            <div class="flex p-1 bg-gray-100 rounded-2xl">
                <button onclick="switchTab('products')" id="tab-products-btn"
                    class="flex-1 py-2.5 text-sm font-black rounded-xl transition-all bg-white shadow-sm text-indigo-600">
                    Pilih Barang
                </button>
                <button onclick="switchTab('cart')" id="tab-cart-btn"
                    class="flex-1 py-2.5 text-sm font-black rounded-xl transition-all text-gray-500">
                    Keranjang
                </button>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-8 h-full">

            <!-- Left Side: Product Exploration -->
            <div id="product-view" class="lg:w-2/3 flex flex-col h-full overflow-hidden">

                <div class="hidden lg:block mb-8">
                    <h1 class="text-4xl font-black text-gray-900 tracking-tight">Kasir Pintar</h1>
                    <p class="text-gray-500 font-medium text-lg mt-1">Sistem penjualan cepat & efisien.</p>
                </div>

                <!-- Search & Filters -->
                <div class="px-4 lg:px-0 space-y-6">
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                            <svg class="h-6 w-6 text-gray-400 group-focus-within:text-indigo-600 transition-colors"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" id="productSearch" placeholder="Cari barang atau kategori..."
                            class="block w-full pl-14 pr-6 py-4 lg:py-5 border-2 border-gray-100 rounded-3xl bg-white focus:bg-white focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all text-lg font-bold placeholder:text-gray-300">
                    </div>

                    <!-- Category Chips -->
                    <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-hide">
                        <button onclick="filterCategory('all', this)"
                            class="cat-chip whitespace-nowrap px-6 py-2.5 rounded-full bg-indigo-600 text-white font-black text-sm shadow-lg shadow-indigo-100 transition-all">
                            Semua
                        </button>
                        @foreach ($categories as $cat)
                            <button onclick="filterCategory('{{ $cat->name }}', this)"
                                class="cat-chip whitespace-nowrap px-6 py-2.5 rounded-full bg-white text-gray-500 border-2 border-gray-50 font-black text-sm hover:border-indigo-100 transition-all">
                                {{ $cat->name }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Product Grid -->
                <div class="flex-1 overflow-y-auto px-4 lg:px-0 mt-6 lg:mt-8 scrollbar-premium" id="productList">
                    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4 pb-10">
                        @foreach ($products as $product)
                            <div class="product-card group bg-white p-4 rounded-[2rem] shadow-sm border-2 border-transparent hover:border-indigo-500 cursor-pointer transition-all hover:shadow-2xl active:scale-95 relative overflow-hidden"
                                data-id="{{ $product->id }}" data-name="{{ $product->name }}"
                                data-price="{{ $product->price }}" data-category="{{ $product->category->name }}"
                                data-unit="{{ $product->unit }}" data-stock="{{ $product->stock }}"
                                onclick="addToCart(this)">

                                <div class="absolute top-0 right-0 p-3">
                                    <div
                                        class="bg-indigo-50 text-indigo-600 p-1.5 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                    </div>
                                </div>

                                <div class="flex flex-col h-full">
                                    <div class="mb-4">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full bg-gray-50 text-[10px] font-black uppercase text-gray-400 border border-gray-100">
                                            {{ $product->category->name }}
                                        </span>
                                    </div>

                                    <h3
                                        class="font-black text-gray-900 group-hover:text-indigo-600 transition-colors uppercase leading-tight text-sm line-clamp-2 min-h-[2.5rem]">
                                        {{ $product->name }}
                                    </h3>

                                    <div class="mt-auto pt-4 flex items-end justify-between">
                                        <div>
                                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider mb-0.5">
                                                Stok: {{ (float) $product->stock }}</p>
                                            <p class="text-lg font-black text-indigo-600 tracking-tighter">
                                                Rp{{ number_format($product->price, 0, ',', '.') }}
                                            </p>
                                        </div>
                                        @if ($product->stock <= 5)
                                            <div class="animate-pulse">
                                                <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Right Side: Smart Cart -->
            <div id="cart-view"
                class="hidden lg:flex lg:w-1/3 flex-col bg-white rounded-[2rem] lg:rounded-[3rem] shadow-2xl border border-gray-100 overflow-hidden h-full">
                <!-- Cart Header -->
                <div class="p-5 lg:p-8 bg-indigo-600">
                    <div class="flex justify-between items-center mb-4 lg:mb-6">
                        <div class="flex items-center gap-3 lg:gap-4">
                            <div class="p-2 lg:p-3 bg-white/20 rounded-xl lg:rounded-2xl backdrop-blur-md">
                                <svg class="h-5 w-5 lg:h-7 lg:w-7 text-white" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="font-black text-white text-lg lg:text-2xl uppercase tracking-tighter">Ringkasan
                                </h2>
                                <p
                                    class="text-indigo-100 text-[10px] font-bold opacity-80 uppercase tracking-widest hidden lg:block">
                                    Detail Pesanan</p>
                            </div>
                        </div>
                        <button onclick="clearCart()"
                            class="p-2 lg:p-3 bg-red-500 text-white rounded-xl lg:rounded-2xl hover:bg-red-600 transition-all shadow-lg active:scale-90"
                            title="Kosongkan Keranjang">
                            <svg class="h-4 w-4 lg:h-5 lg:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>

                    <!-- Order Configuration -->
                    <div class="space-y-3 lg:space-y-4">
                        <div class="relative group">
                            <select id="customerSelect" onchange="toggleCustomerInput()"
                                class="w-full pl-4 lg:pl-5 pr-10 py-3 lg:py-4 bg-white/10 border-2 border-white/10 rounded-xl lg:rounded-2xl text-white font-bold text-xs lg:text-sm appearance-none focus:border-white/30 focus:ring-0 transition-all cursor-pointer">
                                <option value="SPPG DAPUR CERDAS" class="text-gray-900">SPPG DAPUR CERDAS</option>
                                <option value="SPPG DAPUR KABUNGAH" class="text-gray-900">SPPG DAPUR KABUNGAH</option>
                                <option value="SPPG DAPUR SAHABAT" class="text-gray-900">SPPG DAPUR SAHABAT</option>
                                <option value="LAINNYA" class="text-gray-900">LAINNYA (ISI SENDIRI)</option>
                            </select>
                            <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-white/60">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </div>
                        </div>

                        <div id="customCustomerDiv" class="hidden">
                            <input type="text" id="customCustomerName" placeholder="Nama Pelanggan Baru..."
                                class="w-full px-4 lg:px-5 py-3 lg:py-4 bg-white/10 border-2 border-white/10 rounded-xl lg:rounded-2xl text-white font-black text-xs lg:text-sm placeholder:text-white/40 focus:border-white/30 focus:ring-0 transition-all">
                        </div>

                        <div class="relative group">
                            <select id="orderType"
                                class="w-full pl-4 lg:pl-5 pr-10 py-3 lg:py-4 bg-white/10 border-2 border-white/10 rounded-xl lg:rounded-2xl text-white font-bold text-xs lg:text-sm appearance-none focus:border-white/30 focus:ring-0 transition-all cursor-pointer">
                                <option value="BSH" class="text-gray-900">BASAHAN SISWA</option>
                                <option value="KR" class="text-gray-900">KERINGAN SISWA</option>
                                <option value="KRBSBM" class="text-gray-900">KERINGAN BUMIL BUSUI (B3)</option>
                                <option value="OPR" class="text-gray-900">OPERASIONAL</option>
                            </select>
                            <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-white/60">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cart Items -->
                <div class="flex-1 overflow-y-auto p-4 lg:p-8 space-y-3 lg:space-y-4 bg-gray-50/30 scrollbar-premium"
                    id="cartItems">
                    <div id="emptyCart" class="flex flex-col items-center justify-center h-full text-center py-12">
                        <div class="w-32 h-32 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                            <svg class="h-16 w-16 text-gray-200" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <p class="font-black text-gray-300 uppercase tracking-widest text-sm">Keranjang Kosong</p>
                    </div>
                </div>

                <!-- Bottom Summary -->
                <div class="p-6 lg:p-8 border-t border-gray-100 bg-white shadow-[0_-20px_40px_rgba(0,0,0,0.02)]">
                    <div class="flex items-center justify-between mb-4 lg:mb-8">
                        <div>
                            <p
                                class="text-[8px] lg:text-[10px] font-black text-gray-400 uppercase tracking-widest mb-0.5 lg:mb-1">
                                Total Transaksi</p>
                            <h3 id="cartTotal" class="text-2xl lg:text-4xl font-black text-gray-900 tracking-tighter">Rp0
                            </h3>
                        </div>
                        <div class="text-right">
                            <p id="totalItemsText"
                                class="text-[10px] lg:text-sm font-black text-indigo-600 bg-indigo-50 px-3 lg:px-4 py-1 rounded-full inline-block">
                                0 Item</p>
                        </div>
                    </div>

                    <button onclick="checkout()" id="checkoutBtn" disabled
                        class="w-full bg-indigo-600 text-white rounded-2xl lg:rounded-[2rem] py-4 lg:py-5 font-black text-sm lg:text-lg shadow-2xl shadow-indigo-200 hover:bg-indigo-700 active:scale-[0.98] disabled:bg-gray-100 disabled:text-gray-400 disabled:shadow-none transition-all uppercase tracking-widest">
                        Bayar Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Templates -->
    <template id="cartItemTemplate">
        <div
            class="cart-item group bg-white p-3 lg:p-5 rounded-2xl lg:rounded-3xl shadow-sm border border-gray-100 hover:shadow-md transition-all">
            <div class="flex items-start gap-3 lg:gap-4">
                <div class="flex-1">
                    <div class="flex justify-between items-start mb-1 lg:mb-2">
                        <h4
                            class="font-black text-gray-900 item-name uppercase leading-tight text-[11px] lg:text-xs line-clamp-1">
                        </h4>
                        <button onclick="removeFromCart(this)"
                            class="text-gray-300 hover:text-red-500 transition-colors p-0.5">
                            <svg class="h-3.5 w-3.5 lg:h-4 lg:w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="flex items-center justify-between mt-2 lg:mt-4">
                        <div
                            class="flex items-center gap-1 bg-gray-50 rounded-xl lg:rounded-2xl p-0.5 lg:p-1 border border-gray-100">
                            <button onclick="changeQty(this, -1)"
                                class="w-6 h-6 lg:w-8 lg:h-8 flex items-center justify-center rounded-lg lg:rounded-xl bg-white text-gray-400 font-black hover:text-red-500 shadow-sm transition-all text-xs lg:text-base">-</button>
                            <input type="number" step="0.01" value="1"
                                class="w-8 lg:w-12 border-0 text-center font-black text-gray-900 p-0 text-[10px] lg:text-xs focus:ring-0 item-qty bg-transparent"
                                oninput="updateQtyManually(this)">
                            <button onclick="changeQty(this, 1)"
                                class="w-6 h-6 lg:w-8 lg:h-8 flex items-center justify-center rounded-lg lg:rounded-xl bg-white text-indigo-600 font-black hover:bg-indigo-50 shadow-sm transition-all text-xs lg:text-base">+</button>
                        </div>
                        <div class="text-right">
                            <p class="text-[8px] lg:text-[9px] text-gray-400 font-bold uppercase mb-0 item-price-label">
                            </p>
                            <p class="font-black text-indigo-600 item-total text-[11px] lg:text-sm tracking-tighter"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <style>
        .scrollbar-premium::-webkit-scrollbar {
            width: 5px;
        }

        .scrollbar-premium::-webkit-scrollbar-track {
            background: transparent;
        }

        .scrollbar-premium::-webkit-scrollbar-thumb {
            background: #e5e7eb;
            border-radius: 20px;
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        @keyframes bounce-in {
            0% {
                transform: scale(0.3);
                opacity: 0;
            }

            70% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .cart-item {
            animation: bounce-in 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @media (max-width: 1023px) {
            #cart-view.active {
                display: flex !important;
                height: 100%;
            }

            #product-view.hidden {
                display: none !important;
            }
        }
    </style>

    <script>
        let cart = [];

        function switchTab(tab) {
            const prodView = document.getElementById('product-view');
            const cartView = document.getElementById('cart-view');
            const tabProdBtn = document.getElementById('tab-products-btn');
            const tabCartBtn = document.getElementById('tab-cart-btn');

            if (tab === 'products') {
                prodView.classList.remove('hidden');
                cartView.classList.remove('active');
                tabProdBtn.classList.add('bg-white', 'shadow-sm', 'text-indigo-600');
                tabProdBtn.classList.remove('text-gray-500');
                tabCartBtn.classList.add('text-gray-500');
                tabCartBtn.classList.remove('bg-white', 'shadow-sm', 'text-indigo-600');
            } else {
                prodView.classList.add('hidden');
                cartView.classList.add('active');
                tabCartBtn.classList.add('bg-white', 'shadow-sm', 'text-indigo-600');
                tabCartBtn.classList.remove('text-gray-500');
                tabProdBtn.classList.add('text-gray-500');
                tabProdBtn.classList.remove('bg-white', 'shadow-sm', 'text-indigo-600');
            }
        }

        function addToCart(element) {
            const id = element.dataset.id;
            const name = element.dataset.name;
            const price = parseFloat(element.dataset.price);
            const unit = element.dataset.unit;
            const stock = parseFloat(element.dataset.stock);

            const existingItem = cart.find(item => item.id === id);
            if (existingItem) {
                if (existingItem.qty + 1 > stock) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Stok tidak cukup'
                    });
                    return;
                }
                existingItem.qty += 1;
            } else {
                if (1 > stock) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Stok tidak cukup'
                    });
                    return;
                }
                cart.push({
                    id,
                    name,
                    price,
                    unit,
                    qty: 1,
                    stock
                });
            }

            renderCart();

            // Visual feedback
            element.classList.add('ring-8', 'ring-indigo-500/10');
            setTimeout(() => element.classList.remove('ring-8', 'ring-indigo-500/10'), 200);
        }

        function changeQty(btn, delta) {
            const id = btn.closest('.cart-item').dataset.id;
            const item = cart.find(i => i.id === id);
            let newVal = item.qty + delta;

            if (newVal <= 0) {
                cart = cart.filter(i => i.id !== id);
            } else if (newVal > item.stock) {
                Toast.fire({
                    icon: 'error',
                    title: 'Stok tidak mencukupi'
                });
                return;
            } else {
                item.qty = newVal;
            }
            renderCart();
        }

        function updateQtyManually(input) {
            const id = input.closest('.cart-item').dataset.id;
            const item = cart.find(i => i.id === id);
            let newVal = parseFloat(input.value);

            if (isNaN(newVal)) return;

            if (newVal < 0) {
                newVal = 0;
            } else if (newVal > item.stock) {
                Toast.fire({
                    icon: 'error',
                    title: 'Stok tidak cukup'
                });
                newVal = item.stock;
                input.value = newVal;
            }

            item.qty = newVal;
            updateTotalsOnly();
        }

        function updateTotalsOnly() {
            let total = 0;
            let count = 0;
            cart.forEach(item => {
                const subtotal = item.price * item.qty;
                total += subtotal;
                count += 1;
                const row = document.querySelector(`.cart-item[data-id="${item.id}"]`);
                if (row) {
                    row.querySelector('.item-total').innerText = 'Rp' + subtotal.toLocaleString('id-ID');
                }
            });
            document.getElementById('cartTotal').innerText = 'Rp' + total.toLocaleString('id-ID');
            document.getElementById('totalItemsText').innerText = count + ' Item';
            document.getElementById('mobileCount').innerText = count + ' Item';
        }

        function removeFromCart(btn) {
            const id = btn.closest('.cart-item').dataset.id;
            cart = cart.filter(item => item.id !== id);
            renderCart();
        }

        function clearCart() {
            if (cart.length === 0) return;
            Swal.fire({
                title: 'Kosongkan?',
                text: "Semua item di keranjang akan dihapus.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#f3f4f6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-[2rem]',
                    confirmButton: 'rounded-xl',
                    cancelButton: 'rounded-xl text-gray-900'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    cart = [];
                    renderCart();
                }
            })
        }

        function renderCart() {
            const container = document.getElementById('cartItems');
            const emptyMsg = document.getElementById('emptyCart');
            const template = document.getElementById('cartItemTemplate');

            container.innerHTML = '';

            if (cart.length === 0) {
                container.appendChild(emptyMsg);
                document.getElementById('checkoutBtn').disabled = true;
                updateTotalsOnly();
            } else {
                cart.forEach(item => {
                    const subtotal = item.price * item.qty;
                    const clone = template.content.cloneNode(true);
                    const row = clone.querySelector('.cart-item');
                    row.dataset.id = item.id;
                    clone.querySelector('.item-name').innerText = item.name;
                    clone.querySelector('.item-qty').value = item.qty;
                    clone.querySelector('.item-price-label').innerText =
                        `@ Rp${item.price.toLocaleString('id-ID')}`;
                    clone.querySelector('.item-total').innerText = 'Rp' + subtotal.toLocaleString('id-ID');
                    container.appendChild(clone);
                });
                document.getElementById('checkoutBtn').disabled = false;
                updateTotalsOnly();
            }
        }

        function filterCategory(cat, btn) {
            // UI Update
            document.querySelectorAll('.cat-chip').forEach(c => {
                c.classList.remove('bg-indigo-600', 'text-white', 'shadow-lg', 'shadow-indigo-100');
                c.classList.add('bg-white', 'text-gray-500', 'border-gray-50');
            });
            btn.classList.add('bg-indigo-600', 'text-white', 'shadow-lg', 'shadow-indigo-100');
            btn.classList.remove('bg-white', 'text-gray-500', 'border-gray-50');

            // Logic
            document.querySelectorAll('.product-card').forEach(card => {
                if (cat === 'all' || card.dataset.category === cat) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function toggleCustomerInput() {
            const select = document.getElementById('customerSelect');
            const customDiv = document.getElementById('customCustomerDiv');
            if (select.value === 'LAINNYA') {
                customDiv.classList.remove('hidden');
                document.getElementById('customCustomerName').focus();
            } else {
                customDiv.classList.add('hidden');
            }
        }

        async function checkout() {
            const customerSelect = document.getElementById('customerSelect');
            let customerName = customerSelect.value;
            if (customerName === 'LAINNYA') {
                customerName = document.getElementById('customCustomerName').value || 'Umum';
            }

            const orderType = document.getElementById('orderType').value;
            const checkoutBtn = document.getElementById('checkoutBtn');

            checkoutBtn.disabled = true;
            checkoutBtn.innerHTML =
                '<span class="flex items-center justify-center gap-3"><svg class="animate-spin h-6 w-6 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> MEMPROSES...</span>';

            try {
                const response = await fetch('{{ route('pos.checkout') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        customer_name: customerName,
                        order_type: orderType,
                        items: cart.map(item => ({
                            product_id: item.id,
                            quantity: item.qty
                        }))
                    })
                });

                const result = await response.json();

                if (result.success) {
                    Swal.fire({
                        title: 'Transaksi Berhasil!',
                        text: 'Pesanan telah diproses ke sistem.',
                        icon: 'success',
                        confirmButtonText: 'LIHAT INVOICE',
                        confirmButtonColor: '#4f46e5',
                        customClass: {
                            popup: 'rounded-[2.5rem]',
                            confirmButton: 'rounded-2xl font-black py-4 px-8'
                        }
                    }).then(() => {
                        window.location.href = result.redirect;
                    });
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                Swal.fire({
                    title: 'Opps!',
                    text: error.message,
                    icon: 'error',
                    confirmButtonColor: '#4f46e5',
                    customClass: {
                        popup: 'rounded-[2.5rem]',
                        confirmButton: 'rounded-2xl font-black'
                    }
                });
                checkoutBtn.disabled = false;
                checkoutBtn.innerText = 'Bayar Sekarang';
            }
        }

        document.getElementById('productSearch').addEventListener('input', function(e) {
            const search = e.target.value.toLowerCase();
            document.querySelectorAll('.product-card').forEach(card => {
                const name = card.dataset.name.toLowerCase();
                const category = card.dataset.category.toLowerCase();
                if (name.includes(search) || category.includes(search)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    </script>
@endsection
