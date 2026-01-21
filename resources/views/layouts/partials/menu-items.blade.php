<!-- Master Data Dropdown -->
<li>
    <button
        @click="if (sidebarCollapsed) { sidebarCollapsed = false; openMenu = 'master'; } else { openMenu = (openMenu === 'master' ? '' : 'master'); }"
        class="w-full flex items-center gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-gray-400 hover:text-white hover:bg-gray-800 transition-all duration-200"
        :class="sidebarCollapsed ? 'justify-center' : ''" x-data="{ tooltip: false }">
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
            <a href="{{ route('products.index') }}"
                class="{{ request()->routeIs('products.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} flex items-center gap-3 rounded-md py-2 pl-9 pr-2 text-sm font-semibold">
                <i class="fas fa-boxes text-sm w-4"></i>
                Produk
            </a>
        </li>
    </ul>
</li>

<!-- Operasional Dropdown -->
<li>
    <button
        @click="if (sidebarCollapsed) { sidebarCollapsed = false; openMenu = 'ops'; } else { openMenu = (openMenu === 'ops' ? '' : 'ops'); }"
        class="w-full flex items-center gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-gray-400 hover:text-white hover:bg-gray-800 transition-all duration-200"
        :class="sidebarCollapsed ? 'justify-center' : ''">
        <i class="fas fa-cash-register text-lg w-6 text-center"></i>
        <span x-show="!sidebarCollapsed">Operasional</span>
        <i :class="openMenu === 'ops' ? 'fa-angle-down' : 'fa-angle-right'"
            class="fas ml-auto transition-transform duration-200" x-show="!sidebarCollapsed"></i>
    </button>
    <ul x-show="openMenu === 'ops' && !sidebarCollapsed" x-collapse class="mt-1 px-2 space-y-1">
        <li>
            <a href="{{ route('invoices.index') }}"
                class="{{ request()->routeIs('invoices.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} flex items-center gap-3 rounded-md py-2 pl-9 pr-2 text-sm font-semibold">
                <i class="fas fa-file-invoice text-sm w-4"></i>
                Invoice Generator
            </a>
        </li>
        <li>
            <a href="{{ route('pos.index') }}"
                class="{{ request()->routeIs('pos.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} flex items-center gap-3 rounded-md py-2 pl-9 pr-2 text-sm font-semibold">
                <i class="fas fa-shopping-cart text-sm w-4"></i>
                Buat Pesanan (POS)
            </a>
        </li>
    </ul>
</li>

<!-- Inventori Dropdown -->
<li>
    <button
        @click="if (sidebarCollapsed) { sidebarCollapsed = false; openMenu = 'inv'; } else { openMenu = (openMenu === 'inv' ? '' : 'inv'); }"
        class="w-full flex items-center gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-gray-400 hover:text-white hover:bg-gray-800 transition-all duration-200"
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

<!-- Analisis/Laporan Dropdown -->
<li>
    <button
        @click="if (sidebarCollapsed) { sidebarCollapsed = false; openMenu = 'fin'; } else { openMenu = (openMenu === 'fin' ? '' : 'fin'); }"
        class="w-full flex items-center gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-gray-400 hover:text-white hover:bg-gray-800 transition-all duration-200"
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
    </ul>
</li>

@if (auth()->user() && (auth()->user()->isSuperAdmin() || auth()->user()->isKetua()))
    <!-- Kepegawaian Dropdown -->
    <li>
        <button
            @click="if (sidebarCollapsed) { sidebarCollapsed = false; openMenu = 'hrd'; } else { openMenu = (openMenu === 'hrd' ? '' : 'hrd'); }"
            class="w-full flex items-center gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-gray-400 hover:text-white hover:bg-gray-800 transition-all duration-200"
            :class="sidebarCollapsed ? 'justify-center' : ''">
            <i class="fas fa-user-tie text-lg w-6 text-center"></i>
            <span x-show="!sidebarCollapsed">Kepegawaian</span>
            <i :class="openMenu === 'hrd' ? 'fa-angle-down' : 'fa-angle-right'"
                class="fas ml-auto transition-transform duration-200" x-show="!sidebarCollapsed"></i>
        </button>
        <ul x-show="openMenu === 'hrd' && !sidebarCollapsed" x-collapse class="mt-1 px-2 space-y-1">
            <li>
                <a href="{{ route('salaries.index') }}"
                    class="{{ request()->routeIs('salaries.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} flex items-center gap-3 rounded-md py-2 pl-9 pr-2 text-sm font-semibold">
                    <i class="fas fa-wallet text-sm w-4"></i>
                    Gaji Pegawai
                </a>
            </li>
            <li>
                <a href="{{ route('attendance.report') }}"
                    class="{{ request()->routeIs('attendance.report') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} flex items-center gap-3 rounded-md py-2 pl-9 pr-2 text-sm font-semibold">
                    <i class="fas fa-clipboard-list text-sm w-4"></i>
                    Laporan Absensi
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

@if (auth()->user() && auth()->user()->isSuperAdmin())
    <!-- Pengaturan Dropdown -->
    <li>
        <button
            @click="if (sidebarCollapsed) { sidebarCollapsed = false; openMenu = 'usr'; } else { openMenu = (openMenu === 'usr' ? '' : 'usr'); }"
            class="w-full flex items-center gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-gray-400 hover:text-white hover:bg-gray-800 transition-all duration-200"
            :class="sidebarCollapsed ? 'justify-center' : ''">
            <i class="fas fa-users-cog text-lg w-6 text-center"></i>
            <span x-show="!sidebarCollapsed">Pengaturan</span>
            <i :class="openMenu === 'usr' ? 'fa-angle-down' : 'fa-angle-right'"
                class="fas ml-auto transition-transform duration-200" x-show="!sidebarCollapsed"></i>
        </button>
        <ul x-show="openMenu === 'usr' && !sidebarCollapsed" x-collapse class="mt-1 px-2 space-y-1">
            <li>
                <a href="{{ route('users.index') }}"
                    class="{{ request()->routeIs('users.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} flex items-center gap-3 rounded-md py-2 pl-9 pr-2 text-sm font-semibold">
                    <i class="fas fa-user-shield text-sm w-4"></i>
                    Manajemen User
                </a>
            </li>
        </ul>
    </li>
@endif
