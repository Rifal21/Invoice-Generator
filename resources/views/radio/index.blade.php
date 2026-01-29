@extends('layouts.app')

@section('title', 'Radio Koperasi JR')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- Left Column: Player & Queue -->
            <div class="lg:col-span-8 space-y-6">

                <!-- Live Player -->
                <div
                    class="bg-gray-900 rounded-3xl overflow-hidden shadow-2xl relative border border-gray-800 aspect-video group">
                    <div id="player" class="w-full h-full opacity-0 transition-opacity duration-1000"></div>

                    <!-- Play Overlay (To solve Autoplay Block) -->
                    <div id="play-overlay"
                        class="absolute inset-0 flex flex-col items-center justify-center bg-gray-900/95 z-50 transition-all duration-500">
                        <div class="relative">
                            <div class="absolute inset-0 bg-indigo-500 rounded-full animate-ping opacity-20"></div>
                            <button onclick="startRadio()"
                                class="relative bg-indigo-600 text-white w-24 h-24 rounded-full flex items-center justify-center shadow-2xl hover:bg-indigo-700 transition-all transform hover:scale-110 active:scale-95 group/play border-4 border-indigo-500/30">
                                <i class="fas fa-play text-3xl ml-2 group-hover/play:scale-110 transition-transform"></i>
                            </button>
                        </div>
                        <h3 class="text-white font-black mt-8 tracking-widest uppercase text-lg">Aktifkan Suara Radio</h3>
                        <p class="text-indigo-400 text-sm mt-2 font-medium animate-pulse">Klik tombol play di atas untuk
                            mendengar siaran</p>
                        <div
                            class="mt-8 flex items-center gap-4 text-gray-500 text-xs bg-gray-800/50 px-4 py-2 rounded-full">
                            <i class="fas fa-info-circle text-indigo-500"></i>
                            <span>Browser memblokir suara otomatis demi kenyamanan Anda</span>
                        </div>
                    </div>

                    <!-- Error Overlay -->
                    <div id="error-overlay"
                        class="absolute inset-0 flex flex-col items-center justify-center bg-red-950/90 z-40 hidden transition-all duration-300">
                        <i class="fas fa-exclamation-triangle text-red-500 text-4xl mb-4"></i>
                        <h3 class="text-white font-bold">Video tidak bisa diputar</h3>
                        <p id="error-message" class="text-red-300 text-xs mt-2 px-8 text-center">Video ini memiliki batasan
                            hak cipta atau telah dihapus.</p>
                        <button onclick="skipSong()"
                            class="mt-6 bg-white/10 hover:bg-white/20 text-white text-xs px-6 py-2 rounded-full transition-all border border-white/20">
                            Putar Lagu Berikutnya
                        </button>
                    </div>

                    <!-- Overlay when no music -->
                    <div id="no-music-overlay"
                        class="absolute inset-0 flex flex-col items-center justify-center bg-gray-900/90 z-10 hidden">
                        <div class="text-indigo-500 mb-4 scale-150">
                            <i class="fas fa-radio fa-spin text-5xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-white">Radio Sedang Istirahat</h3>
                        <p class="text-gray-400 mt-2">Yuk request lagu favoritmu!</p>
                    </div>

                    <!-- Info Overlay -->
                    <div
                        class="p-6 bg-gray-900/50 border-t border-gray-800 flex items-center justify-between absolute bottom-0 left-0 right-0 z-20 transition-transform translate-y-full group-hover:translate-y-0">
                        <div class="flex items-center gap-4 group">
                            <div
                                class="w-12 h-12 bg-indigo-500/20 rounded-2xl flex items-center justify-center text-indigo-400 group-hover:bg-indigo-500 transition-all duration-500 group-hover:text-white">
                                <i class="fas fa-broadcast-tower animate-pulse"></i>
                            </div>
                            <div>
                                <p class="text-[10px] text-indigo-400 font-bold uppercase tracking-wider mb-1">Now Playing
                                </p>
                                <h2 id="current-song-title"
                                    class="text-white font-bold truncate max-w-md group-hover:text-indigo-400 transition-colors">
                                    Memuat...</h2>
                                <p id="requested-by" class="text-xs text-gray-500 mt-0.5">Requested by: -</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <button onclick="syncRadio()" title="Refresh Player"
                                class="w-10 h-10 rounded-xl bg-gray-800 text-gray-400 hover:bg-gray-700 hover:text-white transition-all flex items-center justify-center">
                                <i class="fas fa-sync-alt text-sm"></i>
                            </button>
                            <button onclick="skipSong()" title="Skip This Song"
                                class="w-12 h-12 rounded-2xl bg-red-500/10 text-red-400 border border-red-500/20 hover:bg-red-500 hover:text-white transition-all duration-500 flex items-center justify-center shadow-lg hover:shadow-red-500/20">
                                <i class="fas fa-forward"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Queue List -->
                <div class="bg-white rounded-3xl shadow-xl p-6 border border-gray-100">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-black text-gray-900 uppercase tracking-widest">Antrean Lagu</h3>
                        <span id="queue-count"
                            class="bg-indigo-100 text-indigo-600 px-3 py-1 rounded-full text-xs font-black">0 Lagu</span>
                    </div>
                    <div id="queue-list" class="space-y-4 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                        <!-- Queue items injected here -->
                        <div class="text-center py-10 text-gray-400">
                            <i class="fas fa-list-ul mb-2 text-2xl opacity-20"></i>
                            <p class="text-sm font-bold">Antrean kosong</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Search Only -->
            <div class="lg:col-span-4 space-y-6 flex flex-col">

                <!-- Search Lagu -->
                <div class="bg-white rounded-3xl shadow-xl p-6 border border-gray-100 flex-shrink-0">
                    <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-4">Request Lagu</h3>
                    <div class="relative">
                        <input type="text" id="song-search" placeholder="Cari di YouTube..."
                            class="w-full bg-gray-50 border-gray-100 rounded-2xl py-3 pl-10 pr-4 text-sm focus:ring-2 focus:ring-indigo-500 transition-all font-bold">
                        <i class="fas fa-search absolute left-4 top-4 text-gray-400"></i>
                    </div>

                    <!-- Search Results Dropdown -->
                    <div id="search-results"
                        class="hidden absolute left-0 right-0 mt-2 bg-white rounded-3xl shadow-2xl border border-gray-100 p-2 z-50 max-h-[300px] overflow-y-auto">
                        <!-- Results injected here -->
                    </div>
                </div>

            </div>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #E2E8F0;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #CBD5E1;
        }
    </style>
