<!-- Mobile More Menu Modal -->
<div x-show="mobileMenuOpen" class="fixed inset-0 z-50 lg:hidden" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-full" x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-full">
    <div @click="mobileMenuOpen = false" class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm"></div>

    <div class="absolute bottom-0 left-0 right-0 bg-white rounded-t-[2.5rem] p-8 shadow-2xl">
        <div class="w-12 h-1.5 bg-gray-200 rounded-full mx-auto mb-8"></div>

        <h3 class="text-xl font-black text-gray-900 mb-6 text-center">Menu Lainnya</h3>

        <div class="grid grid-cols-2 gap-4 pb-8">
            <a href="{{ route('profit.index') }}"
                class="flex flex-col items-center gap-3 p-5 rounded-3xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition-all border border-emerald-100">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z" />
                </svg>
                <span class="text-sm font-black">Laba Rugi</span>
            </a>

            <a href="{{ route('categories.index') }}"
                class="flex flex-col items-center gap-3 p-5 rounded-3xl bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition-all border border-indigo-100">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581a1.125 1.125 0 001.591 0l7.581-7.581a1.125 1.125 0 000-1.591l-9.581-9.581a1.125 1.125 0 00-1.591 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                </svg>
                <span class="text-sm font-black">Kategori</span>
            </a>

            @if (auth()->user() && auth()->user()->isSuperAdmin())
                <a href="{{ route('users.index') }}"
                    class="flex flex-col items-center gap-3 p-5 rounded-3xl bg-orange-50 text-orange-700 hover:bg-orange-100 transition-all border border-orange-100">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                    <span class="text-sm font-black">Users</span>
                </a>
            @endif

            @if (auth()->user() && (auth()->user()->isSuperAdmin() || auth()->user()->isKetua()))
                <a href="{{ route('salaries.index') }}"
                    class="flex flex-col items-center gap-3 p-5 rounded-3xl bg-pink-50 text-pink-700 hover:bg-pink-100 transition-all border border-pink-100">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm font-black">Gaji</span>
                </a>

                <a href="{{ route('attendance.report') }}"
                    class="flex flex-col items-center gap-3 p-5 rounded-3xl bg-blue-50 text-blue-700 hover:bg-blue-100 transition-all border border-blue-100">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    <span class="text-sm font-black">Absensi</span>
                </a>
            @endif
        </div>

        <div class="border-t border-gray-100 pt-6">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="w-full flex items-center justify-center gap-3 py-4 bg-red-50 text-red-700 rounded-2xl font-black hover:bg-red-100 transition-all">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                    </svg>
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
