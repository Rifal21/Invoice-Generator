<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon"
        href="{{ isset($site_settings['brand_logo']) ? (Storage::disk('public')->exists($site_settings['brand_logo']) ? asset('storage/' . $site_settings['brand_logo']) : asset($site_settings['brand_logo'])) : asset('images/kopinvoice.png') }}"
        type="image/x-icon">
    <title>@yield('title', 'Dashboard') | {{ $site_settings['app_name'] ?? 'KOPERASI JR' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- PWA -->
    <meta name="theme-color" content="#111827">
    <link rel="apple-touch-icon"
        href="{{ isset($site_settings['brand_logo']) ? (Storage::disk('public')->exists($site_settings['brand_logo']) ? asset('storage/' . $site_settings['brand_logo']) : asset($site_settings['brand_logo'])) : asset('images/kopinvoice.png') }}">
    <link rel="manifest" href="{{ asset('/manifest.json') }}">

    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/@hotwired/turbo@7.3.0/dist/turbo.es2017-umd.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

    <script type="text/javascript"
        src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
        data-client-key="{{ config('services.midtrans.client_key') }}"></script>

    <style>
        .select2-container .select2-selection--single {
            height: 38px !important;
            border-color: #d1d5db !important;
            border-radius: 0.375rem !important;
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
            box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2) !important;
        }
    </style>
</head>

<body class="h-full">
    <div x-data="{ sidebarOpen: false, mobileMenuOpen: false, sidebarCollapsed: false }" class="min-h-full overflow-x-hidden">
        @include('layouts.partials.bottom-nav')
        @include('layouts.partials.mobile-menu')
        @include('layouts.partials.sidebar')

        <div :class="sidebarCollapsed ? 'lg:pl-20' : 'lg:pl-72'" class="transition-all duration-300 ease-in-out">
            <div
                class="sticky top-0 z-40 flex h-14 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white/80 backdrop-blur-md px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
                <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                    <div class="flex flex-1 items-center gap-3">
                        <div class="flex items-center gap-2">
                            <img src="{{ isset($site_settings['brand_logo']) ? (Storage::disk('public')->exists($site_settings['brand_logo']) ? asset('storage/' . $site_settings['brand_logo']) : asset($site_settings['brand_logo'])) : asset('images/kopinvoice.png') }}"
                                class="h-8 w-8 object-contain" alt="Logo">
                            <span
                                class="text-sm font-black text-gray-900 uppercase tracking-widest">@yield('title', 'Dashboard')</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-x-4 lg:gap-x-6">
                        <!-- GLOBAL APP BALANCE -->
                        @php $isBilled = ($site_settings['app_billing_status'] ?? 'active') === 'active'; @endphp
                        <a href="{{ route('billing.index') }}"
                            class="hidden sm:flex items-center gap-2 px-3 py-1.5 {{ $isBilled ? 'bg-emerald-50 border-emerald-100' : 'bg-gray-50 border-gray-100' }} border rounded-full hover:opacity-80 transition-all group">
                            <i
                                class="fas {{ $isBilled ? 'fa-wallet text-emerald-600' : 'fa-power-off text-gray-400' }} text-xs"></i>
                            <div class="flex flex-col items-start leading-none">
                                <span
                                    class="text-[9px] font-black {{ $isBilled ? 'text-emerald-500' : 'text-gray-400' }} uppercase tracking-widest leading-none mb-0.5">
                                    {{ $isBilled ? 'Saldo Ops' : 'Billing Off' }}
                                </span>
                                <span class="text-xs font-bold text-gray-900" id="navbarBalance"
                                    data-value="{{ $site_settings['app_balance'] ?? 0 }}"
                                    data-rate="{{ $site_settings['app_billing_rate_per_minute'] ?? 14 }}"
                                    data-status="{{ $site_settings['app_billing_status'] ?? 'active' }}">
                                    @if ($isBilled)
                                        Rp {{ number_format($site_settings['app_balance'] ?? 0, 0, ',', '.') }}
                                    @else
                                        FREE ACCESS
                                    @endif
                                </span>
                            </div>
                        </a>

                        <!-- GLOBAL RADIO PLAYER -->
                        @if (!request()->routeIs('radio.index'))
                            <div id="global-radio-player-container" data-turbo-permanent x-data="globalRadio()"
                                x-init="initPlayer()" class="relative flex items-center z-50">
                                <div x-show="expanded" x-transition:enter="transition ease-out duration-300 transform"
                                    x-transition:enter-start="opacity-0 translate-x-8 scale-95"
                                    x-transition:enter-end="opacity-100 translate-x-0 scale-100"
                                    x-transition:leave="transition ease-in duration-200 transform"
                                    x-transition:leave-start="opacity-100 translate-x-0 scale-100"
                                    x-transition:leave-end="opacity-0 translate-x-8 scale-95"
                                    class="absolute right-10 top-1/2 -translate-y-1/2 flex items-center gap-3 bg-white/90 backdrop-blur-md border border-indigo-100 shadow-xl rounded-full pl-4 pr-2 py-1.5 h-10 w-[300px] overflow-hidden">
                                    <div class="flex items-end gap-[1px] h-4 w-6 shrink-0" x-show="isPlaying">
                                        <div class="w-1 bg-indigo-500 animate-music-bar h-2"></div>
                                        <div class="w-1 bg-indigo-500 animate-music-bar h-4"
                                            style="animation-delay: 0.1s"></div>
                                        <div class="w-1 bg-indigo-500 animate-music-bar h-3"
                                            style="animation-delay: 0.2s"></div>
                                        <div class="w-1 bg-indigo-500 animate-music-bar h-1"
                                            style="animation-delay: 0.3s"></div>
                                    </div>
                                    <div class="flex items-center justify-center h-4 w-6 shrink-0" x-show="!isPlaying">
                                        <div class="w-4 h-4 rounded-full border-2 border-gray-300"></div>
                                    </div>
                                    <div class="flex-grow min-w-0 flex flex-col justify-center h-full">
                                        <span
                                            class="text-[10px] uppercase font-black text-indigo-500 leading-none">Global
                                            Radio</span>
                                        <div class="overflow-hidden relative w-full h-4">
                                            <div
                                                class="whitespace-nowrap absolute animate-marquee text-xs font-bold text-gray-700">
                                                <span x-text="currentSong || 'Radio Koperasi JR - Live Stream'"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <button @click="toggleSource()"
                                        class="w-7 h-7 flex items-center justify-center rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors shrink-0">
                                        <i class="fas fa-exchange-alt text-[10px]"></i>
                                    </button>
                                    <button @click="togglePlay()"
                                        class="w-7 h-7 flex items-center justify-center rounded-full bg-indigo-600 text-white hover:bg-indigo-700 transition-colors shrink-0 shadow-md">
                                        <i class="fas"
                                            :class="isPlaying ? 'fa-pause text-[10px]' : 'fa-play text-[10px] ml-0.5'"></i>
                                    </button>
                                    <button @click="expanded = false"
                                        class="w-6 h-6 flex items-center justify-center rounded-full text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors shrink-0">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                </div>
                                <button @click="expanded = !expanded"
                                    class="relative p-2 rounded-full hover:bg-gray-100 transition-all duration-300 group"
                                    :class="{ 'bg-indigo-50 text-indigo-600': expanded, 'text-gray-400': !expanded }">
                                    <i class="fas fa-broadcast-tower text-xl transition-colors"
                                        :class="{
                                            'text-indigo-600 animate-pulse': isPlaying,
                                            'group-hover:text-indigo-500': !
                                                isPlaying
                                        }"></i>
                                    <span class="absolute top-2 right-2 flex h-2.5 w-2.5" x-show="isPlaying">
                                        <span
                                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
                                    </span>
                                </button>
                                <audio x-ref="audioPlayer" preload="none" class="hidden"></audio>
                            </div>
                        @endif

                        <!-- Notification Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" type="button"
                                class="-m-2.5 p-2.5 text-gray-400 hover:text-gray-500 relative transition-colors">
                                <i class="fas fa-bell text-xl"></i>
                                <span id="notification-badge"
                                    class="absolute top-2 right-2 h-2.5 w-2.5 rounded-full bg-red-600 ring-2 ring-white hidden animate-pulse"></span>
                            </button>
                            <div x-show="open" @click.away="open = false"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute right-0 z-50 mt-2.5 w-80 origin-top-right rounded-2xl bg-white py-2 shadow-xl ring-1 ring-gray-900/5 focus:outline-none overflow-hidden text-left"
                                role="menu">
                                <div
                                    class="px-4 py-3 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                                    <h3 class="text-sm font-black text-gray-900">Notifikasi</h3>
                                    <button onclick="markAllRead()"
                                        class="text-[10px] font-bold text-indigo-600 hover:text-indigo-500 bg-indigo-50 px-2 py-1 rounded-md">Tandai
                                        semua dibaca</button>
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

            <main class="px-4 sm:px-6 lg:px-8 py-4 pb-28 lg:py-6">
                <div>
                    <script>
                        window.Toast = Swal.mixin({
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
                                title: "Kesalahan input",
                                text: "{{ $errors->first() }}"
                            });
                        @endif
                    </script>
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        if (!navigator.serviceWorker.controller) {
            navigator.serviceWorker.register("/sw.js").then(function(reg) {
                console.log("Service worker has been registered for scope: " + reg.scope);
            });
        }
    </script>
    @stack('scripts')

    <script>
        // Real-time Balance Simulation for Navigation Bar
        (function() {
            const display = document.getElementById('navbarBalance');
            if (!display) return;

            const status = display.dataset.status || 'active';
            if (status !== 'active') return;

            let currentBalance = parseFloat(display.dataset.value);
            const ratePerMinute = parseFloat(display.dataset.rate);

            setInterval(() => {
                currentBalance -= ratePerMinute;
                if (currentBalance < 0) currentBalance = 0;
                display.textContent = 'Rp ' + Math.floor(currentBalance).toLocaleString('id-ID');
            }, 60000);
        })();

        // Notifications logic
        window.lastNotificationCount = window.lastNotificationCount || 0;
        var pingAudio = new Audio("https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3");
        async function fetchNotifications() {
            try {
                const response = await fetch('{{ route('notifications.index') }}');
                const data = await response.json();
                const badge = document.getElementById('notification-badge');
                if (data.count > 0) badge.classList.remove('hidden');
                else badge.classList.add('hidden');
                if (typeof window.isFirstLoad === 'undefined') window.isFirstLoad = true;
                if (!window.isFirstLoad && data.count > window.lastNotificationCount) {
                    const latest = data.notifications[0];
                    if (latest) {
                        Toast.fire({
                            icon: 'info',
                            title: latest.data.title ?? 'Notifikasi Baru',
                            text: latest.data.message
                        });
                        pingAudio.play().catch(e => {});
                    }
                }
                window.lastNotificationCount = data.count;
                const list = document.getElementById('notification-list');
                if (data.notifications.length > 0) {
                    list.innerHTML = data.notifications.map(notif => `
                        <div class="px-4 py-3 hover:bg-gray-50 border-b border-gray-50 last:border-0 transition-colors cursor-pointer" onclick="markRead('${notif.id}')">
                            <p class="text-sm font-bold text-gray-800">${notif.data.title ?? 'Notifikasi'}</p>
                            <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">${notif.data.message ?? '-'}</p>
                        </div>
                    `).join('');
                }
                window.isFirstLoad = false;
            } catch (e) {}
        }
        setInterval(fetchNotifications, 10000);
        fetchNotifications();
    </script>

    <!-- Radio Logic -->
    <script>
        function globalRadio() {
            return {
                expanded: false,
                isPlaying: false,
                currentSource: 'fkstudio',
                sources: {
                    'fkstudio': 'https://radio.fkstudio.my.id/listen/radio_fkstudio/radio.mp3',
                    'alhastream': 'https://s3.alhastream.com/listen/station_38/radio'
                },
                currentSong: 'Memuat Info...',
                pollInterval: null,
                initPlayer() {
                    const audio = this.$refs.audioPlayer;
                    audio.src = this.sources[this.currentSource];
                    audio.addEventListener('play', () => {
                        this.isPlaying = true;
                        this.startPolling();
                    });
                    audio.addEventListener('pause', () => {
                        this.isPlaying = false;
                        this.stopPolling();
                    });
                    this.fetchMeta();
                },
                togglePlay() {
                    const audio = this.$refs.audioPlayer;
                    if (audio.paused) audio.play();
                    else audio.pause();
                },
                toggleSource() {
                    this.currentSource = this.currentSource === 'fkstudio' ? 'alhastream' : 'fkstudio';
                    const audio = this.$refs.audioPlayer;
                    const wasPlaying = !audio.paused;
                    audio.src = this.sources[this.currentSource];
                    if (wasPlaying) audio.play();
                    this.currentSong = 'Memuat Info...';
                    this.fetchMeta();
                },
                startPolling() {
                    this.fetchMeta();
                    this.pollInterval = setInterval(() => this.fetchMeta(), 10000);
                },
                stopPolling() {
                    clearInterval(this.pollInterval);
                    this.pollInterval = null;
                },
                async fetchMeta() {
                    try {
                        let url = this.currentSource === 'fkstudio' ? '{{ route('radio.status') }}' :
                            'https://s3.alhastream.com/api/nowplaying/station_38';
                        const res = await fetch(url);
                        const data = await res.json();
                        if (this.currentSource === 'fkstudio') this.currentSong = data.current.text;
                        else this.currentSong = data.now_playing.song.text;
                    } catch (e) {
                        this.currentSong = 'Radio Live';
                    }
                }
            }
        }
    </script>

    <style>
        @keyframes marquee {
            0% {
                transform: translateX(100%);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        .animate-marquee {
            display: inline-block;
            white-space: nowrap;
            animation: marquee 15s linear infinite;
            padding-left: 100%;
        }

        @keyframes music-bar {

            0%,
            100% {
                height: 4px;
            }

            50% {
                height: 16px;
            }
        }

        .animate-music-bar {
            animation: music-bar 0.6s ease-in-out infinite;
        }
    </style>
</body>

</html>
