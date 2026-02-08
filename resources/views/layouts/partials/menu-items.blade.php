<li>
    <a href="{{ route('dashboard') }}"
        class="{{ request()->routeIs('dashboard') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} flex items-center gap-3 rounded-md p-2 text-sm font-semibold transition-all duration-200"
        :class="sidebarCollapsed ? 'justify-center' : ''">
        <i class="fas fa-home text-lg w-6 text-center text-indigo-400"></i>
        <span x-show="!sidebarCollapsed">Dashboard</span>
    </a>
</li>

<li>
    <a href="{{ route('users.my-barcode') }}"
        class="{{ request()->routeIs('users.my-barcode') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} flex items-center gap-3 rounded-md p-2 text-sm font-semibold transition-all duration-200"
        :class="sidebarCollapsed ? 'justify-center' : ''">
        <i class="fas fa-qrcode text-lg w-6 text-center text-emerald-400"></i>
        <span x-show="!sidebarCollapsed">Barcode Saya</span>
    </a>
</li>

{{-- <li>
    <a href="{{ route('users.profile') }}"
        class="{{ request()->routeIs('users.profile') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} flex items-center gap-3 rounded-md p-2 text-sm font-semibold transition-all duration-200"
        :class="sidebarCollapsed ? 'justify-center' : ''">
        <i class="fas fa-user-gear text-lg w-6 text-center text-amber-400"></i>
        <span x-show="!sidebarCollapsed">Profil Saya</span>
    </a>
</li> --}}

<li>
    <a href="{{ route('ai.index') }}"
        class="{{ request()->routeIs('ai.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} flex items-center gap-3 rounded-md p-2 text-sm font-semibold transition-all duration-200"
        :class="sidebarCollapsed ? 'justify-center' : ''">
        <i class="fas fa-brain text-lg w-6 text-center text-pink-400"></i>
        <span x-show="!sidebarCollapsed">AI Insights Analyst</span>
    </a>
</li>

<li>
    <a href="{{ route('documents.index') }}"
        class="{{ request()->routeIs('documents.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} flex items-center gap-3 rounded-md p-2 text-sm font-semibold transition-all duration-200"
        :class="sidebarCollapsed ? 'justify-center' : ''">
        <i class="fas fa-folder-open text-lg w-6 text-center text-yellow-500"></i>
        <span x-show="!sidebarCollapsed">Dokumen Legalitas</span>
    </a>
</li>

@if (!auth()->user()->isAdminAbsensi())
    <!-- Master Data Dropdown -->
    <li>
        <button
            @click="if (sidebarCollapsed) { sidebarCollapsed = false; openMenu = 'master'; } else { openMenu = (openMenu === 'master' ? '' : 'master'); }"
            class="w-full flex items-center gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ $currentRouteGroup === 'master' ? 'text-white bg-gray-800' : 'text-gray-400' }} hover:text-white hover:bg-gray-800 transition-all duration-200"
            :class="sidebarCollapsed ? 'justify-center' : ''">
            <i class="fas fa-database text-lg w-6 text-center"></i>
            <span x-show="!sidebarCollapsed">Master Data</span>
            <i :class="openMenu === 'master' ? 'fa-angle-down' : 'fa-angle-right'"
                class="fas ml-auto transition-transform duration-200" x-show="!sidebarCollapsed"></i>
        </button>
        <ul x-show="openMenu === 'master' && !sidebarCollapsed" x-collapse class="mt-1 px-2 space-y-1">
            <li>
                <a href="{{ route('categories.index') }}"
                    class="{{ request()->routeIs('categories.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} flex items-center gap-3 rounded-md py-2 pl-9 pr-2 text-sm font-semibold">
                    <i class="fas fa-tags text-sm w-4"></i>
                    Kategori Produk
                </a>
            </li>
            <li>
                <a href="{{ route('customers.index') }}"
                    class="{{ request()->routeIs('customers.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} flex items-center gap-3 rounded-md py-2 pl-9 pr-2 text-sm font-semibold">
                    <i class="fas fa-users text-sm w-4"></i>
                    Pelanggan
                </a>
            </li>
            <li>
                <a href="{{ route('suppliers.index') }}"
                    class="{{ request()->routeIs('suppliers.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} flex items-center gap-3 rounded-md py-2 pl-9 pr-2 text-sm font-semibold">
                    <i class="fas fa-truck text-sm w-4"></i>
                    Supplier
                </a>
            </li>
            <li>
                <a href="{{ route('supplier-notas.index') }}"
                    class="{{ request()->routeIs('supplier-notas.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} flex items-center gap-3 rounded-md py-2 pl-9 pr-2 text-sm font-semibold">
                    <i class="fas fa-file-invoice-dollar text-sm w-4"></i>
                    Nota Supplier
                </a>
            </li>
            <li>
                <a href="{{ route('products.index') }}"
                    class="{{ request()->routeIs('products.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} flex items-center gap-3 rounded-md py-2 pl-9 pr-2 text-sm font-semibold">
                    <i class="fas fa-boxes text-sm w-4"></i>
                    Produk
                </a>
            </li>
        </ul>
    </li>
