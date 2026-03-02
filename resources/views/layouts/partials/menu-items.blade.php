@foreach ($sidebarItems as $item)
    @php
        $isActive = false;
        if ($item->route) {
            $isActive = request()->routeIs($item->route . '*');
        } else {
            foreach ($item->children as $child) {
                if (request()->routeIs($child->route . '*')) {
                    $isActive = true;
                    break;
                }
            }
        }

        // Permission Logic
        $canSee = true;
        $user = auth()->user();

        if (!$user) {
            $canSee = false;
        } else {
            if (in_array($item->label, ['Kepegawaian'])) {
                $canSee = $user->isSuperAdmin() || $user->isKetua() || $user->isAdminAbsensi();
            }

            if (in_array($item->label, ['Log Aktivitas', 'Monitor Login', 'Cloud Backup', 'Settings'])) {
                $canSee = $user->isSuperAdmin() || $user->isKetua() || $user->name === 'Rifal Kurniawan';
            }

            if ($item->label === 'Billing & Token') {
                $canSee = $user->isSuperAdmin() || $user->name === 'Rifal Kurniawan';
            }

            // If it's Master Data or Operasional or Inventori or Analisa Keuangan
    if (
        $user->isAdminAbsensi() &&
        in_array($item->label, ['Master Data', 'Operasional', 'Gudang & Stok', 'Analisa Keuangan'])
            ) {
                $canSee = false;
            }
        }
    @endphp

    @if ($canSee)
        @if ($item->route)
            <li>
                <a href="{{ route($item->route) }}"
                    class="{{ $isActive ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} flex items-center gap-3 rounded-md p-2 text-sm font-semibold transition-all duration-200"
                    :class="sidebarCollapsed ? 'justify-center' : ''">
                    <i class="{{ $item->icon }} text-lg w-6 text-center"></i>
                    <span x-show="!sidebarCollapsed">{{ $item->label }}</span>
                </a>
            </li>
        @else
            <li>
                <button
                    @click="if (sidebarCollapsed) { sidebarCollapsed = false; openMenu = '{{ $item->id }}'; } else { openMenu = (openMenu == '{{ $item->id }}' ? '' : '{{ $item->id }}'); }"
                    class="w-full flex items-center gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ $isActive ? 'text-white bg-gray-800' : 'text-gray-400' }} hover:text-white hover:bg-gray-800 transition-all duration-200"
                    :class="sidebarCollapsed ? 'justify-center' : ''">
                    <i class="{{ $item->icon }} text-lg w-6 text-center"></i>
                    <span x-show="!sidebarCollapsed">{{ $item->label }}</span>
                    <i :class="openMenu == '{{ $item->id }}' ? 'fa-angle-down' : 'fa-angle-right'"
                        class="fas ml-auto transition-transform duration-200" x-show="!sidebarCollapsed"></i>
                </button>
                <ul x-show="openMenu == '{{ $item->id }}' && !sidebarCollapsed" x-collapse
                    class="mt-1 px-2 space-y-1">
                    @foreach ($item->children as $child)
                        <li>
                            <a href="{{ route($child->route) }}"
                                class="{{ request()->routeIs($child->route . '*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} flex items-center gap-3 rounded-md py-2 pl-9 pr-2 text-sm font-semibold">
                                <i class="{{ $child->icon }} text-sm w-4"></i>
                                {{ $child->label }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
        @endif
    @endif
@endforeach
