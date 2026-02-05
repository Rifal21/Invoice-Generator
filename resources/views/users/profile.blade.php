@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
    <div class="min-h-[calc(100vh-4rem)] flex items-center justify-center py-8 px-4 sm:px-6 lg:px-8 bg-slate-50/50">
        <div class="w-full max-w-2xl">
            <!-- Profile Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-2">PENGATURAN PROFIL</h1>
                <p class="text-indigo-600 font-bold uppercase tracking-[0.2em] text-xs">Kelola Informasi Akun & Keamanan</p>
            </div>

            <form action="{{ route('users.update-profile') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Information Card -->
                <div
                    class="bg-white border border-slate-200 rounded-[2.5rem] shadow-xl shadow-slate-200/50 relative overflow-hidden">
                    <div class="p-8 sm:p-10">
                        <div class="flex items-center gap-4 mb-8 border-b border-slate-100 pb-6">
                            <div
                                class="w-16 h-16 bg-indigo-600 rounded-2xl flex items-center justify-center text-white text-2xl font-black">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div>
                                <h2 class="text-xl font-black text-slate-900 leading-tight">{{ strtoupper($user->name) }}
                                </h2>
                                <p class="text-slate-400 font-bold text-[10px] uppercase tracking-widest">
                                    {{ $user->role }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <!-- Name Field -->
                            <div class="space-y-2">
                                <label for="name"
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Nama
                                    Lengkap</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i
                                            class="fas fa-user text-slate-300 group-focus-within:text-indigo-500 transition-colors"></i>
                                    </div>
                                    <input type="text" name="name" id="name"
                                        value="{{ old('name', $user->name) }}" required
                                        class="block w-full pl-11 pr-4 py-4 bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none">
                                </div>
                                @error('name')
                                    <p class="text-xs font-bold text-red-500 ml-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email Field -->
                            <div class="space-y-2">
                                <label for="email"
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Email</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i
                                            class="fas fa-envelope text-slate-300 group-focus-within:text-indigo-500 transition-colors"></i>
                                    </div>
                                    <input type="email" name="email" id="email"
                                        value="{{ old('email', $user->email) }}" required
                                        class="block w-full pl-11 pr-4 py-4 bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none">
                                </div>
                                @error('email')
                                    <p class="text-xs font-bold text-red-500 ml-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Password Card (Optional) -->
                <div x-data="{ changePassword: false }"
                    class="bg-white border border-slate-200 rounded-[2.5rem] shadow-xl shadow-slate-200/50 overflow-hidden transition-all duration-300">
                    <div class="p-8 sm:p-10">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600">
                                    <i class="fas fa-lock text-lg"></i>
                                </div>
                                <div>
                                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-wide">Kata Sandi</h3>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase">Biarkan kosong jika tidak
                                        ingin ganti</p>
                                </div>
                            </div>
                            <button type="button" @click="changePassword = !changePassword"
                                class="text-xs font-black text-indigo-600 hover:text-indigo-700 uppercase tracking-widest p-2 bg-indigo-50 rounded-lg transition-colors">
                                <span x-show="!changePassword">Ganti Password</span>
                                <span x-show="changePassword">Batal Ganti</span>
                            </button>
                        </div>

                        <div x-show="changePassword" x-collapse x-cloak class="space-y-6 pt-4 border-t border-slate-100">
                            <!-- Current Password -->
                            <div class="space-y-2">
                                <label for="current_password"
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Password
                                    Saat Ini</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i
                                            class="fas fa-shield-alt text-slate-300 group-focus-within:text-indigo-500 transition-colors"></i>
                                    </div>
                                    <input type="password" name="current_password" id="current_password"
                                        class="block w-full pl-11 pr-4 py-4 bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none"
                                        placeholder="••••••••">
                                </div>
                                @error('current_password')
                                    <p class="text-xs font-bold text-red-500 ml-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <!-- New Password -->
                                <div class="space-y-2">
                                    <label for="password"
                                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Password
                                        Baru</label>
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i
                                                class="fas fa-key text-slate-300 group-focus-within:text-indigo-500 transition-colors"></i>
                                        </div>
                                        <input type="password" name="password" id="password"
                                            class="block w-full pl-11 pr-4 py-4 bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none"
                                            placeholder="••••••••">
                                    </div>
                                    @error('password')
                                        <p class="text-xs font-bold text-red-500 ml-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Confirm New Password -->
                                <div class="space-y-2">
                                    <label for="password_confirmation"
                                        class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Konfirmasi
                                        Password Baru</label>
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i
                                                class="fas fa-check-double text-slate-300 group-focus-within:text-indigo-500 transition-colors"></i>
                                        </div>
                                        <input type="password" name="password_confirmation" id="password_confirmation"
                                            class="block w-full pl-11 pr-4 py-4 bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none"
                                            placeholder="••••••••">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full bg-slate-900 hover:bg-slate-800 text-white font-black py-5 rounded-[2rem] shadow-xl shadow-slate-200 transition-all active:scale-95 flex items-center justify-center gap-3">
                    <i class="fas fa-save opacity-70"></i>
                    <span class="tracking-widest">SIMPAN PERUBAHAN PROFIL</span>
                </button>
            </form>

            <div class="mt-8 text-center">
                <a href="{{ route('users.my-barcode') }}"
                    class="text-xs font-black text-slate-400 hover:text-indigo-600 transition-colors uppercase tracking-widest flex items-center justify-center gap-2">
                    <i class="fas fa-qrcode"></i>
                    Lihat Barcode Saya
                </a>
            </div>
        </div>
    </div>

    @if ($errors->has('current_password') || $errors->has('password'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Trigger the alpine show if there are password errors
                const passCard = document.querySelector('[x-data]');
                if (passCard.__x) {
                    passCard.__x.$data.changePassword = true;
                }
            });
        </script>
    @endif
@endsection
