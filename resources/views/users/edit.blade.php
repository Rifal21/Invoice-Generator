@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="mb-8">
            <a href="{{ route('users.index') }}"
                class="inline-flex items-center text-sm font-bold text-indigo-600 hover:text-indigo-700 transition-all gap-2 mb-4">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                KEMBALI KE DAFTAR
            </a>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Edit Profile User</h1>
            <p class="text-gray-500 font-medium">Ubah informasi atau peran dari user <strong>{{ $user->name }}</strong>.
            </p>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8 lg:p-12">
            <form action="{{ route('users.update', $user->id) }}" method="POST" class="space-y-8">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <label class="block text-sm font-black text-gray-900 uppercase tracking-widest pl-1">Nama
                            Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                            class="block w-full rounded-2xl border-2 border-gray-100 py-4 px-6 text-gray-900 shadow-sm focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all font-bold">
                    </div>

                    <div class="space-y-4">
                        <label class="block text-sm font-black text-gray-900 uppercase tracking-widest pl-1">Alamat
                            Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                            class="block w-full rounded-2xl border-2 border-gray-100 py-4 px-6 text-gray-900 shadow-sm focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all font-bold">
                    </div>

                    <div class="space-y-4">
                        <label class="block text-sm font-black text-gray-900 uppercase tracking-widest pl-1">Role /
                            Peran</label>
                        <div class="relative">
                            <select name="role" required
                                class="block w-full rounded-2xl border-2 border-gray-100 py-4 px-6 text-gray-900 shadow-sm focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all font-bold appearance-none">
                                <option value="pegawai" {{ old('role', $user->role) == 'pegawai' ? 'selected' : '' }}>
                                    PEGAWAI</option>
                                <option value="admin_absensi"
                                    {{ old('role', $user->role) == 'admin_absensi' ? 'selected' : '' }}>
                                    ADMIN ABSENSI</option>
                                <option value="ketua" {{ old('role', $user->role) == 'ketua' ? 'selected' : '' }}>KETUA
                                </option>
                                <option value="super_admin"
                                    {{ old('role', $user->role) == 'super_admin' ? 'selected' : '' }}>SUPER ADMIN</option>
                            </select>
                            <div class="absolute inset-y-0 right-6 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label class="block text-sm font-black text-gray-900 uppercase tracking-widest pl-1">Gaji Harian
                            (Rp)</label>
                        <input type="number" name="daily_salary" value="{{ old('daily_salary', $user->daily_salary) }}"
                            required
                            class="block w-full rounded-2xl border-2 border-gray-100 py-4 px-6 text-gray-900 shadow-sm focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all font-bold">
                    </div>

                    <div class="col-span-full border-t border-gray-100 pt-8">
                        <div class="bg-indigo-50/50 p-6 rounded-3xl mb-8">
                            <div class="flex items-start gap-4">
                                <div class="p-2 bg-indigo-100 rounded-xl text-indigo-600 mt-1">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <p class="text-xs text-indigo-700 font-bold leading-relaxed uppercase tracking-wide">
                                    Kosongkan kata sandi jika tidak ingin mengubahnya.</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label class="block text-sm font-black text-gray-900 uppercase tracking-widest pl-1">Kata Sandi
                            Baru</label>
                        <input type="password" name="password" placeholder="••••••••"
                            class="block w-full rounded-2xl border-2 border-gray-100 py-4 px-6 text-gray-900 shadow-sm focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all font-bold placeholder:text-gray-300">
                    </div>

                    <div class="space-y-4">
                        <label class="block text-sm font-black text-gray-900 uppercase tracking-widest pl-1">Konfirmasi
                            Sandi</label>
                        <input type="password" name="password_confirmation" placeholder="••••••••"
                            class="block w-full rounded-2xl border-2 border-gray-100 py-4 px-6 text-gray-900 shadow-sm focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all font-bold placeholder:text-gray-300">
                    </div>
                </div>

                <div class="pt-8">
                    <button type="submit"
                        class="w-full py-5 bg-indigo-600 text-white font-black rounded-3xl shadow-2xl shadow-indigo-200 hover:bg-indigo-700 transition-all active:scale-[0.98] uppercase tracking-widest">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
