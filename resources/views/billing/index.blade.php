@extends('layouts.app')

@section('title', 'Billing & Saldo')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">BILLING & SALDO</h1>
            <p class="text-gray-500 font-medium">Kelola saldo aplikasi dan pantau riwayat penggunaan sistem</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
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

            <!-- Top Up Form -->
            <div class="lg:col-span-1">
                <div class="h-full">
                    <!-- Midtrans Top Up Form -->
                    <div
                        class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between h-full">
                        <div>
                            <div class="flex items-center gap-3 mb-6">
                                <div
                                    class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600">
                                    <i class="fas fa-wallet"></i>
                                </div>
                                <div>
                                    <h3 class="font-black text-gray-900 text-sm uppercase">Isi Saldo Cepat</h3>
                                    <p class="text-[10px] font-medium text-gray-500">Via QRIS, VA, atau E-Wallet
                                        (Otomatis)
                                    </p>
                                </div>
                            </div>

                            <form action="{{ route('billing.topup') }}" method="POST" id="topupForm">
                                @csrf
                                <input type="hidden" name="total_amount" id="totalAmountField" value="0">
                                <div class="mb-4">
                                    <label
                                        class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Nominal
                                        Saldo yang Diinginkan</label>
                                    <div class="relative">
                                        <span
                                            class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-gray-400">Rp</span>
                                        <input type="number" name="amount" id="topupAmount" required placeholder="50000"
                                            min="10000"
                                            class="w-full bg-gray-50 border border-gray-100 rounded-xl px-12 py-3 text-sm focus:ring-2 focus:ring-indigo-500 outline-none font-bold">
                                    </div>
                                    <p class="text-[9px] text-gray-400 mt-2 font-medium">Minimal topup: Rp 10.000</p>
                                </div>

                                <!-- Rincian biaya -->
                                <div id="feeBreakdown"
                                    class="hidden mb-4 space-y-2 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                                    <div class="flex justify-between text-[11px] font-medium text-gray-500">
                                        <span>Saldo yang ditambahkan</span>
                                        <span id="nominalDisplay" class="font-black text-gray-700">Rp 0</span>
                                    </div>
                                    <div class="flex justify-between text-[11px] font-medium text-gray-500">
                                        <span>Biaya Admin</span>
                                        <span>Rp 10.000</span>
                                    </div>
                                    <div class="flex justify-between text-[11px] font-medium text-gray-500">
                                        <span>PPN (11%)</span>
                                        <span id="ppnDisplay">Rp 0</span>
                                    </div>
                                    <div
                                        class="pt-2 border-t border-gray-200 flex justify-between text-xs font-black text-gray-900">
                                        <span>Total yang Dibayarkan</span>
                                        <span id="totalDisplay" class="text-indigo-600">Rp 0</span>
                                    </div>
                                </div>

                                <button type="submit" id="topupBtn"
                                    class="w-full bg-indigo-600 text-white font-black py-3 rounded-xl hover:bg-indigo-700 transition-all shadow-md mt-2 flex justify-center items-center gap-2">
                                    <svg id="btnSpinner" class="animate-spin hidden h-5 w-5 text-white"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    <span id="btnText">BAYAR SEKARANG</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Alternatif: Topup via QRIS Manual --}}
            @if ($qrisImage)
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-50 bg-gradient-to-r from-purple-50 to-indigo-50">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center">
                                <i class="fas fa-qrcode text-purple-600"></i>
                            </div>
                            <div>
                                <h3 class="font-black text-gray-900 text-sm uppercase">Topup via QRIS</h3>
                                <p class="text-[10px] font-medium text-gray-500">Input nominal &rarr; QR muncul &rarr; transfer &rarr; admin konfirmasi</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 space-y-4">

                        {{-- Nominal Input --}}
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">
                                Nominal Saldo yang Diinginkan
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-gray-400">Rp</span>
                                <input type="number" id="qrisAmount" placeholder="50000" min="10000"
                                    oninput="calcQrisFee()"
                                    class="w-full bg-gray-50 border border-gray-100 rounded-xl px-12 py-3 text-sm focus:ring-2 focus:ring-purple-500 outline-none font-bold">
                            </div>
                            <p class="text-[9px] text-gray-400 mt-2 font-medium">Minimal topup: Rp 10.000</p>
                        </div>

                        {{-- Rincian Biaya --}}
                        <div id="qrisFeeBreakdown" class="hidden space-y-2 p-4 bg-purple-50 rounded-2xl border border-purple-100">
                            <div class="flex justify-between text-[11px] font-medium text-gray-500">
                                <span>Saldo yang ditambahkan</span>
                                <span id="qrisNominalDisplay" class="font-black text-gray-700">Rp 0</span>
                            </div>
                            <div class="flex justify-between text-[11px] font-medium text-gray-500">
                                <span>Biaya Admin</span>
                                <span>Rp 10.000</span>
                            </div>
                            <div class="flex justify-between text-[11px] font-medium text-gray-500">
                                <span>PPN (11%)</span>
                                <span id="qrisPpnDisplay">Rp 0</span>
                            </div>
                            <div class="pt-2 border-t border-purple-200 flex justify-between text-xs font-black text-gray-900">
                                <span>Total yang Ditransfer</span>
                                <span id="qrisTotalDisplay" class="text-purple-600">Rp 0</span>
                            </div>
                        </div>

                        <button id="qrisBtn" onclick="submitQrisTopup()"
                            class="w-full bg-purple-600 text-white font-black py-3 rounded-xl hover:bg-purple-700 transition-all shadow-md flex justify-center items-center gap-2">
                            <i class="fas fa-qrcode"></i>
                            <span id="qrisBtnText">LIHAT QR &amp; LANJUT BAYAR</span>
                        </button>

                        <div class="p-3 bg-amber-50 border border-amber-100 rounded-xl">
                            <p class="text-[10px] text-amber-700 font-medium leading-relaxed">
                                <i class="fas fa-info-circle mr-1"></i>
                                QR code akan muncul setelah klik tombol. Scan &amp; transfer, lalu admin akan tambahkan saldo secara manual.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
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
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Metode
                            </th>
                            <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Status
                            </th>
                            <th
                                class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">
                                Jumlah</th>
                            <th
                                class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">
                                Saldo Akhir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50" id="trxTableBody">
                        @forelse($transactions as $trx)
                            <tr class="hover:bg-gray-50/50 transition-colors" id="trx-row-{{ $trx->id }}"
                                data-order-id="{{ $trx->reference_id }}">
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
                                    <div class="text-xs font-medium text-gray-700 leading-relaxed">
                                        {{ Str::limit($trx->description, 60) }}</div>
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
                                <td class="px-6 py-4" id="method-cell-{{ $trx->id }}">
                                    @php
                                        $channelLabel = match (strtolower($trx->payment_channel ?? '')) {
                                            'qris' => ['QRIS', 'fa-qrcode', 'text-purple-600 bg-purple-50'],
                                            'gopay' => ['GoPay', 'fa-wallet', 'text-green-600 bg-green-50'],
                                            'shopeepay' => [
                                                'ShopeePay',
                                                'fa-shopping-bag',
                                                'text-orange-600 bg-orange-50',
                                            ],
                                            'dana' => ['DANA', 'fa-wallet', 'text-blue-600 bg-blue-50'],
                                            'ovo' => ['OVO', 'fa-wallet', 'text-purple-600 bg-purple-50'],
                                            'bank_transfer', 'bca_va', 'bni_va', 'bri_va', 'other_va' => [
                                                'Transfer VA',
                                                'fa-university',
                                                'text-indigo-600 bg-indigo-50',
                                            ],
                                            'echannel' => [
                                                'Mandiri Bill',
                                                'fa-university',
                                                'text-yellow-600 bg-yellow-50',
                                            ],
                                            'credit_card' => [
                                                'Kartu Kredit',
                                                'fa-credit-card',
                                                'text-gray-600 bg-gray-100',
                                            ],
                                            'manual' => ['Manual Admin', 'fa-user-shield', 'text-teal-600 bg-teal-50'],
                                            default => ['—', 'fa-question', 'text-gray-400 bg-gray-100'],
                                        };
                                    @endphp
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-1 {{ $channelLabel[2] }} text-[9px] font-black rounded uppercase">
                                        <i class="fas {{ $channelLabel[1] }}"></i> {{ $channelLabel[0] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4" id="status-cell-{{ $trx->id }}">
                                    @php
                                        $statusConfig = match ($trx->status) {
                                            'success' => [
                                                'bg-emerald-50 text-emerald-600',
                                                'fa-check-circle',
                                                'Sukses',
                                            ],
                                            'pending' => ['bg-amber-50 text-amber-500', 'fa-clock', 'Pending'],
                                            'denied' => ['bg-rose-50 text-rose-600', 'fa-times-circle', 'Ditolak'],
                                            'expired' => ['bg-gray-100 text-gray-400', 'fa-hourglass-end', 'Expired'],
                                            'cancelled' => ['bg-gray-100 text-gray-400', 'fa-ban', 'Dibatalkan'],
                                            default => [
                                                'bg-gray-100 text-gray-400',
                                                'fa-question-circle',
                                                ucfirst($trx->status),
                                            ],
                                        };
                                    @endphp
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-1 {{ $statusConfig[0] }} text-[9px] font-black rounded uppercase">
                                        <i class="fas {{ $statusConfig[1] }}"></i>
                                        {{ $statusConfig[2] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span
                                        class="text-xs font-black {{ $trx->type === 'topup' ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ $trx->type === 'topup' ? '+' : '-' }}Rp
                                        {{ number_format($trx->amount, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right" id="balance-cell-{{ $trx->id }}">
                                    <span class="text-xs font-bold text-gray-900">Rp
                                        {{ number_format($trx->balance_after, 0, ',', '.') }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr id="empty-row">
                                <td colspan="9" class="px-6 py-12 text-center text-gray-400 text-sm font-medium">Belum
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
    <script data-turbo-eval="false">
        // ============================================
        // Helper: status badge HTML
        // ============================================
        const STATUS_MAP = {
            success: ['bg-emerald-50 text-emerald-600', 'fa-check-circle', 'Sukses'],
            pending: ['bg-amber-50 text-amber-500', 'fa-clock', 'Pending'],
            denied: ['bg-rose-50 text-rose-600', 'fa-times-circle', 'Ditolak'],
            expired: ['bg-gray-100 text-gray-400', 'fa-hourglass-end', 'Expired'],
            cancelled: ['bg-gray-100 text-gray-400', 'fa-ban', 'Dibatalkan'],
        };
        const METHOD_MAP = {
            qris: ['QRIS', 'fa-qrcode', 'text-purple-600 bg-purple-50'],
            gopay: ['GoPay', 'fa-wallet', 'text-green-600 bg-green-50'],
            shopeepay: ['ShopeePay', 'fa-shopping-bag', 'text-orange-600 bg-orange-50'],
            dana: ['DANA', 'fa-wallet', 'text-blue-600 bg-blue-50'],
            ovo: ['OVO', 'fa-wallet', 'text-purple-600 bg-purple-50'],
            bank_transfer: ['Transfer VA', 'fa-university', 'text-indigo-600 bg-indigo-50'],
            bca_va: ['BCA VA', 'fa-university', 'text-indigo-600 bg-indigo-50'],
            bni_va: ['BNI VA', 'fa-university', 'text-indigo-600 bg-indigo-50'],
            bri_va: ['BRI VA', 'fa-university', 'text-indigo-600 bg-indigo-50'],
            other_va: ['Transfer VA', 'fa-university', 'text-indigo-600 bg-indigo-50'],
            echannel: ['Mandiri Bill', 'fa-university', 'text-yellow-600 bg-yellow-50'],
            credit_card: ['Kartu Kredit', 'fa-credit-card', 'text-gray-600 bg-gray-100'],
            manual: ['Manual Admin', 'fa-user-shield', 'text-teal-600 bg-teal-50'],
        };

        function buildStatusBadge(status) {
            const [cls, icon, label] = STATUS_MAP[status] || ['bg-gray-100 text-gray-400', 'fa-question-circle', status ||
                '?'
            ];
            return `<span class="inline-flex items-center gap-1 px-2 py-1 ${cls} text-[9px] font-black rounded uppercase"><i class="fas ${icon}"></i> ${label}</span>`;
        }

        function buildMethodBadge(channel) {
            const [label, icon, cls] = METHOD_MAP[channel] || ['—', 'fa-question', 'text-gray-400 bg-gray-100'];
            return `<span class="inline-flex items-center gap-1 px-2 py-1 ${cls} text-[9px] font-black rounded uppercase"><i class="fas ${icon}"></i> ${label}</span>`;
        }

        function injectPendingRow(trxData, orderId) {
            const tbody = document.getElementById('trxTableBody');
            if (!tbody) {
                console.warn('[Billing] trxTableBody not found');
                return;
            }

            const emptyRow = document.getElementById('empty-row');
            if (emptyRow) emptyRow.remove();

            // Hapus row yang sama jika sudah ada
            const existing = document.getElementById('trx-row-' + trxData.id);
            if (existing) existing.remove();

            const row = document.createElement('tr');
            row.id = 'trx-row-' + trxData.id;
            row.setAttribute('data-order-id', orderId);
            row.style.cssText = 'background: #fffbeb; border-left: 3px solid #f59e0b;';
            row.innerHTML = `
                <td style="padding:12px 16px; white-space:nowrap;">
                    <div style="font-size:11px;font-weight:900;color:#059669;">${trxData.user_name}</div>
                </td>
                <td style="padding:12px 16px; white-space:nowrap;">
                    <div style="font-size:11px;font-weight:700;color:#111827;">${trxData.date}</div>
                    <div style="font-size:10px;color:#9ca3af;">${trxData.time}</div>
                </td>
                <td style="padding:12px 16px;">
                    <div style="font-size:11px;color:#374151;">${trxData.description}</div>
                </td>
                <td style="padding:12px 16px;">
                    <span style="display:inline-flex;align-items:center;gap:4px;padding:2px 6px;background:#ecfdf5;color:#059669;font-size:9px;font-weight:900;border-radius:4px;text-transform:uppercase;">
                        <i class="fas fa-arrow-up"></i> Top Up
                    </span>
                </td>
                <td style="padding:12px 16px;" id="method-cell-${trxData.id}">${buildMethodBadge('')}</td>
                <td style="padding:12px 16px;" id="status-cell-${trxData.id}">${buildStatusBadge('pending')}</td>
                <td style="padding:12px 16px; text-align:right;">
                    <span style="font-size:11px;font-weight:900;color:#059669;">${trxData.amount_fmt}</span>
                </td>
                <td style="padding:12px 16px; text-align:right;" id="balance-cell-${trxData.id}">
                    <span style="font-size:11px;color:#9ca3af;">—</span>
                </td>
            `;
            tbody.insertBefore(row, tbody.firstChild);
            console.log('[Billing] Row pending diinjeksi untuk trx #' + trxData.id);
        }

        function updateTrxRow(trxId, status, channel, balanceAfter = null) {
            const statusCell = document.getElementById('status-cell-' + trxId);
            const methodCell = document.getElementById('method-cell-' + trxId);
            const row = document.getElementById('trx-row-' + trxId);
            const balanceCell = document.getElementById('balance-cell-' + trxId);

            console.log('[Billing] Update row ' + trxId + ' → status=' + status + ' channel=' + channel);

            if (statusCell) statusCell.innerHTML = buildStatusBadge(status);
            if (methodCell) methodCell.innerHTML = buildMethodBadge(channel || '');
            if (balanceCell && balanceAfter) {
                const bal = parseInt(balanceAfter).toLocaleString('id-ID');
                balanceCell.innerHTML = `<span class="text-xs font-bold text-gray-900">Rp ${bal}</span>`;
            }
            if (row) {
                if (status === 'success') {
                    row.style.cssText = 'background: #f0fdf4; border-left: 3px solid #22c55e;';
                } else if (['denied', 'expired', 'cancelled'].includes(status)) {
                    row.style.cssText = 'background: #fff1f2; border-left: 3px solid #f43f5e;';
                }
            }
        }

        // Update tampilan saldo tanpa reload
        function updateBalanceDisplay(newBalance) {
            if (newBalance === undefined || newBalance === null) return;
            const amount = parseInt(newBalance);
            const formatted = amount.toLocaleString('id-ID'); // 9.000
            const formattedRp = 'Rp ' + formatted; // Rp 9.000

            // Saldo besar di halaman billing (hanya angka, tanpa 'Rp')
            const mainDisplay = document.getElementById('currentBalanceDisplay');
            if (mainDisplay) {
                mainDisplay.textContent = formatted;
                mainDisplay.dataset.value = amount;
            }

            // Saldo di navbar/sidebar (dengan prefix 'Rp ')
            const navDisplay = document.getElementById('navbarBalance');
            if (navDisplay && navDisplay.dataset.status !== 'inactive') {
                navDisplay.textContent = formattedRp;
                navDisplay.dataset.value = amount;
            }

            console.log('[Billing] Saldo diperbarui → ' + formattedRp);
        }

        // ============================================
        // Visual balance simulation
        // ============================================
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

        // ============================================
        // Fee breakdown calculator
        // ============================================
        const topupInput = document.getElementById('topupAmount');
        const feeBreakdown = document.getElementById('feeBreakdown');
        const nominalDisplay = document.getElementById('nominalDisplay');
        const ppnDisplay = document.getElementById('ppnDisplay');
        const totalDisplay = document.getElementById('totalDisplay');
        const totalAmountField = document.getElementById('totalAmountField');
        const adminFee = 10000;

        if (topupInput) {
            topupInput.addEventListener('input', function() {
                const nominal = parseFloat(this.value) || 0;
                if (nominal >= 10000) {
                    const ppn = Math.round(nominal * 0.11);
                    const total = nominal + adminFee + ppn;
                    nominalDisplay.textContent = 'Rp ' + nominal.toLocaleString('id-ID');
                    ppnDisplay.textContent = 'Rp ' + ppn.toLocaleString('id-ID');
                    totalDisplay.textContent = 'Rp ' + total.toLocaleString('id-ID');
                    totalAmountField.value = total;
                    feeBreakdown.classList.remove('hidden');
                } else {
                    feeBreakdown.classList.add('hidden');
                    totalAmountField.value = 0;
                }
            });
        }

        // ============================================
        // Auto-poll semua baris PENDING saat halaman load
        // Ini memastikan polling berjalan meski user refresh atau kembali ke halaman
        // ============================================
        function startRowPolling(trxId, orderId) {
            let count = 0;
            console.log('[AutoPoll] Mulai polling untuk trx #' + trxId + ' order: ' + orderId);
            const timer = setInterval(async () => {
                count++;
                if (count > 120) {
                    clearInterval(timer);
                    return;
                }
                try {
                    const r = await fetch('/api/billing/check-status/' + orderId);
                    const s = await r.json();
                    console.log('[AutoPoll] trx #' + trxId + ' poll #' + count + ': status=' + s.status);

                    updateTrxRow(trxId, s.status, s.payment_channel, s.balance_after);

                    if (s.status === 'success') {
                        clearInterval(timer);
                        updateBalanceDisplay(s.balance_after);
                        setTimeout(() => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Pembayaran Terkonfirmasi!',
                                html: `Saldo bertambah <b>Rp ${parseInt(s.amount).toLocaleString('id-ID')}</b>`,
                                timer: 4000,
                                showConfirmButton: false
                            });
                        }, 150);
                    } else if (['denied', 'expired', 'cancelled'].includes(s.status)) {
                        clearInterval(timer);
                    }
                } catch (err) {
                    console.warn('[AutoPoll] Error polling trx #' + trxId, err);
                }
            }, 3000);
        }

        function scanAndPollPendingRows() {
            const tbody = document.getElementById('trxTableBody');
            if (!tbody) return;
            const allRows = tbody.querySelectorAll('tr[data-order-id]');
            allRows.forEach(row => {
                const orderId = row.getAttribute('data-order-id');
                const trxId = row.id ? row.id.replace('trx-row-', '') : '';
                const statusCell = document.getElementById('status-cell-' + trxId);
                if (!orderId || !trxId || !statusCell) return;
                const isPending = statusCell.textContent.trim().toLowerCase().includes('pending');
                if (isPending) {
                    console.log('[AutoPoll] Baris pending ditemukan: trx #' + trxId + ' order: ' + orderId);
                    startRowPolling(trxId, orderId);
                }
            });
        }

        // Jalankan langsung (DOM sudah siap karena script ada di bottom of body)
        scanAndPollPendingRows();

        // ============================================
        // Topup Form Submit + Midtrans + Polling
        // ============================================
        const form = document.getElementById('topupForm');
        const btn = document.getElementById('topupBtn');
        const btnText = document.getElementById('btnText');
        const spinner = document.getElementById('btnSpinner');

        if (form) {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                btn.disabled = true;
                btnText.innerText = 'MEMPROSES...';
                spinner.classList.remove('hidden');

                try {
                    const response = await fetch('{{ route('billing.topup') }}', {
                        method: 'POST',
                        body: new FormData(this),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();
                    console.log('[Billing] Response dari server:', data);

                    if (!data.success || !data.snap_token) {
                        Swal.fire('Error!', data.message || 'Gagal generate pembayaran', 'error');
                        return;
                    }

                    const orderId = data.order_id;
                    const trx = data.transaction;

                    // 1. Inject baris pending SEKARANG (sebelum popup muncul)
                    injectPendingRow(trx, orderId);

                    // 2. Setup polling
                    let pollInterval = null;
                    let pollCount = 0;

                    const stopPolling = () => {
                        if (pollInterval) {
                            clearInterval(pollInterval);
                            pollInterval = null;
                        }
                    };

                    const startPolling = () => {
                        if (pollInterval) return;
                        console.log('[Billing] Mulai polling untuk order: ' + orderId);
                        pollInterval = setInterval(async () => {
                            pollCount++;
                            if (pollCount > 120) {
                                stopPolling();
                                return;
                            }
                            try {
                                const r = await fetch('/api/billing/check-status/' + orderId);
                                const s = await r.json();
                                console.log('[Billing] Poll #' + pollCount + ' status:', s
                                    .status, 'channel:', s.payment_channel);

                                if (s.status === 'success') {
                                    stopPolling();
                                    updateTrxRow(trx.id, 'success', s.payment_channel, s
                                        .balance_after);
                                    updateBalanceDisplay(s.balance_after);
                                    // Tutup dialog apapun yg sedang terbuka (termasuk "Memverifikasi...")
                                    Swal.close();
                                    setTimeout(() => {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Pembayaran Terkonfirmasi!',
                                            html: `Saldo bertambah <b>Rp ${parseInt(s.amount).toLocaleString('id-ID')}</b>`,
                                            timer: 4000,
                                            showConfirmButton: false
                                        });
                                    }, 300);
                                } else if (['denied', 'expired', 'cancelled'].includes(s
                                        .status)) {
                                    stopPolling();
                                    updateTrxRow(trx.id, s.status, s.payment_channel, s
                                        .balance_after);
                                } else {
                                    updateTrxRow(trx.id, s.status, s.payment_channel, s
                                        .balance_after);
                                }
                            } catch (err) {
                                console.warn('[Billing] Polling error:', err);
                            }
                        }, 3000);
                    };

                    // 3. Langsung mulai polling
                    startPolling();

                    // 4. Buka Midtrans Snap
                    window.snap.pay(data.snap_token, {
                        onSuccess: function(result) {
                            // ⚠️ Jangan langsung sukses! onSuccess dari Snap hanya konfirmasi
                            // client-side. Harus tunggu webhook dari Midtrans ke backend dulu.
                            console.log(
                                '[Billing] Snap onSuccess (client-side only), menunggu webhook...',
                                result);
                            const ch = result.payment_type || '';
                            updateTrxRow(trx.id, 'pending', ch);
                            Swal.fire({
                                icon: 'info',
                                title: 'Memverifikasi...',
                                html: `Pembayaran diproses, menunggu konfirmasi dari Midtrans.<br>
                                       <small style="color:#9ca3af">Status akan terupdate otomatis.</small>`,
                                showConfirmButton: false,
                                allowOutsideClick: false,
                                didOpen: () => Swal.showLoading()
                            });
                            // Polling tetap berjalan — saat webhook masuk & DB update ke 'success',
                            // polling akan menutup dialog ini dan menampilkan sukses.
                        },
                        onPending: function(result) {
                            const ch = result.payment_type || '';
                            console.log('[Billing] Snap onPending, channel:', ch);
                            updateTrxRow(trx.id, 'pending', ch);
                            Swal.fire({
                                icon: 'info',
                                title: 'Menunggu Pembayaran',
                                html: `Selesaikan pembayaran Anda.<br><small style="color:#9ca3af">Riwayat akan terupdate otomatis setelah dikonfirmasi Midtrans.</small>`
                            });
                        },
                        onError: function(result) {
                            stopPolling();
                            console.log('[Billing] Snap onError:', result);
                            updateTrxRow(trx.id, 'denied', result.payment_type || '');
                            Swal.fire({
                                icon: 'error',
                                title: 'Pembayaran Gagal',
                                text: 'Terjadi kesalahan pada transaksi.'
                            });
                        },
                        onClose: function() {
                            console.log('[Billing] Snap ditutup, polling tetap berjalan.');
                            Swal.fire({
                                icon: 'info',
                                title: 'Memantau di Latar Belakang',
                                html: `Polling tetap berjalan.<br><small style="color:#9ca3af">Riwayat akan terupdate jika pembayaran berhasil.</small>`,
                                confirmButtonText: 'OK'
                            });
                        }
                    });

                } catch (error) {
                    console.error('[Billing] Submit error:', error);
                    Swal.fire('Error!', 'Terjadi kesalahan memproses request.', 'error');
                } finally {
                    btn.disabled = false;
                    btnText.innerText = 'BAYAR SEKARANG';
                    spinner.classList.add('hidden');
                }
            });
        }

        // ============================================
        // QRIS Topup
        // ============================================
        function calcQrisFee() {
            const nominal = parseFloat(document.getElementById('qrisAmount')?.value || 0);
            const breakdown = document.getElementById('qrisFeeBreakdown');

            if (!nominal || nominal < 1) {
                breakdown?.classList.add('hidden');
                return;
            }

            const adminFee = 10000;
            const ppn = nominal * 0.11;
            const total = nominal + adminFee + ppn;

            const fmt = (n) => 'Rp ' + Math.round(n).toLocaleString('id-ID');
            document.getElementById('qrisNominalDisplay').textContent = fmt(nominal);
            document.getElementById('qrisPpnDisplay').textContent = fmt(ppn);
            document.getElementById('qrisTotalDisplay').textContent = fmt(total);

            breakdown?.classList.remove('hidden');
        }

        async function submitQrisTopup() {
            const amountInput = document.getElementById('qrisAmount');
            const btn         = document.getElementById('qrisBtn');
            const btnText     = document.getElementById('qrisBtnText');
            const amount      = parseFloat(amountInput?.value || 0);

            if (!amount || amount < 10000) {
                Swal.fire('Perhatian', 'Nominal minimal Rp 10.000', 'warning');
                return;
            }

            const adminFee    = 10000;
            const ppn         = amount * 0.11;
            const totalAmount = amount + adminFee + ppn;

            btn.disabled = true;
            btnText.textContent = 'MEMPROSES...';

            try {
                const response = await fetch('{{ route("billing.qrisTopup") }}', {
                    method:  'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept':       'application/json',
                    },
                    body: JSON.stringify({ amount, total_amount: totalAmount }),
                });

                const data = await response.json();
                if (!data.success) {
                    Swal.fire('Gagal', data.message || 'Terjadi kesalahan.', 'error');
                    return;
                }

                // Inject baris pending ke tabel
                injectPendingRow(data.transaction, data.order_id);

                // Tampilkan QR code dalam modal SweetAlert
                const fmt = (n) => 'Rp ' + Math.round(n).toLocaleString('id-ID');
                const qrHtml = data.qris_image_url
                    ? `<img src="${data.qris_image_url}" style="width:200px;height:200px;object-fit:contain;border-radius:16px;border:1px solid #e5e7eb;margin:0 auto 12px;" />`
                    : '';

                await Swal.fire({
                    title: 'Scan QR & Transfer',
                    html: `
                        ${qrHtml}
                        <p style="font-size:12px;color:#6b7280;margin-bottom:12px">Scan menggunakan aplikasi e-wallet / mobile banking</p>
                        <div style="background:#f5f3ff;border:1px solid #e9d5ff;border-radius:12px;padding:12px;text-align:left;font-size:12px;">
                            <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                                <span style="color:#6b7280">Saldo ditambahkan</span>
                                <b>${fmt(amount)}</b>
                            </div>
                            <div style="display:flex;justify-content:space-between;margin-bottom:6px;">
                                <span style="color:#6b7280">Biaya Admin</span>
                                <span>Rp 10.000</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
                                <span style="color:#6b7280">PPN (11%)</span>
                                <span>${fmt(ppn)}</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;border-top:1px solid #d8b4fe;padding-top:8px;">
                                <b>Total Transfer</b>
                                <b style="color:#7c3aed">${fmt(totalAmount)}</b>
                            </div>
                        </div>
                        <p style="font-size:10px;color:#f59e0b;margin-top:10px">
                            <i class="fas fa-info-circle"></i>
                            Notifikasi WA sudah terkirim ke admin. Saldo akan diisi setelah pembayaran dikonfirmasi.
                        </p>`,
                    confirmButtonText: 'Selesai',
                    confirmButtonColor: '#7c3aed',
                    width: 380,
                });

                amountInput.value = '';
                document.getElementById('qrisFeeBreakdown')?.classList.add('hidden');
            } catch (err) {
                console.error('[QRIS] Error:', err);
                Swal.fire('Error', 'Terjadi kesalahan koneksi.', 'error');
            } finally {
                btn.disabled = false;
                btnText.textContent = 'LIHAT QR & LANJUT BAYAR';
            }
        }
    </script>
@endpush