@endif

@if (!auth()->user()->isAdminAbsensi())
    <!-- Operasional Dropdown -->
    <li>
        <button
            @click="if (sidebarCollapsed) { sidebarCollapsed = false; openMenu = 'ops'; } else { openMenu = (openMenu === 'ops' ? '' : 'ops'); }"
            class="w-full flex items-center gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ $currentRouteGroup === 'ops' ? 'text-white bg-gray-800' : 'text-gray-400' }} hover:text-white hover:bg-gray-800 transition-all duration-200"
            :class="sidebarCollapsed ? 'justify-center' : ''">
            <i class="fas fa-cash-register text-lg w-6 text-center"></i>
            <span x-show="!sidebarCollapsed">Operasional</span>
            <i :class="openMenu === 'ops' ? 'fa-angle-down' : 'fa-angle-right'"
                class="fas ml-auto transition-transform duration-200" x-show="!sidebarCollapsed"></i>
        </button>
        <ul x-show="openMenu === 'ops' && !sidebarCollapsed" x-collapse class="mt-1 px-2 space-y-1">
            <li>
                <a href="{{ route('rice-deliveries.index') }}"
                    class="{{ request()->routeIs('rice-deliveries.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} flex items-center gap-3 rounded-md py-2 pl-9 pr-2 text-sm font-semibold">
                    <i class="fas fa-shipping-fast text-sm w-4"></i>
                    Nota Pengiriman Beras
                </a>
            </li>
            <li>
                <a href="{{ route('delivery-orders.index') }}"
                    class="{{ request()->routeIs('delivery-orders.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} flex items-center gap-3 rounded-md py-2 pl-9 pr-2 text-sm font-semibold">
                    <i class="fas fa-truck text-sm w-4"></i>
                    Surat Jalan
                </a>
            </li>
            <li>
                <a href="{{ route('invoices.index') }}"
                    class="{{ request()->routeIs('invoices.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} flex items-center gap-3 rounded-md py-2 pl-9 pr-2 text-sm font-semibold">
                    <i class="fas fa-file-invoice text-sm w-4"></i>
                    Invoice Generator
                </a>
            </li>
            <li>
                <a href="{{ route('vehicle-rentals.index') }}"
                    class="{{ request()->routeIs('vehicle-rentals.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} flex items-center gap-3 rounded-md py-2 pl-9 pr-2 text-sm font-semibold">
                    <i class="fas fa-car text-sm w-4"></i>
                    Invoice Sewa Kendaraan
                </a>
            </li>
            <li>
                <a href="{{ route('dedi-invoices.index') }}"
                    class="{{ request()->routeIs('dedi-invoices.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} flex items-center gap-3 rounded-md py-2 pl-9 pr-2 text-sm font-semibold">
                    <i class="fas fa-file-invoice text-sm w-4"></i>
                    Nota Faktur H Dedi
                </a>
            </li>
            <li>
                <a href="{{ route('kitchen-incentives.index') }}"
                    class="{{ request()->routeIs('kitchen-incentives.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} flex items-center gap-3 rounded-md py-2 pl-9 pr-2 text-sm font-semibold">
                    <i class="fas fa-utensils text-sm w-4"></i>
                    Invoice Insentif Dapur
                </a>
            </li>
            <li>
                <a href="{{ route('pos.index') }}"
                    class="{{ request()->routeIs('pos.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} flex items-center gap-3 rounded-md py-2 pl-9 pr-2 text-sm font-semibold">
                    <i class="fas fa-shopping-cart text-sm w-4"></i>
                    Buat Pesanan (POS)
                </a>
            </li>
            <li class="border-t border-gray-800/50 mt-1 pt-1">
                <a href="{{ route('chat.index') }}"
                    class="{{ request()->routeIs('chat.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} flex items-center gap-3 rounded-md py-2 pl-9 pr-2 text-sm font-bold text-emerald-400">
                    <i class="fas fa-comments text-sm w-4"></i>
                    Global Chat
                    <span
                        class="ml-auto inline-flex items-center rounded-md bg-emerald-400/10 px-1.5 py-0.5 text-[10px] font-medium text-emerald-400 ring-1 ring-inset ring-emerald-400/20">NEW</span>
                </a>
            </li>
            @if (auth()->user()->name === 'Rifal Kurniawan')
                <li>
                    <a href="{{ route('radio.index') }}"
                        class="{{ request()->routeIs('radio.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} flex items-center gap-3 rounded-md py-2 pl-9 pr-2 text-sm font-bold text-indigo-400">
                        <i class="fas fa-radio text-sm w-4"></i>
                        Live Radio
                        <span
                            class="ml-auto inline-flex items-center rounded-md bg-red-400/10 px-1.5 py-0.5 text-[10px] font-medium text-red-400 ring-1 ring-inset ring-red-400/20 animate-pulse">LIVE</span>
                    </a>
                </li>
            @endif
        </ul>
    </li>
