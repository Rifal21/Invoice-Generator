@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-10">
        <!-- Centered Layout -->
        <div class="max-w-3xl mx-auto">
            <div class="mb-8">
                <nav class="flex mb-4" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2">
                        <li><a href="{{ route('products.index') }}"
                                class="text-xs md:text-sm font-bold text-gray-400 hover:text-indigo-600">Produk</a></li>
                        <li><svg class="h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd" />
                            </svg></li>
                        <li class="text-xs md:text-sm font-bold text-indigo-600">Edit</li>
                    </ol>
                </nav>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Edit Produk</h1>
                <p class="mt-2 text-sm text-gray-500">Perbarui informasi produk ini.</p>
            </div>

            <div class="bg-white shadow-xl rounded-3xl border border-gray-100 overflow-hidden">
                <form action="{{ route('products.update', $product->id) }}" method="POST" class="p-6 md:p-10">
                    @csrf
                    @method('PUT')

                    <div class="space-y-8">
                        <!-- Basic Info Section -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-2 mb-6">Informasi Dasar
                            </h3>
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-bold text-gray-900 mb-2">Nama
                                        Produk</label>
                                    <input type="text" name="name" id="name" value="{{ $product->name }}"
                                        required placeholder="Contoh: Kopi Susu Aren"
                                        class="block w-full rounded-2xl border-gray-200 py-3 px-4 text-gray-900 shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                                </div>

                                <div>
                                    <label for="category_id"
                                        class="block text-sm font-bold text-gray-900 mb-2">Kategori</label>
                                    <div class="relative">
                                        <select id="category_id" name="category_id" required
                                            class="block w-full rounded-2xl border-gray-200 py-3 px-4 text-gray-900 shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all appearance-none">
                                            <option value="">Pilih Kategori</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div
                                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pricing & Unit Section -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-2 mb-6">Harga & Satuan
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="price" class="block text-sm font-bold text-gray-900 mb-2">Harga
                                        Jual</label>
                                    <div class="relative">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                            <span class="text-gray-500 font-bold">Rp</span>
                                        </div>
                                        <input type="number" name="price" id="price" step="0.01"
                                            value="{{ $product->price }}" required placeholder="0"
                                            class="block w-full rounded-2xl border-gray-200 py-3 pl-12 pr-4 text-gray-900 shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-bold">
                                    </div>
                                </div>

                                <div>
                                    <label for="purchase_price" class="block text-sm font-bold text-gray-900 mb-2">Harga
                                        Beli (HPP)</label>
                                    <div class="relative">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                            <span class="text-gray-500 font-bold">Rp</span>
                                        </div>
                                        <input type="number" name="purchase_price" id="purchase_price" step="0.01"
                                            value="{{ $product->purchase_price }}" placeholder="0"
                                            class="block w-full rounded-2xl border-gray-200 py-3 pl-12 pr-4 text-gray-900 shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-bold">
                                    </div>
                                </div>

                                <div>
                                    <label for="unit" class="block text-sm font-bold text-gray-900 mb-2">Satuan</label>
                                    <input type="text" name="unit" id="unit" value="{{ $product->unit }}"
                                        required placeholder="Contoh: Pcs, Kg, Box"
                                        class="block w-full rounded-2xl border-gray-200 py-3 px-4 text-gray-900 shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                                </div>

                                <div>
                                    <label for="stock" class="block text-sm font-bold text-gray-900 mb-2">Stok</label>
                                    <input type="number" name="stock" id="stock" step="0.01"
                                        value="{{ $product->stock }}" required
                                        class="block w-full rounded-2xl border-gray-200 py-3 px-4 text-gray-900 shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                                </div>
                            </div>
                        </div>

                        <!-- Description Section -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-2 mb-6">Keterangan
                                Tambahan</h3>
                            <label for="description" class="block text-sm font-bold text-gray-900 mb-2">Deskripsi
                                (Opsional)</label>
                            <textarea id="description" name="description" rows="4" placeholder="Tulis deskripsi singkat tentang produk ini..."
                                class="block w-full rounded-2xl border-gray-200 py-3 px-4 text-gray-900 shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">{{ $product->description }}</textarea>
                        </div>
                    </div>

                    <div class="mt-10 pt-6 border-t border-gray-100 flex items-center justify-end gap-4">
                        <a href="{{ route('products.index') }}"
                            class="px-6 py-3 rounded-2xl text-sm font-bold text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition-all">
                            Batal
                        </a>
                        <button type="submit"
                            class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-sm font-bold rounded-2xl text-white bg-indigo-600 shadow-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all hover:-translate-y-0.5">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
