@extends('layouts.app')

@section('title', 'Barcode Saya')

@section('content')
    <div class="min-h-[calc(100vh-4rem)] flex items-center justify-center py-4 px-4 sm:px-6 lg:px-8 bg-slate-50/50">
        <div class="w-full max-w-lg">
            <!-- Card Header -->
            <div class="text-center mb-6 sm:mb-8">
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mb-2">IDENTITAS DIGITAL</h1>
                <p class="text-indigo-600 font-bold uppercase tracking-[0.2em] text-[10px] sm:text-xs">Koperasi Konsumen
                    Jembar Rahayu Sejahtera</p>
            </div>

            <!-- Main Card -->
            <div
                class="bg-white border border-slate-200 rounded-[2rem] sm:rounded-[3rem] shadow-2xl shadow-slate-200/50 relative overflow-hidden">
                <!-- Decorative Elements (Visible only on desktop/large screens to keep mobile clean) -->
                <div
                    class="hidden sm:block absolute -top-24 -right-24 w-64 h-64 bg-indigo-50 rounded-full blur-3xl opacity-60">
                </div>
                <div
                    class="hidden sm:block absolute -bottom-24 -left-24 w-64 h-64 bg-emerald-50 rounded-full blur-3xl opacity-60">
                </div>

                <div class="relative z-10 p-6 sm:p-10">
                    <!-- User Profile Info -->
                    <div class="flex flex-col items-center mb-8">
                        <div class="relative mb-4">
                            <div
                                class="w-20 h-20 sm:w-24 sm:h-24 bg-gradient-to-br from-indigo-600 to-indigo-700 rounded-3xl shadow-xl shadow-indigo-200 flex items-center justify-center transform rotate-3">
                                <span
                                    class="text-3xl sm:text-4xl font-black text-white transform -rotate-3">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                            <div
                                class="absolute -bottom-1 -right-1 w-8 h-8 bg-emerald-500 border-4 border-white rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-white text-[10px]"></i>
                            </div>
                        </div>
                        <div class="text-center">
                            <h2 class="text-xl sm:text-2xl font-black text-slate-900 mb-1 leading-tight">
                                {{ strtoupper($user->name) }}</h2>
                            <div class="inline-flex items-center gap-2 px-3 py-1 bg-slate-100 rounded-full">
                                <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full animate-pulse"></span>
                                <span
                                    class="text-slate-500 font-black uppercase tracking-wider text-[9px] sm:text-[10px]">{{ $user->role }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Barcode/QR Section -->
                    <div class="relative group">
                        <div
                            class="absolute inset-0 bg-slate-900 rounded-[2rem] opacity-0 group-hover:opacity-5 transition-opacity duration-300">
                        </div>
                        <div
                            class="bg-slate-50 rounded-[2.5rem] p-6 sm:p-8 mb-8 border-2 border-dashed border-slate-200 flex flex-col items-center transition-all duration-300 group-hover:border-indigo-300 group-hover:bg-indigo-50/30">
                            <div
                                class="mb-6 p-4 bg-white rounded-3xl shadow-sm border border-slate-100 transform transition-transform group-hover:scale-105 duration-300">
                                <img src="{{ route('users.qr', $user->unique_code) }}" alt="QR Code"
                                    class="w-48 h-48 sm:w-64 sm:h-64 object-contain">
                            </div>
                            <div class="text-center space-y-1">
                                <p class="text-slate-400 font-bold uppercase tracking-[0.2em] text-[10px]">Unique Identity
                                    Code</p>
                                <p class="text-indigo-600 font-black tracking-[0.3em] text-lg sm:text-xl">
                                    {{ $user->unique_code }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Instructions Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 mb-8">
                        <div
                            class="flex items-center gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-100 hover:border-indigo-200 transition-colors">
                            <div
                                class="flex-shrink-0 w-10 h-10 bg-white rounded-xl shadow-sm border border-slate-100 flex items-center justify-center">
                                <i class="fas fa-qrcode text-indigo-600"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-slate-900 text-xs sm:text-sm font-black truncate">Scan Absensi</p>
                                <p class="text-slate-500 text-[10px] leading-tight">Gunakan saat masuk & pulang kerja.</p>
                            </div>
                        </div>

                        <div
                            class="flex items-center gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-100 hover:border-emerald-200 transition-colors">
                            <div
                                class="flex-shrink-0 w-10 h-10 bg-white rounded-xl shadow-sm border border-slate-100 flex items-center justify-center">
                                <i class="fas fa-mobile-alt text-emerald-600"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-slate-900 text-xs sm:text-sm font-black truncate">Simpan di HP</p>
                                <p class="text-slate-500 text-[10px] leading-tight">Bisa di-screenshot untuk offline.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button onclick="window.print()"
                            class="flex-1 flex items-center justify-center gap-3 bg-slate-900 hover:bg-slate-800 text-white font-black py-4 px-6 rounded-2xl transition-all active:scale-95 shadow-lg shadow-slate-200">
                            <i class="fas fa-print opacity-70"></i>
                            <span class="text-sm tracking-wide">CETAK</span>
                        </button>
                        <button onclick="downloadAsPNG('{{ $user->unique_code }}')"
                            class="flex-1 flex items-center justify-center gap-3 bg-indigo-600 hover:bg-indigo-700 text-white font-black py-4 px-6 rounded-2xl transition-all active:scale-95 shadow-lg shadow-indigo-200">
                            <i class="fas fa-file-image opacity-70"></i>
                            <span class="text-sm tracking-wide">DOWNLOAD QR</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Footer Footer -->
            <div class="mt-8 text-center pb-8 border-t border-slate-200 pt-6">
                <p class="text-slate-400 text-[9px] font-black uppercase tracking-[0.2em] leading-relaxed">
                    &copy; {{ date('Y') }} KOPERASI KONSUMEN JEMBAR RAHAYU SEJAHTERA<br>
                    SISTEM INFORMASI MANAJEMEN INTERNAL
                </p>
            </div>
        </div>
    </div>

    <script>
        function downloadAsPNG(uniqueCode) {
            const img = document.querySelector('img[alt="QR Code"]');
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            const tempImg = new Image();

            // Selesaikan masalah CORS jika ada
            tempImg.crossOrigin = "anonymous";

            tempImg.onload = function() {
                // Set resolusi tinggi (2x lipat ukuran asli)
                const scale = 2;
                canvas.width = tempImg.width * scale;
                canvas.height = tempImg.height * scale;

                // Isi background putih agar tidak transparan saat jadi PNG
                ctx.fillStyle = "white";
                ctx.fillRect(0, 0, canvas.width, canvas.height);

                // Gambar QR Code ke canvas
                ctx.drawImage(tempImg, 0, 0, canvas.width, canvas.height);

                // Download hasil konversi
                const link = document.createElement('a');
                link.download = `barcode-${uniqueCode}.png`;
                link.href = canvas.toDataURL("image/png");
                link.click();
            };

            // Masukkan src gambar (SVG) ke image temporary
            tempImg.src = img.src;
        }
    </script>
    <style>
        /* Prevent content being cut off on very small phones */
        @media (max-height: 700px) {
            .min-h-\[calc\(100vh-4rem\)\] {
                min-height: auto;
                padding-top: 2rem;
                padding-bottom: 2rem;
            }
        }

        @media print {
            body * {
                visibility: hidden !important;
            }

            .bg-white.border.border-slate-200,
            .bg-white.border.border-slate-200 * {
                visibility: visible !important;
            }

            .bg-white.border.border-slate-200 {
                position: fixed !important;
                left: 50% !important;
                top: 50% !important;
                transform: translate(-50%, -50%) !important;
                width: 90% !important;
                max-width: none !important;
                box-shadow: none !important;
                border: 2px solid #000 !important;
            }

            button,
            a[download],
            .sm\:block.absolute,
            .mt-8.text-center {
                display: none !important;
            }

            /* Extra print styling to ensure QR is clear */
            img {
                max-width: 100% !important;
                print-color-adjust: exact;
            }
        }
    </style>
@endsection