@endif

@if (!auth()->user()->isAdminAbsensi())
    <!-- Inventori Dropdown -->
    <li>
        <button
            @click="if (sidebarCollapsed) { sidebarCollapsed = false; openMenu = 'inv'; } else { openMenu = (openMenu === 'inv' ? '' : 'inv'); }"
            class="w-full flex items-center gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ $currentRouteGroup === 'inv' ? 'text-white bg-gray-800' : 'text-gray-400' }} hover:text-white hover:bg-gray-800 transition-all duration-200"
            :class="sidebarCollapsed ? 'justify-center' : ''">
            <i class="fas fa-warehouse text-lg w-6 text-center"></i>
            <span x-show="!sidebarCollapsed">Gudang & Stok</span>
            <i :class="openMenu === 'inv' ? 'fa-angle-down' : 'fa-angle-right'"
                class="fas ml-auto transition-transform duration-200" x-show="!sidebarCollapsed"></i>
        </button>
        <ul x-show="openMenu === 'inv' && !sidebarCollapsed" x-collapse class="mt-1 px-2 space-y-1">
            <li>
                <a href="{{ route('inventory.index') }}"
                    class="{{ request()->routeIs('inventory.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} flex items-center gap-3 rounded-md py-2 pl-9 pr-2 text-sm font-semibold">
                    <i class="fas fa-cubes text-sm w-4"></i>
                    Gudang Utama
                </a>
            </li>
        </ul>
    </li>
@endif

@if (!auth()->user()->isAdminAbsensi())
    <!-- Analisa Keuangan Dropdown -->
    <li>
        <button
            @click="if (sidebarCollapsed) { sidebarCollapsed = false; openMenu = 'fin'; } else { openMenu = (openMenu === 'fin' ? '' : 'fin'); }"
            class="w-full flex items-center gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ $currentRouteGroup === 'fin' ? 'text-white bg-gray-800' : 'text-gray-400' }} hover:text-white hover:bg-gray-800 transition-all duration-200"
            :class="sidebarCollapsed ? 'justify-center' : ''">
            <i class="fas fa-chart-pie text-lg w-6 text-center"></i>
            <span x-show="!sidebarCollapsed">Analisa Keuangan</span>
            <i :class="openMenu === 'fin' ? 'fa-angle-down' : 'fa-angle-right'"
                class="fas ml-auto transition-transform duration-200" x-show="!sidebarCollapsed"></i>
        </button>
        <ul x-show="openMenu === 'fin' && !sidebarCollapsed" x-collapse class="mt-1 px-2 space-y-1">
            <li>
                <a href="{{ route('finance.summary') }}"
                    class="{{ request()->routeIs('finance.summary') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} flex items-center gap-3 rounded-md py-2 pl-9 pr-2 text-sm font-semibold">
                    <i class="fas fa-chart-line text-sm w-4"></i>
                    Ringkasan Keuangan
                </a>
            </li>
            <li>
                <a href="{{ route('expenses.index') }}"
                    class="{{ request()->routeIs('expenses.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} flex items-center gap-3 rounded-md py-2 pl-9 pr-2 text-sm font-semibold">
                    <i class="fas fa-money-bill-wave text-sm w-4"></i>
                    Pengeluaran
                </a>
            </li>
            <li>
                <a href="{{ route('profit.index') }}"
                    class="{{ request()->routeIs('profit.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} flex items-center gap-3 rounded-md py-2 pl-9 pr-2 text-sm font-semibold">
                    <i class="fas fa-sack-dollar text-sm w-4"></i>
                    Detail Laba Rugi
                </a>
            </li>
            <li>
                <a href="{{ route('rice-order-recap.index') }}"
                    class="{{ request()->routeIs('rice-order-recap.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} flex items-center gap-3 rounded-md py-2 pl-9 pr-2 text-sm font-semibold">
                    <i class="fas fa-list-check text-sm w-4"></i>
                    Rekap Pesanan Beras
                </a>
            </li>
        </ul>
    </li>
@endif

