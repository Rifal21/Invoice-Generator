@php
    $currentRouteGroup = request()->routeIs('categories.*', 'products.*')
        ? 'master'
        : (request()->routeIs('invoices.*', 'pos.*')
            ? 'ops'
            : (request()->routeIs('inventory.*')
                ? 'inv'
                : (request()->routeIs('profit.*')
                    ? 'fin'
                    : (request()->routeIs('users.*')
                        ? 'usr'
                        : (request()->routeIs('salaries.*')
                            ? 'hrd'
                            : '')))));
@endphp

<!-- Mobile Sidebar Overlay -->
<div x-show="sidebarOpen" class="relative z-50 lg:hidden" role="dialog" aria-modal="true">
    <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/80"></div>

    <div class="fixed inset-0 flex">
        <div x-show="sidebarOpen" x-transition:enter="transition ease-in-out duration-300 transform"
            x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full" class="relative mr-16 flex w-full max-w-xs flex-1">
            <div class="absolute top-0 left-full flex w-16 justify-center pt-5">
                <button type="button" @click="sidebarOpen = false" class="-m-2.5 p-2.5 text-white">
                    <span class="sr-only">Tutup sidebar</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-gray-900 px-6 pb-4 ring-1 ring-white/10">
                <div class="flex h-16 shrink-0 items-center">
                    <h1 class="text-xl font-bold text-white">Invoice App</h1>
                </div>
                <nav class="flex flex-1 flex-col">
                    <ul role="list" class="flex flex-1 flex-col gap-y-7" x-data="{ openMenu: '{{ $currentRouteGroup }}' }">
                        <li>
                            <ul role="list" class="-mx-2 space-y-2">
                                @include('layouts.partials.menu-items')
                            </ul>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Static sidebar for desktop -->
<div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
    <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-gray-900 px-6 pb-4">
        <div class="flex h-16 shrink-0 items-center">
            <h1 class="text-xl font-bold text-white">KOPERASI JR</h1>
        </div>
        <nav class="flex flex-1 flex-col">
            <ul role="list" class="flex flex-1 flex-col gap-y-7" x-data="{ openMenu: '{{ $currentRouteGroup }}' }">
                <li>
                    <ul role="list" class="-mx-2 space-y-2">
                        @include('layouts.partials.menu-items')
                    </ul>
                </li>
            </ul>
        </nav>

        <!-- User Profile Section -->
        @auth
            <div class="mt-auto border-t border-white/10 p-4">
                <div class="flex items-center gap-3 px-2 py-3 rounded-2xl bg-white/5 mb-3">
                    <div
                        class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-black shadow-lg">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-black text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                            {{ auth()->user()->role }}</p>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-3 px-4 py-3 text-sm font-bold text-gray-400 hover:text-white hover:bg-red-500/10 hover:text-red-400 rounded-xl transition-all group">
                        <svg class="h-5 w-5 shrink-0 transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                        </svg>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        @endauth
    </div>
</div>
