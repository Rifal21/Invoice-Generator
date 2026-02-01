@php
    $currentRouteGroup = '';
    if (request()->routeIs('categories.*', 'customers.*', 'suppliers.*', 'products.*')) {
        $currentRouteGroup = 'master';
    } elseif (
        request()->routeIs(
            'rice-deliveries.*',
            'delivery-orders.*',
            'invoices.*',
            'vehicle-rentals.*',
            'dedi-invoices.*',
            'kitchen-incentives.*',
            'pos.*',
            'chat.*',
            'radio.*',
        )
    ) {
        $currentRouteGroup = 'ops';
    } elseif (request()->routeIs('inventory.*')) {
        $currentRouteGroup = 'inv';
    } elseif (request()->routeIs('finance.summary', 'expenses.*', 'profit.*', 'rice-order-recap.*')) {
        $currentRouteGroup = 'fin';
    } elseif (request()->routeIs('users.*', 'salaries.*', 'attendance.*')) {
        $currentRouteGroup = 'hrd';
    }
@endphp



<!-- Static sidebar for desktop -->
<div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:flex-col" :class="sidebarCollapsed ? 'lg:w-20' : 'lg:w-72'">
    <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-gray-900 px-6 pb-4 transition-all duration-300">
        <div class="flex h-16 shrink-0 items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/kopinvoice.png') }}" class="h-8 w-8 object-contain" alt="Logo">
                <h1 x-show="!sidebarCollapsed" class="text-xl font-bold text-white tracking-tight uppercase">KOPERASI JR
                </h1>
            </div>
            <button @click="sidebarCollapsed = !sidebarCollapsed"
                class="p-2 text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg">
                <i :class="sidebarCollapsed ? 'fa-angles-right' : 'fa-angles-left'" class="fas"></i>
            </button>
        </div>
        <nav class="flex flex-1 flex-col">
            <ul role="list" class="flex flex-1 flex-col gap-y-7" x-data="{ openMenu: '{{ $currentRouteGroup }}', collapsed: sidebarCollapsed }">
                <li>
                    <ul role="list" class="-mx-2 space-y-2">
                        @include('layouts.partials.menu-items')
                    </ul>
                </li>
            </ul>
        </nav>

        <!-- User Profile Section -->
        @auth
            <div class="mt-auto border-t border-white/10 pt-4 transition-all duration-300"
                :class="sidebarCollapsed ? 'px-2' : 'px-4'">
                <!-- User Info Card -->
                <div class="flex items-center gap-3 rounded-2xl bg-white/5 mb-3 transition-all duration-300"
                    :class="sidebarCollapsed ? 'px-0 py-2 justify-center' : 'px-2 py-3'">
                    <div
                        class="h-10 w-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-black shadow-lg flex-shrink-0">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0 overflow-hidden transition-all duration-300" x-show="!sidebarCollapsed"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform scale-95"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 transform scale-100"
                        x-transition:leave-end="opacity-0 transform scale-95">
                        <p class="text-sm font-black text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                            {{ auth()->user()->role }}</p>
                    </div>
                </div>

                <!-- Logout Button -->
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-3 text-sm font-bold text-gray-400 hover:text-white hover:bg-red-500/10 hover:text-red-400 rounded-xl transition-all group"
                        :class="sidebarCollapsed ? 'justify-center px-2 py-3' : 'px-4 py-3'">
                        <i class="fas fa-right-from-bracket text-lg"></i>
                        <span x-show="!sidebarCollapsed" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0">Keluar</span>
                    </button>
                </form>
            </div>
        @endauth
    </div>
</div>