@if (auth()->user() && (auth()->user()->isSuperAdmin() || auth()->user()->isKetua() || auth()->user()->isAdminAbsensi()))
    <!-- Kepegawaian Dropdown -->
    <li>
        <button
            @click="if (sidebarCollapsed) { sidebarCollapsed = false; openMenu = 'hrd'; } else { openMenu = (openMenu === 'hrd' ? '' : 'hrd'); }"
            class="w-full flex items-center gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ $currentRouteGroup === 'hrd' ? 'text-white bg-gray-800' : 'text-gray-400' }} hover:text-white hover:bg-gray-800 transition-all duration-200"
            :class="sidebarCollapsed ? 'justify-center' : ''">
            <i class="fas fa-user-tie text-lg w-6 text-center"></i>
            <span x-show="!sidebarCollapsed">Kepegawaian</span>
            <i :class="openMenu === 'hrd' ? 'fa-angle-down' : 'fa-angle-right'"
                class="fas ml-auto transition-transform duration-200" x-show="!sidebarCollapsed"></i>
        </button>
        <ul x-show="openMenu === 'hrd' && !sidebarCollapsed" x-collapse class="mt-1 px-2 space-y-1">
            @if (auth()->user()->isSuperAdmin() || auth()->user()->isKetua())
                <li>
                    <a href="{{ route('users.index') }}"
                        class="{{ request()->routeIs('users.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} flex items-center gap-3 rounded-md py-2 pl-9 pr-2 text-sm font-semibold">
                        <i class="fas fa-users text-sm w-4"></i>
                        Pegawai
                    </a>
                </li>
                <li>
                    <a href="{{ route('salaries.index') }}"
                        class="{{ request()->routeIs('salaries.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} flex items-center gap-3 rounded-md py-2 pl-9 pr-2 text-sm font-semibold">
                        <i class="fas fa-wallet text-sm w-4"></i>
                        Gaji Pegawai
                    </a>
                </li>
            @endif
            <li>
                <a href="{{ route('attendance.report') }}"
                    class="{{ request()->routeIs('attendance.report') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} flex items-center gap-3 rounded-md py-2 pl-9 pr-2 text-sm font-semibold">
                    <i class="fas fa-clipboard-list text-sm w-4"></i>
                    Laporan Absensi
                </a>
            </li>
            <li>
                <a href="{{ route('attendance.bulk') }}"
                    class="{{ request()->routeIs('attendance.bulk') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} flex items-center gap-3 rounded-md py-2 pl-9 pr-2 text-sm font-semibold">
                    <i class="fas fa-users-cog text-sm w-4"></i>
                    Input Absensi Bulk
                </a>
            </li>
            <li>
                <a href="{{ route('attendance.settings') }}"
                    class="{{ request()->routeIs('attendance.settings') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} flex items-center gap-3 rounded-md py-2 pl-9 pr-2 text-sm font-semibold">
                    <i class="fas fa-cog text-sm w-4"></i>
                    Pengaturan Absensi
                </a>
            </li>
            <li>
                <a href="{{ route('attendance.public') }}" target="_blank"
                    class="text-emerald-400 hover:text-emerald-300 hover:bg-gray-800 flex items-center gap-3 rounded-md py-2 pl-9 pr-2 text-sm font-bold">
                    <i class="fas fa-qrcode text-sm w-4"></i>
                    Buka Scan Absensi ↗
                </a>
            </li>
        </ul>
    </li>
@endif
@if (auth()->user()->name === 'Rifal Kurniawan')
    <li class="mt-4">
        <a href="{{ route('activity-logs.index') }}"
            class="{{ request()->routeIs('activity-logs.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} flex items-center gap-3 rounded-md p-2 text-sm font-semibold transition-all duration-200"
            :class="sidebarCollapsed ? 'justify-center' : ''">
            <i class="fas fa-history text-lg w-6 text-center text-amber-500"></i>
            <span x-show="!sidebarCollapsed">Log Aktivitas</span>
        </a>
    </li>

    <li>
        <a href="{{ route('monitor.index') }}"
            class="{{ request()->routeIs('monitor.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} flex items-center gap-3 rounded-md p-2 text-sm font-semibold transition-all duration-200"
            :class="sidebarCollapsed ? 'justify-center' : ''">
            <i class="fas fa-desktop text-lg w-6 text-center text-green-500"></i>
            <span x-show="!sidebarCollapsed">Monitor Login</span>
            <span x-show="!sidebarCollapsed"
                class="ml-auto inline-flex items-center rounded-md bg-green-400/10 px-1.5 py-0.5 text-[10px] font-medium text-green-400 ring-1 ring-inset ring-green-400/20 animate-pulse">LIVE</span>
        </a>
    </li>

    <li>
        <a href="{{ route('backup.index') }}"
            class="{{ request()->routeIs('backup.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} flex items-center gap-3 rounded-md p-2 text-sm font-semibold transition-all duration-200"
            :class="sidebarCollapsed ? 'justify-center' : ''">
            <i class="fab fa-google-drive text-lg w-6 text-center text-blue-500"></i>
            <span x-show="!sidebarCollapsed">Cloud Backup</span>
        </a>
    </li>
@endif
