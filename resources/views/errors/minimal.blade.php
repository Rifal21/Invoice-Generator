<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="{{ asset('images/kopinvoice.png') }}" type="image/x-icon">
    <title>@yield('title') | KOPERASI JR</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
</head>

<body class="h-full bg-gray-900 antialiased flex items-center justify-center p-6">
    <div class="max-w-md w-full text-center space-y-8">
        <!-- Icon Container -->
        <div class="relative inline-flex items-center justify-center">
            <div class="absolute inset-0 bg-indigo-500/20 blur-3xl rounded-full"></div>
            <div
                class="relative h-32 w-32 md:h-40 md:w-40 bg-gray-800 rounded-[2.5rem] border border-white/10 shadow-2xl flex items-center justify-center transform rotate-6 hover:rotate-0 transition-transform duration-500">
                <span class="text-6xl md:text-7xl font-black text-white drop-shadow-2xl">
                    @yield('code-icon', '!')
                </span>
            </div>
        </div>

        <!-- Text Content -->
        <div class="space-y-4">
            <h1 class="text-6xl md:text-8xl font-black text-white tracking-tighter">
                @yield('code')
            </h1>
            <h2 class="text-2xl md:text-3xl font-bold text-gray-200 uppercase tracking-widest px-4">
                @yield('message')
            </h2>
            <p class="text-gray-400 text-sm md:text-base px-6">
                @yield('description', 'Maaf, terjadi kesalahan yang tidak terduga. Silakan kembali ke beranda atau hubungi admin.')
            </p>
        </div>

        <!-- Action Button -->
        <div class="pt-6">
            <a href="{{ url('/') }}"
                class="inline-flex items-center gap-3 px-8 py-4 bg-indigo-600 hover:bg-indigo-500 text-white font-black rounded-2xl shadow-[0_10px_40px_-10px_rgba(79,70,229,0.5)] transition-all transform active:scale-95 group">
                <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                KEMBALI KE BERANDA
            </a>
        </div>

        <!-- Branding -->
        <div class="pt-12 flex items-center justify-center gap-2 opacity-50">
            <img src="{{ asset('images/kopinvoice.png') }}" class="h-6 w-6 grayscale" alt="Logo">
            <span class="text-[10px] font-black text-white tracking-[0.3em] uppercase">Koperasi JR</span>
        </div>
    </div>
</body>

</html>
