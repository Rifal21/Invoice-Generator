@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="md:flex md:items-center md:justify-between mb-6">
            <div class="flex-1 min-w-0">
                <h2 class="text-3xl font-black text-gray-900 sm:truncate tracking-tight flex items-center gap-3">
                    <div class="p-2 bg-indigo-100 rounded-xl">
                        <svg class="h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                    </div>
                    Inventory Barang
                </h2>
                <p class="mt-2 text-sm text-gray-500 font-medium">Pantau dan kelola stok barang Anda secara real-time.</p>
            </div>
            <div class="mt-4 flex md:ml-4 md:mt-0 gap-3">
                <form action="{{ route('inventory.index') }}" method="GET" class="flex gap-2 w-full md:w-auto">
                    <div class="relative flex-1 md:w-64">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari barang..."
                            class="block w-full pl-10 pr-3 py-2 border-0 rounded-2xl bg-white shadow-sm ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-indigo-600 sm:text-sm">
                    </div>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 rounded-2xl text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition-all">
                        Filter
                    </button>
                </form>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-3 mb-6">
            <div class="bg-white overflow-hidden shadow-sm rounded-3xl border border-gray-100 p-6 flex items-center gap-4">
                <div class="p-3 bg-blue-50 rounded-2xl">
                    <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Produk</p>
                    <p class="text-2xl font-black text-gray-900">{{ $products->total() }}</p>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow-sm rounded-3xl border border-gray-100 p-6 flex items-center gap-4">
                <div class="p-3 bg-red-50 rounded-2xl">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Stok Menipis</p>
                    <p class="text-2xl font-black text-gray-900">{{ \App\Models\Product::where('stock', '<=', 5)->count() }}
                    </p>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow-sm rounded-3xl border border-gray-100 p-6 flex items-center gap-4">
                <div class="p-3 bg-green-50 rounded-2xl">
                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Mutasi Hari Ini</p>
                    <p class="text-2xl font-black text-gray-900">
                        {{ \App\Models\StockHistory::whereDate('created_at', today())->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="bg-white shadow-xl rounded-3xl border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th scope="col"
                                class="py-4 pl-6 pr-3 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">
                                Nama Barang</th>
                            <th scope="col"
                                class="px-3 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">
                                Kategori</th>
                            <th scope="col"
                                class="px-3 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider text-center">
                                Stok</th>
                            <th scope="col"
                                class="px-3 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Satuan
                            </th>
                            <th scope="col" class="relative py-4 pl-3 pr-6 text-right">
                                <span class="sr-only">Aksi</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 bg-white">
                        @forelse($products as $product)
                            <tr class="hover:bg-indigo-50/30 transition-colors group">
                                <td class="whitespace-nowrap py-5 pl-6 pr-3">
                                    <div
                                        class="text-sm font-bold text-gray-900 group-hover:text-indigo-600 transition-colors">
                                        {{ $product->name }}</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-5">
                                    <span
                                        class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-bold text-gray-600">
                                        {{ $product->category->name }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-5 text-center">
                                    <span
                                        class="inline-flex items-center rounded-xl px-3 py-1 text-sm font-black {{ $product->stock <= 5 ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-600' }}">
                                        {{ $product->stock }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-5 text-sm text-gray-500 font-medium lowercase">
                                    {{ $product->unit }}</td>
                                <td class="relative whitespace-nowrap py-5 pl-3 pr-6 text-right text-sm font-bold">
                                    <div class="flex justify-end gap-3">
                                        <button type="button"
                                            onclick="openAdjustModal({{ $product->id }}, '{{ $product->name }}')"
                                            class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-xl transition-all">Update</button>
                                        <a href="{{ route('inventory.history', $product->id) }}"
                                            class="text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded-xl transition-all">Riwayat</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-gray-500">Tidak ada data ditemukan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($products->hasPages())
                <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Premium Adjust Stock Modal -->
    <div id="adjustModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity" aria-hidden="true"
                onclick="closeAdjustModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-white">
                <div class="bg-indigo-600 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg font-black text-white" id="modal-title">Update Stok</h3>
                    <button onclick="closeAdjustModal()" class="text-indigo-100 hover:text-white transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-8">
                    <div class="mb-6">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Barang</p>
                        <p class="text-xl font-black text-gray-900" id="productName"></p>
                    </div>
                    <form id="adjustForm" method="POST">
                        @csrf
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-black text-gray-700 mb-3 uppercase tracking-wider">Tipe
                                    Perubahan</label>
                                <div class="grid grid-cols-2 gap-4">
                                    <label
                                        class="type-label relative flex cursor-pointer rounded-2xl border-2 border-gray-100 bg-white p-4 shadow-sm transition-all hover:bg-gray-50">
                                        <input type="radio" name="type" value="in" checked class="sr-only"
                                            onchange="updateTypeColor(this)">
                                        <span
                                            class="flex flex-1 items-center justify-center gap-3 font-black text-gray-900 text-sm">
                                            <div class="p-1.5 bg-green-100 rounded-xl"><svg class="w-5 h-5 text-green-600"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                                                </svg></div>
                                            STOK MASUK
                                        </span>
                                    </label>
                                    <label
                                        class="type-label relative flex cursor-pointer rounded-2xl border-2 border-gray-100 bg-white p-4 shadow-sm transition-all hover:bg-gray-50">
                                        <input type="radio" name="type" value="out" class="sr-only"
                                            onchange="updateTypeColor(this)">
                                        <span
                                            class="flex flex-1 items-center justify-center gap-3 font-black text-gray-900 text-sm">
                                            <div class="p-1.5 bg-red-100 rounded-xl"><svg class="w-5 h-5 text-red-600"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2.5" d="M20 12H4"></path>
                                                </svg></div>
                                            STOK KELUAR
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-black text-gray-700 mb-2 uppercase tracking-wider">Jumlah
                                    Barang</label>
                                <input type="number" step="0.01" name="quantity" required placeholder="0.00"
                                    class="block w-full rounded-2xl border-gray-200 bg-gray-50 py-4 px-5 text-gray-900 shadow-inner focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-black text-xl">
                            </div>
                            <div>
                                <label class="block text-sm font-black text-gray-700 mb-2 uppercase tracking-wider">Catatan
                                    Tambahan</label>
                                <textarea name="description" rows="3" placeholder="Misal: Retur barang atau barang masuk dari gudang..."
                                    class="block w-full rounded-2xl border-gray-200 bg-gray-50 py-4 px-5 text-gray-900 shadow-inner focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-medium text-sm"></textarea>
                            </div>
                        </div>
                        <div class="mt-10 flex gap-4">
                            <button type="button" onclick="closeAdjustModal()"
                                class="flex-1 px-6 py-4 rounded-2xl text-sm font-black text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-all border-2 border-gray-50 uppercase tracking-widest">Batal</button>
                            <button type="submit"
                                class="flex-[2] inline-flex items-center justify-center px-8 py-4 border border-transparent text-sm font-black rounded-2xl text-white bg-indigo-600 shadow-xl shadow-indigo-100 hover:bg-indigo-700 hover:-translate-y-1 transition-all uppercase tracking-widest">Update
                                Sekarang</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openAdjustModal(id, name) {
            document.getElementById('productName').innerText = name;
            document.getElementById('adjustForm').action = `/inventory/${id}/adjust`;
            document.getElementById('adjustModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';

            // Reset and initialize selection style
            const activeRadio = document.querySelector('input[name="type"]:checked');
            updateTypeColor(activeRadio);
        }

        function closeAdjustModal() {
            document.getElementById('adjustModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function updateTypeColor(input) {
            const labels = document.querySelectorAll('.type-label');
            labels.forEach(label => {
                label.classList.remove('ring-4', 'ring-indigo-500/20', 'border-indigo-600', 'bg-indigo-50/50');
                label.classList.add('border-gray-100', 'bg-white');
            });

            if (input.checked) {
                const parent = input.closest('label');
                parent.classList.add('ring-4', 'ring-indigo-500/20', 'border-indigo-600', 'bg-indigo-50/50');
                parent.classList.remove('border-gray-100', 'bg-white');
            }
        }
    </script>
@endsection
