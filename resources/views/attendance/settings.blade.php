@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">PENGATURAN ABSENSI</h1>
                <p class="text-gray-500 font-medium">Tentukan jam kerja operasional</p>
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] p-8 shadow-sm border border-gray-100 relative overflow-hidden">
            <div class="absolute top-0 right-0 p-8">
                <div class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            <form action="{{ route('attendance.update-settings') }}" method="POST" class="max-w-md">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-black text-gray-700 uppercase tracking-widest mb-3">Jam Masuk
                            (Check-in)</label>
                        <input type="time" name="check_in_time" value="{{ substr($settings->check_in_time, 0, 5) }}"
                            required
                            class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-600 font-bold text-lg">
                        <p class="mt-2 text-xs text-gray-400 font-bold">Karyawan yang absen setelah jam ini (+15 menit
                            toleransi) akan ditandai Terlambat.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-gray-700 uppercase tracking-widest mb-3">Jam Pulang
                            (Check-out)</label>
                        <input type="time" name="check_out_time" value="{{ substr($settings->check_out_time, 0, 5) }}"
                            required
                            class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-600 font-bold text-lg">
                    </div>

                    <div class="pt-4">
                        <button type="submit"
                            class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-black shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition-all transform hover:-translate-y-1">
                            SIMPAN PENGATURAN
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
