@extends('layouts.app')

@section('title', 'Monitor User Login')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" wire:poll.10s>
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
            <div class="min-w-0 flex-1">
                <h1 class="text-3xl md:text-4xl font-black text-gray-900 tracking-tight">Monitor User Login</h1>
                <p class="mt-2 text-sm md:text-lg text-gray-500">Pantau pengguna yang sedang aktif secara realtime.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="relative flex h-3 w-3">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                </span>
                <span class="text-xs font-bold text-green-600 uppercase tracking-widest">Live Update</span>
            </div>
        </div>

        <!-- Active Users Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="users-grid">
            @forelse ($activeUsers as $user)
                <div
                    class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100 relative group hover:shadow-2xl transition-all duration-300">
                    <!-- Status Indicator -->
                    <div class="absolute top-4 right-4 flex items-center gap-2">
                        @if ($user['is_current_device'])
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black bg-indigo-100 text-indigo-800 uppercase tracking-widest">
                                This Device
                            </span>
                        @endif
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black bg-green-100 text-green-800 uppercase tracking-widest">
                            Online
                        </span>
                    </div>

                    <div class="p-6">
                        <div class="flex items-center gap-4 mb-4">
                            <div
                                class="h-14 w-14 rounded-2xl bg-indigo-50 flex items-center justify-center border-2 border-indigo-100">
                                <span class="text-xl font-black text-indigo-600">{{ substr($user['name'], 0, 2) }}</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-gray-900 line-clamp-1">{{ $user['name'] }}</h3>
                                <p class="text-xs text-gray-500 font-bold">{{ $user['email'] }}</p>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <!-- Device Info -->
                            <div class="bg-gray-50 rounded-2xl p-3 flex items-start gap-3">
                                <div class="mt-1">
                                    @if (strtolower($user['device']['platform']) == 'windows')
                                        <i class="fab fa-windows text-lg text-blue-500"></i>
                                    @elseif(strtolower($user['device']['platform']) == 'android')
                                        <i class="fab fa-android text-lg text-green-500"></i>
                                    @elseif(strtolower($user['device']['platform']) == 'ios' || strtolower($user['device']['platform']) == 'macos')
                                        <i class="fab fa-apple text-lg text-gray-800"></i>
                                    @elseif(strtolower($user['device']['platform']) == 'linux')
                                        <i class="fab fa-linux text-lg text-yellow-600"></i>
                                    @else
                                        <i class="fas fa-desktop text-lg text-gray-400"></i>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-0.5">Perangkat
                                    </p>
                                    <p class="text-sm font-bold text-gray-800">
                                        {{ $user['device']['platform'] }} &bull; {{ $user['device']['browser'] }}
                                    </p>
                                </div>
                            </div>

                            <!-- IP & Time -->
                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-gray-50 rounded-2xl p-3">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">IP
                                        Address</p>
                                    <p class="text-xs font-bold text-gray-800 font-mono">{{ $user['ip_address'] }}</p>
                                </div>
                                <div class="bg-gray-50 rounded-2xl p-3">
                                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Aktivitas
                                    </p>
                                    <p class="text-xs font-bold text-gray-800">{{ $user['last_activity'] }}</p>
                                </div>
                            </div>

                            <!-- Logout Button -->
                            @if (!$user['is_current_device'])
                                <div class="mt-4 border-t border-gray-100 pt-4">
                                    <form action="{{ route('monitor.logout', $user['id']) }}" method="POST"
                                        onsubmit="return confirm('Apakah Anda yakin ingin mengeluarkan user ini?')">
                                        @csrf
                                        <button type="submit"
                                            class="w-full inline-flex items-center justify-center px-4 py-3 border border-transparent text-xs font-black rounded-2xl text-red-600 bg-red-50 hover:bg-red-100 transition-all uppercase tracking-widest active:scale-95">
                                            <i class="fas fa-sign-out-alt mr-2"></i>
                                            Paksa Keluar
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center">
                    <div class="inline-flex h-20 w-20 items-center justify-center rounded-full bg-gray-50 mb-4">
                        <i class="fas fa-users-slash text-3xl text-gray-300"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Tidak ada user aktif</h3>
                    <p class="mt-1 text-sm text-gray-500">Belum ada user yang login saat ini.</p>
                </div>
            @endforelse
        </div>
    </div>

    @push('scripts')
        <script>
            // Simple auto-refresh every 10 seconds if Livewire is not available
            setInterval(function() {
                // Check if we are not interacting with anything
                if (!document.hidden) {
                    window.location.reload();
                }
            }, 10000); // 10 seconds
        </script>
    @endpush
@endsection
