@extends('layouts.app')

@section('title', 'Data Produk')

@section('content')
    <div class=" mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-10">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-10 gap-6">
            <div class="flex items-center gap-5">
                <div
                    class="h-16 w-16 flex items-center justify-center bg-indigo-600 rounded-2xl shadow-xl shadow-indigo-100">
                    <i class="fas fa-boxes text-2xl text-white"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">Katalog Produk</h1>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="flex h-2 w-2 rounded-full bg-green-500"></span>
                        <p class="text-sm text-gray-500 font-medium">Sistem Inventaris Real-time</p>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('products.create') }}"
                    class="inline-flex items-center justify-center px-6 py-3.5 bg-indigo-600 rounded-xl text-white font-bold shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all hover:-translate-y-0.5 active:scale-95">
                    <i class="fas fa-plus-circle mr-2.5"></i>
                    Tambah Item
                </a>
            </div>
        </div>

        <!-- Toolbar Section -->
        <div class="bg-white rounded-3xl p-4 shadow-sm border border-gray-100 mb-2">
            <div class="flex flex-col lg:flex-row gap-4">
                <!-- Search -->
                <div class="flex-1 relative group">
                    <div
                        class="absolute inset-y-0 left-4 flex items-center pointer-events-none transition-colors group-focus-within:text-indigo-500 text-gray-400">
                        <i class="fas fa-search"></i>
                    </div>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        placeholder="Nama produk, barcode, atau deskripsi..."
                        class="block w-full pl-11 pr-4 py-3 bg-gray-50 border-transparent focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100 rounded-2xl text-sm font-medium transition-all">
                </div>

                <!-- Category -->
                <div class="lg:w-56">
                    <select name="category_id" id="category_id_filter"
                        onchange="performSearch(document.getElementById('search').value)"
                        class="block w-full py-3 pl-4 pr-10 bg-gray-50 border-transparent focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100 rounded-2xl text-sm font-bold text-gray-700 transition-all cursor-pointer appearance-none">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Global Actions -->
                <div class="flex items-center gap-2">
                    <button type="button" onclick="initiateCloudBackup()" id="btn-cloud-backup"
                        class="h-11 w-11 flex items-center justify-center bg-indigo-50 text-indigo-600 rounded-xl hover:bg-indigo-600 hover:text-white transition-all shadow-sm"
                        title="Backup Cloud">
                        <i id="backup-spinner" class="fas fa-spinner fa-spin text-xs" style="display: none;"></i>
                        <i id="backup-drive-icon" class="fab fa-google-drive"></i>
                        <span id="backup-btn-text" style="display: none;">Backup Drive</span>
                    </button>

                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false" type="button"
                            class="h-11 px-4 flex items-center gap-2 bg-amber-50 text-amber-600 rounded-xl hover:bg-amber-100 transition-all font-bold text-xs uppercase tracking-widest">
                            <i class="fas fa-file-export"></i> Ekspor
                        </button>
                        <div x-show="open" x-transition
                            class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl z-[100] py-2 border border-gray-100">
                            <a href="{{ route('products.export', ['type' => 'client']) }}"
                                class="block px-4 py-2.5 text-xs font-bold text-gray-600 hover:bg-indigo-50 hover:text-indigo-600">Download
                                Katalog</a>
                            <a href="{{ route('products.export', ['type' => 'internal']) }}"
                                class="block px-4 py-2.5 text-xs font-bold text-gray-600 hover:bg-indigo-50 hover:text-indigo-600">Data
                                Internal</a>
                        </div>
                    </div>

                    <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <label
                            class="h-11 px-4 flex items-center gap-2 bg-green-50 text-green-600 rounded-xl hover:bg-green-100 transition-all font-bold text-xs uppercase tracking-widest cursor-pointer">
                            <i class="fas fa-file-import"></i> Impor
                            <input type="file" name="file" class="hidden" onchange="this.form.submit()">
                        </label>
                    </form>
                </div>
            </div>
        </div>


    </div>

    <form id="product-backup-form" action="{{ route('backup.process') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="type" value="products">
    </form>

    <form id="bulk-delete-form" action="{{ route('products.bulk-delete') }}" method="POST">
        @csrf
        <div class="bg-white shadow-2xl rounded-[2.5rem] overflow-hidden border border-gray-100 hidden md:block">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th scope="col" class="py-6 pl-8 pr-3 text-left w-16">
                                <input type="checkbox" id="select-all-desktop"
                                    class="h-6 w-6 rounded-xl border-gray-200 text-indigo-600 focus:ring-indigo-500 transition-all cursor-pointer">
                            </th>
                            <th scope="col"
                                class="px-3 py-5 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">No
                            </th>
                            <th scope="col"
                                class="px-3 py-5 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                                Informasi Produk</th>
                            <th scope="col"
                                class="px-3 py-5 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                                Kategori & Pemasok</th>
                            <th scope="col"
                                class="px-3 py-5 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                                Harga & HPP</th>
                            <th scope="col"
                                class="px-3 py-5 text-right text-[11px] font-bold text-gray-400 uppercase tracking-wider pr-8">
                                Operasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach ($products as $product)
                            <tr class="hover:bg-gray-50/80 transition-all cursor-pointer group"
                                data-product-id="{{ $product->id }}" onclick="showDetail({{ $product->id }})">
                                <td class="py-5 pl-8 pr-3 w-16">
                                    <input type="checkbox" name="ids[]" value="{{ $product->id }}"
                                        onclick="event.stopPropagation()"
                                        class="product-checkbox h-5 w-5 rounded-lg border-gray-200 text-indigo-600 focus:ring-indigo-500 transition-all cursor-pointer">
                                </td>
                                <td class="whitespace-nowrap px-3 py-5 text-xs font-bold text-gray-400">
                                    #{{ str_pad($loop->iteration + ($products->currentPage() - 1) * $products->perPage(), 2, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-3 py-5">
                                    <div class="flex items-center gap-4">
                                        <div class="h-14 w-14 flex-shrink-0 relative">
                                            @if ($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}"
                                                    class="h-14 w-14 rounded-2xl object-cover border border-gray-100 shadow-sm transition-transform group-hover:scale-105"
                                                    onclick="event.stopPropagation(); openLightbox(this.src)">
                                            @else
                                                <div
                                                    class="h-14 w-14 rounded-2xl bg-gray-50 flex items-center justify-center text-gray-300 border border-dashed border-gray-100">
                                                    <i class="fas fa-image text-xl"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <div
                                                class="text-sm font-black text-gray-900 leading-tight mb-1 truncate product-name-display">
                                                {{ $product->name }}</div>
                                            <div
                                                class="text-[11px] text-gray-400 line-clamp-1 italic product-description-display">
                                                {{ $product->description ?: 'Beri deskripsi untuk produk ini...' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-5">
                                    <div class="flex flex-col gap-1.5">
                                        <span
                                            class="inline-flex w-fit px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 text-indigo-600 uppercase tracking-wider border border-indigo-100 category-name-display">
                                            {{ $product->category->name }}
                                        </span>
                                        <span
                                            class="inline-flex w-fit px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-gray-50 text-gray-500 uppercase tracking-wider border border-gray-100 supplier-name-display">
                                            {{ $product->supplier ? $product->supplier->name : 'UMUM' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-5">
                                    <div class="flex flex-col">
                                        <div class="text-sm font-black text-indigo-600">Rp
                                            <span
                                                class="product-price-display">{{ number_format($product->price, 0, ',', '.') }}</span>
                                            <span
                                                class="text-[10px] text-gray-400 font-medium ml-1 product-unit-display">/{{ $product->unit }}</span>
                                        </div>
                                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">
                                            HPP: Rp <span
                                                class="product-purchase-price-display">{{ number_format($product->purchase_price, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap py-5 pl-3 pr-8 text-right w-32">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('products.edit', array_merge(['product' => $product->id], request()->query())) }}"
                                            onclick="event.stopPropagation()"
                                            class="h-9 w-9 flex items-center justify-center rounded-xl bg-white text-amber-500 border border-amber-100 shadow-sm hover:bg-amber-500 hover:text-white transition-all"
                                            title="Edit Toolbar">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                        <button type="button"
                                            class="h-9 w-9 flex items-center justify-center rounded-xl bg-white text-red-500 border border-red-100 shadow-sm hover:bg-red-500 hover:text-white transition-all"
                                            onclick="event.stopPropagation(); deleteProduct({{ $product->id }})"
                                            title="Hapus Produk">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Card View -->
        <div id="mobile-products-list" class="md:hidden space-y-4">
            <div class="flex items-center gap-3 px-2 mb-2">
                <input type="checkbox" id="select-all-mobile"
                    class="h-6 w-6 rounded-xl border-gray-300 text-indigo-600 focus:ring-indigo-500 transition-all cursor-pointer">
                <label for="select-all-mobile" class="text-sm font-black text-gray-400 uppercase tracking-widest">Pilih
                    Semua Produk</label>
            </div>
            @foreach ($products as $product)
                <div class="product-card-mobile bg-white rounded-2xl p-4 shadow-sm border border-gray-100 mb-4"
                    onclick="showDetail({{ $product->id }})">
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center gap-2">
                            <input type="checkbox" name="ids[]" value="{{ $product->id }}"
                                onclick="event.stopPropagation()"
                                class="product-checkbox h-5 w-5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            @if ($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}"
                                    class="h-14 w-14 rounded-xl object-cover border border-gray-50"
                                    onclick="event.stopPropagation(); openLightbox(this.src)">
                            @else
                                <div
                                    class="h-14 w-14 rounded-xl bg-gray-50 flex items-center justify-center text-gray-300">
                                    <i class="fas fa-image"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start mb-1">
                                <h3
                                    class="text-sm font-bold text-gray-900 leading-tight truncate pr-2 mobile-product-name">
                                    {{ $product->name }}</h3>
                                <span
                                    class="text-[10px] font-bold text-gray-300 whitespace-nowrap">#{{ $loop->iteration + ($products->currentPage() - 1) * $products->perPage() }}</span>
                            </div>
                            <div class="flex flex-wrap gap-1.5 mb-2">
                                <span
                                    class="text-[9px] font-black bg-indigo-50 text-indigo-600 px-1.5 py-0.5 rounded uppercase tracking-wider mobile-category-name">{{ $product->category->name }}</span>
                                <span
                                    class="text-[9px] font-black bg-gray-50 text-gray-400 px-1.5 py-0.5 rounded uppercase tracking-wider mobile-supplier-name">{{ $product->supplier ? $product->supplier->name : 'UMUM' }}</span>
                            </div>
                            <div class="flex items-baseline gap-2">
                                <span class="text-sm font-black text-indigo-600">Rp
                                    <span
                                        class="mobile-product-price">{{ number_format($product->price, 0, ',', '.') }}</span></span>
                                <span class="text-[10px] text-gray-400">/<span
                                        class="mobile-product-unit">{{ $product->unit }}</span></span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-2">
                            <a href="{{ route('products.edit', array_merge(['product' => $product->id], request()->query())) }}"
                                onclick="event.stopPropagation()"
                                class="h-8 w-8 flex items-center justify-center rounded-lg bg-amber-50 text-amber-600 border border-amber-100">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                            <button type="button"
                                class="h-8 w-8 flex items-center justify-center rounded-lg bg-red-50 text-red-600 border border-red-100"
                                onclick="event.stopPropagation(); deleteProduct({{ $product->id }})">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Loading Indicator for Infinite Scroll -->
            <div id="infinite-scroll-loader" class="py-10 transition-opacity duration-300" style="opacity: 0;">
                <div class="flex flex-col items-center gap-3">
                    <div class="w-10 h-10 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin">
                    </div>
                    <span class="text-xs font-black text-gray-400 uppercase tracking-widest">Memuat produk...</span>
                </div>
            </div>
        </div>
    </form>

    <div id="pagination-container" class="mt-8 lg:block">
        {{ $products->links() }}
    </div>

    <!-- Sticky Bulk Action Bar -->
    <div id="bulk-action-bar"
        class="fixed bottom-10 left-1/2 -translate-x-1/2 z-[60] transform scale-0 opacity-0 transition-all duration-500">
        <div
            class="bg-gray-900 border border-gray-800 text-white rounded-[2.5rem] px-10 py-6 shadow-[0_20px_50px_rgba(0,0,0,0.5)] flex items-center gap-10 backdrop-blur-2xl">
            <div class="flex items-center gap-4">
                <div
                    class="h-12 w-12 rounded-2xl bg-indigo-500/10 flex items-center justify-center border border-indigo-500/20">
                    <span id="selected-count" class="text-xl font-black text-indigo-400">0</span>
                </div>
                <div class="flex flex-col">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Item Dipilih</span>
                    <span class="text-xs font-bold text-gray-200">Manajemen Bulk</span>
                </div>
            </div>

            <div class="h-10 w-px bg-white/10"></div>

            <button type="button" onclick="confirmBulkDelete()"
                class="bg-red-600 hover:bg-red-700 text-white px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-[0.2em] transition-all active:scale-95 shadow-lg shadow-red-900/20 flex items-center gap-3 group">
                <i class="fas fa-trash-alt text-xs group-hover:rotate-12 transition-transform"></i>
                Hapus Permanen
            </button>
        </div>
    </div>

    <form id="delete-form" action="" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    @push('scripts')
        <script>
            const bulkActionBar = document.getElementById('bulk-action-bar');
            const selectedCountText = document.getElementById('selected-count');
            const selectAllDesktop = document.getElementById('select-all-desktop');
            const selectAllMobile = document.getElementById('select-all-mobile');

            function updateBulkBar() {
                const checkedCount = document.querySelectorAll('.product-checkbox:checked').length;
                selectedCountText.innerText = checkedCount;

                if (checkedCount > 0) {
                    bulkActionBar.classList.remove('scale-0', 'opacity-0');
                    bulkActionBar.classList.add('scale-100', 'opacity-100');
                } else {
                    bulkActionBar.classList.add('scale-0', 'opacity-0');
                    bulkActionBar.classList.remove('scale-100', 'opacity-100');
                }
            }

            // Use event delegation for dynamically added checkboxes
            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('product-checkbox')) {
                    updateBulkBar();
                }
            });

            if (selectAllDesktop) {
                selectAllDesktop.addEventListener('change', function() {
                    document.querySelectorAll('.product-checkbox').forEach(cb => cb.checked = this.checked);
                    if (selectAllMobile) selectAllMobile.checked = this.checked;
                    updateBulkBar();
                });
            }

            if (selectAllMobile) {
                selectAllMobile.addEventListener('change', function() {
                    document.querySelectorAll('.product-checkbox').forEach(cb => cb.checked = this.checked);
                    if (selectAllDesktop) selectAllDesktop.checked = this.checked;
                    updateBulkBar();
                });
            }

            // AJAX SEARCH LOGIC
            const searchInput = document.getElementById('search');
            let searchTimeout = null;

            if (searchInput) {
                searchInput.addEventListener('input', function(e) {
                    const query = e.target.value;

                    // Clear previous timeout
                    if (searchTimeout) clearTimeout(searchTimeout);

                    // Logic: Search if length >= 3 OR if empty (to reset)
                    if (query.length >= 3 || query.length === 0) {
                        searchTimeout = setTimeout(() => {
                            performSearch(query);
                        }, 500); // 500ms debounce
                    }
                });
            }

            async function performSearch(query) {
                const currentUrl = new URL(window.location.href);
                if (query) {
                    currentUrl.searchParams.set('search', query);
                } else {
                    currentUrl.searchParams.delete('search');
                }

                // Get Category Filter
                const catFilter = document.getElementById('category_id_filter');
                if (catFilter && catFilter.value) {
                    currentUrl.searchParams.set('category_id', catFilter.value);
                } else {
                    currentUrl.searchParams.delete('category_id');
                }

                // Get Per Page Filter
                const perPageFilter = document.getElementById('per_page_filter');
                if (perPageFilter && perPageFilter.value) {
                    currentUrl.searchParams.set('per_page', perPageFilter.value);
                } else {
                    currentUrl.searchParams.delete('per_page');
                }

                // Reset page to 1 on new search
                currentUrl.searchParams.delete('page');

                // Update Browser URL without reload
                window.history.pushState({}, '', currentUrl);

                // Show loading state (optional, or just opacity)
                const desktopTableBody = document.querySelector('table tbody');
                const mobileListContainer = document.getElementById('mobile-products-list');

                if (desktopTableBody) desktopTableBody.style.opacity = '0.5';
                if (mobileListContainer) mobileListContainer.style.opacity = '0.5';

                try {
                    const response = await fetch(currentUrl, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) throw new Error('Network response was not ok');

                    const html = await response.text();
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');

                    // Update Desktop Table
                    const newTbody = doc.querySelector('table tbody');
                    if (desktopTableBody && newTbody) {
                        desktopTableBody.innerHTML = newTbody.innerHTML;
                    }

                    // Update Mobile List
                    const newMobileList = doc.getElementById('mobile-products-list');
                    // We need to preserve the "Check All" header in mobile list if it exists, or just replace content properly
                    // The mobile list structure in the file includes the header div inside #mobile-products-list. 
                    // Let's replace the INNER HTML of the container
                    if (mobileListContainer && newMobileList) {
                        mobileListContainer.innerHTML = newMobileList.innerHTML;
                    }

                    // Update Pagination
                    const newPagination = doc.getElementById('pagination-container');
                    const currentPagination = document.getElementById('pagination-container');
                    if (currentPagination && newPagination) {
                        currentPagination.innerHTML = newPagination.innerHTML;
                    }

                    // Re-initialize any listeners if needed
                    initInfiniteScroll();

                } catch (error) {
                    console.error('Search failed:', error);
                } finally {
                    if (desktopTableBody) desktopTableBody.style.opacity = '1';
                    if (mobileListContainer) mobileListContainer.style.opacity = '1';
                }
            }

            // INFINITE SCROLL LOGIC
            let isLoading = false;
            const mobileList = document.getElementById('mobile-products-list');
            const getLoader = () => document.getElementById('infinite-scroll-loader');
            const getPagination = () => document.getElementById('pagination-container');

            let observer = null;

            function initInfiniteScroll() {
                if (window.innerWidth < 1024) {
                    if ('IntersectionObserver' in window) {
                        const pc = getPagination();
                        const ld = getLoader();

                        if (pc) pc.style.display = 'none';
                        if (!ld) return;

                        if (observer) observer.disconnect();

                        observer = new IntersectionObserver((entries) => {
                            if (entries[0].isIntersecting && !isLoading) {
                                loadMoreProducts();
                            }
                        }, {
                            rootMargin: '400px',
                            threshold: 0
                        });

                        observer.observe(ld);
                    }
                }
            }

            // Initial call
            initInfiniteScroll();

            async function loadMoreProducts() {
                const pc = getPagination();
                const ld = getLoader();
                const nextLink = pc ? pc.querySelector('a[rel="next"]') : null;

                if (!nextLink) {
                    if (ld) ld.style.display = 'none';
                    return;
                }

                isLoading = true;
                if (ld) ld.style.opacity = '1';
                const url = nextLink.href;

                try {
                    const response = await fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const html = await response.text();
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');

                    const newCards = doc.querySelectorAll('.product-card-mobile');
                    newCards.forEach(card => {
                        const idInput = card.querySelector('input[type="checkbox"]');
                        if (idInput) {
                            const id = idInput.value;
                            if (!mobileList.querySelector(`input[value="${id}"]`)) {
                                mobileList.insertBefore(card, ld);
                            }
                        }
                    });

                    const newPagination = doc.getElementById('pagination-container');
                    if (newPagination && pc) {
                        pc.innerHTML = newPagination.innerHTML;
                    }

                    if (!pc || !pc.querySelector('a[rel="next"]')) {
                        if (ld) {
                            ld.innerHTML =
                                '<p class="text-center text-gray-300 font-bold uppercase tracking-widest text-[10px] py-4">Semua produk telah dimuat</p>';
                            ld.style.opacity = '1';
                        }
                    } else {
                        if (ld) ld.style.opacity = '0';
                    }

                    if (selectAllMobile && selectAllMobile.checked) {
                        document.querySelectorAll('.product-checkbox').forEach(cb => cb.checked = true);
                    }

                } catch (error) {
                    console.error('Error loading more products:', error);
                    if (ld) ld.style.opacity = '0';
                } finally {
                    isLoading = false;
                }
            }

            function confirmBulkDelete() {
                const count = document.querySelectorAll('.product-checkbox:checked').length;
                Swal.fire({
                    title: 'Hapus ' + count + ' Produk?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#4f46e5',
                    confirmButtonText: 'Ya, Hapus Semua!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        container: 'rounded-3xl',
                        popup: 'rounded-3xl',
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('bulk-delete-form').submit();
                    }
                });
            }

            function deleteProduct(id) {
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Produk yang dihapus tidak dapat dikembalikan!",
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
                        form.action = `/products/${id}`;
                        form.submit();
                    }
                })
            }

            // Detail Modal & Quick Update
            async function showDetail(productId) {
                try {
                    const response = await fetch(`/products/${productId}`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) throw new Error('Failed to fetch product');
                    const product = await response.json();

                    // Populate Modal with safety checks
                    const setVal = (id, val) => {
                        const el = document.getElementById(id);
                        if (el) el.value = val || '';
                    };

                    setVal('detail-name', product.name);
                    setVal('detail-stock', product.stock);
                    setVal('detail-price', product.price);
                    setVal('detail-purchase-price', product.purchase_price);
                    setVal('detail-unit', product.unit);
                    setVal('detail-description', product.description);

                    // Set Edit Link
                    const currentQuery = window.location.search;
                    document.getElementById('detail-edit-link').href = `/products/${productId}/edit${currentQuery}`;

                    // Image logic
                    const imgEl = document.getElementById('detail-image');
                    const noImgEl = document.getElementById('detail-no-image');
                    const deleteBtn = document.getElementById('btn-delete-image');

                    if (product.image) {
                        imgEl.src = '/storage/' + product.image;
                        imgEl.classList.remove('hidden');
                        deleteBtn.classList.remove('hidden');
                        noImgEl.classList.add('hidden');
                    } else {
                        imgEl.src = '';
                        imgEl.classList.add('hidden');
                        deleteBtn.classList.add('hidden');
                        noImgEl.classList.remove('hidden');
                    }

                    // Set selects
                    const categorySelect = document.getElementById('detail-category');
                    const supplierSelect = document.getElementById('detail-supplier');

                    if (categorySelect) categorySelect.value = product.category_id;
                    if (supplierSelect) supplierSelect.value = product.supplier_id || '';

                    // Store ID for updates
                    document.getElementById('detail-modal').dataset.productId = productId;

                    // Open Modal
                    document.getElementById('detail-modal').classList.remove('hidden');
                    document.body.style.overflow = 'hidden';

                } catch (error) {
                    console.error(error);
                    Swal.fire('Error', 'Gagal memuat detail produk', 'error');
                }
            }

            async function deleteDetailImage() {
                const modal = document.getElementById('detail-modal');
                const productId = modal.dataset.productId;

                const result = await Swal.fire({
                    title: 'Hapus Foto?',
                    text: "Foto akan dihapus permanen.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#4f46e5',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        container: 'rounded-3xl',
                        popup: 'rounded-3xl',
                    }
                });

                if (result.isConfirmed) {
                    try {
                        const response = await fetch(`/products/${productId}/image`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            // Update UI
                            document.getElementById('detail-image').classList.add('hidden');
                            document.getElementById('btn-delete-image').classList.add('hidden');
                            document.getElementById('detail-no-image').classList.remove('hidden');

                            // Also update list items if possible (reload page easiest or generic update)
                            // For now, let's just toast
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });
                            Toast.fire({
                                icon: 'success',
                                title: 'Foto dihapus'
                            });

                            // Ideally update the row image too, but page reload is cleaner for now or simple JS DOM manip
                            const rowImg = document.querySelector(`tr[data-product-id="${productId}"] img`);
                            if (rowImg) {
                                // replace img with placeholder div
                                const container = rowImg.parentElement;
                                container.innerHTML = `
                                <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-300 border border-gray-100 flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                             `;
                            }

                            // Update Mobile Card
                            // Mobile card logic is similar, find by id
                            const mobileCard = document.querySelector(
                                `.product-card-mobile[onclick="showDetail(${productId})"]`);
                            if (mobileCard) {
                                const imgContainer = mobileCard.querySelector(
                                    '.w-16.h-16'); // kinda fragile selector but works given structure
                                if (imgContainer) {
                                    imgContainer.innerHTML = `
                                        <div class="w-full h-full flex items-center justify-center text-gray-300">
                                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                 `;
                                }
                            }

                        } else {
                            Swal.fire('Error', 'Gagal menghapus foto', 'error');
                        }
                    } catch (e) {
                        console.error(e);
                        Swal.fire('Error', 'Terjadi kesalahan', 'error');
                    }
                }
            }

            function closeDetailModal() {
                document.getElementById('detail-modal').classList.add('hidden');
                document.body.style.overflow = '';
            }

            async function quickUpdateFromModal(field, value) {
                const modal = document.getElementById('detail-modal');
                const productId = modal.dataset.productId;

                await quickUpdate(productId, field, value);
            }

            async function quickUpdate(id, field, value) {
                try {
                    const response = await fetch(`/products/${id}/quick-update`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            [field]: value
                        })
                    });

                    if (!response.ok) {
                        const errorData = await response.json();
                        throw new Error(errorData.message || 'Update failed');
                    }

                    const data = await response.json();

                    if (data.success) {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });

                        Toast.fire({
                            icon: 'success',
                            title: 'Berhasil diperbarui'
                        });

                        const formatRupiah = (num) => {
                            return new Intl.NumberFormat('id-ID').format(num);
                        };

                        // Update Desktop Table Row
                        const row = document.querySelector(`tr[data-product-id="${id}"]`);
                        if (row) {
                            if (field === 'name') {
                                const el = row.querySelector('.product-name-display');
                                if (el) el.innerText = value;
                            }
                            if (field === 'description') {
                                const el = row.querySelector('.product-description-display');
                                if (el) el.innerText = value || 'Beri deskripsi untuk produk ini...';
                            }
                            if (field === 'category_id') {
                                const el = row.querySelector('.category-name-display');
                                if (el) el.innerText = data.category_name;
                            }
                            if (field === 'supplier_id') {
                                const el = row.querySelector('.supplier-name-display');
                                if (el) el.innerText = data.supplier_name;
                            }
                            if (field === 'price') {
                                const el = row.querySelector('.product-price-display');
                                if (el) el.innerText = formatRupiah(value);
                            }
                            if (field === 'purchase_price') {
                                const el = row.querySelector('.product-purchase-price-display');
                                if (el) el.innerText = formatRupiah(value);
                            }
                            if (field === 'unit') {
                                const el = row.querySelector('.product-unit-display');
                                if (el) el.innerText = '/' + value;
                            }
                        }

                        // Update Mobile Card
                        const mobileCard = document.querySelector(`.product-card-mobile input[value="${id}"]`)?.closest(
                            '.product-card-mobile');
                        if (mobileCard) {
                            if (field === 'name') {
                                const el = mobileCard.querySelector('.mobile-product-name');
                                if (el) el.innerText = value;
                            }
                            if (field === 'category_id') {
                                const el = mobileCard.querySelector('.mobile-category-name');
                                if (el) el.innerText = data.category_name;
                            }
                            if (field === 'supplier_id') {
                                const el = mobileCard.querySelector('.mobile-supplier-name');
                                if (el) el.innerText = data.supplier_name;
                            }
                            if (field === 'price') {
                                const el = mobileCard.querySelector('.mobile-product-price');
                                if (el) el.innerText = formatRupiah(value);
                            }
                            if (field === 'unit') {
                                const el = mobileCard.querySelector('.mobile-product-unit');
                                if (el) el.innerText = value;
                            }
                        }
                    }
                } catch (error) {
                    console.error('Update failed:', error);
                    Swal.fire({
                        title: 'Update Gagal',
                        text: error.message,
                        icon: 'error',
                        confirmButtonColor: '#4f46e5',
                        customClass: {
                            popup: 'rounded-3xl'
                        }
                    });
                }
            }

            // Lightbox Functions
            function openLightbox(src) {
                const lightbox = document.getElementById('image-lightbox');
                const img = document.getElementById('lightbox-image');

                img.src = src;
                lightbox.classList.remove('hidden');
                document.body.style.overflow = 'hidden';

                // Animation
                setTimeout(() => {
                    img.classList.remove('scale-95', 'opacity-0');
                    img.classList.add('scale-100', 'opacity-100');
                }, 10);
            }

            function closeLightbox() {
                const lightbox = document.getElementById('image-lightbox');
                const img = document.getElementById('lightbox-image');

                img.classList.remove('scale-100', 'opacity-100');
                img.classList.add('scale-95', 'opacity-0');

                setTimeout(() => {
                    lightbox.classList.add('hidden');
                    img.src = '';
                    document.body.style.overflow = '';
                }, 300);
            }
        </script>
    @endpush

    <!-- Image Lightbox Modal -->
    <div id="image-lightbox" class="fixed inset-0 z-[110] hidden bg-black/90 backdrop-blur-sm transition-all duration-300"
        onclick="closeLightbox()">
        <div class="absolute top-4 right-4 z-20">
            <button onclick="closeLightbox()" class="text-white hover:text-gray-300 focus:outline-none">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>
        <div class="h-full w-full flex items-center justify-center p-4">
            <img id="lightbox-image" src="" alt="Full view"
                class="max-h-full max-w-full object-contain rounded-lg shadow-2xl scale-95 opacity-0 transition-all duration-300">
        </div>
    </div>

    <!-- Detail Modal -->
    <div id="detail-modal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="closeDetailModal()">
            </div>

            <!-- Modal Panel -->
            <div
                class="relative transform overflow-hidden rounded-[2.5rem] bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">
                <div class="px-8 pt-8 pb-6">
                    <div class="flex justify-between items-center mb-8">
                        <div>
                            <h2 class="text-2xl font-black text-gray-900 tracking-tight">Detail Produk</h2>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-1">Informasi & Update
                                Cepat</p>
                        </div>
                        <button type="button" onclick="closeDetailModal()"
                            class="h-10 w-10 flex items-center justify-center rounded-xl bg-gray-50 text-gray-400 hover:bg-red-50 hover:text-red-500 transition-all">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="flex flex-col items-center mb-8">
                        <div
                            class="h-40 w-40 rounded-[2.5rem] bg-gray-50 flex items-center justify-center overflow-hidden border-2 border-dashed border-gray-200 group relative shadow-inner">
                            <img id="detail-image" src="" alt="Product"
                                class="w-full h-full object-cover hidden transition-transform duration-500 group-hover:scale-110">
                            <div id="detail-no-image" class="text-gray-300">
                                <i class="fas fa-box-open text-5xl"></i>
                            </div>
                            <button id="btn-delete-image" type="button" onclick="deleteDetailImage()"
                                class="absolute top-3 right-3 bg-red-500 text-white h-8 w-8 flex items-center justify-center rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-all hover:scale-110 hidden">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="group">
                            <label
                                class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Label
                                Produk</label>
                            <input type="text" id="detail-name" onchange="quickUpdateFromModal('name', this.value)"
                                class="block w-full text-lg font-black text-gray-900 bg-gray-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100/50 rounded-2xl py-3.5 px-5 transition-all outline-none">
                        </div>

                        <div class="grid grid-cols-2 gap-5">
                            <div class="group">
                                <label
                                    class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Harga
                                    Jual</label>
                                <div class="relative">
                                    <div
                                        class="absolute inset-y-0 left-5 flex items-center pointer-events-none text-indigo-600 font-black">
                                        Rp</div>
                                    <input type="number" id="detail-price"
                                        onchange="quickUpdateFromModal('price', this.value)"
                                        class="block w-full pl-12 pr-5 py-3.5 bg-gray-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100/50 rounded-2xl text-sm font-black text-indigo-600 transition-all outline-none">
                                </div>
                            </div>
                            <div class="group">
                                <label
                                    class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">HPP
                                    (Modal)</label>
                                <div class="relative">
                                    <div
                                        class="absolute inset-y-0 left-5 flex items-center pointer-events-none text-gray-400 font-black">
                                        Rp</div>
                                    <input type="number" id="detail-purchase-price"
                                        onchange="quickUpdateFromModal('purchase_price', this.value)"
                                        class="block w-full pl-12 pr-5 py-3.5 bg-gray-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100/50 rounded-2xl text-sm font-bold text-gray-700 transition-all outline-none">
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-5">
                            <div class="group">
                                <label
                                    class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Kategori</label>
                                <div class="relative">
                                    <select id="detail-category"
                                        onchange="quickUpdateFromModal('category_id', this.value)"
                                        class="block w-full py-3.5 px-5 bg-gray-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100/50 rounded-2xl text-sm font-bold text-gray-700 transition-all cursor-pointer appearance-none outline-none">
                                        @foreach ($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                    <div
                                        class="absolute inset-y-0 right-5 flex items-center pointer-events-none text-gray-400">
                                        <i class="fas fa-chevron-down text-xs"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="group">
                                <label
                                    class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Supplier</label>
                                <div class="relative">
                                    <select id="detail-supplier"
                                        onchange="quickUpdateFromModal('supplier_id', this.value)"
                                        class="block w-full py-3.5 px-5 bg-gray-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100/50 rounded-2xl text-sm font-bold text-gray-700 transition-all cursor-pointer appearance-none outline-none">
                                        <option value="">Tanpa Supplier</option>
                                        @foreach ($suppliers as $sup)
                                            <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                                        @endforeach
                                    </select>
                                    <div
                                        class="absolute inset-y-0 right-5 flex items-center pointer-events-none text-gray-400">
                                        <i class="fas fa-chevron-down text-xs"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-5">
                            <div class="group">
                                <label
                                    class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Stok
                                    Sedia</label>
                                <input type="number" id="detail-stock"
                                    onchange="quickUpdateFromModal('stock', this.value)"
                                    class="block w-full py-3.5 px-5 bg-gray-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100/50 rounded-2xl text-sm font-bold text-gray-700 transition-all outline-none"
                                    placeholder="0">
                            </div>
                            <div class="group">
                                <label
                                    class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Satuan
                                    Unit</label>
                                <input type="text" id="detail-unit"
                                    onchange="quickUpdateFromModal('unit', this.value)"
                                    class="block w-full py-3.5 px-5 bg-gray-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100/50 rounded-2xl text-sm font-bold text-gray-700 transition-all outline-none"
                                    placeholder="Contoh: Pcs, Kg...">
                            </div>
                        </div>

                        <div class="group">
                            <label
                                class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Catatan
                                / Deskripsi</label>
                            <textarea id="detail-description" rows="2" onchange="quickUpdateFromModal('description', this.value)"
                                class="block w-full py-3.5 px-5 bg-gray-50 border-2 border-transparent focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100/50 rounded-2xl text-sm font-medium text-gray-700 transition-all outline-none resize-none"
                                placeholder="Tambahkan keterangan produk..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50/80 px-8 py-8 flex flex-col gap-3">
                    <a id="detail-edit-link" href="#"
                        class="flex items-center justify-center gap-3 py-4 bg-indigo-600 rounded-[1.5rem] text-white font-black text-sm shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all active:scale-[98%] uppercase tracking-widest">
                        <i class="fas fa-external-link-alt text-xs"></i>
                        Edit Lengkap & Media
                    </a>
                    <button type="button" onclick="closeDetailModal()"
                        class="py-4 bg-white border border-gray-200 rounded-[1.5rem] text-gray-500 font-extrabold text-sm hover:bg-gray-50 transition-all uppercase tracking-widest">
                        Tutup Panel
                    </button>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            function initiateCloudBackup() {
                Swal.fire({
                    title: 'Backup ke Google Drive?',
                    text: "Data produk akan diekspor ke Excel dan diletakkan di folder 'Product Backups' di Google Drive Anda.",
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#4f46e5',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Mulai Backup!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        container: 'rounded-3xl',
                        popup: 'rounded-3xl',
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const btn = document.getElementById('btn-cloud-backup');
                        const text = document.getElementById('backup-btn-text');
                        const spinner = document.getElementById('backup-spinner');
                        const icon = document.getElementById('backup-drive-icon');
                        const form = document.getElementById('product-backup-form');

                        if (!btn || !form) {
                            console.error('Backup elements not found');
                            return;
                        }

                        // UI State
                        btn.disabled = true;
                        btn.classList.add('opacity-75', 'cursor-not-allowed');
                        if (spinner) spinner.style.display = 'inline-block';
                        if (icon) icon.style.display = 'none';
                        // We keep text hidden for square button to avoid layout break

                        const formData = new FormData(form);

                        fetch(form.action, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status === 'success') {
                                    // Trigger Global Backup Indicator
                                    localStorage.setItem('backup_active', 'true');
                                    if (typeof window.startGlobalPolling === 'function') {
                                        window.startGlobalPolling();
                                    }

                                    Swal.fire({
                                        title: 'Sukses Terjadwal',
                                        text: 'Backup produk sedang berjalan di latar belakang. Anda bisa memantau detailnya di menu Cloud Backup.',
                                        icon: 'success',
                                        confirmButtonText: 'Oke',
                                        customClass: {
                                            popup: 'rounded-3xl'
                                        }
                                    });
                                } else {
                                    throw new Error(data.message || 'Gagal memulai backup');
                                }
                            })
                            .catch(error => {
                                Swal.fire({
                                    title: 'Gagal',
                                    text: error.message,
                                    icon: 'error',
                                    customClass: {
                                        popup: 'rounded-3xl'
                                    }
                                });
                            })
                            .finally(() => {
                                btn.disabled = false;
                                btn.classList.remove('opacity-75', 'cursor-not-allowed');
                                if (spinner) spinner.style.display = 'none';
                                if (icon) icon.style.display = 'inline-block';
                            });
                    }
                });
            }
        </script>
    @endpush
@endsection
