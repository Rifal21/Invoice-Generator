<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('images/kopinvoice.png') }}" type="image/x-icon">
    <title>@yield('title', 'KOPERASI JR') | KOPERASI JR</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- PWA -->
    <meta name="theme-color" content="#111827">
    <link rel="apple-touch-icon" href="{{ asset('images/kopinvoice.png') }}">
    <link rel="manifest" href="{{ asset('/manifest.json') }}">

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

        /* Bottom Bar Padding removed - handled by utility classes */
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


                <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                    <div class="flex flex-1 items-center gap-3">
                        <div class="flex items-center gap-2">
                            <img src="{{ asset('images/kopinvoice.png') }}" class="h-8 w-8 object-contain"
                                alt="Logo">
                            <span class="text-sm font-black text-gray-900 uppercase tracking-widest">
                                @yield('title', 'Dashboard')
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-x-4 lg:gap-x-6">
                        <!-- Notification Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" type="button"
                                class="-m-2.5 p-2.5 text-gray-400 hover:text-gray-500 relative transition-colors">
                                <span class="sr-only">View notifications</span>
                                <i class="fas fa-bell text-xl"></i>
                                <span id="notification-badge"
                                    class="absolute top-2 right-2 h-2.5 w-2.5 rounded-full bg-red-600 ring-2 ring-white hidden animate-pulse"></span>
                            </button>

                            <!-- Dropdown menu -->
                            <div x-show="open" @click.away="open = false"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute right-0 z-50 mt-2.5 w-80 origin-top-right rounded-2xl bg-white py-2 shadow-xl ring-1 ring-gray-900/5 focus:outline-none overflow-hidden"
                                role="menu" aria-orientation="vertical" tabindex="-1">
                                <div
                                    class="px-4 py-3 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                                    <h3 class="text-sm font-black text-gray-900">Notifikasi</h3>
                                    <div class="flex gap-2">
                                        <button onclick="requestNotificationPermission()"
                                            class="text-[10px] font-bold text-emerald-600 hover:text-emerald-500 bg-emerald-50 px-2 py-1 rounded-md"
                                            id="btn-enable-notif" style="display: none;">
                                            <i class="fas fa-bell"></i> Aktifkan
                                        </button>
                                        <button onclick="markAllRead()"
                                            class="text-[10px] font-bold text-indigo-600 hover:text-indigo-500 bg-indigo-50 px-2 py-1 rounded-md">
                                            Tandai semua dibaca
                                        </button>
                                    </div>
                                </div>
                                <div id="notification-list" class="max-h-80 overflow-y-auto">
                                    <div class="px-4 py-8 text-center text-sm text-gray-400 font-medium">
                                        <i class="fas fa-bell-slash text-2xl mb-2 opacity-50"></i>
                                        <p>Tidak ada notifikasi baru</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <main class="px-4 sm:px-6 lg:px-8 pyt-4 pb-28 lg:py-6">
                <div>
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
    <script>
        if (!navigator.serviceWorker.controller) {
            navigator.serviceWorker.register("/sw.js").then(function(reg) {
                console.log("Service worker has been registered for scope: " + reg.scope);
            });
        }
    </script>
    @stack('scripts')
    <script>
        // Initialize state
        let lastNotificationCount = 0;
        let isFirstLoad = true;

        // Simple Ding Sound (Base64)
        const notificationSound = new Audio(
            "data:audio/wav;base64,UklGRl9vT19XQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YU"
        ); // Short placeholder, replacing with a real functional base64 short beep below
        const beepSound = new Audio(
            "data:audio/mp3;base64,//uQRAAAAWMSLwUIYAAsYkXgoQwAEaYLWfkWgAI0wWs/ItAAAG84AAD555 GAP555MAAA777wAAPPMAAAAAAAAA9998AA//333////99999//333333/ //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////VEFHAAAAREZXUjD///8A"
        );

        // Use a better verified Base64 for a "Ping" sound
        const pingAudio = new Audio("https://cdn.freesound.org/previews/536/536108_11568472-lq.mp3");

        async function fetchNotifications() {
            try {
                // 1. Fetch System Notifications (Now includes Chat messages)
                const response = await fetch('{{ route('notifications.index') }}');
                const data = await response.json();

                const badge = document.getElementById('notification-badge');
                if (data.count > 0) {
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }

                if (!isFirstLoad && data.count > lastNotificationCount) {
                    const latest = data.notifications[0];
                    if (latest) {
                        // 1. In-App Toast
                        Toast.fire({
                            icon: 'info',
                            title: latest.data.title ?? 'Notifikasi Baru',
                            text: latest.data.message ?? 'Anda memiliki notifikasi baru'
                        });

                        // 2. Browser Notification
                        if (Notification.permission === 'granted') {
                            new Notification(latest.data.title ?? 'Notifikasi Koperasi JR', {
                                body: latest.data.message ?? 'Anda memiliki notifikasi baru',
                                icon: '{{ asset('images/kopinvoice.png') }}'
                            });
                        }

                        // Play Notification Sound
                        pingAudio.play().catch(error => console.log(
                            'Audio play failed (user interaction needed first):', error));
                    }
                }
                lastNotificationCount = data.count;

                // Render List
                const list = document.getElementById('notification-list');
                if (data.notifications.length > 0) {
                    list.innerHTML = data.notifications.map(notif => `
                        <div class="px-4 py-3 hover:bg-gray-50 border-b border-gray-50 last:border-0 transition-colors cursor-pointer" onclick="markRead('${notif.id}')">
                            <p class="text-sm font-bold text-gray-800">${notif.data.title ?? 'Notifikasi'}</p>
                            <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">${notif.data.message ?? '-'}</p>
                            <p class="text-[10px] text-gray-400 mt-2 font-mono">${new Date(notif.created_at).toLocaleString()}</p>
                        </div>
                    `).join('');
                } else {
                    list.innerHTML = `
                        <div class="px-4 py-8 text-center text-sm text-gray-400 font-medium">
                            <i class="fas fa-bell-slash text-2xl mb-2 opacity-50"></i>
                            <p>Tidak ada notifikasi baru</p>
                        </div>
                    `;
                }

                isFirstLoad = false;

            } catch (error) {
                console.error('Notification Error:', error);
            }
        }

        async function markRead(id) {
            try {
                await fetch('{{ route('notifications.read') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        id
                    })
                });
                fetchNotifications();
            } catch (error) {
                console.error('Mark Read Error:', error);
            }
        }

        async function markAllRead() {
            try {
                await fetch('{{ route('notifications.read') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                fetchNotifications();
            } catch (error) {
                console.error('Mark All Read Error:', error);
            }
        }

        // Check Permission on Load
        function checkNotificationPermission() {
            if (Notification.permission === 'default') {
                document.getElementById('btn-enable-notif').style.display = 'inline-block';
            } else {
                document.getElementById('btn-enable-notif').style.display = 'none';
            }
        }

        async function requestNotificationPermission() {
            const permission = await Notification.requestPermission();
            if (permission === 'granted') {
                Toast.fire({
                    icon: 'success',
                    title: 'Notifikasi Diaktifkan',
                    text: 'Anda akan menerima notifikasi sistem di browser ini.'
                });
                checkNotificationPermission();
                new Notification('Tes Notifikasi', {
                    body: 'Notifikasi browser berhasil diaktifkan!',
                    icon: '{{ asset('images/kopinvoice.png') }}'
                });
            } else {
                Toast.fire({
                    icon: 'warning',
                    title: 'Ijin Ditolak',
                    text: 'Anda memblokir notifikasi.'
                });
            }
        }

        checkNotificationPermission();

        // Fetch initially and every 10s (faster for chat)
        fetchNotifications();
        setInterval(fetchNotifications, 10000);
    </script>
</body>

</html>
