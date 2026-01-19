@extends('layouts.app')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-black text-gray-900 tracking-tight">Pengelolaan Pengeluaran</h1>
        <p class="text-gray-500 font-medium">Catat dan pantau semua pengeluaran operasional.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <div class="lg:col-span-1">
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8">
                <h2 class="text-xl font-black text-gray-900 mb-6 uppercase tracking-widest text-sm">Tambah Pengeluaran</h2>
                <form action="{{ route('expenses.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="space-y-2">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest pl-1">Tanggal</label>
                        <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                            class="block w-full rounded-2xl border-2 border-gray-50 py-3 px-4 text-gray-900 shadow-sm focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all font-bold">
                    </div>

                    <div class="space-y-2">
                        <label
                            class="block text-xs font-black text-gray-400 uppercase tracking-widest pl-1">Kategori</label>
                        <select name="category" required
                            class="block w-full rounded-2xl border-2 border-gray-50 py-3 px-4 text-gray-900 shadow-sm focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all font-bold">
                            <option value="Operasional">Operasional</option>
                            <option value="Listrik & Air">Listrik & Air</option>
                            <option value="Pemeliharaan">Pemeliharaan</option>
                            <option value="Perlengkapan">Perlengkapan</option>
                            <option value="Sewa">Sewa</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest pl-1">Jumlah
                            (Rp)</label>
                        <input type="number" name="amount" placeholder="0" required
                            class="block w-full rounded-2xl border-2 border-gray-50 py-3 px-4 text-gray-900 shadow-sm focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all font-bold">
                    </div>

                    <div class="space-y-2">
                        <label
                            class="block text-xs font-black text-gray-400 uppercase tracking-widest pl-1">Keterangan</label>
                        <textarea name="description" rows="3" placeholder="Contoh: Beli sapu dan lap..."
                            class="block w-full rounded-2xl border-2 border-gray-50 py-3 px-4 text-gray-900 shadow-sm focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all font-bold"></textarea>
                    </div>

                    <button type="submit"
                        class="w-full py-4 bg-gray-900 text-white font-black rounded-2xl hover:bg-gray-800 transition-all active:scale-[0.98] uppercase tracking-widest text-xs">
                        Simpan Pengeluaran
                    </button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-black text-gray-900 uppercase tracking-widest text-sm">Riwayat Pengeluaran
                        </h2>
                        <p class="text-gray-400 text-xs font-bold mt-1">Total:
                            Rp{{ number_format($totalExpenses, 0, ',', '.') }}</p>
                    </div>

                    <form action="{{ route('expenses.index') }}" method="GET" class="flex items-center gap-2">
                        <input type="date" name="start_date" value="{{ $startDate }}"
                            class="rounded-xl border-gray-100 text-xs font-bold">
                        <span class="text-gray-400 font-black text-xs">S/D</span>
                        <input type="date" name="end_date" value="{{ $endDate }}"
                            class="rounded-xl border-gray-100 text-xs font-bold">
                        <button type="submit" class="p-2 bg-gray-100 rounded-xl hover:bg-gray-200 transition-all">
                            <svg class="w-4 h-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">
                                    Tanggal</th>
                                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">
                                    Kategori</th>
                                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">
                                    Keterangan</th>
                                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Jumlah
                                </th>
                                <th
                                    class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-center">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($expenses as $expense)
                                <tr class="hover:bg-gray-50/50 transition-colors group">
                                    <td class="px-8 py-5">
                                        <p class="text-sm font-black text-gray-900">
                                            {{ \Carbon\Carbon::parse($expense->date)->format('d M Y') }}</p>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-indigo-50 text-indigo-600">
                                            {{ $expense->category }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-5">
                                        <p class="text-xs font-medium text-gray-500">{{ $expense->description ?: '-' }}</p>
                                    </td>
                                    <td class="px-8 py-5">
                                        <p class="text-sm font-black text-gray-900">
                                            Rp{{ number_format($expense->amount, 0, ',', '.') }}</p>
                                    </td>
                                    <td class="px-8 py-5 text-center">
                                        <div class="flex justify-center gap-3">
                                            <a href="{{ route('expenses.edit', $expense) }}"
                                                class="p-2 text-gray-400 hover:text-indigo-600 transition-colors">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <form action="{{ route('expenses.destroy', $expense) }}" method="POST"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-2 text-gray-400 hover:text-red-500 transition-colors">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-8 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="p-4 bg-gray-50 rounded-full mb-4">
                                                <svg class="w-8 h-8 text-gray-300" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                </svg>
                                            </div>
                                            <p class="text-gray-400 font-bold uppercase tracking-widest text-[10px]">Belum
                                                ada data pengeluaran</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($expenses->hasPages())
                    <div class="px-8 py-4 border-t border-gray-50">
                        {{ $expenses->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
