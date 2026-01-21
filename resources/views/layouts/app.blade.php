<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>KOPERASI JR</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <style>
        /* Select2 Tailwind Integration Fixes */
        .select2-container .select2-selection--single {
            height: 38px !important;
            border-color: #d1d5db !important;
            /* gray-300 */
            border-radius: 0.375rem !important;
            /* rounded-md */
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px !important;
            padding-left: 0.75rem !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
        }

        .select2-container--default .select2-selection--single:focus {
            border-color: #4f46e5 !important;
            /* indigo-600 */
            box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2) !important;
        }

        /* Bottom Bar Padding */
        @media (max-width: 1023px) {
            body {
                padding-bottom: 5rem;
            }
        }
    </style>
</head>

<body class="h-full">
    <div x-data="{ sidebarOpen: false, mobileMenuOpen: false, sidebarCollapsed: false }" class="min-h-full">
        @include('layouts.partials.bottom-nav')
        @include('layouts.partials.mobile-menu')

        @include('layouts.partials.sidebar')

        <div :class="sidebarCollapsed ? 'lg:pl-20' : 'lg:pl-72'" class="transition-all duration-300 ease-in-out">
            <div
                class="sticky top-0 z-40 flex h-14 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white/80 backdrop-blur-md px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
                <button type="button" @click="sidebarOpen = true" class="-m-2.5 p-2.5 text-gray-700 lg:hidden">
                    <span class="sr-only">Buka sidebar</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                    <div class="flex flex-1 items-center">
                        <span
                            class="text-xs font-black text-gray-400 uppercase tracking-widest lg:hidden">Dashboard</span>
                    </div>
                </div>
            </div>

            <main class="py-4 md:py-6">
                <div class="px-4 sm:px-6 lg:px-8">
                    <script>
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer)
                                toast.addEventListener('mouseleave', Swal.resumeTimer)
                            }
                        });

                        @if (session('success'))
                            Toast.fire({
                                icon: 'success',
                                title: "{{ session('success') }}"
                            });
                        @endif

                        @if (session('error'))
                            Toast.fire({
                                icon: 'error',
                                title: "{{ session('error') }}"
                            });
                        @endif

                        @if ($errors->any())
                            Toast.fire({
                                icon: 'error',
                                title: "Terjadi kesalahan pada input Anda",
                                text: "{{ $errors->first() }}"
                            });
                        @endif
                    </script>

                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    @stack('scripts')
</body>

</html>
