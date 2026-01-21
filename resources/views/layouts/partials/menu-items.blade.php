<!-- Master Data Dropdown -->
<li>
    <button @click="openMenu = (openMenu === 'master' ? '' : 'master')"
        class="w-full flex items-center gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-gray-400 hover:text-white hover:bg-gray-800 transition-all duration-200">
        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.128 16.556 17.975 12 17.975s-8.25-1.847-8.25-4.125v-3.75m16.5 0v3.75" />
        </svg>
        <span>Master Data</span>
        <svg :class="openMenu === 'master' ? 'rotate-90' : ''"
            class="ml-auto h-4 w-4 shrink-0 transition-transform duration-200" fill="none" viewBox="0 0 24 24"
            stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
    </button>
    <ul x-show="openMenu === 'master'" x-collapse class="mt-1 px-2 space-y-1">
        <li>
            <a href="{{ route('categories.index') }}"
                class="{{ request()->routeIs('categories.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} block rounded-md py-2 pl-9 pr-2 text-sm font-semibold">
                Kategori Produk
            </a>
        </li>
        <li>
            <a href="{{ route('customers.index') }}"
                class="{{ request()->routeIs('customers.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} block rounded-md py-2 pl-9 pr-2 text-sm font-semibold">
                Pelanggan
            </a>
        </li>
        <li>
            <a href="{{ route('suppliers.index') }}"
                class="{{ request()->routeIs('suppliers.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} block rounded-md py-2 pl-9 pr-2 text-sm font-semibold">
                Supplier
            </a>
        </li>
        <li>
            <a href="{{ route('products.index') }}"
                class="{{ request()->routeIs('products.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} block rounded-md py-2 pl-9 pr-2 text-sm font-semibold">
                Produk
            </a>
        </li>
    </ul>
</li>

<!-- Operasional Dropdown -->
<li>
    <button @click="openMenu = (openMenu === 'ops' ? '' : 'ops')"
        class="w-full flex items-center gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-gray-400 hover:text-white hover:bg-gray-800 transition-all duration-200">
        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
        </svg>
        <span>Operasional</span>
        <svg :class="openMenu === 'ops' ? 'rotate-90' : ''"
            class="ml-auto h-4 w-4 shrink-0 transition-transform duration-200" fill="none" viewBox="0 0 24 24"
            stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
    </button>
    <ul x-show="openMenu === 'ops'" x-collapse class="mt-1 px-2 space-y-1">
        <li>
            <a href="{{ route('invoices.index') }}"
                class="{{ request()->routeIs('invoices.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} block rounded-md py-2 pl-9 pr-2 text-sm font-semibold">
                Invoice Generator
            </a>
        </li>
        <li>
            <a href="{{ route('pos.index') }}"
                class="{{ request()->routeIs('pos.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} block rounded-md py-2 pl-9 pr-2 text-sm font-semibold">
                Buat Pesanan (POS)
            </a>
        </li>
    </ul>
</li>

<!-- Inventori Dropdown -->
<li>
    <button @click="openMenu = (openMenu === 'inv' ? '' : 'inv')"
        class="w-full flex items-center gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-gray-400 hover:text-white hover:bg-gray-800 transition-all duration-200">
        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
        </svg>
        <span>Gudang & Stok</span>
        <svg :class="openMenu === 'inv' ? 'rotate-90' : ''"
            class="ml-auto h-4 w-4 shrink-0 transition-transform duration-200" fill="none" viewBox="0 0 24 24"
            stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
    </button>
    <ul x-show="openMenu === 'inv'" x-collapse class="mt-1 px-2 space-y-1">
        <li>
            <a href="{{ route('inventory.index') }}"
                class="{{ request()->routeIs('inventory.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} block rounded-md py-2 pl-9 pr-2 text-sm font-semibold">
                Gudang Utama
            </a>
        </li>
    </ul>
</li>

