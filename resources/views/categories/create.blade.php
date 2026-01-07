@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-10">
        <!-- Centered Layout for better focus -->
        <div class="max-w-2xl mx-auto">
            <div class="mb-8">
                <nav class="flex mb-4" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2">
                        <li><a href="{{ route('categories.index') }}"
                                class="text-xs md:text-sm font-bold text-gray-400 hover:text-indigo-600">Kategori</a></li>
                        <li><svg class="h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd" />
                            </svg></li>
                        <li class="text-xs md:text-sm font-bold text-indigo-600">Buat Baru</li>
                    </ol>
                </nav>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Tambah Kategori</h1>
                <p class="mt-2 text-sm text-gray-500">Tambahkan kategori baru untuk mengelompokkan produk Anda.</p>
            </div>

            <div class="bg-white shadow-xl rounded-3xl border border-gray-100 overflow-hidden">
                <form action="{{ route('categories.store') }}" method="POST" class="p-6 md:p-8">
                    @csrf

                    <div class="space-y-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-bold text-gray-900 mb-2">Nama Kategori</label>
                            <input type="text" name="name" id="name" required
                                placeholder="Contoh: Makanan Ringan"
                                class="block w-full rounded-2xl border-gray-200 py-3 px-4 text-gray-900 shadow-sm focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                            <p class="mt-2 text-xs text-gray-500">Nama kategori harus unik dan deskriptif.</p>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-end gap-4">
                        <a href="{{ route('categories.index') }}"
                            class="px-6 py-3 rounded-2xl text-sm font-bold text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition-all">
                            Batal
                        </a>
                        <button type="submit"
                            class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm font-bold rounded-2xl text-white bg-indigo-600 shadow-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all hover:-translate-y-0.5">
                            Simpan Kategori
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
