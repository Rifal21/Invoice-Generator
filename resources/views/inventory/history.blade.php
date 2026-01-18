@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Riwayat Mutasi</h1>
                <p class="text-sm text-gray-500 font-medium">Lacak alur stok produk secara mendetail.</p>
            </div>
            <a href="{{ route('inventory.index') }}"
                class="inline-flex items-center px-6 py-3 bg-white border border-gray-100 rounded-2xl text-sm font-bold text-gray-700 shadow-sm hover:bg-gray-50 transition-all">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
                Kembali
            </a>
        </div>

        <!-- Product Card -->
        <div
            class="bg-indigo-600 rounded-[2rem] p-8 mb-8 shadow-xl shadow-indigo-100 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-6">
                <div class="p-4 bg-white/20 rounded-3xl text-white">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <div>
                    <span
                        class="text-indigo-100 text-xs font-black uppercase tracking-widest">{{ $product->category->name }}</span>
                    <h2 class="text-3xl font-black text-white uppercase tracking-tight">{{ $product->name }}</h2>
                </div>
            </div>
            <div class="bg-white/10 px-8 py-4 rounded-3xl backdrop-blur-md">
                <p class="text-indigo-100 text-xs font-bold text-center mb-1">Stok Saat Ini</p>
                <p class="text-3xl font-black text-white text-center">{{ $product->stock }} <span
                        class="text-sm font-medium opacity-70">{{ $product->unit }}</span></p>
            </div>
        </div>

        <!-- History Timeline -->
        <div class="relative">
            <div class="absolute left-8 top-0 bottom-0 w-0.5 bg-gray-100"></div>

            <div class="space-y-8 relative">
                @forelse($histories as $history)
                    <div class="flex items-start gap-8 group">
                        <div
                            class="z-10 flex-shrink-0 w-16 h-16 rounded-3xl flex items-center justify-center transition-all group-hover:scale-110 {{ $history->type == 'in' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                            @if ($history->type == 'in')
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                            @else
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4">
                                    </path>
                                </svg>
                            @endif
                        </div>

                        <div
                            class="flex-1 bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 group-hover:shadow-md transition-all">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
                                <div>
                                    <span
                                        class="text-xs font-black text-gray-400 tracking-wider uppercase">{{ $history->created_at->format('d M Y, H:i') }}</span>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span
                                            class="font-black text-xl {{ $history->type == 'in' ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $history->type == 'in' ? '+' : '-' }}{{ (float) $history->quantity }}
                                        </span>
                                        <span class="text-sm font-bold text-gray-400 lowercase">{{ $product->unit }}</span>
                                    </div>
                                </div>
                                <div class="flex flex-col items-start md:items-end">
                                    <span
                                        class="text-[10px] font-black uppercase text-gray-400 tracking-widest mb-1">Referensi</span>
                                    <span
                                        class="inline-flex items-center px-3 py-1 bg-gray-50 text-gray-600 rounded-xl text-xs font-black ring-1 ring-inset ring-gray-100">
                                        {{ $history->reference ?? '-' }}
                                    </span>
                                </div>
                            </div>
                            @if ($history->description)
                                <div class="pt-4 border-t border-gray-50">
                                    <p class="text-sm text-gray-500 font-medium italic">"{{ $history->description }}"</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="bg-white p-12 rounded-[2rem] text-center shadow-sm border border-gray-100">
                        <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="font-bold text-gray-400">Belum ada mutasi untuk barang ini.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="mt-8">
            {{ $histories->links() }}
        </div>
    </div>
@endsection
