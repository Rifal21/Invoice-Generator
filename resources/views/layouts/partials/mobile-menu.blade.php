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
            <!-- Archive Section -->
            <div>
                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4 ml-2">Arsip & Legalitas
                </h4>
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('documents.index') }}"
                        class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-white border border-gray-100 shadow-sm hover:bg-gray-50 transition-colors">
                        <div class="w-10 h-10 rounded-xl bg-yellow-50 flex items-center justify-center text-yellow-600">
                            <i class="fas fa-folder-open text-lg"></i>
                        </div>
                        <span class="text-[10px] font-bold text-center text-gray-700">Dokumen Legalitas</span>
                    </a>
                </div>
            </div>

            @if (!auth()->user()->isAdminAbsensi())
                <!-- Master Data Section -->
                <div>
                    <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4 ml-2">Master Data
                    </h4>
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('categories.index') }}"
                            class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-white border border-gray-100 shadow-sm hover:bg-gray-50 transition-colors">
                            <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-gray-600">
                                <i class="fas fa-tags text-lg"></i>
                            </div>
                            <span class="text-[10px] font-bold text-center text-gray-700">Kategori</span>
                        </a>
                        <a href="{{ route('customers.index') }}"
                            class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-white border border-gray-100 shadow-sm hover:bg-gray-50 transition-colors">
                            <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-gray-600">
                                <i class="fas fa-users text-lg"></i>
                            </div>
                            <span class="text-[10px] font-bold text-center text-gray-700">Pelanggan</span>
                        </a>
                        <a href="{{ route('suppliers.index') }}"
                            class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-white border border-gray-100 shadow-sm hover:bg-gray-50 transition-colors">
                            <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-gray-600">
                                <i class="fas fa-truck text-lg"></i>
                            </div>
                            <span class="text-[10px] font-bold text-center text-gray-700">Supplier</span>
                        </a>
                        <a href="{{ route('products.index') }}"
                            class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-white border border-gray-100 shadow-sm hover:bg-gray-50 transition-colors">
                            <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center text-gray-600">
                                <i class="fas fa-boxes text-lg"></i>
                            </div>
                            <span class="text-[10px] font-bold text-center text-gray-700">Produk</span>
                        </a>
                    </div>
                </div>

                <!-- Operasional Section -->
                <div>
                    <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4 ml-2">Operasional
                    </h4>
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('rice-deliveries.index') }}"
                            class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-white border border-gray-100 shadow-sm hover:bg-gray-50 transition-colors">
                            <div
                                class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                                <i class="fas fa-shipping-fast text-xl"></i>
                            </div>
                            <span class="text-[11px] font-black text-center text-gray-800">Nota Beras</span>
                        </a>
                        <a href="{{ route('delivery-orders.index') }}"
                            class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-white border border-gray-100 shadow-sm hover:bg-gray-50 transition-colors">
                            <div
                                class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600">
                                <i class="fas fa-truck text-xl"></i>
                            </div>
                            <span class="text-[11px] font-black text-center text-gray-800">Surat Jalan</span>
                        </a>
                        <a href="{{ route('invoices.index') }}"
                            class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-white border border-gray-100 shadow-sm hover:bg-gray-50 transition-colors">
                            <div
                                class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                                <i class="fas fa-file-invoice text-xl"></i>
                            </div>
                            <span class="text-[11px] font-black text-center text-gray-800">Invoice Gen</span>
                        </a>
                        <a href="{{ route('vehicle-rentals.index') }}"
                            class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-white border border-gray-100 shadow-sm hover:bg-gray-50 transition-colors">
                            <div
                                class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center text-amber-600">
                                <i class="fas fa-car text-xl"></i>
                            </div>
                            <span class="text-[11px] font-black text-center text-gray-800">Sewa Mobil</span>
                        </a>
                        <a href="{{ route('dedi-invoices.index') }}"
                            class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-white border border-gray-100 shadow-sm hover:bg-gray-50 transition-colors">
                            <div
                                class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                                <i class="fas fa-file-invoice text-xl"></i>
                            </div>
                            <span class="text-[11px] font-black text-center text-gray-800">Faktur H. Dedi</span>
                        </a>
                        <a href="{{ route('kitchen-incentives.index') }}"
                            class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-white border border-gray-100 shadow-sm hover:bg-gray-50 transition-colors">
                            <div
                                class="w-12 h-12 rounded-2xl bg-orange-50 flex items-center justify-center text-orange-600">
                                <i class="fas fa-utensils text-xl"></i>
                            </div>
                            <span class="text-[11px] font-black text-center text-gray-800">Insentif Dapur</span>
                        </a>
                        <a href="{{ route('pos.index') }}"
                            class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-white border border-gray-100 shadow-sm hover:bg-gray-50 transition-colors">
                            <div
                                class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                                <i class="fas fa-shopping-cart text-xl"></i>
                            </div>
                            <span class="text-[11px] font-black text-center text-gray-800">Kasir (POS)</span>
                        </a>
                        <a href="{{ route('chat.index') }}"
                            class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-emerald-600 border border-emerald-500 shadow-lg shadow-emerald-200">
                            <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center text-white">
                                <i class="fas fa-comments text-xl"></i>
                            </div>
                            <span class="text-[11px] font-black text-center text-white">Global Chat</span>
                        </a>
                        @if (auth()->user()->name === 'Rifal Kurniawan')
                            <a href="{{ route('radio.index') }}"
                                class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-indigo-600 border border-indigo-500 shadow-lg shadow-indigo-200">
                                <div
                                    class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center text-white">
                                    <i class="fas fa-radio text-xl animate-pulse"></i>
                                </div>
                                <span class="text-[11px] font-black text-center text-white">Live Radio</span>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Inventori Section -->
                <div>
                    <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4 ml-2">Inventori &
                        Stok
                    </h4>
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('inventory.index') }}"
                            class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-white border border-gray-100 shadow-sm hover:bg-gray-50 transition-colors">
                            <div
                                class="w-12 h-12 rounded-2xl bg-purple-50 flex items-center justify-center text-purple-600">
                                <i class="fas fa-cubes text-xl"></i>
                            </div>
                            <span class="text-[11px] font-black text-center text-gray-800">Gudang Utama</span>
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
                            class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-white border border-gray-100 shadow-sm hover:bg-gray-50 transition-colors">
                            <div
                                class="w-12 h-12 rounded-2xl bg-rose-50 flex items-center justify-center text-rose-600">
                                <i class="fas fa-chart-line text-xl"></i>
                            </div>
                            <span class="text-[11px] font-black text-center text-gray-800">Ringkasan</span>
                        </a>
                        <a href="{{ route('profit.index') }}"
                            class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-white border border-gray-100 shadow-sm hover:bg-gray-50 transition-colors">
                            <div
                                class="w-12 h-12 rounded-2xl bg-rose-50 flex items-center justify-center text-rose-600">
                                <i class="fas fa-sack-dollar text-xl"></i>
                            </div>
                            <span class="text-[11px] font-black text-center text-gray-800">Laba Rugi</span>
                        </a>
                        <a href="{{ route('expenses.index') }}"
                            class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-white border border-gray-100 shadow-sm hover:bg-gray-50 transition-colors">
                            <div
                                class="w-12 h-12 rounded-2xl bg-rose-50 flex items-center justify-center text-rose-600">
                                <i class="fas fa-money-bill-wave text-xl"></i>
                            </div>
                            <span class="text-[11px] font-black text-center text-gray-800">Pengeluaran</span>
                        </a>
                        <a href="{{ route('rice-order-recap.index') }}"
                            class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-white border border-gray-100 shadow-sm hover:bg-gray-50 transition-colors">
                            <div
                                class="w-12 h-12 rounded-2xl bg-rose-50 flex items-center justify-center text-rose-600">
                                <i class="fas fa-list-check text-xl"></i>
                            </div>
                            <span class="text-[11px] font-black text-center text-gray-800">Rekap Beras</span>
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
                                class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-white border border-gray-100 shadow-sm hover:bg-gray-50 transition-colors">
                                <div
                                    class="w-10 h-10 rounded-xl bg-cyan-50 flex items-center justify-center text-cyan-600">
                                    <i class="fas fa-user-tie text-lg"></i>
                                </div>
                                <span class="text-[10px] font-bold text-center text-gray-700">Pegawai</span>
                            </a>
                            <a href="{{ route('salaries.index') }}"
                                class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-white border border-gray-100 shadow-sm hover:bg-gray-50 transition-colors">
                                <div
                                    class="w-10 h-10 rounded-xl bg-cyan-50 flex items-center justify-center text-cyan-600">
                                    <i class="fas fa-wallet text-lg"></i>
                                </div>
                                <span class="text-[10px] font-bold text-center text-gray-700">Gaji</span>
                            </a>
                        @endif
                        <a href="{{ route('attendance.report') }}"
                            class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-white border border-gray-100 shadow-sm hover:bg-gray-50 transition-colors">
                            <div
                                class="w-10 h-10 rounded-xl bg-cyan-50 flex items-center justify-center text-cyan-600">
                                <i class="fas fa-clipboard-list text-lg"></i>
                            </div>
                            <span class="text-[10px] font-bold text-center text-gray-700">Absensi</span>
                        </a>
                        <a href="{{ route('attendance.settings') }}"
                            class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-white border border-gray-100 shadow-sm hover:bg-gray-50 transition-colors">
                            <div
                                class="w-10 h-10 rounded-xl bg-cyan-50 flex items-center justify-center text-cyan-600">
                                <i class="fas fa-cog text-lg"></i>
                            </div>
                            <span class="text-[10px] font-bold text-center text-gray-700">Pengaturan</span>
                        </a>
                        <a href="{{ route('attendance.public') }}" target="_blank"
                            class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-emerald-50 border border-emerald-100 shadow-sm hover:bg-emerald-100 transition-colors">
                            <div
                                class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-emerald-600">
                                <i class="fas fa-qrcode text-lg"></i>
                            </div>
                            <span class="text-[10px] font-black text-center text-emerald-700">Scan QR</span>
                        </a>
                    </div>
                </div>
            @endif

            @if (auth()->user()->name === 'Rifal Kurniawan')
                <!-- Log Aktivitas -->
                <div>
                    <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4 ml-2">Log Sistem
                    </h4>
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('activity-logs.index') }}"
                            class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-white border border-gray-100 shadow-sm hover:bg-gray-50 transition-colors">
                            <div
                                class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-500">
                                <i class="fas fa-history text-lg"></i>
                            </div>
                            <span class="text-[10px] font-bold text-center text-gray-700">Log Aktivitas</span>
                        </a>

                        <a href="{{ route('monitor.index') }}"
                            class="flex flex-col items-center gap-2 p-4 rounded-3xl bg-white border border-gray-100 shadow-sm hover:bg-gray-50 transition-colors relative overflow-hidden">
                            <div class="absolute top-2 right-2 flex h-2 w-2">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                            </div>
                            <div
                                class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center text-green-600">
                                <i class="fas fa-desktop text-lg"></i>
                            </div>
                            <span class="text-[10px] font-bold text-center text-gray-700">Monitor Login</span>
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
