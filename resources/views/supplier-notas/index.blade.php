@extends('layouts.app')

@section('title', 'Nota Supplier')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-end md:justify-between mb-8 gap-4">
            <div class="min-w-0 flex-1">
                <h1 class="text-3xl md:text-5xl font-black text-gray-900 tracking-tight">Nota Supplier</h1>
                <p class="mt-3 text-base md:text-lg text-gray-500 font-medium">Manajemen arsip digital nota transaksi dari
                    mitra supplier.</p>
            </div>
            <div class="flex shrink-0">
                <a href="{{ route('supplier-notas.create') }}"
                    class="group w-full md:w-auto inline-flex items-center justify-center px-8 py-4 border border-transparent text-lg font-black rounded-[2rem] text-white bg-indigo-600 shadow-2xl shadow-indigo-200 hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-500/50 transition-all duration-300 transform hover:-translate-y-1">
                    <i class="fas fa-plus-circle mr-3 text-xl group-hover:rotate-12 transition-transform"></i>
                    Upload Nota Baru
                </a>
            </div>
        </div>

        <!-- Quick Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div
                class="bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-[2.5rem] p-8 shadow-2xl text-white relative overflow-hidden group">
                <div
                    class="absolute right-0 top-0 opacity-10 transform translate-x-4 -translate-y-4 transition-transform group-hover:scale-110">
                    <i class="fas fa-file-invoice-dollar text-[10rem]"></i>
                </div>
                <div class="relative z-10">
                    <p class="text-xs font-black text-indigo-100 uppercase tracking-[0.2em] mb-2">Total Nota Tersimpan</p>
                    <h3 class="text-4xl font-black">{{ number_format($stats['total_count']) }} <span
                            class="text-lg font-medium">Bks</span></h3>
                    <div
                        class="mt-4 inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 text-[10px] font-bold">
                        <i class="fas fa-cloud-arrow-up"></i> Terintegrasi Cloud
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-[2.5rem] p-8 shadow-xl border border-gray-100 relative overflow-hidden group">
                <div
                    class="absolute right-6 top-6 bg-emerald-100 rounded-2xl p-4 text-emerald-600 transition-transform group-hover:rotate-6">
                    <i class="fas fa-wallet text-2xl"></i>
                </div>
                <div>
                    <p class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Total Nominal Transaksi</p>
                    <h3 class="text-3xl font-black text-gray-900">Rp
                        {{ number_format($stats['total_amount'], 0, ',', '.') }}</h3>
                    <p class="text-xs text-emerald-600 mt-4 font-bold flex items-center gap-1">
                        <i class="fas fa-chart-line"></i> Akumulasi Seluruh Nota
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-[2.5rem] p-8 shadow-xl border border-gray-100 relative overflow-hidden group">
                <div
                    class="absolute right-6 top-6 bg-amber-100 rounded-2xl p-4 text-amber-600 transition-transform group-hover:-rotate-6">
                    <i class="fas fa-file-circle-plus text-2xl"></i>
                </div>
                <div>
                    <p class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Upload Hari Ini</p>
                    <h3 class="text-3xl font-black text-gray-900">{{ number_format($stats['today_count']) }} <span
                            class="text-lg font-medium text-gray-400">Nota</span></h3>
                    <p class="text-xs text-amber-600 mt-4 font-bold flex items-center gap-1">
                        <i class="fas fa-history"></i> {{ now()->format('d F Y') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Search & Filter Card -->
        <div class="bg-white shadow-2xl rounded-[2.5rem] p-2 mb-10 border border-gray-100 relative z-10">
            <div class="p-6">
                <form action="{{ route('supplier-notas.index') }}" method="GET"
                    class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
                    <div class="md:col-span-4">
                        <label for="search"
                            class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2">Cari
                            Nota</label>
                        <div class="relative group">
                            <i
                                class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 transition-colors group-focus-within:text-indigo-500"></i>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                placeholder="Nomor nota atau supplier..."
                                class="block w-full rounded-[1.5rem] border-gray-100 bg-gray-50/50 py-4 pl-14 pr-5 text-sm font-medium text-gray-900 shadow-inner focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                        </div>
                    </div>
                    <div class="md:col-span-3">
                        <label for="supplier_id"
                            class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2">Filter
                            Supplier</label>
                        <select name="supplier_id" id="supplier_id"
                            class="block w-full rounded-[1.5rem] border-gray-100 bg-gray-50/50 py-4 px-5 text-sm font-medium text-gray-900 shadow-inner focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all appearance-none cursor-pointer">
                            <option value="">Semua Supplier</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}"
                                    {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-3">
                        <label for="date"
                            class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-2">Pilih
                            Tanggal</label>
                        <input type="date" name="date" id="date" value="{{ request('date') }}"
                            class="block w-full rounded-[1.5rem] border-gray-100 bg-gray-50/50 py-4 px-5 text-sm font-medium text-gray-900 shadow-inner focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all">
                    </div>
                    <div class="md:col-span-2">
                        <button type="submit"
                            class="w-full flex items-center justify-center py-4 px-6 border border-transparent rounded-[1.5rem] shadow-xl text-sm font-black text-white bg-gray-900 hover:bg-black focus:outline-none focus:ring-4 focus:ring-gray-300 transition-all transform active:scale-95">
                            <i class="fas fa-filter mr-2"></i>
                            Terapkan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Display Section -->
        <div class="bg-white shadow-2xl rounded-[3rem] overflow-hidden border border-gray-100 mb-10">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th scope="col"
                                class="px-8 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">
                                Info Transaksi</th>
                            <th scope="col"
                                class="px-8 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">
                                Mitra Supplier</th>
                            <th scope="col"
                                class="px-8 py-6 text-left text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">
                                Status & Nominal</th>
                            <th scope="col"
                                class="px-8 py-6 text-right text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">
                                Opsi Utama</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse ($notas as $nota)
                            <tr class="hover:bg-indigo-50/30 transition-colors group">
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="flex-shrink-0 w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 transition-transform group-hover:scale-110">
                                            <i
                                                class="fas {{ Str::endsWith($nota->file_path, '.pdf') ? 'fa-file-pdf' : 'fa-file-image' }} text-xl"></i>
                                        </div>
                                        <div>
                                            <a href="{{ route('supplier-notas.show', $nota) }}"
                                                class="text-sm font-black text-gray-900 hover:text-indigo-600 transition-colors">#{{ $nota->nota_number }}</a>
                                            <div class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-tight">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ \Carbon\Carbon::parse($nota->transaction_date)->format('d M Y') }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <a href="{{ route('supplier-notas.show', $nota) }}"
                                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-gray-50 border border-gray-100 hover:bg-gray-100 transition-all group/chip">
                                        <div
                                            class="w-6 h-6 rounded-lg bg-indigo-100 flex items-center justify-center text-[10px] font-black text-indigo-700 uppercase group-hover/chip:bg-indigo-600 group-hover/chip:text-white transition-colors">
                                            {{ substr($nota->supplier->name, 0, 1) }}
                                        </div>
                                        <span class="text-sm font-bold text-gray-700">{{ $nota->supplier->name }}</span>
                                    </a>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="text-sm font-black text-indigo-600 mb-1">Rp
                                        {{ number_format($nota->total_amount, 0, ',', '.') }}</div>
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-md text-[8px] font-black tracking-widest uppercase {{ $nota->transaction_date < today() ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ $nota->transaction_date < today() ? 'Terarsip' : 'Baru' }}
                                    </span>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap text-right text-sm font-medium">
                                    <div
                                        class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a href="{{ route('supplier-notas.show', $nota) }}"
                                            class="flex items-center justify-center w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-all shadow-sm"
                                            title="Detail Nota">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('supplier-notas.download', $nota) }}"
                                            class="flex items-center justify-center w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white transition-all shadow-sm"
                                            title="Unduh File">
                                            <i class="fas fa-download"></i>
                                        </a>

                                        <form action="{{ route('supplier-notas.destroy', $nota) }}" method="POST"
                                            class="inline-block"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus nota ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="flex items-center justify-center w-10 h-10 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition-all shadow-sm"
                                                title="Hapus Nota">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-8 py-20 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div
                                            class="w-20 h-20 rounded-full bg-gray-50 flex items-center justify-center text-gray-300 mb-4 animate-bounce">
                                            <i class="fas fa-file-invoice text-3xl"></i>
                                        </div>
                                        <h4 class="text-lg font-black text-gray-400">Belum Ada Data Nota</h4>
                                        <p class="text-sm text-gray-300 mt-1">Silakan mulai dengan mengunggah nota
                                            transaksi pertama Anda.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-100">
                {{ $notas->withQueryString()->links() }}
            </div>
        </div>
    </div>
@endsection
