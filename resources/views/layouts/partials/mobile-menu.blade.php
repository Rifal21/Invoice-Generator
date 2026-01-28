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
            @if (!auth()->user()->isAdminAbsensi())
                <!-- Operasional Section -->
                <div>
                    <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4 ml-2">Operasional
                    </h4>
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('invoices.index') }}"
                            class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-white border border-gray-100 shadow-sm">
                            <div
                                class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                                <i class="fas fa-file-invoice text-xl"></i>
                            </div>
                            <span class="text-[11px] font-black text-center text-gray-800">Invoice Gen</span>
                        </a>
                        <a href="{{ route('pos.index') }}"
                            class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-white border border-gray-100 shadow-sm">
                            <div
                                class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                                <i class="fas fa-shopping-cart text-xl"></i>
                            </div>
                            <span class="text-[11px] font-black text-center text-gray-800">Kasir (POS)</span>
                        </a>
                        <a href="{{ route('rice-deliveries.index') }}"
                            class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-white border border-gray-100 shadow-sm">
                            <div
                                class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                                <i class="fas fa-shipping-fast text-xl"></i>
                            </div>
                            <span class="text-[11px] font-black text-center text-gray-800">Nota Beras</span>
                        </a>
                        <a href="{{ route('delivery-orders.index') }}"
                            class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-white border border-gray-100 shadow-sm">
                            <div
                                class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600">
                                <i class="fas fa-truck text-xl"></i>
                            </div>
                            <span class="text-[11px] font-black text-center text-gray-800">Surat Jalan</span>
                        </a>
                        <a href="{{ route('vehicle-rentals.index') }}"
                            class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-white border border-gray-100 shadow-sm">
                            <div
                                class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600">
                                <i class="fas fa-car text-xl"></i>
                            </div>
                            <span class="text-[11px] font-black text-center text-gray-800">Sewa Mobil</span>
                        </a>
                        <a href="{{ route('inventory.index') }}"
                            class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-white border border-gray-100 shadow-sm">
                            <div
                                class="w-12 h-12 rounded-2xl bg-purple-50 flex items-center justify-center text-purple-600">
                                <i class="fas fa-warehouse text-xl"></i>
                            </div>
                            <span class="text-[11px] font-black text-center text-gray-800">Gudang</span>
                        </a>
                        <a href="{{ route('radio.index') }}"
                            class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-indigo-600 border border-indigo-500 shadow-lg shadow-indigo-200">
                            <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center text-white">
                                <i class="fas fa-radio text-xl animate-pulse"></i>
                            </div>
                            <span class="text-[11px] font-black text-center text-white">Live Radio</span>
                        </a>
                    </div>
                </div>

                <!-- Analisa Keuangan Section -->
                <div>
                    <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4 ml-2">Analisa
                        Keuangan
                    </h4>
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('finance.summary') }}"
                            class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-white border border-gray-100 shadow-sm">
                            <div
                                class="w-12 h-12 rounded-2xl bg-rose-50 flex items-center justify-center text-rose-600">
                                <i class="fas fa-chart-line text-xl"></i>
                            </div>
                            <span class="text-[11px] font-black text-center text-gray-800">Ringkasan</span>
                        </a>
                        <a href="{{ route('profit.index') }}"
                            class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-white border border-gray-100 shadow-sm">
                            <div
                                class="w-12 h-12 rounded-2xl bg-rose-50 flex items-center justify-center text-rose-600">
                                <i class="fas fa-sack-dollar text-xl"></i>
                            </div>
                            <span class="text-[11px] font-black text-center text-gray-800">Laba Rugi</span>
                        </a>
                        <a href="{{ route('expenses.index') }}"
                            class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-white border border-gray-100 shadow-sm">
                            <div
                                class="w-12 h-12 rounded-2xl bg-rose-50 flex items-center justify-center text-rose-600">
                                <i class="fas fa-money-bill-wave text-xl"></i>
                            </div>
                            <span class="text-[11px] font-black text-center text-gray-800">Pengeluaran</span>
                        </a>
                        <a href="{{ route('rice-order-recap.index') }}"
                            class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-white border border-gray-100 shadow-sm">
                            <div
                                class="w-12 h-12 rounded-2xl bg-rose-50 flex items-center justify-center text-rose-600">
                                <i class="fas fa-list-check text-xl"></i>
                            </div>
                            <span class="text-[11px] font-black text-center text-gray-800">Rekap Beras</span>
                        </a>
                    </div>
                </div>

                <!-- Master Data Section -->
                <div>
                    <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4 ml-2">Master Data
                    </h4>
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('products.index') }}"
                            class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-white border border-gray-100 shadow-sm">
                            <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-gray-600">
                                <i class="fas fa-boxes text-lg"></i>
                            </div>
                            <span class="text-[10px] font-bold text-center text-gray-700">Produk</span>
                        </a>
                        <a href="{{ route('categories.index') }}"
                            class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-white border border-gray-100 shadow-sm">
                            <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-gray-600">
                                <i class="fas fa-tags text-lg"></i>
                            </div>
                            <span class="text-[10px] font-bold text-center text-gray-700">Kategori</span>
                        </a>
                        <a href="{{ route('customers.index') }}"
                            class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-white border border-gray-100 shadow-sm">
                            <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-gray-600">
                                <i class="fas fa-users text-lg"></i>
                            </div>
                            <span class="text-[10px] font-bold text-center text-gray-700">Pelanggan</span>
                        </a>
                        <a href="{{ route('suppliers.index') }}"
                            class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-white border border-gray-100 shadow-sm">
                            <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-gray-600">
                                <i class="fas fa-truck text-lg"></i>
                            </div>
                            <span class="text-[10px] font-bold text-center text-gray-700">Supplier</span>
                        </a>
                    </div>
                </div>
            @endif

            <!-- Kepegawaian Section -->
            @if (auth()->user() && (auth()->user()->isSuperAdmin() || auth()->user()->isKetua() || auth()->user()->isAdminAbsensi()))
                <div>
                    <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4 ml-2">Kepegawaian
                    </h4>
                    <div class="grid grid-cols-2 gap-3">
                        @if (auth()->user()->isSuperAdmin() || auth()->user()->isKetua())
                            <a href="{{ route('users.index') }}"
                                class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-white border border-gray-100 shadow-sm">
                                <div
                                    class="w-10 h-10 rounded-xl bg-cyan-50 flex items-center justify-center text-cyan-600">
                                    <i class="fas fa-user-tie text-lg"></i>
                                </div>
                                <span class="text-[10px] font-bold text-center text-gray-700">Pegawai</span>
                            </a>
                            <a href="{{ route('salaries.index') }}"
                                class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-white border border-gray-100 shadow-sm">
                                <div
                                    class="w-10 h-10 rounded-xl bg-cyan-50 flex items-center justify-center text-cyan-600">
                                    <i class="fas fa-wallet text-lg"></i>
                                </div>
                                <span class="text-[10px] font-bold text-center text-gray-700">Gaji</span>
                            </a>
                        @endif
                        <a href="{{ route('attendance.report') }}"
                            class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-white border border-gray-100 shadow-sm">
                            <div
                                class="w-10 h-10 rounded-xl bg-cyan-50 flex items-center justify-center text-cyan-600">
                                <i class="fas fa-clipboard-list text-lg"></i>
                            </div>
                            <span class="text-[10px] font-bold text-center text-gray-700">Absensi</span>
                        </a>
                        <a href="{{ route('attendance.public') }}" target="_blank"
                            class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-emerald-50 border border-emerald-100 shadow-sm">
                            <div
                                class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-emerald-600">
                                <i class="fas fa-qrcode text-lg"></i>
                            </div>
                            <span class="text-[10px] font-black text-center text-emerald-700">Scan QR</span>
                        </a>
                    </div>
                </div>
            @endif

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
