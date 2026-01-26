@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">PENGATURAN ABSENSI</h1>
                <p class="text-gray-500 font-medium">Tentukan jam kerja operasional</p>
            </div>
        </div>

        <form action="{{ route('attendance.update-settings') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Basic Time Settings -->
                <div class="space-y-6">
                    <h3 class="text-lg font-black text-gray-900 border-b pb-2">JAM OPERASIONAL</h3>
                    <div>
                        <label class="block text-sm font-black text-gray-700 uppercase tracking-widest mb-3">Jam Masuk
                            (Check-in)</label>
                        <input type="time" name="check_in_time" value="{{ substr($settings->check_in_time, 0, 5) }}"
                            required
                            class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-600 font-bold text-lg">
                        <p class="mt-2 text-xs text-gray-400 font-bold">Toleransi 15 menit setelah jam ini.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-black text-gray-700 uppercase tracking-widest mb-3">Jam Pulang
                            (Check-out)</label>
                        <input type="time" name="check_out_time" value="{{ substr($settings->check_out_time, 0, 5) }}"
                            required
                            class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-indigo-600 font-bold text-lg">
                    </div>

                    <div class="space-y-4 pt-4">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <div class="relative">
                                <input type="checkbox" name="strict_time" value="1"
                                    {{ $settings->strict_time ? 'checked' : '' }} class="sr-only peer">
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600">
                                </div>
                            </div>
                            <span class="text-sm font-bold text-gray-700">Batasi Waktu Absen (Strict Time)</span>
                        </label>
                        <p class="text-xs text-gray-400 ml-14 font-medium -mt-3">Mencegah absen masuk/pulang terlalu awal.
                        </p>
                    </div>
                </div>

                <!-- Security & Location Settings -->
                <div class="space-y-6">
                    <h3 class="text-lg font-black text-gray-900 border-b pb-2">KEAMANAN & LOKASI</h3>

                    <div class="space-y-4">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <div class="relative">
                                <input type="checkbox" name="require_photo" value="1"
                                    {{ $settings->require_photo ? 'checked' : '' }} class="sr-only peer">
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600">
                                </div>
                            </div>
                            <span class="text-sm font-bold text-gray-700">Wajib Foto Selfie</span>
                        </label>

                        <label class="flex items-center gap-3 cursor-pointer">
                            <div class="relative">
                                <input type="checkbox" name="require_location" value="1"
                                    {{ $settings->require_location ? 'checked' : '' }} class="sr-only peer">
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600">
                                </div>
                            </div>
                            <span class="text-sm font-bold text-gray-700">Wajib GPS (Geofencing)</span>
                        </label>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-black text-gray-500 uppercase mb-2">Latitude Kantor</label>
                            <input type="text" name="office_latitude" value="{{ $settings->office_latitude }}"
                                placeholder="-6.xxxxx"
                                class="w-full px-4 py-3 bg-gray-50 border-none rounded-xl focus:ring-2 focus:ring-indigo-600 font-bold text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-500 uppercase mb-2">Longitude Kantor</label>
                            <input type="text" name="office_longitude" value="{{ $settings->office_longitude }}"
                                placeholder="107.xxxxx"
                                class="w-full px-4 py-3 bg-gray-50 border-none rounded-xl focus:ring-2 focus:ring-indigo-600 font-bold text-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase mb-2">Radius Kehadiran
                            (Meter)</label>
                        <input type="number" name="allowed_radius" value="{{ $settings->allowed_radius }}"
                            class="w-full px-4 py-3 bg-gray-50 border-none rounded-xl focus:ring-2 focus:ring-indigo-600 font-bold text-sm">
                        <p class="mt-2 text-xs text-gray-400 font-medium">Jarak maksimal karyawan dari titik kantor.</p>
                    </div>
                </div>
            </div>

            <div class="mt-10 pt-6 border-t">
                <button type="submit"
                    class="w-full md:w-auto px-12 py-4 bg-indigo-600 text-white rounded-2xl font-black shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition-all transform hover:-translate-y-1">
                    SIMPAN PENGATURAN
                </button>
            </div>
        </form>
    </div>
@endsection