<!-- Analisis/Laporan Dropdown -->
<li>
    <button @click="openMenu = (openMenu === 'fin' ? '' : 'fin')"
        class="w-full flex items-center gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-gray-400 hover:text-white hover:bg-gray-800 transition-all duration-200">
        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" />
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" />
        </svg>
        <span>Analisa Keuangan</span>
        <svg :class="openMenu === 'fin' ? 'rotate-90' : ''"
            class="ml-auto h-4 w-4 shrink-0 transition-transform duration-200" fill="none" viewBox="0 0 24 24"
            stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
    </button>
    <ul x-show="openMenu === 'fin'" x-collapse class="mt-1 px-2 space-y-1">
        <li>
            <a href="{{ route('finance.summary') }}"
                class="{{ request()->routeIs('finance.summary') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} block rounded-md py-2 pl-9 pr-2 text-sm font-semibold">
                Ringkasan Keuangan
            </a>
        </li>
        <li>
            <a href="{{ route('expenses.index') }}"
                class="{{ request()->routeIs('expenses.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} block rounded-md py-2 pl-9 pr-2 text-sm font-semibold">
                Pengeluaran
            </a>
        </li>
        <li>
            <a href="{{ route('profit.index') }}"
                class="{{ request()->routeIs('profit.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} block rounded-md py-2 pl-9 pr-2 text-sm font-semibold">
                Detail Laba Rugi
            </a>
        </li>
    </ul>
</li>

@if (auth()->user() && (auth()->user()->isSuperAdmin() || auth()->user()->isKetua()))
    <!-- Kepegawaian Dropdown -->
    <li>
        <button @click="openMenu = (openMenu === 'hrd' ? '' : 'hrd')"
            class="w-full flex items-center gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-gray-400 hover:text-white hover:bg-gray-800 transition-all duration-200">
            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Kepegawaian</span>
            <svg :class="openMenu === 'hrd' ? 'rotate-90' : ''"
                class="ml-auto h-4 w-4 shrink-0 transition-transform duration-200" fill="none" viewBox="0 0 24 24"
                stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
            </svg>
        </button>
        <ul x-show="openMenu === 'hrd'" x-collapse class="mt-1 px-2 space-y-1">
            <li>
                <a href="{{ route('salaries.index') }}"
                    class="{{ request()->routeIs('salaries.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} block rounded-md py-2 pl-9 pr-2 text-sm font-semibold">
                    Gaji Pegawai
                </a>
            </li>
            <li>
                <a href="{{ route('attendance.report') }}"
                    class="{{ request()->routeIs('attendance.report') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} block rounded-md py-2 pl-9 pr-2 text-sm font-semibold">
                    Laporan Absensi
                </a>
            </li>
            <li>
                <a href="{{ route('attendance.settings') }}"
                    class="{{ request()->routeIs('attendance.settings') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} block rounded-md py-2 pl-9 pr-2 text-sm font-semibold">
                    Pengaturan Absensi
                </a>
            </li>
            <li>
                <a href="{{ route('attendance.public') }}" target="_blank"
                    class="text-emerald-400 hover:text-emerald-300 hover:bg-gray-800 block rounded-md py-2 pl-9 pr-2 text-sm font-bold">
                    Buka Scan Absensi ↗
                </a>
            </li>
        </ul>
    </li>
@endif

@if (auth()->user() && auth()->user()->isSuperAdmin())
    <!-- Pengaturan Dropdown -->
    <li>
        <button @click="openMenu = (openMenu === 'usr' ? '' : 'usr')"
            class="w-full flex items-center gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-gray-400 hover:text-white hover:bg-gray-800 transition-all duration-200">
            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
            </svg>
            <span>Pengaturan</span>
            <svg :class="openMenu === 'usr' ? 'rotate-90' : ''"
                class="ml-auto h-4 w-4 shrink-0 transition-transform duration-200" fill="none" viewBox="0 0 24 24"
                stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
            </svg>
        </button>
        <ul x-show="openMenu === 'usr'" x-collapse class="mt-1 px-2 space-y-1">
            <li>
                <a href="{{ route('users.index') }}"
                    class="{{ request()->routeIs('users.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} block rounded-md py-2 pl-9 pr-2 text-sm font-semibold">
                    Manajemen User
                </a>
            </li>
        </ul>
    </li>
@endif
