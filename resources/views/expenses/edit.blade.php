@extends('layouts.app')

@section('content')
    <div class="mb-8">
        <a href="{{ route('expenses.index') }}"
            class="text-indigo-600 font-black text-xs uppercase tracking-widest flex items-center gap-2 mb-4 hover:gap-3 transition-all">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Daftar
        </a>
        <h1 class="text-3xl font-black text-gray-900 tracking-tight">Edit Pengeluaran</h1>
        <p class="text-gray-500 font-medium">Perbarui data pengeluaran operasional Anda.</p>
    </div>

    <div class="max-w-2xl bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-10">
        <form action="{{ route('expenses.update', $expense) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest pl-1">Tanggal</label>
                    <input type="date" name="date" value="{{ $expense->date }}" required
                        class="block w-full rounded-2xl border-2 border-gray-50 py-3 px-4 text-gray-900 shadow-sm focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all font-bold">
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest pl-1">Kategori</label>
                    <select name="category" required
                        class="block w-full rounded-2xl border-2 border-gray-50 py-3 px-4 text-gray-900 shadow-sm focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all font-bold">
                        <option value="Operasional" {{ $expense->category == 'Operasional' ? 'selected' : '' }}>Operasional
                        </option>
                        <option value="Listrik & Air" {{ $expense->category == 'Listrik & Air' ? 'selected' : '' }}>Listrik
                            & Air</option>
                        <option value="Pemeliharaan" {{ $expense->category == 'Pemeliharaan' ? 'selected' : '' }}>
                            Pemeliharaan</option>
                        <option value="Perlengkapan" {{ $expense->category == 'Perlengkapan' ? 'selected' : '' }}>
                            Perlengkapan</option>
                        <option value="Sewa" {{ $expense->category == 'Sewa' ? 'selected' : '' }}>Sewa</option>
                        <option value="Lainnya" {{ $expense->category == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest pl-1">Jumlah (Rp)</label>
                <input type="number" name="amount" value="{{ (int) $expense->amount }}" required
                    class="block w-full rounded-2xl border-2 border-gray-50 py-3 px-4 text-gray-900 shadow-sm focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all font-bold">
            </div>

            <div class="space-y-2">
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest pl-1">Keterangan</label>
                <textarea name="description" rows="4"
                    class="block w-full rounded-2xl border-2 border-gray-50 py-3 px-4 text-gray-900 shadow-sm focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all font-bold">{{ $expense->description }}</textarea>
            </div>

            <div class="pt-4">
                <button type="submit"
                    class="w-full py-4 bg-gray-900 text-white font-black rounded-2xl hover:bg-gray-800 transition-all active:scale-[0.98] uppercase tracking-widest text-xs">
                    Perbarui Pengeluaran
                </button>
            </div>
        </form>
    </div>
@endsection
