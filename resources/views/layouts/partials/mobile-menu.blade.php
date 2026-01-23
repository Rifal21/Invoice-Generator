<!-- Mobile More Menu Modal -->
<div x-show="mobileMenuOpen" class="fixed inset-0 z-50 lg:hidden" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-full" x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-full">
    <div @click="mobileMenuOpen = false" class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>

    <div
        class="absolute bottom-0 left-0 right-0 bg-white rounded-t-[2.5rem] p-8 shadow-2xl overflow-y-auto max-h-[90vh]">
        <div class="w-12 h-1.5 bg-gray-200 rounded-full mx-auto mb-8"></div>

        <h3 class="text-xl font-black text-gray-900 mb-6 text-center">Menu Lainnya</h3>

        <div class="grid grid-cols-2 gap-4 pb-8">
            <a href="{{ route('rice-deliveries.index') }}"
                class="flex flex-col items-center gap-3 p-5 rounded-3xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition-all border border-emerald-100">
                <i class="fas fa-shipping-fast text-3xl"></i>
                <span class="text-sm font-black text-center">Nota Beras</span>
            </a>

            <a href="{{ route('vehicle-rentals.index') }}"
                class="flex flex-col items-center gap-3 p-5 rounded-3xl bg-amber-50 text-amber-700 hover:bg-amber-100 transition-all border border-amber-100">
                <i class="fas fa-car text-3xl"></i>
                <span class="text-sm font-black text-center">Sewa Mobil</span>
            </a>

            <a href="{{ route('delivery-orders.index') }}"
                class="flex flex-col items-center gap-3 p-5 rounded-3xl bg-blue-50 text-blue-700 hover:bg-blue-100 transition-all border border-blue-100">
                <i class="fas fa-truck text-3xl"></i>
                <span class="text-sm font-black text-center">Surat Jalan</span>
            </a>


            <a href="{{ route('profit.index') }}"
                class="flex flex-col items-center gap-3 p-5 rounded-3xl bg-blue-50 text-blue-700 hover:bg-blue-100 transition-all border border-blue-100">
                <i class="fas fa-chart-pie text-3xl"></i>
                <span class="text-sm font-black text-center">Laba Rugi</span>
            </a>

            <a href="{{ route('categories.index') }}"
                class="flex flex-col items-center gap-3 p-5 rounded-3xl bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition-all border border-indigo-100">
                <i class="fas fa-tags text-3xl"></i>
                <span class="text-sm font-black text-center">Kategori</span>
            </a>

            @if (auth()->user() && auth()->user()->isSuperAdmin())
                <a href="{{ route('users.index') }}"
                    class="flex flex-col items-center gap-3 p-5 rounded-3xl bg-orange-50 text-orange-700 hover:bg-orange-100 transition-all border border-orange-100 col-span-2">
                    <i class="fas fa-users-cog text-3xl"></i>
                    <span class="text-sm font-black">Manajemen User</span>
                </a>
            @endif
        </div>

        <div class="border-t border-gray-100 pt-6">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="w-full flex items-center justify-center gap-3 py-4 bg-red-50 text-red-700 rounded-2xl font-black hover:bg-red-100 transition-all">
                    <i class="fas fa-right-from-bracket text-lg"></i>
                    LOGOUT
                </button>
            </form>
        </div>

        <button @click="mobileMenuOpen = false"
            class="w-full py-4 text-gray-400 font-bold hover:text-gray-600 transition-all mt-2 uppercase tracking-widest text-[10px]">
            Tutup
        </button>
    </div>
</div>
