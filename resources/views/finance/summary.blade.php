@extends('layouts.app')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-black text-gray-900 tracking-tight">Ringkasan Keuangan</h1>
        <p class="text-gray-500 font-medium">Laporan menyeluruh aset, pemasukan, dan pengeluaran.</p>
    </div>

    <!-- Date Filter -->
    <div
        class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 mb-8 flex flex-wrap items-center justify-between gap-4">
        <form action="{{ route('finance.summary') }}" method="GET" class="flex flex-wrap items-center gap-4">
            <div class="flex items-center gap-3">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Periode:</label>
                <input type="date" name="start_date" value="{{ $startDate }}"
                    class="rounded-xl border-gray-200 text-sm font-bold focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                <span class="text-gray-300 font-black text-xs">S/D</span>
                <input type="date" name="end_date" value="{{ $endDate }}"
                    class="rounded-xl border-gray-200 text-sm font-bold focus:ring-indigo-500 focus:border-indigo-500 transition-all">
            </div>
            <button type="submit"
                class="bg-gray-900 text-white px-6 py-2 rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-gray-800 transition-all active:scale-95 shadow-lg shadow-gray-200">
                Filter Laporan
            </button>
        </form>
        <div>
            <a href="{{ route('finance.export-pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                target="_blank"
                class="inline-flex items-center px-4 py-2 rounded-xl bg-red-50 text-red-600 text-xs font-black uppercase tracking-widest border border-red-100 hover:bg-red-100 transition-all mr-2">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export PDF
            </a>
            <span
                class="inline-flex items-center px-4 py-2 rounded-xl bg-indigo-50 text-indigo-700 text-xs font-black uppercase tracking-widest border border-indigo-100">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ \Carbon\Carbon::parse($startDate)->format('d M') }} -
                {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
            </span>
        </div>
    </div>

    <!-- Top Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Asset -->
        <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm relative overflow-hidden group">
            <div
                class="absolute -right-4 -top-4 w-24 h-24 bg-indigo-50 rounded-full opacity-50 group-hover:scale-110 transition-transform">
            </div>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 relative">Total Nilai Aset (Stok)
            </p>
            <h3 class="text-3xl font-black text-gray-900 tracking-tighter relative">
                Rp{{ number_format($totalAssetValue, 0, ',', '.') }}</h3>
            <p class="text-xs font-bold text-indigo-500 mt-2 relative">{{ number_format($totalStockCount, 0, ',', '.') }}
                Item Barang</p>
        </div>

        <!-- Total Sales -->
        <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm relative overflow-hidden group">
            <div
                class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-50 rounded-full opacity-50 group-hover:scale-110 transition-transform">
            </div>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 relative">Total Omset (Sales)</p>
            <h3 class="text-3xl font-black text-emerald-600 tracking-tighter relative">
                Rp{{ number_format($totalSales, 0, ',', '.') }}</h3>
            <p class="text-xs font-bold text-gray-400 mt-2 relative">Dihitung dari Invoice</p>
        </div>

        <!-- Total Operational Expenses -->
        <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm relative overflow-hidden group">
            <div
                class="absolute -right-4 -top-4 w-24 h-24 bg-red-50 rounded-full opacity-50 group-hover:scale-110 transition-transform">
            </div>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 relative">Total Pengeluaran</p>
            <h3 class="text-3xl font-black text-red-500 tracking-tighter relative">
                Rp{{ number_format($totalOperationalExpenses + $totalSalaries, 0, ',', '.') }}</h3>
            <p class="text-xs font-bold text-gray-400 mt-2 relative">Biaya Ops + Gaji</p>
        </div>

        <!-- Net Profit -->
        <div class="bg-gray-900 rounded-[2.5rem] p-8 shadow-2xl shadow-gray-300 relative overflow-hidden group">
            <div
                class="absolute -right-4 -top-4 w-24 h-24 bg-white/5 rounded-full opacity-50 group-hover:scale-110 transition-transform">
            </div>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 relative text-white/50">Laba
                Bersih (Net)</p>
            <h3
                class="text-3xl font-black {{ $netProfit >= 0 ? 'text-emerald-400' : 'text-red-400' }} tracking-tighter relative">
                Rp{{ number_format($netProfit, 0, ',', '.') }}</h3>
            <p class="text-xs font-bold text-white/30 mt-2 relative">Sudah Potong Semua Biaya</p>
        </div>
    </div>

    <!-- Trend Chart -->
    <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-black text-gray-900 uppercase tracking-widest text-sm">Trend Arus Kas (Harian)</h2>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                    <span class="text-xs font-bold text-gray-500">Pemasukan</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-red-500"></span>
                    <span class="text-xs font-bold text-gray-500">Pengeluaran</span>
                </div>
            </div>
        </div>
        <div class="h-80 w-full relative">
            <canvas id="balanceChart"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- P&L Breakdown -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-10">
            <h2 class="text-xl font-black text-gray-900 mb-8 uppercase tracking-widest text-sm">Rincian Laba Rugi</h2>
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-black text-gray-900">Penjualan Kotor</p>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total dari customer</p>
                    </div>
                    <p class="text-lg font-black text-gray-900">Rp{{ number_format($totalSales, 0, ',', '.') }}</p>
                </div>

                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-black text-red-500">HPP (Modal Barang)</p>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Harga beli barang terjual
                        </p>
                    </div>
                    <p class="text-lg font-black text-red-500">- Rp{{ number_format($totalHpp, 0, ',', '.') }}</p>
                </div>

                <div class="h-px bg-gray-100"></div>

                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-black text-emerald-600">Laba Kotor (Gross)</p>
                        <p
                            class="text-[10px] font-bold text-gray-400 uppercase tracking-widest underline decoration-2 decoration-emerald-100">
                            Margin Penjualan</p>
                    </div>
                    <p class="text-lg font-black text-emerald-600">Rp{{ number_format($grossProfit, 0, ',', '.') }}</p>
                </div>

                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-black text-gray-600">Beban Gaji Pegawai</p>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total slip gaji periode ini
                        </p>
                    </div>
                    <p class="text-lg font-black text-gray-600">- Rp{{ number_format($totalSalaries, 0, ',', '.') }}</p>
                </div>

                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-black text-gray-600">Beban Operasional</p>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Listrik, air, pemeliharaan,
                            dll</p>
                    </div>
                    <p class="text-lg font-black text-gray-600">-
                        Rp{{ number_format($totalOperationalExpenses, 0, ',', '.') }}</p>
                </div>

                <div class="pt-6 border-t-2 border-dashed border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-lg font-black text-gray-900 uppercase tracking-[0.1em]">Laba Bersih Akhir</p>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Keuntungan murni</p>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-black {{ $netProfit >= 0 ? 'text-indigo-600' : 'text-red-600' }}">
                                Rp{{ number_format($netProfit, 0, ',', '.') }}</p>
                            <span class="text-[10px] font-black {{ $totalSales > 0 ? 'text-gray-400' : 'hidden' }}">
                                MARGE: {{ $totalSales > 0 ? number_format(($netProfit / $totalSales) * 100, 1) : 0 }}%
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Expenses Breakdown -->
        <div class="bg-gray-50 rounded-[2.5rem] p-10 flex flex-col">
            <h2 class="text-xl font-black text-gray-900 mb-8 uppercase tracking-widest text-sm text-center">Analisa
                Pengeluaran</h2>

            <div class="h-64 w-full relative mb-8">
                <canvas id="expenseChart"></canvas>
            </div>

            @if ($totalOperationalExpenses > 0)
                <div class="space-y-4 flex-1 overflow-y-auto pr-4">
                    @foreach ($expensesByCategory as $exp)
                        @php
                            $percentage = ($exp->total / $totalOperationalExpenses) * 100;
                        @endphp
                        <div class="bg-white p-5 rounded-3xl border border-gray-200">
                            <div class="flex justify-between items-center mb-3">
                                <div>
                                    <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest">
                                        {{ $exp->category }}</h4>
                                    <p class="text-sm font-black text-gray-900">
                                        Rp{{ number_format($exp->total, 0, ',', '.') }}</p>
                                </div>
                                <span
                                    class="text-xs font-black text-indigo-500 bg-indigo-50 px-2 py-1 rounded-lg">{{ number_format($percentage, 0) }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-indigo-600 h-1.5 rounded-full transition-all"
                                    style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex-1 flex flex-col items-center justify-center text-center opacity-40">
                    <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                    </svg>
                    <p class="font-black text-gray-400 uppercase tracking-widest text-xs">Data Pengeluaran Kosong</p>
                </div>
            @endif

            <div class="mt-8">
                <a href="{{ route('expenses.index') }}"
                    class="block w-full text-center py-4 bg-white border-2 border-gray-200 rounded-2xl text-[10px] font-black uppercase tracking-widest text-gray-600 hover:bg-gray-50 transition-all">
                    Kelola Detail Pengeluaran
                </a>
            </div>
        </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // --- Balance Trend Chart ---
        const ctxBalance = document.getElementById('balanceChart').getContext('2d');

        // Gradient for Sales
        const gradientSales = ctxBalance.createLinearGradient(0, 0, 0, 400);
        gradientSales.addColorStop(0, 'rgba(16, 185, 129, 0.2)'); // Emerald-500
        gradientSales.addColorStop(1, 'rgba(16, 185, 129, 0)');

        // Gradient for Expenses
        const gradientExpenses = ctxBalance.createLinearGradient(0, 0, 0, 400);
        gradientExpenses.addColorStop(0, 'rgba(239, 68, 68, 0.2)'); // Red-500
        gradientExpenses.addColorStop(1, 'rgba(239, 68, 68, 0)');

        new Chart(ctxBalance, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                        label: 'Pemasukan',
                        data: @json($chartSales),
                        borderColor: '#10b981', // Emerald-500
                        backgroundColor: gradientSales,
                        borderWidth: 3,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#10b981',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Pengeluaran',
                        data: @json($chartExpenses),
                        borderColor: '#ef4444', // Red-500
                        backgroundColor: gradientExpenses,
                        borderWidth: 3,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#ef4444',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false // We use custom custom legend
                    },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        titleColor: '#f3f4f6',
                        bodyColor: '#f3f4f6',
                        padding: 12,
                        cornerRadius: 12,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('id-ID', {
                                        style: 'currency',
                                        currency: 'IDR'
                                    }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 10,
                                weight: 'bold'
                            },
                            color: '#9ca3af'
                        }
                    },
                    y: {
                        grid: {
                            color: '#f3f4f6',
                            drawBorder: false,
                            borderDash: [5, 5]
                        },
                        ticks: {
                            font: {
                                size: 10,
                                weight: 'bold'
                            },
                            color: '#9ca3af',
                            callback: function(value) {
                                if (value >= 1000000) return 'Rp' + (value / 1000000).toFixed(1) + 'jt';
                                if (value >= 1000) return 'Rp' + (value / 1000).toFixed(0) + 'rb';
                                return value;
                            }
                        },
                        beginAtZero: true
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
            }
        });

        // --- Expense Donut Chart ---
        const ctxExpense = document.getElementById('expenseChart');
        if (ctxExpense) {
            const expenseData = @json($expensesByCategory);

            // Extract labels and values
            const expenseLabels = expenseData.map(item => item.category);
            const expenseValues = expenseData.map(item => item.total);

            // Colors (Generate palette or use predefined)
            const backgroundColors = [
                '#6366f1', '#8b5cf6', '#ec4899', '#f43f5e', '#f59e0b', '#10b981', '#06b6d4', '#3b82f6'
            ];

            new Chart(ctxExpense, {
                type: 'doughnut',
                data: {
                    labels: expenseLabels,
                    datasets: [{
                        data: expenseValues,
                        backgroundColor: backgroundColors,
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: {
                                    size: 11,
                                    weight: 'bold'
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: '#1f2937',
                            padding: 12,
                            cornerRadius: 12,
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    let value = context.parsed;
                                    let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    let percentage = Math.round((value / total) * 100) + '%';

                                    if (context.parsed !== null) {
                                        label += new Intl.NumberFormat('id-ID', {
                                            style: 'currency',
                                            currency: 'IDR'
                                        }).format(context.parsed);
                                    }
                                    return label + ' (' + percentage + ')';
                                }
                            }
                        }
                    }
                }
            });
        }
    </script>
@endsection
