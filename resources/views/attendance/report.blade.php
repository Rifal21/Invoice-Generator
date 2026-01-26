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
                            <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-right">Aksi
                            </th>
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
                                    @elseif($attendance->status == 'late')
                                        <span
                                            class="px-3 py-1 bg-amber-100 text-amber-700 text-[10px] font-black uppercase tracking-widest rounded-full">Terlambat</span>
                                    @else
                                        <span
                                            class="px-3 py-1 bg-red-100 text-red-700 text-[10px] font-black uppercase tracking-widest rounded-full">Mangkir</span>
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
                                <td class="px-6 py-5 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button onclick="editAttendance({{ json_encode($attendance) }})"
                                            class="p-2 bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-100 transition-colors"
                                            title="Edit">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <form id="delete-form-{{ $attendance->id }}"
                                            action="{{ route('attendance.destroy', $attendance->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDelete({{ $attendance->id }})"
                                                class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors"
                                                title="Hapus">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-8 py-10 text-center">
                                    <div class="flex flex-col items-center">
                                        <div
                                            class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mb-4">
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

    <!-- Edit Modal -->
    <div id="editModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="max-w-md w-full bg-white rounded-[2.5rem] p-8 relative shadow-2xl">
            <button onclick="closeEditModal()" class="absolute top-6 right-6 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <h2 class="text-2xl font-black text-gray-900 mb-6">Edit Absensi</h2>

            <form id="editForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase mb-2 text-left">Nama
                            Karyawan</label>
                        <input type="text" id="edit_name" disabled
                            class="w-full px-4 py-3 bg-gray-50 border-none rounded-xl font-bold text-gray-400">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase mb-2 text-left">Tanggal</label>
                        <input type="date" name="date" id="edit_date" required
                            class="w-full px-4 py-3 bg-gray-50 border-none rounded-xl focus:ring-2 focus:ring-indigo-600 font-bold">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-black text-gray-500 uppercase mb-2 text-left">Jam
                                Masuk</label>
                            <input type="time" name="check_in" id="edit_check_in" required
                                class="w-full px-4 py-3 bg-gray-50 border-none rounded-xl focus:ring-2 focus:ring-indigo-600 font-bold">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-500 uppercase mb-2 text-left">Jam
                                Pulang</label>
                            <input type="time" name="check_out" id="edit_check_out"
                                class="w-full px-4 py-3 bg-gray-50 border-none rounded-xl focus:ring-2 focus:ring-indigo-600 font-bold">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase mb-2 text-left">Status</label>
                        <select name="status" id="edit_status" required
                            class="w-full px-4 py-3 bg-gray-50 border-none rounded-xl focus:ring-2 focus:ring-indigo-600 font-bold">
                            <option value="present">Hadir (Tepat Waktu)</option>
                            <option value="late">Terlambat</option>
                            <option value="absent">Mangkir (Absent)</option>
                        </select>
                    </div>

                    <div class="pt-4">
                        <button type="submit"
                            class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-black shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all">
                            SIMPAN PERUBAHAN
                        </button>
                    </div>
                </div>
            </form>
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

        function editAttendance(attendance) {
            const form = document.getElementById('editForm');
            form.action = `/attendance/${attendance.id}`;

            document.getElementById('edit_name').value = attendance.user.name;
            document.getElementById('edit_date').value = attendance.date.split('T')[0];
            document.getElementById('edit_check_in').value = attendance.check_in.includes(' ') ?
                attendance.check_in.split(' ')[1].substring(0, 5) :
                attendance.check_in.substring(0, 5);

            document.getElementById('edit_check_out').value = attendance.check_out ?
                (attendance.check_out.includes(' ') ?
                    attendance.check_out.split(' ')[1].substring(0, 5) :
                    attendance.check_out.substring(0, 5)) :
                '';

            document.getElementById('edit_status').value = attendance.status;

            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        function confirmDelete(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data absensi ini akan dihapus permanen beserta fotonya!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6366f1',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                background: '#fff',
                customClass: {
                    popup: 'rounded-[2rem]',
                    confirmButton: 'rounded-xl font-bold py-3 px-6',
                    cancelButton: 'rounded-xl font-bold py-3 px-6'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${id}`).submit();
                }
            })
        }

        // Close modals on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePhotoModal();
                closeEditModal();
            }
        });
    </script>
@endsection
