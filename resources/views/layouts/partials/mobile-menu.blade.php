<!-- Mobile More Menu Modal -->
<div x-show="mobileMenuOpen" class="fixed inset-0 z-50 lg:hidden" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-full" x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-full">

    <div @click="mobileMenuOpen = false" class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>

    <div
        class="absolute bottom-0 left-0 right-0 bg-gray-50 rounded-t-[2.5rem] shadow-2xl overflow-y-auto max-h-[90vh] pb-safe">
        <div class="sticky top-0 bg-gray-50/80 backdrop-blur-md pt-4 pb-2 px-8 z-10">
            <div class="w-12 h-1.5 bg-gray-300 rounded-full mx-auto mb-4"></div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-black text-gray-900">Menu Navigasi</h3>
                <button @click="mobileMenuOpen = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times-circle text-2xl"></i>
                </button>
            </div>
        </div>

        <div class="px-6 pb-12 space-y-8">
            <!-- Dynamic Menu Sections from Sidebar Settings -->
            @foreach ($sidebarItems as $item)
                @php
                    $canSee = true;
                    $user = auth()->user();
                    if (!$user) {
                        $canSee = false;
                    } else {
                        if (in_array($item->label, ['Kepegawaian'])) {
                            $canSee = $user->isSuperAdmin() || $user->isKetua() || $user->isAdminAbsensi();
                        }
                        if (in_array($item->label, ['Log Aktivitas', 'Monitor Login', 'Cloud Backup', 'Settings'])) {
                            $canSee = $user->isSuperAdmin() || $user->isKetua() || $user->name === 'Rifal Kurniawan';
                        }
                        if ($item->label === 'Billing & Token') {
                            $canSee = $user->isSuperAdmin() || $user->name === 'Rifal Kurniawan';
                        }
                        if (
                            $user->isAdminAbsensi() &&
                            in_array($item->label, ['Master Data', 'Operasional', 'Gudang & Stok', 'Analisa Keuangan'])
                        ) {
                            $canSee = false;
                        }
                    }
                @endphp

                @if ($canSee)
                    @if ($item->route === 'ai.index' || $item->label === 'Business Insights')
                        <!-- Featured AI Section (Special Layout) -->
                        <div class="relative group">
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-indigo-500/20 to-pink-500/20 rounded-[2rem] blur-xl opacity-50 group-hover:opacity-100 transition-opacity">
                            </div>
                            <a href="{{ route('ai.index') }}"
                                class="relative flex items-center gap-5 p-5 rounded-[2rem] bg-gradient-to-br from-indigo-600 to-indigo-800 border-none shadow-xl shadow-indigo-200 overflow-hidden">
                                <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
                                <div
                                    class="w-14 h-14 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center text-white border border-white/20">
                                    <i class="fas fa-brain text-2xl animate-pulse"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="px-2 py-0.5 rounded-full bg-pink-500 text-[8px] font-black text-white uppercase tracking-tighter">New
                                            / AI</span>
                                        <h4 class="text-xs font-black text-indigo-100 uppercase tracking-widest">
                                            {{ $item->label }}</h4>
                                    </div>
                                    <p class="text-[10px] font-bold text-indigo-200/80 mt-1">Analisis performa bisnis
                                        cerdas</p>
                                </div>
                                <div
                                    class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-white">
                                    <i class="fas fa-arrow-right text-xs"></i>
                                </div>
                            </a>
                        </div>
                    @else
                        <div>
                            <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4 ml-2">
                                {{ $item->label }}</h4>
                            <div class="grid grid-cols-2 gap-3">
                                @if ($item->route)
                                    <a href="{{ route($item->route) }}"
                                        class="flex flex-col items-center gap-2 p-4 rounded-3xl {{ $item->label === 'Global Chat' || $item->label === 'Live Radio' ? 'bg-indigo-600 border-indigo-500 text-white shadow-indigo-100' : 'bg-white border-gray-100 text-gray-700 shadow-sm' }} border shadow-sm hover:opacity-80 transition-all">
                                        <div
                                            class="w-10 h-10 rounded-xl {{ $item->label === 'Global Chat' || $item->label === 'Live Radio' ? 'bg-white/20' : 'bg-gray-50' }} flex items-center justify-center">
                                            <i class="{{ $item->icon }} text-lg"></i>
                                        </div>
                                        <span class="text-[10px] font-bold text-center">{{ $item->label }}</span>
                                    </a>
                                @endif

                                @foreach ($item->children as $child)
                                    <a href="{{ route($child->route) }}"
                                        class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-white border border-gray-100 shadow-sm hover:bg-gray-50 transition-colors">
                                        <div
                                            class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-gray-600">
                                            <i class="{{ $child->icon }} text-lg"></i>
                                        </div>
                                        <span class="text-[10px] font-bold text-center text-gray-700">
                                            {{ $child->label }}
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif
            @endforeach

            <!-- Secondary Actions -->
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('users.profile') }}"
                    class="flex items-center gap-3 p-4 rounded-3xl bg-white border border-gray-100 shadow-sm">
                    <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <span class="text-[11px] font-black text-gray-900 uppercase">Profil</span>
                </a>
                <a href="{{ route('users.my-barcode') }}"
                    class="flex items-center gap-3 p-4 rounded-3xl bg-white border border-gray-100 shadow-sm">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <span class="text-[11px] font-black text-gray-900 uppercase">Barcode</span>
                </a>
            </div>

            <div class="pt-4">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center justify-center gap-3 py-4 bg-red-50 text-red-600 rounded-3xl font-black hover:bg-red-100 transition-all border border-red-100">
                        <i class="fas fa-power-off"></i>
                        KELUAR APLIKASI
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