@endsection

@push('scripts')
    <script src="https://www.youtube.com/iframe_api"></script>
    <script>
        let player;
        let currentVideoId = @json($current->video_id ?? null);
        let isStarted = false;

        function startRadio() {
            const overlay = document.getElementById('play-overlay');
            overlay.style.opacity = '0';
            setTimeout(() => overlay.style.display = 'none', 500);

            if (player && typeof player.playVideo === 'function') {
                player.playVideo();
                document.getElementById('player').classList.remove('opacity-0');
            }
            isStarted = true;
            syncRadio();
        }

        // Initialize YouTube Player
        function onYouTubeIframeAPIReady() {
            const initialVideoId = currentVideoId || '';

            player = new YT.Player('player', {
                height: '100%',
                width: '100%',
                videoId: initialVideoId,
                playerVars: {
                    'autoplay': 1,
                    'controls': 1,
                    'rel': 0,
                    'showinfo': 0,
                    'modestbranding': 1,
                    'enablejsapi': 1,
                    'origin': window.location.origin
                },
                events: {
                    'onReady': onPlayerReady,
                    'onStateChange': onPlayerStateChange,
                    'onError': onPlayerError
                }
            });
        }

        function onPlayerReady(event) {
            if (currentVideoId) {
                document.getElementById('player').classList.remove('opacity-0');
            }
            setInterval(syncRadio, 5000);
        }

        function onPlayerError(event) {
            console.error('Player Error:', event.data);
            const errorOverlay = document.getElementById('error-overlay');
            const errorMsg = document.getElementById('error-message');

            errorOverlay.classList.remove('hidden');

            if (event.data === 150 || event.data === 101) {
                errorMsg.innerText = "Lagu ini dilarang diputar di luar YouTube (Content Restriction).";
                // Auto skip after 3s for users
                setTimeout(() => {
                    errorOverlay.classList.add('hidden');
                    skipSong();
                }, 3000);
            } else {
                errorMsg.innerText = "Terjadi kesalahan pada player YouTube (Error " + event.data + ").";
            }
        }

        function onPlayerStateChange(event) {
            // If song ended, notify server
            if (event.data === YT.PlayerState.ENDED) {
                skipSong();
            }
        }

        // Sync Radio State
        async function syncRadio() {
            if (!isStarted) return;
            try {
                const response = await fetch('{{ route('radio.status') }}');
                const data = await response.json();

                updateQueue(data.queue);

                const noMusicOverlay = document.getElementById('no-music-overlay');

                if (data.current) {
                    noMusicOverlay.classList.add('hidden');
                    document.getElementById('current-song-title').innerText = data.current.title;
                    document.getElementById('requested-by').innerText = 'Requested by: ' + data.current.requested_by;

                    if (currentVideoId !== data.current.video_id) {
                        currentVideoId = data.current.video_id;
                        const startedAt = new Date(data.current.started_at);
                        const serverTime = new Date(data.server_time);
                        const diffSeconds = Math.max(0, Math.floor((serverTime - startedAt) / 1000));

                        if (player && typeof player.loadVideoById === 'function') {
                            document.getElementById('player').classList.remove('opacity-0');
                            player.loadVideoById({
                                videoId: data.current.video_id,
                                startSeconds: diffSeconds
                            });
                        }
                    }
                } else {
                    noMusicOverlay.classList.remove('hidden');
                    if (player && typeof player.stopVideo === 'function') {
                        player.stopVideo();
                    }
                    currentVideoId = null;
                }
            } catch (error) {
                console.error('Sync Error:', error);
            }
        }

        function updateQueue(queue) {
            const list = document.getElementById('queue-list');
            document.getElementById('queue-count').innerText = queue.length + ' Lagu';

            if (queue.length === 0) {
                list.innerHTML =
                    `<div class="text-center py-10 text-gray-400"><i class="fas fa-list-ul mb-2 text-2xl opacity-20"></i><p class="text-sm font-bold">Antrean kosong</p></div>`;
                return;
            }

            list.innerHTML = queue.map((item, index) => `
            <div class="flex items-center gap-4 bg-gray-50 p-3 rounded-2xl hover:bg-gray-100 transition-all group">
                <span class="text-xs font-black text-gray-300 w-4">${index + 1}</span>
                <img src="${item.thumbnail}" class="w-12 h-12 rounded-xl object-cover shadow-sm">
                <div class="flex-grow min-w-0">
                    <h4 class="text-sm font-bold text-gray-900 truncate">${item.title}</h4>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">${item.requested_by}</p>
                </div>
            </div>
        `).join('');
        }

        // Search Logic
        const searchInput = document.getElementById('song-search');
        const resultsBox = document.getElementById('search-results');
        let searchTimeout;

        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            const q = e.target.value;
            if (q.length < 3) {
                resultsBox.classList.add('hidden');
                return;
            }

            searchTimeout = setTimeout(async () => {
                const res = await fetch(`{{ route('radio.search') }}?q=${q}`);
                const data = await res.json();

                resultsBox.innerHTML = data.map(item => `
                <div onclick="requestSong('${item.id}', '${item.title.replace(/'/g, "\\'")}', '${item.thumbnail}')" 
                    class="flex items-center gap-3 p-3 hover:bg-indigo-50 rounded-2xl cursor-pointer transition-all group">
                    <img src="${item.thumbnail}" class="w-10 h-10 rounded-lg object-cover">
                    <div class="flex-grow min-w-0">
                        <p class="text-xs font-bold text-gray-900 truncate group-hover:text-indigo-600">${item.title}</p>
                    </div>
                </div>
            `).join('');
                resultsBox.classList.remove('hidden');
            }, 500);
        });

        async function requestSong(id, title, thumb) {
            resultsBox.classList.add('hidden');
            searchInput.value = '';

            try {
                const res = await fetch('{{ route('radio.request') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        video_id: id,
                        title,
                        thumbnail: thumb
                    })
                });
                const data = await res.json();
                Toast.fire({
                    icon: 'success',
                    title: data.message
                });
                syncRadio();
            } catch (error) {
                console.error('Request Error:', error);
            }
        }

        async function skipSong() {
            await fetch('{{ route('radio.skip') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            syncRadio();
        }

        // Close results when clicking outside
        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
                resultsBox.classList.add('hidden');
            }
        });
    </script>
@endpush
