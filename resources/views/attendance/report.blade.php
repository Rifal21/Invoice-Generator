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
                            <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Karyawan</th>
                            <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Tanggal</th>
                            <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Waktu Masuk
                            </th>
                            <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Foto Masuk</th>
                            <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Waktu Pulang
                            </th>
                            <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Foto Pulang
                            </th>
                            <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Status</th>
                            <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Jarak</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($attendances as $attendance)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-5">
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
                                <td class="px-6 py-5">
                                    <div class="space-y-0.5">
                                        <p class="text-sm font-bold text-gray-900">
                                            {{ \Carbon\Carbon::parse($attendance->date)->format('d M Y') }}</p>
                                        <p class="text-[10px] text-gray-400 font-bold uppercase">
                                            {{ \Carbon\Carbon::parse($attendance->date)->format('l') }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span class="text-sm font-bold text-gray-700">
                                            {{ \Carbon\Carbon::parse($attendance->check_in)->format('H:i') }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    @if ($attendance->check_in_photo)
                                        <button
                                            onclick="showPhoto('{{ Storage::url($attendance->check_in_photo) }}', '{{ $attendance->user->name }} - Check In')"
                                            class="group relative w-16 h-16 rounded-xl overflow-hidden border-2 border-emerald-200 hover:border-emerald-400 transition-all hover:scale-110">
                                            <img src="{{ Storage::url($attendance->check_in_photo) }}" alt="Check In Photo"
                                                class="w-full h-full object-cover">
                                            <div
                                                class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all flex items-center justify-center">
                                                <svg class="w-6 h-6 text-white opacity-0 group-hover:opacity-100 transition-all"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                                </svg>
                                            </div>
                                        </button>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Tidak ada foto</span>
                                    @endif
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="w-1.5 h-1.5 rounded-full {{ $attendance->check_out ? 'bg-red-500' : 'bg-gray-300' }}"></span>
                                        <span class="text-sm font-bold text-gray-700">
                                            {{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '--:--' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    @if ($attendance->check_out_photo)
                                        <button
                                            onclick="showPhoto('{{ Storage::url($attendance->check_out_photo) }}', '{{ $attendance->user->name }} - Check Out')"
                                            class="group relative w-16 h-16 rounded-xl overflow-hidden border-2 border-red-200 hover:border-red-400 transition-all hover:scale-110">
                                            <img src="{{ Storage::url($attendance->check_out_photo) }}"
                                                alt="Check Out Photo" class="w-full h-full object-cover">
                                            <div
                                                class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-all flex items-center justify-center">
                                                <svg class="w-6 h-6 text-white opacity-0 group-hover:opacity-100 transition-all"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                                </svg>
                                            </div>
                                        </button>
                                    @else
                                        <span
                                            class="text-xs text-gray-400 italic">{{ $attendance->check_out ? 'Tidak ada foto' : '-' }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-5">
                                    @if ($attendance->status == 'present')
                                        <span
                                            class="px-3 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-black uppercase tracking-widest rounded-full">Hadir</span>
                                    @else
                                        <span
                                            class="px-3 py-1 bg-red-100 text-red-700 text-[10px] font-black uppercase tracking-widest rounded-full">Terlambat</span>
                                    @endif
                                </td>
                                <td class="px-6 py-5">
                                    @if ($attendance->check_in_distance)
                                        <div class="space-y-0.5">
                                            <p class="text-xs font-bold text-gray-700">
                                                <span class="text-emerald-600">In:</span>
                                                {{ number_format($attendance->check_in_distance, 0) }}m
                                            </p>
                                            @if ($attendance->check_out_distance)
                                                <p class="text-xs font-bold text-gray-700">
                                                    <span class="text-red-600">Out:</span>
                                                    {{ number_format($attendance->check_out_distance, 0) }}m
                                                </p>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 italic">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-8 py-10 text-center">
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

    <!-- Photo Modal -->
    <div id="photoModal" class="hidden fixed inset-0 bg-black/80 z-50 flex items-center justify-center p-4"
        onclick="closePhotoModal()">
        <div class="relative max-w-4xl w-full" onclick="event.stopPropagation()">
            <button onclick="closePhotoModal()"
                class="absolute -top-12 right-0 text-white hover:text-gray-300 transition-colors">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <div class="bg-white rounded-3xl overflow-hidden shadow-2xl">
                <div class="p-4 bg-gray-50 border-b border-gray-100">
                    <h3 id="photoTitle" class="text-lg font-black text-gray-900"></h3>
                </div>
                <div class="p-6 flex items-center justify-center bg-gray-900">
                    <img id="photoImage" src="" alt="Attendance Photo" class="max-h-[70vh] w-auto rounded-xl">
                </div>
            </div>
        </div>
    </div>

    <script>
        function showPhoto(photoUrl, title) {
            document.getElementById('photoImage').src = photoUrl;
            document.getElementById('photoTitle').textContent = title;
            document.getElementById('photoModal').classList.remove('hidden');
        }

        function closePhotoModal() {
            document.getElementById('photoModal').classList.add('hidden');
        }

        // Close modal on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePhotoModal();
            }
        });
    </script>
@endsection
