<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-50">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Koperasi JR</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="h-full">
    <div
        class="flex min-h-full flex-col justify-center py-12 sm:px-6 lg:px-8 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-indigo-100 via-white to-white">
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
            <div
                class="mx-auto h-20 w-20 bg-indigo-600 rounded-[2rem] flex items-center justify-center shadow-2xl shadow-indigo-200 mb-8 animate-bounce transition-all duration-1000">
                <svg class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                </svg>
            </div>
            <h1 class="text-4xl font-black text-gray-900 tracking-tight uppercase">Koperasi JR</h1>
            <p class="mt-2 text-sm font-bold text-gray-500 uppercase tracking-widest">Sistem Manajemen Terpadu</p>
        </div>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[440px]">
            <div
                class="bg-white px-8 py-12 shadow-2xl shadow-indigo-100/50 sm:rounded-[3rem] border border-gray-100/50 backdrop-blur-sm relative overflow-hidden">
                <!-- Decorative element -->
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-indigo-50 rounded-full opacity-50"></div>

                <form class="space-y-8 relative z-10" action="{{ route('login.post') }}" method="POST">
                    @csrf

                    <div class="space-y-4">
                        <label for="email"
                            class="block text-sm font-black text-gray-900 uppercase tracking-widest pl-1">Alamat
                            Email</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-indigo-600 transition-colors"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16.5 12a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zm0 0c0 1.657 1.007 3 2.25 3S21 13.657 21 12a9 9 0 10-2.636 6.364M16.5 12V8.25" />
                                </svg>
                            </div>
                            <input id="email" name="email" type="email" autocomplete="email" required
                                value="{{ old('email') }}"
                                class="block w-full rounded-2xl border-2 border-gray-100 py-4 pl-14 pr-6 text-gray-900 shadow-sm focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all font-bold placeholder:text-gray-300"
                                placeholder="nama@koperasi.com">
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label for="password"
                            class="block text-sm font-black text-gray-900 uppercase tracking-widest pl-1">Kata
                            Sandi</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-indigo-600 transition-colors"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                </svg>
                            </div>
                            <input id="password" name="password" type="password" autocomplete="current-password"
                                required
                                class="block w-full rounded-2xl border-2 border-gray-100 py-4 pl-14 pr-6 text-gray-900 shadow-sm focus:ring-8 focus:ring-indigo-500/5 focus:border-indigo-500 transition-all font-bold placeholder:text-gray-300"
                                placeholder="••••••••">
                        </div>
                    </div>

                    <div class="flex items-center justify-between px-1">
                        <div class="flex items-center">
                            <input id="remember" name="remember" type="checkbox"
                                class="h-5 w-5 rounded-lg border-2 border-gray-100 text-indigo-600 focus:ring-indigo-500 transition-all cursor-pointer">
                            <label for="remember"
                                class="ml-3 block text-xs font-bold text-gray-500 uppercase tracking-wider cursor-pointer">Ingat
                                Saya</label>
                        </div>
                    </div>

                    <div>
                        <button type="submit"
                            class="flex w-full justify-center rounded-[2rem] bg-indigo-600 px-6 py-5 text-sm font-black text-white shadow-2xl shadow-indigo-200 hover:bg-indigo-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all active:scale-[0.98] uppercase tracking-[0.2em]">
                            Masuk Sekarang
                        </button>
                    </div>
                </form>
            </div>

            <p class="mt-8 text-center text-xs text-gray-400 font-bold uppercase tracking-widest">
                &copy; {{ date('Y') }} KOPERASI JR. ALL RIGHTS RESERVED.
            </p>
        </div>
    </div>

    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });

        @if (session('success'))
            Toast.fire({
                icon: 'success',
                title: "{{ session('success') }}"
            });
        @endif

        @if ($errors->any())
            Toast.fire({
                icon: 'error',
                title: "{{ $errors->first() }}"
            });
        @endif
    </script>
</body>

</html>
