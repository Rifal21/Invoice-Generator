<!-- Bottom Navigation for Mobile -->
<div
    class="lg:hidden fixed bottom-0 left-0 right-0 z-50 bg-white border-t border-gray-200 pb-safe shadow-[0_-4px_10px_rgba(0,0,0,0.05)]">
    <div class="flex items-center justify-around h-20 px-2">
        <!-- Home/Dashboard -->
        <a href="{{ route('finance.summary') }}"
            class="flex flex-col items-center justify-center w-full h-full gap-1 transition-all duration-200 {{ request()->routeIs('finance.summary') ? 'text-indigo-600' : 'text-gray-400' }}">
            <i class="fas fa-home text-xl"></i>
            <span class="text-[10px] font-bold uppercase tracking-wider">Beranda</span>
        </a>

        <!-- Gudang -->
        <a href="{{ route('products.index') }}"
            class="flex flex-col items-center justify-center w-full h-full gap-1 transition-all duration-200 {{ request()->routeIs('products.*') ? 'text-indigo-600' : 'text-gray-400' }}">
            <i class="fas fa-warehouse text-xl"></i>
            <span class="text-[10px] font-bold uppercase tracking-wider">Produk</span>
        </a>

        <!-- POS -->
        <a href="{{ route('invoices.index') }}"
            class="flex flex-col items-center justify-center w-full h-full gap-1 transition-all duration-200">
            <div
                class="{{ request()->routeIs('invoices.*') ? 'bg-indigo-700' : 'bg-indigo-600' }} p-3.5 rounded-2xl shadow-lg -mt-10 border-4 border-white transform transition-transform active:scale-95 group">
                <i class="fas fa-file-invoice text-xl text-white"></i>
            </div>
            <span class="text-[10px] font-black uppercase tracking-wider text-indigo-600 mt-1">Invoice</span>
        </a>

        <!-- Laporan -->
        <a href="{{ route('profit.index') }}"
            class="flex flex-col items-center justify-center w-full h-full gap-1 transition-all duration-200 {{ request()->routeIs('profit.*', 'finance.*') ? 'text-indigo-600' : 'text-gray-400' }}">
            <i class="fas fa-chart-bar text-xl"></i>
            <span class="text-[10px] font-bold uppercase tracking-wider">Laporan</span>
        </a>

        <!-- More -->
        <button @click="mobileMenuOpen = true"
            class="flex flex-col items-center justify-center w-full h-full gap-1 transition-all duration-200 text-gray-400">
            <i class="fas fa-th-large text-xl"></i>
            <span class="text-[10px] font-bold uppercase tracking-wider">Menu</span>
        </button>
    </div>
</div>
