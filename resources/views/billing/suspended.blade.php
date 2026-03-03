@extends('layouts.app')

@section('title', 'Akses Ditangguhkan')

@section('content')
    <div class="min-h-screen flex items-center justify-center px-4"
        style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);">
        <div class="max-w-lg w-full">

            {{-- Icon --}}
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-24 h-24 rounded-full mb-4"
                    style="background: linear-gradient(135deg, #fef2f2, #fee2e2);">
                    <i class="fas fa-ban text-4xl text-red-500"></i>
                </div>
                <div
                    class="inline-flex items-center gap-2 px-3 py-1 bg-red-100 text-red-600 text-xs font-black rounded-full uppercase tracking-wider">
                    <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                    Akses Ditangguhkan
                </div>
            </div>

            {{-- Card --}}
            <div class="bg-white rounded-3xl shadow-xl border border-red-100 overflow-hidden">
                {{-- Header merah --}}
                <div class="px-8 py-6 text-center" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                    <h1 class="text-2xl font-black text-white mb-1">Saldo Aplikasi Habis</h1>
                    <p class="text-red-100 text-sm">Layanan tidak dapat diakses hingga saldo diisi ulang</p>
                </div>

                {{-- Body --}}
                <div class="px-8 py-8">
                    <div class="flex items-start gap-4 p-4 bg-amber-50 border border-amber-200 rounded-2xl mb-6">
                        <div
                            class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i class="fas fa-info text-amber-600 text-xs"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-amber-800 mb-1">Mengapa ini terjadi?</p>
                            <p class="text-xs text-amber-700 leading-relaxed">
                                Sistem billing aktif memerlukan saldo agar semua fitur dapat digunakan.
                                Saldo saat ini sudah mencapai <strong>Rp 0</strong> sehingga akses ke semua fitur
                                telah ditangguhkan sementara.
                            </p>
                        </div>
                    </div>

                    {{-- Saldo display --}}
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl mb-6 border border-gray-100">
                        <div>
                            <p class="text-xs text-gray-400 font-medium">Saldo Saat Ini</p>
                            <p class="text-2xl font-black text-red-500">Rp 0</p>
                        </div>
                        <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-wallet text-red-400"></i>
                        </div>
                    </div>

                    {{-- Langkah --}}
                    <div class="space-y-3 mb-8">
                        <p class="text-xs font-black text-gray-400 uppercase tracking-wider">Langkah Selanjutnya</p>
                        @if (Auth::user()->isBillingManager())
                            <a href="{{ route('billing.index') }}"
                                class="flex items-center gap-4 p-4 bg-emerald-50 border-2 border-emerald-200 rounded-2xl hover:border-emerald-400 hover:bg-emerald-100 transition-all group">
                                <div
                                    class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-plus text-white text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-black text-emerald-700">Isi Saldo Sekarang</p>
                                    <p class="text-xs text-emerald-500">Klik untuk topup via Midtrans</p>
                                </div>
                                <i
                                    class="fas fa-arrow-right text-emerald-400 ml-auto group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        @else
                            <div class="flex items-start gap-4 p-4 bg-blue-50 border border-blue-200 rounded-2xl">
                                <div
                                    class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-user-shield text-blue-500 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-black text-blue-700">Hubungi Administrator</p>
                                    <p class="text-xs text-blue-500 mt-0.5">Minta administrator untuk melakukan Top Up saldo
                                        aplikasi.</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Logout --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center justify-center gap-2 py-3 px-6 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold text-sm rounded-2xl transition-all">
                            <i class="fas fa-sign-out-alt"></i>
                            Keluar dari Akun
                        </button>
                    </form>
                </div>
            </div>

            <p class="text-center text-xs text-gray-400 mt-6">
                Akses akan pulih otomatis setelah saldo berhasil diisi.
            </p>
        </div>
    </div>
@endsection
