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
                    <div id="player-container"
                        class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-gray-900 via-purple-900 to-gray-900 relative overflow-hidden">

                        <!-- Visualizer Animation -->
                        <div class="absolute inset-0 flex items-center justify-center opacity-30">
                            <div class="flex items-end gap-1 h-32" id="visualizer-bars">
                                <div class="w-3 bg-indigo-500 rounded-t-lg animate-music-bar h-12"></div>
                                <div class="w-3 bg-indigo-500 rounded-t-lg animate-music-bar h-24"
                                    style="animation-delay: 0.1s"></div>
                                <div class="w-3 bg-indigo-500 rounded-t-lg animate-music-bar h-16"
                                    style="animation-delay: 0.2s"></div>
                                <div class="w-3 bg-indigo-500 rounded-t-lg animate-music-bar h-28"
                                    style="animation-delay: 0.3s"></div>
                                <div class="w-3 bg-indigo-500 rounded-t-lg animate-music-bar h-20"
                                    style="animation-delay: 0.15s"></div>
                                <div class="w-3 bg-indigo-500 rounded-t-lg animate-music-bar h-32"
                                    style="animation-delay: 0.4s"></div>
                                <div class="w-3 bg-indigo-500 rounded-t-lg animate-music-bar h-14"
                                    style="animation-delay: 0.25s"></div>
                                <div class="w-3 bg-indigo-500 rounded-t-lg animate-music-bar h-22"
                                    style="animation-delay: 0.05s"></div>
                                <div class="w-3 bg-indigo-500 rounded-t-lg animate-music-bar h-10"
                                    style="animation-delay: 0.35s"></div>
                            </div>
                        </div>

                        <!-- Radio Icon/Logo -->
                        <div
                            class="z-10 bg-white/10 backdrop-blur-md p-8 rounded-full shadow-2xl border border-white/20 animate-pulse-slow">
                            <i class="fas fa-broadcast-tower text-6xl text-white"></i>
                        </div>

                        <div class="z-10 mt-6 text-center">
                            <h2 class="text-2xl font-black text-white tracking-widest uppercase">Live Streaming</h2>
                            <p class="text-indigo-300 font-bold mt-1">Radio Koperasi JR</p>
                        </div>

                        <audio id="audio-player" preload="none">
                            <source src="https://radio.fkstudio.my.id/listen/radio_fkstudio/radio.mp3" type="audio/mpeg">
                            Your browser does not support the audio element.
                        </audio>
                    </div>

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
                    </div>

                    <!-- Info Overlay -->
                    <div
                        class="p-6 bg-gray-900/50 border-t border-gray-800 flex items-center justify-between absolute bottom-0 left-0 right-0 z-20 transition-transform translate-y-full group-hover:translate-y-0 backdrop-blur-md">
                        <div class="flex items-center gap-4 group">
                            <div id="now-playing-art-container"
                                class="w-12 h-12 bg-indigo-500/20 rounded-2xl flex items-center justify-center text-indigo-400 group-hover:bg-indigo-500 transition-all duration-500 group-hover:text-white overflow-hidden relative">
                                <i id="now-playing-icon" class="fas fa-music animate-pulse"></i>
                                <img id="now-playing-img" src="" class="hidden w-full h-full object-cover">
                            </div>
                            <div>
                                <p class="text-[10px] text-indigo-400 font-bold uppercase tracking-wider mb-1">Now Playing
                                </p>
                                <h2 id="current-song-title"
                                    class="text-white font-bold truncate max-w-md group-hover:text-indigo-400 transition-colors">
                                    Live Radio Stream</h2>
                                <p id="requested-by" class="text-xs text-gray-500 mt-0.5">FK Studio</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <button onclick="refreshStream()" title="Reconnect Stream"
                                class="w-10 h-10 rounded-xl bg-gray-800 text-gray-400 hover:bg-gray-700 hover:text-white transition-all flex items-center justify-center">
                                <i class="fas fa-sync-alt text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Queue List (Hidden/Placeholder for now as stream is continuous) -->
                <div class="bg-white rounded-3xl shadow-xl p-6 border border-gray-100 hidden">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-black text-gray-900 uppercase tracking-widest">Antrean Lagu</h3>
                        <span id="queue-count"
                            class="bg-indigo-100 text-indigo-600 px-3 py-1 rounded-full text-xs font-black">0 Lagu</span>
                    </div>
                    <div id="queue-list" class="space-y-4 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                        <div class="text-center py-10 text-gray-400">
                            <i class="fas fa-list-ul mb-2 text-2xl opacity-20"></i>
                            <p class="text-sm font-bold">Antrean kosong</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Request & Info -->
            <div class="lg:col-span-4 space-y-6 flex flex-col">

                <!-- Search Request Box -->
                <div class="bg-white rounded-3xl shadow-xl p-6 border border-gray-100 flex-shrink-0">
                    <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-4">Request Lagu (Ke DJ)</h3>
                    <div class="relative">
                        <input type="text" id="song-search" placeholder="Cari lagu..."
                            class="w-full bg-gray-50 border-gray-100 rounded-2xl py-3 pl-10 pr-4 text-sm focus:ring-2 focus:ring-indigo-500 transition-all font-bold">
                        <i class="fas fa-search absolute left-4 top-4 text-gray-400"></i>
                    </div>
                    <div class="mt-3 text-right">
                        <a href="https://radio.fkstudio.my.id/public/radio_fkstudio" target="_blank"
                            class="text-xs text-indigo-500 hover:text-indigo-700 font-bold flex items-center justify-end gap-1">
                            Atau request via Halaman Publik <i class="fas fa-external-link-alt"></i>
                        </a>
                    </div>

                    <!-- Search Results Dropdown -->
                    <div id="search-results"
                        class="hidden absolute left-0 right-0 mt-2 bg-white rounded-3xl shadow-2xl border border-gray-100 p-2 z-50 max-h-[300px] overflow-y-auto">
                        <!-- Results injected here -->
                    </div>
                </div>

                <!-- Status Card -->
                <div class="bg-indigo-600 rounded-3xl shadow-xl p-8 text-white relative overflow-hidden flex-grow">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
                    <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-32 h-32 bg-purple-500/20 rounded-full blur-2xl">
                    </div>

                    <h3 class="text-lg font-bold mb-2">Status Siaran</h3>
                    <div class="flex items-center gap-3 mb-6">
                        <span class="w-3 h-3 bg-green-400 rounded-full animate-ping"></span>
                        <span class="font-medium opacity-90">On Air</span>
                    </div>

                    <p class="text-indigo-100 text-sm leading-relaxed mb-4">
                        Anda sedang mendengarkan siaran langsung dari FK Studio.
                    </p>
                    <div class="border-t border-white/10 pt-4 mt-4">
                        <p class="text-xs text-indigo-300 uppercase tracking-widest font-bold mb-2">Jadwal</p>
                        <ul class="space-y-2 text-sm text-indigo-50">
                            <li class="flex justify-between"><span>Pagi</span> <span class="font-bold">Musik
                                    Semangat</span>
                            </li>
                            <li class="flex justify-between"><span>Siang</span> <span class="font-bold">Request
                                    Lagu</span>
                            </li>
                            <li class="flex justify-between"><span>Malam</span> <span class="font-bold">Slow Rock &
                                    Jazz</span></li>
                        </ul>
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

        @keyframes music-bar {

            0%,
            100% {
                height: 1rem;
            }

            50% {
                height: 3rem;
            }
        }

        .animate-music-bar {
            animation: music-bar 1s ease-in-out infinite;
        }

        .animate-pulse-slow {
            animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
@endsection

@push('scripts')
    <script>
        let isStarted = false;
        const audioPlayer = document.getElementById('audio-player');
        // Handle volume if needed

        function startRadio() {
            const overlay = document.getElementById('play-overlay');
            if (overlay) {
                overlay.style.opacity = '0';
                setTimeout(() => overlay.style.display = 'none', 500);
            }

            if (audioPlayer) {
                audioPlayer.play().then(() => {
                    isStarted = true;
                    syncRadio(); // Initial sync after starting
                }).catch(e => {
                    console.error("Playback failed", e);
                });
            }
        }

        function refreshStream() {
            if (audioPlayer) {
                // Reload src to reconnect live stream
                const currentSrc = audioPlayer.querySelector('source').src;
                audioPlayer.src = currentSrc;
                audioPlayer.load();
                if (isStarted) audioPlayer.play();
            }
        }

        // Sync Radio State
        async function syncRadio() {
            if (!isStarted) return;
            try {
                const response = await fetch('{{ route('radio.status') }}');
                const data = await response.json();

                // updateQueue(data.queue); // Queue data might be empty now, handle gracefully

                const noMusicOverlay = document.getElementById(
                    'no-music-overlay'
                ); // This element is not in the provided HTML, assuming it's meant for future use or another part.

                if (data.current) {
                    if (noMusicOverlay) noMusicOverlay.classList.add('hidden');

                    const titleElem = document.getElementById('current-song-title');
                    const reqElem = document.getElementById('requested-by');
                    const artElem = document.querySelector('.group-hover\\:bg-indigo-500 i'); // Icon or Image?

                    // Update Title
                    if (titleElem) titleElem.innerText = data.current.text || (data.current.artist + ' - ' + data
                        .current.title);

                    // Update Listeners/RequestedBy
                    if (reqElem) reqElem.innerText = `Listeners: ${data.current.listeners || '-'}`;

                    // Update Art
                    const artImg = document.getElementById('now-playing-img');
                    const artIcon = document.getElementById('now-playing-icon');

                    if (data.current.art && artImg && artIcon) {
                        artImg.src = data.current.art;
                        artImg.classList.remove('hidden');
                        artIcon.classList.add('hidden');
                    } else if (artImg && artIcon) {
                        artImg.classList.add('hidden');
                        artIcon.classList.remove('hidden');
                    }

                } else {
                    if (noMusicOverlay) noMusicOverlay.classList.remove('hidden');
                }
            } catch (error) {
                console.error('Sync Error:', error);
            }
        }

        // Start polling
        setInterval(syncRadio, 5000);

        // Search Logic for Requests (Adapted for stream context)
        const searchInput = document.getElementById('song-search');
        const resultsBox = document.getElementById('search-results');
        let searchTimeout;

        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                const q = e.target.value;
                if (q.length < 3) {
                    resultsBox.classList.add('hidden');
                    return;
                }

                searchTimeout = setTimeout(async () => {
                    try {
                        const res = await fetch(`{{ route('radio.search') }}?q=${q}`);
                        if (!res.ok) throw new Error('Network response was not ok');
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
                    } catch (error) {
                        console.error("Search error:", error);
                    }
                }, 500);
            });
        }

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

                if (!res.ok) {
                    throw new Error(data.message || 'Gagal request lagu.');
                }

                // Use global Toast if available, otherwise Swal
                if (typeof Toast !== 'undefined') {
                    Toast.fire({
                        icon: 'success',
                        title: data.message || 'Request berhasil dikirim ke DJ!'
                    });
                } else {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data.message || 'Request berhasil dikirim ke DJ!',
                        timer: 3000,
                        showConfirmButton: false
                    });
                }

            } catch (error) {
                console.error('Request Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Request',
                    text: error.message || 'Terjadi kesalahan saat mengirim request.',
                });
            }
        }

        // Close results when clicking outside
        document.addEventListener('click', (e) => {
            if (searchInput && resultsBox) {
                if (!searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
                    resultsBox.classList.add('hidden');
                }
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush
