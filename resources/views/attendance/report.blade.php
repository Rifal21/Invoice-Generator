@extends('layouts.app')

@section('content')
    <div class="space-y-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">LAPORAN ABSENSI</h1>
                <p class="text-gray-500 font-medium">Rekapitulasi kehadiran seluruh karyawan</p>
            </div>

            <form action="{{ route('attendance.report') }}" method="GET" class="flex gap-2">
                <input type="date" name="date" value="{{ request('date', date('Y-m-d')) }}"
                    class="px-4 py-2 border-none bg-white rounded-xl shadow-sm focus:ring-2 focus:ring-indigo-600 font-bold text-sm">
                <button type="submit"
                    class="px-6 py-2 bg-indigo-600 text-white rounded-xl font-bold shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all">
                    FILTER
                </button>
            </form>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @php
                $present = \App\Models\Attendance::where('date', request('date', date('Y-m-d')))
                    ->where('status', 'present')
                    ->count();
                $late = \App\Models\Attendance::where('date', request('date', date('Y-m-d')))
                    ->where('status', 'late')
                    ->count();
                $absent = \App\Models\User::count() - ($present + $late);
            @endphp
            <div class="bg-emerald-50 border border-emerald-100 p-6 rounded-[2rem]">
                <p class="text-emerald-600 font-black text-xs uppercase tracking-widest mb-1">Tepat Waktu</p>
                <p class="text-3xl font-black text-emerald-700">{{ $present }}</p>
            </div>
            <div class="bg-orange-50 border border-orange-100 p-6 rounded-[2rem]">
                <p class="text-orange-600 font-black text-xs uppercase tracking-widest mb-1">Terlambat</p>
                <p class="text-3xl font-black text-orange-700">{{ $late }}</p>
            </div>
            <div class="bg-red-50 border border-red-100 p-6 rounded-[2rem]">
                <p class="text-red-600 font-black text-xs uppercase tracking-widest mb-1">Dinas Luar/Belum Absen</p>
                <p class="text-3xl font-black text-red-700">{{ max(0, $absent) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] overflow-hidden shadow-sm border border-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Karyawan</th>
                            <th class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Waktu</th>
                            <th class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Status</th>
                            <th class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($attendances as $attendance)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-700 font-black text-sm">
                                            {{ substr($attendance->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-black text-gray-900">{{ $attendance->user->name }}</p>
                                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                                {{ $attendance->user->role }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            <span class="text-sm font-bold text-gray-700">Masuk:
                                                {{ substr($attendance->check_in, 0, 5) }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="w-1.5 h-1.5 rounded-full {{ $attendance->check_out ? 'bg-red-500' : 'bg-gray-300' }}"></span>
                                            <span class="text-sm font-bold text-gray-700">Pulang:
                                                {{ $attendance->check_out ? substr($attendance->check_out, 0, 5) : '--:--' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-5">
                                    @if ($attendance->status == 'present')
                                        <span
                                            class="px-3 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-black uppercase tracking-widest rounded-full">Hadir</span>
                                    @else
                                        <span
                                            class="px-3 py-1 bg-red-100 text-red-700 text-[10px] font-black uppercase tracking-widest rounded-full">Terlambat</span>
                                    @endif
                                </td>
                                <td class="px-8 py-5">
                                    <p class="text-xs text-gray-500 font-medium italic">{{ $attendance->notes ?? '-' }}</p>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-8 py-10 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mb-4">
                                            <svg class="w-8 h-8 text-gray-300" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                            </svg>
                                        </div>
                                        <p class="text-gray-400 font-bold uppercase tracking-widest text-[10px]">Belum ada
                                            data absensi hari ini</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4">
            {{ $attendances->links() }}
        </div>
    </div>
@endsection
