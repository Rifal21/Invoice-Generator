@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-10">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
            <div class="min-w-0 flex-1">
                <h1 class="text-3xl md:text-4xl font-black text-gray-900 tracking-tight">Data Produk</h1>
                <p class="mt-2 text-sm md:text-lg text-gray-500">Kelola daftar produk dan layanan Anda.</p>
            </div>
            <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                <a href="{{ route('products.create') }}"
                    class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-bold rounded-2xl text-white bg-indigo-600 shadow-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Tambah Produk
                </a>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-3xl p-5 mb-8 border border-gray-100">
            <div class="flex flex-col md:flex-row gap-4 justify-between items-end">
                <!-- Search and Filter -->
                <form action="{{ route('products.index') }}" method="GET"
                    class="w-full md:flex-1 grid grid-cols-1 sm:grid-cols-12 gap-4">
                    <div class="sm:col-span-5">
                        <label for="search" class="sr-only">Cari</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            placeholder="Cari produk..."
                            class="block w-full rounded-2xl border-gray-200 py-2.5 px-3 text-sm text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                    </div>
                    <div class="sm:col-span-4">
                        <label for="category_id" class="sr-only">Kategori</label>
                        <select name="category_id" id="category_id"
                            class="block w-full rounded-2xl border-gray-200 py-2.5 px-3 text-sm text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                            <option value="">Semua Kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-3">
                        <button type="submit"
                            class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-2xl shadow-sm text-sm font-bold text-gray-900 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all">
                            Filter
                        </button>
                    </div>
                </form>

                <!-- Import Form -->
                <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data"
                    class="w-full md:w-auto flex flex-col sm:flex-row items-center gap-2 pt-4 md:pt-0 border-t md:border-t-0 border-gray-100">
                    @csrf
                    <input type="file" name="file" required
                        class="block w-full text-xs text-gray-500 file:mr-2 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <div class="flex gap-2 w-full sm:w-auto mt-2 sm:mt-0">
                        <button type="submit"
                            class="flex-1 sm:flex-none inline-flex justify-center items-center px-4 py-2 border border-transparent text-xs font-bold rounded-2xl text-white bg-green-600 shadow-md hover:bg-green-700 transition-all">
                            Impor
                        </button>
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" @click.away="open = false" type="button"
                                class="flex-1 sm:flex-none inline-flex justify-center items-center px-4 py-2 border border-transparent text-xs font-bold rounded-2xl text-white bg-amber-500 shadow-md hover:bg-amber-600 transition-all">
                                Ekspor
                                <svg class="-mr-1 ml-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div x-show="open" x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg ring-1 ring-black ring-opacity-5 z-50 py-1"
                                style="display: none;">
                                <a href="{{ route('products.export', ['type' => 'client']) }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Ekspor Client</a>
                                <a href="{{ route('products.export', ['type' => 'internal']) }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Ekspor Internal</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <form id="bulk-delete-form" action="{{ route('products.bulk-delete') }}" method="POST">
            @csrf
            <!-- Desktop Table -->
            <div class="hidden md:block bg-white shadow-2xl rounded-3xl overflow-hidden border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="py-4 pl-6 pr-3 text-left">
                                    <input type="checkbox" id="select-all-desktop"
                                        class="h-5 w-5 rounded-lg border-gray-300 text-indigo-600 focus:ring-indigo-500 transition-all cursor-pointer">
                                </th>
                                <th scope="col"
                                    class="px-3 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                    No</th>
                                <th scope="col"
                                    class="px-3 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                    Nama
                                </th>
                                <th scope="col"
                                    class="px-3 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                    Kategori</th>
                                <th scope="col"
                                    class="px-3 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                    Supplier</th>
                                <th scope="col"
                                    class="px-3 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                    Harga
                                    Beli
                                </th>
                                <th scope="col"
                                    class="px-3 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                    Harga Jual</th>
                                <th scope="col"
                                    class="px-3 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                    Satuan</th>
                                <th scope="col"
                                    class="px-3 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                    Stok</th>
                                <th scope="col"
                                    class="px-3 py-4 text-left text-xs font-black text-gray-400 uppercase tracking-widest">
                                    Deskripsi</th>
                                <th scope="col"
                                    class="relative py-4 pl-3 pr-6 text-right text-xs font-black text-gray-400 uppercase tracking-widest">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach ($products as $product)
                                <tr class="hover:bg-gray-50 transition-colors group cursor-pointer"
                                    data-product-id="{{ $product->id }}" onclick="showDetail({{ $product->id }})">
                                    <td class="py-5 pl-6 pr-3">
                                        <input type="checkbox" name="ids[]" value="{{ $product->id }}"
                                            onclick="event.stopPropagation()"
                                            class="product-checkbox h-5 w-5 rounded-lg border-gray-300 text-indigo-600 focus:ring-indigo-500 transition-all cursor-pointer">
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-5 text-sm font-bold text-gray-400">
                                        {{ $loop->iteration + ($products->currentPage() - 1) * $products->perPage() }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-3 py-5 text-sm font-bold text-gray-900 group-hover:text-indigo-600 transition-colors">
                                        {{ $product->name }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-5 text-sm font-medium text-gray-500">
                                        <select onchange="quickUpdate({{ $product->id }}, 'category_id', this.value)"
                                            onclick="event.stopPropagation()"
                                            class="category-select block w-full rounded-lg border-transparent bg-transparent py-1 pl-2 pr-8 text-xs font-bold text-gray-700 hover:bg-gray-100 focus:border-indigo-500 focus:bg-white focus:ring-indigo-500 transition-all">
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-5 text-sm font-medium text-gray-500">
                                        <select onchange="quickUpdate({{ $product->id }}, 'supplier_id', this.value)"
                                            onclick="event.stopPropagation()"
                                            class="supplier-select block w-full rounded-lg border-transparent bg-transparent py-1 pl-2 pr-8 text-xs font-bold text-gray-700 hover:bg-gray-100 focus:border-indigo-500 focus:bg-white focus:ring-indigo-500 transition-all">
                                            <option value="">- Pilih -</option>
                                            @foreach ($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}"
                                                    {{ $product->supplier_id == $supplier->id ? 'selected' : '' }}>
                                                    {{ $supplier->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-5 text-sm font-bold text-gray-500">
                                        Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</td>
                                    <td class="whitespace-nowrap px-3 py-5 text-sm font-bold text-indigo-600">
                                        Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                    <td class="whitespace-nowrap px-3 py-5 text-sm text-gray-500">{{ $product->unit }}
                                    </td>
                                    <td
                                        class="whitespace-nowrap px-3 py-5 text-sm font-bold {{ $product->stock <= 5 ? 'text-red-500' : 'text-gray-900' }}">
                                        {{ (float) $product->stock }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-5 text-sm text-gray-500 max-w-xs truncate">
                                        {{ Str::limit($product->description, 30) }}</td>
                                    <td class="relative whitespace-nowrap py-5 pl-3 pr-6 text-right text-sm font-medium">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('products.edit', array_merge(['product' => $product->id], request()->query())) }}"
                                                onclick="event.stopPropagation()"
                                                class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1.5 rounded-lg transition-colors">Edit</a>
                                            <button type="button"
                                                class="text-red-600 hover:text-red-900 bg-red-50 px-3 py-1.5 rounded-lg transition-colors"
                                                onclick="event.stopPropagation(); deleteProduct({{ $product->id }})">Hapus</button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mobile Card View -->
            <div class="md:hidden space-y-4">
                <div class="flex items-center gap-3 px-2 mb-2">
                    <input type="checkbox" id="select-all-mobile"
                        class="h-6 w-6 rounded-xl border-gray-300 text-indigo-600 focus:ring-indigo-500 transition-all cursor-pointer">
                    <label for="select-all-mobile"
                        class="text-sm font-black text-gray-400 uppercase tracking-widest">Pilih Semua Produk</label>
                </div>
                @foreach ($products as $product)
                    <div class="group bg-white rounded-3xl p-5 shadow-lg border border-gray-100 relative overflow-hidden cursor-pointer active:scale-[99%] transition-transform"
                        onclick="showDetail({{ $product->id }})">
                        <div class="absolute top-4 left-4">
                            <input type="checkbox" name="ids[]" value="{{ $product->id }}"
                                onclick="event.stopPropagation()"
                                class="product-checkbox h-6 w-6 rounded-xl border-gray-300 text-indigo-600 focus:ring-indigo-500 transition-all cursor-pointer">
                        </div>
                        <div class="flex justify-between items-start mb-3 ml-16">
                            <div>
                                <div class="flex flex-wrap gap-2 mb-2">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700">
                                        {{ $product->category->name }}
                                    </span>
                                    @if ($product->supplier)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-50 text-green-700">
                                            {{ $product->supplier->name }}
                                        </span>
                                    @endif
                                </div>
                                <h3 class="text-lg font-black text-gray-900 group-hover:text-indigo-600 transition-colors">
                                    {{ $product->name }}
                                </h3>
                            </div>
                            <span class="text-xs font-bold text-gray-400">#{{ $loop->iteration }}</span>
                        </div>

                        <div class="flex items-baseline gap-1 mb-4 ml-16">
                            <span class="text-sm font-medium text-gray-500">Harga:</span>
                            <span class="text-xl font-black text-indigo-600">Rp
                                {{ number_format($product->price, 0, ',', '.') }}</span>
                            <span class="text-sm text-gray-400">/ {{ $product->unit }}</span>
                        </div>

                        <div class="mb-4 flex items-center gap-2 ml-16">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Stok Tersedia:</span>
                            <span
                                class="px-3 py-1 rounded-xl text-xs font-black {{ $product->stock <= 5 ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-600' }}">
                                {{ (float) $product->stock }} {{ $product->unit }}
                            </span>
                        </div>

                        @if ($product->description)
                            <p class="text-sm text-gray-500 mb-5 bg-gray-50 p-3 rounded-xl italic ml-16">
                                "{{ Str::limit($product->description, 80) }}"</p>
                        @endif

                        <div class="grid grid-cols-2 gap-3 ml-16">
                            <a href="{{ route('products.edit', array_merge(['product' => $product->id], request()->query())) }}"
                                onclick="event.stopPropagation()"
                                class="flex items-center justify-center py-2.5 px-4 rounded-xl bg-amber-50 text-amber-700 font-bold text-sm">
                                Edit
                            </a>
                            <button type="button" onclick="event.stopPropagation(); deleteProduct({{ $product->id }})"
                                class="flex items-center justify-center py-2.5 px-4 rounded-xl bg-red-50 text-red-700 font-bold text-sm">
                                Hapus
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </form>

        <div class="mt-8">
            {{ $products->links() }}
        </div>

        <!-- Sticky Bulk Action Bar -->
        <div id="bulk-action-bar"
            class="fixed bottom-20 left-1/2 -translate-x-1/2 z-[60] transform scale-0 opacity-0 transition-all duration-300">
            <div
                class="bg-gray-900 border-2 border-white/10 text-white rounded-[2rem] px-8 py-5 shadow-2xl flex items-center gap-8 backdrop-blur-xl">
                <div class="flex flex-col">
                    <span id="selected-count" class="text-2xl font-black text-indigo-400 leading-none">0</span>
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mt-1">Item Terpilih</span>
                </div>
                <div class="h-10 w-px bg-white/10"></div>
                <button type="button" onclick="confirmBulkDelete()"
                    class="bg-red-500 hover:bg-red-600 text-white px-8 py-3 rounded-2xl font-black text-xs uppercase tracking-widest transition-all active:scale-95 shadow-lg shadow-red-500/20 flex items-center gap-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Hapus Pilihan
                </button>
            </div>
        </div>
    </div>

    <form id="delete-form" action="" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <script>
        const bulkActionBar = document.getElementById('bulk-action-bar');
        const selectedCountText = document.getElementById('selected-count');
        const checkboxes = document.querySelectorAll('.product-checkbox');
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

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateBulkBar);
        });

        if (selectAllDesktop) {
            selectAllDesktop.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
                if (selectAllMobile) selectAllMobile.checked = this.checked;
                updateBulkBar();
            });
        }

        if (selectAllMobile) {
            selectAllMobile.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
                if (selectAllDesktop) selectAllDesktop.checked = this.checked;
                updateBulkBar();
            });
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
                // Determine base URL dynamically or assume /products/{productId} is the show route
                // Actually the show route is just /products/{id} and returns JSON if wantsJson()
                // We will fetch it.
                const response = await fetch(`/products/${productId}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) throw new Error('Failed to fetch product');
                const product = await response.json();

                // Populate Modal
                document.getElementById('detail-name').value = product.name;
                document.getElementById('detail-stock').value = product.stock;
                document.getElementById('detail-price').value = product.price;
                document.getElementById('detail-purchase-price').value = product.purchase_price || 0;
                document.getElementById('detail-description').value = product.description || '';

                // Set selects
                const categorySelect = document.getElementById('detail-category');
                const supplierSelect = document.getElementById('detail-supplier');

                categorySelect.value = product.category_id;
                supplierSelect.value = product.supplier_id || '';

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

                const data = await response.json();

                if (data.success) {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer)
                            toast.addEventListener('mouseleave', Swal.resumeTimer)
                        }
                    });

                    Toast.fire({
                        icon: 'success',
                        title: 'Updated successfully'
                    });

                    // If updated from modal, update the UI in background if visible
                    // Or reload page? No, user wants real-time.
                    // We should update the table text too.
                    if (field === 'category_id') {
                        // Find row and update select value
                        const row = document.querySelector(`tr[data-product-id="${id}"]`);
                        if (row) {
                            const sel = row.querySelector('.category-select');
                            if (sel) sel.value = value;
                        }
                    }
                    if (field === 'supplier_id') {
                        const row = document.querySelector(`tr[data-product-id="${id}"]`);
                        if (row) {
                            const sel = row.querySelector('.supplier-select');
                            if (sel) sel.value = value;
                        }
                    }

                } else {
                    throw new Error('Update failed');
                }
            } catch (error) {
                console.error('Update failed:', error);
                Swal.fire('Error', 'Gagal update data: ' + error.message, 'error');
            }
        }
    </script>

    <!-- Detail Modal -->
    <div id="detail-modal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="fixed inset-0 bg-transparent bg-opacity-10 transition-opacity backdrop-blur-xs"
            onclick="closeDetailModal()"></div>

        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">

                    <!-- Close Button -->
                    <div class="absolute top-4 right-4 z-10">
                        <button type="button" onclick="closeDetailModal()"
                            class="text-gray-400 hover:text-gray-500 transition-colors">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <div class="mb-4">
                                    <label for="detail-name"
                                        class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Nama
                                        Produk</label>
                                    <input type="text" id="detail-name"
                                        onchange="quickUpdateFromModal('name', this.value)"
                                        class="block w-full text-2xl font-black text-gray-900 border-none p-0 focus:ring-0 placeholder-gray-300 transition-all border-b border-transparent focus:border-indigo-500 rounded-none bg-transparent"
                                        placeholder="Nama Produk">
                                </div>

                                <p class="text-sm text-gray-500 mb-6">Edit detail produk secara langsung.</p>

                                <div class="grid grid-cols-2 gap-4 mb-6">
                                    <div
                                        class="bg-gray-50 p-4 rounded-2xl group focus-within:ring-2 focus-within:ring-indigo-500 focus-within:bg-white transition-all">
                                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Stok</p>
                                        <input type="number" id="detail-stock"
                                            onchange="quickUpdateFromModal('stock', this.value)"
                                            class="block w-full text-lg font-black text-gray-900 bg-transparent border-none p-0 focus:ring-0"
                                            placeholder="0">
                                    </div>
                                    <div
                                        class="bg-gray-50 p-4 rounded-2xl group focus-within:ring-2 focus-within:ring-indigo-500 focus-within:bg-white transition-all">
                                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Harga
                                            Jual</p>
                                        <div class="flex items-center">
                                            <span class="text-lg font-black text-indigo-600 mr-1">Rp</span>
                                            <input type="number" id="detail-price"
                                                onchange="quickUpdateFromModal('price', this.value)"
                                                class="block w-full text-lg font-black text-indigo-600 bg-transparent border-none p-0 focus:ring-0"
                                                placeholder="0">
                                        </div>
                                    </div>
                                    <div
                                        class="bg-gray-50 p-4 rounded-2xl group focus-within:ring-2 focus-within:ring-indigo-500 focus-within:bg-white transition-all col-span-2">
                                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Harga
                                            Beli (HPP)</p>
                                        <div class="flex items-center">
                                            <span class="text-lg font-black text-gray-900 mr-1">Rp</span>
                                            <input type="number" id="detail-purchase-price"
                                                onchange="quickUpdateFromModal('purchase_price', this.value)"
                                                class="block w-full text-lg font-black text-gray-900 bg-transparent border-none p-0 focus:ring-0"
                                                placeholder="0">
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-900 mb-1">Kategori</label>
                                        <select id="detail-category"
                                            onchange="quickUpdateFromModal('category_id', this.value)"
                                            class="block w-full rounded-xl border-gray-200 py-2.5 px-3 text-sm text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all">
                                            @foreach ($categories as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-900 mb-1">Supplier</label>
                                        <select id="detail-supplier"
                                            onchange="quickUpdateFromModal('supplier_id', this.value)"
                                            class="block w-full rounded-xl border-gray-200 py-2.5 px-3 text-sm text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all">
                                            <option value="">Tidak ada supplier</option>
                                            @foreach ($suppliers as $sup)
                                                <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label
                                            class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Deskripsi</label>
                                        <textarea id="detail-description" rows="3" onchange="quickUpdateFromModal('description', this.value)"
                                            class="block w-full rounded-xl border-gray-200 py-2.5 px-3 text-sm text-gray-900 shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all bg-gray-50 focus:bg-white"
                                            placeholder="Deskripsi produk..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="button"
                            class="mt-3 inline-flex w-full justify-center rounded-2xl bg-white px-3 py-2 text-sm font-bold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto"
                            onclick="closeDetailModal()">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
