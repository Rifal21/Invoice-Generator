@extends('layouts.app')

@section('title', 'Detail Nota #' . $supplierNota->nota_number)

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
        <!-- Header -->
        <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('supplier-notas.index') }}"
                    class="w-12 h-12 rounded-2xl bg-white shadow-lg border border-gray-100 flex items-center justify-center text-gray-400 hover:text-indigo-600 transition-all">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">Detail Nota Supplier</h1>
                    <div class="flex items-center gap-2 mt-1">
                        <span
                            class="text-xs font-black text-indigo-600 uppercase tracking-widest">#{{ $supplierNota->nota_number }}</span>
                        <span class="text-gray-300">•</span>
                        <span
                            class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $supplierNota->supplier->name }}</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('supplier-notas.download', $supplierNota) }}"
                    class="flex-1 md:flex-none inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white font-black rounded-2xl shadow-xl hover:bg-indigo-700 transition-all transform hover:-translate-y-1 leading-none">
                    <i class="fas fa-download mr-2"></i> Unduh Nota
                </a>
                <form action="{{ route('supplier-notas.destroy', $supplierNota) }}" method="POST"
                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus nota ini?');" class="flex-1 md:flex-none">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="w-full inline-flex items-center justify-center px-6 py-3 bg-rose-50 text-rose-600 font-black rounded-2xl hover:bg-rose-100 transition-all leading-none">
                        <i class="fas fa-trash-alt mr-2"></i> Hapus
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Details Column -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Info Card -->
                <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 overflow-hidden">
                    <div class="p-8">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Ringkasan Transaksi
                        </h3>

                        <div class="space-y-6">
                            <div>
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Nominal
                                </p>
                                <p class="text-2xl font-black text-indigo-600">Rp
                                    {{ number_format($supplierNota->total_amount, 0, ',', '.') }}</p>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Tgl
                                        Transaksi</p>
                                    <p class="text-sm font-bold text-gray-900">
                                        {{ \Carbon\Carbon::parse($supplierNota->transaction_date)->format('d/m/Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Tgl
                                        Upload</p>
                                    <p class="text-sm font-bold text-gray-900">
                                        {{ $supplierNota->created_at->format('d/m/Y') }}</p>
                                </div>
                            </div>

                            <div class="pt-6 border-t border-gray-50">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Mitra
                                    Supplier</p>
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 font-black">
                                        {{ substr($supplierNota->supplier->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-gray-900">{{ $supplierNota->supplier->name }}</p>
                                        <p class="text-[10px] font-bold text-gray-400 italic">ID:
                                            SUP-{{ str_pad($supplierNota->supplier_id, 3, '0', STR_PAD_LEFT) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description Card -->
                <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 p-8">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Keterangan</h3>
                    <p class="text-sm text-gray-600 leading-relaxed italic">
                        {{ $supplierNota->description ?? 'Tidak ada keterangan tambahan untuk nota ini.' }}
                    </p>
                </div>
            </div>

            <!-- Preview Column -->
            <div class="lg:col-span-2">
                <div
                    class="bg-white rounded-[2.5rem] shadow-2xl border border-gray-100 overflow-hidden h-full flex flex-col">
                    <div class="px-8 py-5 border-b border-gray-50 flex items-center justify-between bg-gray-50/30">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-eye text-indigo-500"></i>
                            <span class="text-xs font-black text-gray-900 uppercase tracking-widest">Pratinjau
                                Dokumen</span>
                        </div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Format:
                            {{ strtoupper(pathinfo($supplierNota->file_path, PATHINFO_EXTENSION)) }}</span>
                    </div>

                    <div class="flex-1 p-2 bg-gray-100/50 min-h-[500px] flex items-center justify-center">
                        @php
                            $extension = strtolower(pathinfo($supplierNota->file_path, PATHINFO_EXTENSION));
                        @endphp

                        @if (in_array($extension, ['jpg', 'jpeg', 'png']))
                            <img src="{{ Storage::url($supplierNota->file_path) }}"
                                class="max-w-full h-auto rounded-xl shadow-lg" alt="Nota Preview">
                        @elseif($extension === 'pdf')
                            <iframe src="{{ Storage::url($supplierNota->file_path) }}"
                                class="w-full h-full min-h-[600px] rounded-xl border-none shadow-lg"></iframe>
                        @else
                            <div class="text-center p-12">
                                <div
                                    class="w-20 h-20 rounded-full bg-white shadow-lg flex items-center justify-center text-gray-300 mx-auto mb-4">
                                    <i class="fas fa-file-circle-question text-3xl"></i>
                                </div>
                                <p class="text-sm font-bold text-gray-500">Pratinjau tidak tersedia untuk format ini.</p>
                                <a href="{{ route('supplier-notas.download', $supplierNota) }}"
                                    class="mt-4 inline-block text-xs font-black text-indigo-600 hover:text-indigo-800">
                                    Silakan unduh untuk melihat <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
