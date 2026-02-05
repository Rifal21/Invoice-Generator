@extends('layouts.app')

@section('title', 'Ganti Password')

@section('content')
    <div class="min-h-[calc(100vh-4rem)] flex items-center justify-center py-8 px-4 sm:px-6 lg:px-8 bg-slate-50/50">
        <div class="w-full max-w-md">
            <!-- Card Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-2">KEAMANAN AKUN</h1>
                <p class="text-indigo-600 font-bold uppercase tracking-[0.2em] text-xs">Perbarui Password Anda Secara Berkala
                </p>
            </div>

            <!-- Main Card -->
            <div
                class="bg-white border border-slate-200 rounded-[2.5rem] shadow-2xl shadow-slate-200/50 relative overflow-hidden">
                <div class="relative z-10 p-8 sm:p-10">
                    <div class="flex items-center justify-center mb-8">
                        <div class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600">
                            <i class="fas fa-shield-halved text-3xl"></i>
                        </div>
                    </div>

                    <form action="{{ route('users.update-password') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Current Password -->
                        <div>
                            <label for="current_password"
                                class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Password
                                Saat Ini</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i
                                        class="fas fa-lock text-slate-300 group-focus-within:text-indigo-500 transition-colors"></i>
                                </div>
                                <input type="password" name="current_password" id="current_password" required
                                    class="block w-full pl-11 pr-4 py-4 bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none"
                                    placeholder="••••••••">
                            </div>
                            @error('current_password')
                                <p class="mt-2 text-xs font-bold text-red-500 ml-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- New Password -->
                        <div>
                            <label for="password"
                                class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Password
                                Baru</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i
                                        class="fas fa-key text-slate-300 group-focus-within:text-indigo-500 transition-colors"></i>
                                </div>
                                <input type="password" name="password" id="password" required
                                    class="block w-full pl-11 pr-4 py-4 bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none"
                                    placeholder="••••••••">
                            </div>
                            @error('password')
                                <p class="mt-2 text-xs font-bold text-red-500 ml-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm New Password -->
                        <div>
                            <label for="password_confirmation"
                                class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Konfirmasi
                                Password Baru</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i
                                        class="fas fa-check-double text-slate-300 group-focus-within:text-indigo-500 transition-colors"></i>
                                </div>
                                <input type="password" name="password_confirmation" id="password_confirmation" required
                                    class="block w-full pl-11 pr-4 py-4 bg-slate-50 border border-slate-200 text-slate-900 text-sm font-bold rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all outline-none"
                                    placeholder="••••••••">
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full bg-slate-900 hover:bg-slate-800 text-white font-black py-4 rounded-2xl shadow-xl shadow-slate-200 transition-all active:scale-95 flex items-center justify-center gap-3">
                            <i class="fas fa-save opacity-70"></i>
                            <span>SIMPAN PERUBAHAN</span>
                        </button>
                    </form>
                </div>
            </div>

            <div class="mt-8 text-center">
                <a href="{{ route('users.my-barcode') }}"
                    class="text-xs font-black text-slate-400 hover:text-indigo-600 transition-colors uppercase tracking-widest flex items-center justify-center gap-2">
                    <i class="fas fa-arrow-left"></i>
                    Kembali ke Profil
                </a>
            </div>
        </div>
    </div>
@endsection
