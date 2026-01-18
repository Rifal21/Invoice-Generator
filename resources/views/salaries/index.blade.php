@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Penggajian Pegawai</h1>
                <p class="text-gray-500 font-medium">Kelola pembayaran gaji dan insentif karyawan.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('salaries.create') }}"
                    class="inline-flex items-center justify-center px-6 py-3 bg-indigo-600 text-white font-black rounded-2xl shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all active:scale-95 gap-2 uppercase text-xs tracking-widest">
                    BUAT SLIP GAJI
                </a>
            </div>
        </div>

        @if ($salaries->isEmpty())
            <div class="bg-white rounded-[2.5rem] p-16 text-center border border-dashed border-gray-200">
                <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-black text-gray-900 mb-2">Belum Ada Data Gaji</h3>
                <p class="text-gray-500 font-medium">Data gaji untuk periode ini belum tersedia. Silakan buat slip gaji
                    baru.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Pengeluaran Gaji
                    </p>
                    <h3 class="text-2xl font-black text-gray-900 tracking-tighter">
                        Rp{{ number_format($salaries->sum('net_salary'), 0, ',', '.') }}</h3>
                </div>
                <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Status Lunas</p>
                    <div class="flex items-center gap-2">
                        <h3 class="text-2xl font-black text-emerald-600 tracking-tighter">
                            {{ $salaries->where('status', 'paid')->count() }}</h3>
                        <span class="text-gray-400 font-bold text-sm">Pegawai</span>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Belum Dibayar</p>
                    <div class="flex items-center gap-2">
                        <h3 class="text-2xl font-black text-red-600 tracking-tighter">
                            {{ $salaries->where('status', 'pending')->count() }}</h3>
                        <span class="text-gray-400 font-bold text-sm">Pegawai</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th
                                    class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400 border-b border-gray-100">
                                    Nama Pegawai</th>
                                <th
                                    class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400 border-b border-gray-100">
                                    Tanggal</th>
                                <th
                                    class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400 border-b border-gray-100">
                                    Gaji Pokok</th>
                                <th
                                    class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400 border-b border-gray-100">
                                    Bonus/Potongan</th>
                                <th
                                    class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400 border-b border-gray-100">
                                    Total Bersih</th>
                                <th
                                    class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400 border-b border-gray-100 text-center">
                                    Status</th>
                                <th
                                    class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-gray-400 border-b border-gray-100 text-right">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach ($salaries as $salary)
                                <tr class="hover:bg-gray-50/30 transition-colors">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 font-black text-xs">
                                                {{ substr($salary->user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p
                                                    class="font-bold text-gray-900 uppercase tracking-tight leading-none mb-1">
                                                    {{ $salary->user->name }}</p>
                                                <p class="text-[10px] font-bold text-gray-400 uppercase leading-none">
                                                    {{ $salary->user->role }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex flex-col">
                                            @if ($salary->start_date && $salary->end_date)
                                                <span class="font-bold text-gray-900 leading-none mb-1">
                                                    {{ $salary->start_date->format('d/m/y') }} -
                                                    {{ $salary->end_date->format('d/m/y') }}
                                                </span>
                                                <span
                                                    class="text-[9px] font-bold text-indigo-500 uppercase tracking-widest leading-none">
                                                    {{ $salary->working_days }} HARI KERJA
                                                </span>
                                            @else
                                                <span
                                                    class="font-bold text-gray-900 leading-none mb-1">{{ $salary->period->isoFormat('D MMMM Y') }}</span>
                                                <span
                                                    class="text-[9px] font-bold text-gray-400 uppercase tracking-widest leading-none">{{ $salary->period->translatedFormat('l') }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <span
                                            class="font-bold text-gray-700">Rp{{ number_format($salary->base_salary, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex flex-col">
                                            <span
                                                class="text-[11px] font-black text-emerald-600 uppercase tracking-tighter">+
                                                Rp{{ number_format($salary->bonus, 0, ',', '.') }}</span>
                                            <span class="text-[11px] font-black text-red-500 uppercase tracking-tighter">-
                                                Rp{{ number_format($salary->deductions, 0, ',', '.') }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <span
                                            class="font-black text-indigo-600 text-lg tracking-tighter">Rp{{ number_format($salary->net_salary, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        @if ($salary->status === 'paid')
                                            <div class="flex flex-col items-center">
                                                <span
                                                    class="px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full text-[10px] font-black border border-emerald-100">LUNAS</span>
                                                <span
                                                    class="text-[8px] font-bold text-gray-400 mt-1 uppercase">{{ $salary->paid_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                        @else
                                            <span
                                                class="px-3 py-1 bg-amber-50 text-amber-700 rounded-full text-[10px] font-black border border-amber-100">PENDING</span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <div class="flex justify-end gap-2">
                                            @if ($salary->status === 'pending')
                                                <form action="{{ route('salaries.mark-as-paid', $salary) }}"
                                                    method="POST">
                                                    @csrf
                                                    <button type="submit"
                                                        class="p-2 text-emerald-600 hover:bg-emerald-50 rounded-xl transition-all shadow-sm"
                                                        title="Tandai Sudah Dibayar">
                                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                            <a href="{{ route('salaries.edit', $salary) }}"
                                                class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-xl transition-all shadow-sm">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <form action="{{ route('salaries.destroy', $salary) }}" method="POST"
                                                onsubmit="return confirm('Hapus data gaji ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-2 text-red-600 hover:bg-red-50 rounded-xl transition-all shadow-sm">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="px-8 py-4 border-t border-gray-50">
                        {{ $salaries->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
