@extends('layouts.app')

@section('title', 'Billing & Saldo')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">BILLING & SALDO</h1>
            <p class="text-gray-500 font-medium">Kelola saldo aplikasi dan pantau riwayat penggunaan sistem</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
            <!-- Balance Card -->
            <div class="lg:col-span-1">
                <div
                    class="bg-gradient-to-br from-emerald-600 to-teal-700 rounded-3xl p-8 text-white shadow-2xl relative overflow-hidden">
                    <div class="relative z-10">
                        <div class="text-emerald-100 text-sm font-bold uppercase tracking-widest mb-2">Saldo Aplikasi
                            (Global)</div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-sm font-bold text-emerald-200">Rp</span>
                            <span class="text-5xl font-black" id="currentBalanceDisplay" data-value="{{ $appBalance }}"
                                data-rate="{{ $ratePerMinute }}">{{ number_format($appBalance, 0, ',', '.') }}</span>
                        </div>
                        <div class="mt-8 pt-8 border-t border-white/10 flex items-center justify-between">
                            <div class="text-xs text-emerald-200 font-medium">System Status</div>
                            <div
                                class="px-3 py-1 bg-white/20 rounded-full text-[10px] font-black uppercase tracking-wider backdrop-blur-md">
                                ONLINE</div>
                        </div>
                    </div>
                </div>

                @if (Auth::user()->isBillingManager())
                    <div class="mt-6">
                        <a href="{{ route('billing.manage') }}"
                            class="w-full flex items-center justify-center gap-2 bg-white border-2 border-emerald-600 text-emerald-600 font-black py-4 rounded-2xl hover:bg-emerald-50 transition-all shadow-md">
                            <i class="fas fa-user-shield"></i> PENGATURAN BILLING
                        </a>
                    </div>
                @endif
            </div>

            <!-- Top Up & Methods -->
            <div class="lg:col-span-2">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 h-full">
                    <!-- Top Up Instructions -->
                    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-3 mb-4">
                                <div
                                    class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600">
                                    <i class="fas fa-plus"></i>
                                </div>
                                <h3 class="font-black text-gray-900 text-sm uppercase">Cara Top Up</h3>
                            </div>
                            <ul class="space-y-3 text-xs text-gray-600 font-medium">
                                <li class="flex items-start gap-2">
                                    <span
                                        class="w-5 h-5 bg-gray-100 rounded-full flex items-center justify-center text-[10px] shrink-0 text-gray-400">1</span>
                                    <div>Transfer nominal sesuai keinginan ke salah satu rekening di samping.</div>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span
                                        class="w-5 h-5 bg-gray-100 rounded-full flex items-center justify-center text-[10px] shrink-0 text-gray-400">2</span>
                                    <div>Kirim bukti transfer ke WhatsApp <strong>Admin Rifal</strong>.</div>
                                </li>
                                <li class="flex items-start gap-2">
                                    <span
                                        class="w-5 h-5 bg-gray-100 rounded-full flex items-center justify-center text-[10px] shrink-0 text-gray-400">3</span>
                                    <div>Saldo akan diupdate ke sistem oleh Admin.</div>
                                </li>
                            </ul>
                        </div>
                        <div class="mt-4 p-3 bg-amber-50 rounded-2xl border border-amber-100">
                            <p class="text-[10px] text-amber-700 leading-tight">
                                <i class="fas fa-info-circle mr-1"></i> Biaya: <strong>Rp 10.000 (Admin)</strong> +
                                <strong>PPN 11%</strong> per transaksi.
                            </p>
                        </div>
                    </div>

                    <!-- Payment Methods -->
                    <div class="bg-indigo-900 rounded-3xl p-6 text-white shadow-xl">
                        <h3 class="font-black text-xs uppercase tracking-widest text-indigo-300 mb-4">Metode Pembayaran</h3>
                        <div class="space-y-4">
                            <!-- BCA -->
                            <div class="flex items-center justify-between p-3 bg-white/10 rounded-2xl backdrop-blur-sm border border-white/5 hover:bg-white/20 transition-all cursor-pointer group"
                                onclick="copyToClipboard('0540819231', 'BCA')">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-6 bg-white rounded flex items-center justify-center overflow-hidden grayscale group-hover:grayscale-0 transition-all p-1">
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5c/Bank_Central_Asia.svg/1200px-Bank_Central_Asia.svg.png"
                                            class="h-2 object-contain" alt="BCA">
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-bold text-white/50 leading-none mb-1">Bank BCA</span>
                                        <span class="text-sm font-black tracking-wider leading-none">0540-8192-31</span>
                                    </div>
                                </div>
                                <i class="fas fa-copy text-xs text-white/30 group-hover:text-white transition-colors"></i>
                            </div>
                            <!-- MANDIRI -->
                            <div class="flex items-center justify-between p-3 bg-white/10 rounded-2xl backdrop-blur-sm border border-white/5 hover:bg-white/20 transition-all cursor-pointer group"
                                onclick="copyToClipboard('1630004753333', 'Mandiri')">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-6 bg-white rounded flex items-center justify-center overflow-hidden grayscale group-hover:grayscale-0 transition-all p-1">
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/ad/Bank_Mandiri_logo_2016.svg/1200px-Bank_Mandiri_logo_2016.svg.png"
                                            class="h-1.5 object-contain" alt="Mandiri">
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-bold text-white/50 leading-none mb-1">Bank
                                            Mandiri</span>
                                        <span class="text-sm font-black tracking-wider leading-none">163-000-4753-333</span>
                                    </div>
                                </div>
                                <i class="fas fa-copy text-xs text-white/30 group-hover:text-white transition-colors"></i>
                            </div>
                        </div>
                        <p class="mt-4 text-[9px] text-white/40 italic text-center">Klik nomor rekening untuk menyalin</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaction History -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                <h3 class="font-black text-gray-900 tracking-tight uppercase text-sm">Riwayat Transaksi</h3>
                <div class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-black rounded-lg">TERBARU</div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-white border-b border-gray-50">
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">User</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Tanggal
                            </th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Deskripsi
                            </th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Jenis</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">
                                Jumlah</th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">
                                Saldo Akhir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($transactions as $trx)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-xs font-black text-emerald-600">{{ $trx->user->name ?? 'System' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-xs font-bold text-gray-900">{{ $trx->created_at->format('d M Y') }}
                                    </div>
                                    <div class="text-[10px] text-gray-400">{{ $trx->created_at->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-xs font-medium text-gray-700 leading-relaxed">{{ $trx->description }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($trx->type === 'topup')
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-1 bg-emerald-50 text-emerald-600 text-[9px] font-black rounded uppercase">
                                            <i class="fas fa-arrow-up"></i> Top Up
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-1 bg-rose-50 text-rose-600 text-[9px] font-black rounded uppercase">
                                            <i class="fas fa-arrow-down"></i> Usage
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span
                                        class="text-xs font-black {{ $trx->type === 'topup' ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ $trx->type === 'topup' ? '+' : '-' }}Rp
                                        {{ number_format($trx->amount, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-xs font-bold text-gray-900">Rp
                                        {{ number_format($trx->balance_after, 0, ',', '.') }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400 text-sm font-medium">Belum
                                    ada riwayat transaksi</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($transactions->hasPages())
                <div class="p-4 bg-gray-50/50 border-t border-gray-50">{{ $transactions->links() }}</div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Visual balance simulation
        (function() {
            const display = document.getElementById('currentBalanceDisplay');
            if (!display) return;
            let currentBalance = parseFloat(display.dataset.value);
            const ratePerMinute = parseFloat(display.dataset.rate);
            setInterval(() => {
                currentBalance -= ratePerMinute;
                if (currentBalance < 0) currentBalance = 0;
                display.textContent = Math.floor(currentBalance).toLocaleString('id-ID');
            }, 60000);
        })();

        function copyToClipboard(text, bank) {
            navigator.clipboard.writeText(text).then(() => {
                Toast.fire({
                    icon: 'success',
                    title: 'Tersalin!',
                    text: 'Nomor rekening ' + bank + ' berhasil disalin.'
                });
            });
        }
    </script>
@endpush
