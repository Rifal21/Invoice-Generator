@extends('layouts.app')

@section('title', 'Manage Billing')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">MANAGE BILLING</h1>
                <p class="text-gray-500 font-medium">Panel pengelolaan saldo rupiah untuk seluruh sistem aplikasi</p>
            </div>
            <a href="{{ route('billing.index') }}"
                class="bg-white border border-gray-200 text-gray-700 font-bold px-4 py-2 rounded-xl text-sm hover:bg-gray-50 transition-all flex items-center gap-2 self-start md:self-center shadow-sm">
                <i class="fas fa-arrow-left"></i> KEMBALI
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
            <!-- Configuration Column -->
            <div class="lg:col-span-1 space-y-8">
                <!-- Billing Toggle -->
                <div
                    class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 overflow-hidden relative {{ $billingStatus === 'active' ? 'border-l-4 border-l-emerald-500' : 'border-l-4 border-l-rose-500' }}">
                    <h3 class="text-lg font-black text-gray-900 mb-6 uppercase tracking-tight flex items-center gap-2">
                        <i
                            class="fas fa-power-off {{ $billingStatus === 'active' ? 'text-emerald-500' : 'text-rose-500' }}"></i>
                        Status Billing
                    </h3>

                    <form action="{{ route('billing.updateStatus') }}" method="POST">
                        @csrf
                        <div class="flex items-center gap-4 mb-6">
                            <input type="hidden" name="status"
                                value="{{ $billingStatus === 'active' ? 'disabled' : 'active' }}">
                            <button type="submit"
                                class="flex items-center gap-3 px-6 py-4 rounded-2xl font-black text-xs uppercase tracking-widest transition-all w-full justify-center {{ $billingStatus === 'active' ? 'bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-100' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100 border border-emerald-100' }}">
                                <i
                                    class="fas {{ $billingStatus === 'active' ? 'fa-pause-circle' : 'fa-play-circle' }} text-lg"></i>
                                {{ $billingStatus === 'active' ? 'Nonaktifkan Billing' : 'Aktifkan Billing' }}
                            </button>
                        </div>
                        <div
                            class="p-3 {{ $billingStatus === 'active' ? 'bg-emerald-50' : 'bg-rose-50' }} rounded-xl border {{ $billingStatus === 'active' ? 'border-emerald-100' : 'border-rose-100' }}">
                            <p
                                class="text-[10px] leading-tight font-medium {{ $billingStatus === 'active' ? 'text-emerald-700' : 'text-rose-700' }}">
                                @if ($billingStatus === 'active')
                                    <i class="fas fa-check-circle mr-1"></i> <strong>AKTIF:</strong> Sistem saat ini
                                    memotong saldo Rp{{ number_format($ratePerMinute) }}/menit secara otomatis.
                                @else
                                    <i class="fas fa-exclamation-circle mr-1"></i> <strong>NONAKTIF:</strong> Sistem GRATIS.
                                    Saldo tidak akan berkurang meskipun aplikasi digunakan.
                                @endif
                            </p>
                        </div>
                    </form>
                </div>

                <!-- Topup Form -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 overflow-hidden relative">
                    <h3 class="text-lg font-black text-gray-900 mb-6 uppercase tracking-tight flex items-center gap-2">
                        <i class="fas fa-plus-circle text-indigo-600"></i> Topup Saldo
                    </h3>

                    <form action="{{ route('billing.topup') }}" method="POST" class="space-y-6" id="topupForm">
                        @csrf
                        <div class="p-4 bg-indigo-50 rounded-2xl border border-indigo-100 mb-6">
                            <p class="text-[10px] text-indigo-600 font-black uppercase tracking-widest mb-1">Target</p>
                            <p class="text-sm font-bold text-gray-900">Seluruh Sistem Aplikasi</p>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nominal
                                Topup</label>
                            <div class="relative">
                                <span
                                    class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-gray-400">Rp</span>
                                <input type="number" name="amount" id="topupAmount" required placeholder="50000"
                                    class="w-full bg-gray-50 border border-gray-100 rounded-xl px-12 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none font-bold">
                            </div>
                        </div>

                        <div class="space-y-2 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                            <div class="flex justify-between text-[11px] font-medium text-gray-500">
                                <span>Biaya Admin</span>
                                <span>Rp 10.000</span>
                            </div>
                            <div class="flex justify-between text-[11px] font-medium text-gray-500">
                                <span>PPN (11%)</span>
                                <span id="ppnValue">Rp 0</span>
                            </div>
                            <div
                                class="pt-2 border-t border-gray-200 flex justify-between text-xs font-black text-gray-900">
                                <span>Total Bayar</span>
                                <span id="totalCharge">Rp 10.000</span>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full bg-indigo-600 text-white font-black py-4 rounded-2xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200 uppercase tracking-widest text-xs">ISI
                            SALDO APLIKASI</button>
                    </form>
                </div>

                <!-- Rate Config Form -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 overflow-hidden relative">
                    <h3 class="text-lg font-black text-gray-900 mb-6 uppercase tracking-tight flex items-center gap-2">
                        <i class="fas fa-bolt text-rose-600"></i> Tarif Operasional
                    </h3>

                    <form action="{{ route('billing.updateRate') }}" method="POST" class="space-y-6">
                        @csrf
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Biaya
                                per Menit</label>
                            <div class="relative">
                                <span
                                    class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-gray-400">Rp</span>
                                <input type="number" step="1" name="rate_per_minute" required
                                    value="{{ $ratePerMinute }}"
                                    class="w-full bg-gray-50 border border-gray-100 rounded-xl px-12 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none font-bold">
                            </div>
                            <p class="mt-2 text-[10px] text-gray-400 leading-tight italic">Biaya ini akan ditarik secara
                                otomatis dari saldo global setiap menit aplikasi digunakan.</p>
                        </div>
                        <button type="submit"
                            class="w-full bg-white border-2 border-rose-600 text-rose-600 font-black py-4 rounded-2xl hover:bg-rose-50 transition-all uppercase tracking-widest text-xs">UPDATE
                            TARIF</button>
                    </form>
                </div>
            </div>

            <!-- Stats & Transactions -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-indigo-900 rounded-3xl p-8 text-white relative overflow-hidden shadow-xl">
                    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div>
                            <div class="text-indigo-300 text-[10px] font-black uppercase tracking-widest mb-1">Saldo
                                Aplikasi Saat Ini</div>
                            <div class="text-4xl font-black">Rp {{ number_format($appBalance, 0, ',', '.') }}</div>
                        </div>
                        <div class="flex flex-col items-end">
                            <div class="text-indigo-300 text-[10px] font-black uppercase tracking-widest mb-1">Total
                                Transaksi</div>
                            <div class="text-2xl font-black">{{ number_format($allTransactions->total(), 0) }}</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
                        <h3 class="font-black text-gray-900 tracking-tight uppercase text-sm">Log Transaksi Sistem</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-white border-b border-gray-50">
                                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        User</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        Detail</th>
                                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        Type</th>
                                    <th
                                        class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">
                                        Amount</th>
                                    <th
                                        class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">
                                        Balance After</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 text-[13px]">
                                @foreach ($allTransactions as $trx)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-gray-900">{{ $trx->user->name ?? 'System' }}</div>
                                            <div class="text-[10px] text-gray-400">
                                                {{ $trx->user->isBillingManager() ? 'Manager' : 'User' }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-gray-700 leading-relaxed max-w-xs">
                                                {{ $trx->description }}</div>
                                            <div class="text-[10px] text-gray-400 mt-1">
                                                {{ $trx->created_at->format('d/m/Y H:i') }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="px-2 py-0.5 rounded text-[9px] font-black uppercase {{ $trx->type === 'topup' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                                                {{ $trx->type }}
                                            </span>
                                        </td>
                                        <td
                                            class="px-6 py-4 text-right font-black {{ $trx->type === 'topup' ? 'text-emerald-600' : 'text-rose-600' }}">
                                            {{ $trx->type === 'topup' ? '+' : '-' }}Rp
                                            {{ number_format($trx->amount, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 text-right font-bold text-gray-900">
                                            Rp {{ number_format($trx->balance_after, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 bg-gray-50/50 border-t border-gray-50">{{ $allTransactions->links() }}</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const topupInput = document.getElementById('topupAmount');
        const ppnDisplay = document.getElementById('ppnValue');
        const totalDisplay = document.getElementById('totalCharge');
        const adminFee = 10000;

        topupInput.addEventListener('input', function() {
            const amount = parseFloat(this.value) || 0;
            const ppn = amount * 0.11;
            const total = amount + adminFee + ppn;
            ppnDisplay.textContent = 'Rp ' + ppn.toLocaleString('id-ID');
            totalDisplay.textContent = 'Rp ' + total.toLocaleString('id-ID');
        });
    </script>
@endsection
