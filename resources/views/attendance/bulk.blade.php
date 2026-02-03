@extends('layouts.app')

@section('title', 'Input Absensi Bulk')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">INPUT ABSENSI BULK</h1>
                <p class="text-gray-500 font-medium">Buat data absensi untuk banyak user sekaligus</p>
            </div>
            <a href="{{ route('attendance.report') }}"
                class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200 transition-colors">
                Kembali ke Laporan
            </a>
        </div>

        <form action="{{ route('attendance.store-bulk') }}" method="POST">
            @csrf

            <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 mb-8">
                <h3 class="text-xl font-black text-gray-900 mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                    DETAIL ABSENSI
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-black text-gray-700 uppercase tracking-widest mb-2">Tanggal</label>
                        <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                            class="w-full px-4 py-3 bg-gray-50 border-none rounded-xl focus:ring-2 focus:ring-indigo-600 font-bold text-gray-900">
                    </div>

                    <div>
                        <label class="block text-sm font-black text-gray-700 uppercase tracking-widest mb-2">Status
                            Kehadiran</label>
                        <select name="status" required
                            class="w-full px-4 py-3 bg-gray-50 border-none rounded-xl focus:ring-2 focus:ring-indigo-600 font-bold text-gray-900">
                            <option value="present">Hadir (Present)</option>
                            <option value="late">Terlambat (Late)</option>
                            <option value="absent">Alpha (Absent)</option>
                            <option value="sick">Sakit (Sick)</option>
                            <option value="permit">Izin (Permit)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-black text-gray-700 uppercase tracking-widest mb-2">Jam
                            Masuk</label>
                        <input type="time" name="check_in" value="08:00" required
                            class="w-full px-4 py-3 bg-gray-50 border-none rounded-xl focus:ring-2 focus:ring-indigo-600 font-bold text-gray-900">
                    </div>

                    <div>
                        <label class="block text-sm font-black text-gray-700 uppercase tracking-widest mb-2">Jam Pulang
                            (Opsional)</label>
                        <input type="time" name="check_out" value="17:00"
                            class="w-full px-4 py-3 bg-gray-50 border-none rounded-xl focus:ring-2 focus:ring-indigo-600 font-bold text-gray-900">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-black text-gray-700 uppercase tracking-widest mb-2">Keterangan /
                        Alasan</label>
                    <textarea name="correction_reason" rows="2" placeholder="Contoh: Absensi Manual Massal karena..."
                        class="w-full px-4 py-3 bg-gray-50 border-none rounded-xl focus:ring-2 focus:ring-indigo-600 font-medium text-gray-900"></textarea>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-black text-gray-900 flex items-center gap-2">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                        PILIH USER
                    </h3>
                    <button type="button" id="select-all-btn" class="text-indigo-600 font-bold text-sm hover:underline">
                        Pilih Semua
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($users as $user)
                        <label
                            class="relative flex items-center p-4 rounded-xl border-2 border-transparent bg-gray-50 hover:bg-indigo-50 cursor-pointer transition-all select-user-card group">
                            <input type="checkbox" name="user_ids[]" value="{{ $user->id }}"
                                class="peer sr-only user-checkbox">
                            <div
                                class="w-5 h-5 rounded border border-gray-300 mr-3 flex items-center justify-center peer-checked:bg-indigo-600 peer-checked:border-indigo-600 transition-colors">
                                <svg class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-bold text-gray-900 group-hover:text-indigo-700">{{ $user->name }}</div>
                                <div class="text-xs text-gray-500 font-medium">{{ $user->role }}</div>
                            </div>
                            <div
                                class="absolute inset-0 border-2 border-indigo-600 rounded-xl opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity">
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="mt-8 pt-6 border-t flex justify-end">
                <button type="submit"
                    class="w-full md:w-auto px-12 py-4 bg-indigo-600 text-white rounded-2xl font-black shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition-all transform hover:-translate-y-1">
                    PROSES ABSENSI BULK
                </button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('select-all-btn').addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('.user-checkbox');
            const allChecked = Array.from(checkboxes).every(c => c.checked);

            checkboxes.forEach(c => c.checked = !allChecked);

            this.textContent = allChecked ? 'Pilih Semua' : 'Batalkan Semua';
        });
    </script>
@endsection
