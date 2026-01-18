<!-- Bottom Navigation for Mobile -->
<div
    class="lg:hidden fixed bottom-0 left-0 right-0 z-50 bg-white border-t border-gray-200 pb-safe shadow-[0_-4px_10px_rgba(0,0,0,0.05)]">
    <div class="flex items-center justify-around h-20 px-2">
        <!-- Invoices -->
        <a href="{{ route('invoices.index') }}"
            class="flex flex-col items-center justify-center w-full h-full gap-1 transition-all duration-200 {{ request()->routeIs('invoices.*') ? 'text-indigo-600' : 'text-gray-400' }}">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
            </svg>
            <span class="text-[10px] font-bold uppercase tracking-wider">Invoice</span>
        </a>

        <!-- POS -->
        <a href="{{ route('pos.index') }}"
            class="flex flex-col items-center justify-center w-full h-full gap-1 transition-all duration-200 {{ request()->routeIs('pos.*') ? 'text-indigo-400' : 'text-gray-400' }}">
            <div class="p-2.5 bg-indigo-600 rounded-2xl shadow-lg -mt-8 border-4 border-white">
                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                </svg>
            </div>
        </a>

        <!-- Products -->
        <a href="{{ route('products.index') }}"
            class="flex flex-col items-center justify-center w-full h-full gap-1 transition-all duration-200 {{ request()->routeIs('products.*') ? 'text-indigo-600' : 'text-gray-400' }}">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.128 16.556 17.975 12 17.975s-8.25-1.847-8.25-4.125v-3.75m16.5 0v3.75" />
            </svg>
            <span class="text-[10px] font-bold uppercase tracking-wider">Produk</span>
        </a>

        <!-- Stock -->
        <a href="{{ route('inventory.index') }}"
            class="flex flex-col items-center justify-center w-full h-full gap-1 transition-all duration-200 {{ request()->routeIs('inventory.*') ? 'text-indigo-600' : 'text-gray-400' }}">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
            </svg>
            <span class="text-[10px] font-bold uppercase tracking-wider">Gudang</span>
        </a>

        <!-- More Button -->
        <button @click="mobileMenuOpen = true"
            class="flex flex-col items-center justify-center w-full h-full gap-1 transition-all duration-200 text-gray-400">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
            <span class="text-[10px] font-bold uppercase tracking-wider">Lainnya</span>
        </button>
    </div>
</div>
