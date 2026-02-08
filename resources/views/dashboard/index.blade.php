@extends('layouts.app')

@section('title', 'Dashboard Eksekutif')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Dashboard Eksekutif</h1>
            <p class="mt-2 text-sm text-gray-500">Ringkasan performa bisnis Koperasi JR hari ini.</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Card 1: Omzet Hari Ini -->
            <div
                class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-3xl p-6 shadow-xl text-white relative overflow-hidden group">
                <div
                    class="absolute right-0 top-0 opacity-10 transform translate-x-3 -translate-y-3 group-hover:scale-110 transition-transform">
                    <i class="fas fa-coins text-8xl"></i>
                </div>
                <div class="relative z-10">
                    <p class="text-xs font-bold text-indigo-100 uppercase tracking-widest mb-1">Omzet Hari Ini</p>
                    <h3 class="text-2xl font-black">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</h3>
                    <p class="text-xs text-indigo-200 mt-2 font-medium">
                        {{ $todayInvoices }} Transaksi
                    </p>
                </div>
            </div>

            <!-- Card 2: Omzet Bulan Ini -->
            <div class="bg-white rounded-3xl p-6 shadow-lg border border-gray-100 relative overflow-hidden group">
                <div class="absolute right-4 top-4 bg-emerald-100 rounded-xl p-3 text-emerald-600">
                    <i class="fas fa-chart-line text-xl"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Omzet Bulan Ini</p>
                    <h3 class="text-2xl font-black text-gray-900">Rp {{ number_format($monthRevenue, 0, ',', '.') }}</h3>
                    <p class="text-xs text-emerald-600 mt-2 font-bold flex items-center gap-1">
                        <i class="fas fa-calendar-check"></i> {{ Carbon\Carbon::now()->format('F Y') }}
                    </p>
                </div>
            </div>

            <!-- Card 3: Total Produk -->
            <div class="bg-white rounded-3xl p-6 shadow-lg border border-gray-100 relative overflow-hidden group">
                <div class="absolute right-4 top-4 bg-amber-100 rounded-xl p-3 text-amber-600">
                    <i class="fas fa-box-open text-xl"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Total Produk</p>
                    <h3 class="text-2xl font-black text-gray-900">{{ number_format($totalProducts) }}</h3>
                    <p class="text-xs text-amber-600 mt-2 font-bold flex items-center gap-1">
                        <i class="fas fa-cubes"></i> Item Aktif
                    </p>
                </div>
            </div>

            <!-- Card 4: Stok Menipis -->
            <div class="bg-white rounded-3xl p-6 shadow-lg border border-gray-100 relative overflow-hidden group">
                <div class="absolute right-4 top-4 bg-red-100 rounded-xl p-3 text-red-600">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Stok Menipis</p>
                    <h3 class="text-2xl font-black text-red-600">{{ $lowStockProducts->count() }}</h3>
                    <p class="text-xs text-gray-400 mt-2 font-medium">
                        Perlu Restock Segera
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Main Chart -->
            <div class="lg:col-span-2 bg-white rounded-3xl shadow-lg border border-gray-100 p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-black text-gray-900">Tren Pendapatan (7 Hari Terakhir)</h3>
                </div>
                <div class="relative h-72 w-full">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- Top Customers -->
            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-black text-gray-900 mb-6">Top 5 Pelanggan Bulan Ini</h3>
                <div class="space-y-4">
                    @forelse($topCustomers as $index => $customer)
                        <div class="flex items-center gap-4">
                            <div
                                class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold text-sm">
                                #{{ $index + 1 }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-900 truncate">{{ $customer->customer_name }}</p>
                                <p class="text-xs text-gray-500">{{ number_format($customer->total_invoices) }} Transaksi
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-black text-indigo-600">Rp
                                    {{ number_format($customer->total_spend, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-400 text-sm">Belum ada data transaksi.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Invoices -->
            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                    <h3 class="text-lg font-black text-gray-900">Transaksi Terbaru</h3>
                    <a href="{{ route('invoices.index') }}"
                        class="text-xs font-bold text-indigo-600 hover:text-indigo-800">Lihat Semua</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-500">
                        <thead class="bg-gray-50/50 text-xs font-black text-gray-400 uppercase tracking-widest">
                            <tr>
                                <th class="px-6 py-4">Invoice</th>
                                <th class="px-6 py-4">Pelanggan</th>
                                <th class="px-6 py-4 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach ($recentInvoices as $invoice)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 font-bold text-indigo-600">{{ $invoice->invoice_number }}</td>
                                    <td class="px-6 py-4 font-medium text-gray-900">{{ $invoice->customer_name }}</td>
                                    <td class="px-6 py-4 text-right font-black text-gray-900">Rp
                                        {{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Low Stock Alerts -->
            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-black text-gray-900">Peringatan Stok Menipis</h3>
                    <a href="{{ route('inventory.index') }}" class="text-xs font-bold text-red-600 hover:text-red-800">Cek
                        Gudang</a>
                </div>
                <div class="space-y-3">
                    @forelse($lowStockProducts as $product)
                        <div class="flex items-center justify-between p-3 rounded-2xl bg-red-50 border border-red-100">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-box text-red-400"></i>
                                <div>
                                    <p class="text-sm font-bold text-gray-900">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $product->category->name ?? 'Uncategorized' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-white text-red-600 border border-red-200">
                                    Sisa {{ $product->stock }} {{ $product->unit }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-check-circle text-4xl text-green-200 mb-2"></i>
                            <p class="text-gray-500 text-sm">Semua stok aman!</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Pendapatan (Harian)',
                    data: {!! json_encode($chartData) !!},
                    backgroundColor: 'rgba(99, 102, 241, 0.2)', // Indigo 500
                    borderColor: 'rgba(79, 70, 229, 1)', // Indigo 600
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: 'rgba(79, 70, 229, 1)',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        titleFont: {
                            size: 13
                        },
                        bodyFont: {
                            size: 13
                        },
                        cornerRadius: 10,
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
                                        currency: 'IDR',
                                        minimumFractionDigits: 0
                                    }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#f1f5f9',
                            borderDash: [5, 5]
                        },
                        ticks: {
                            font: {
                                size: 11
                            },
                            color: '#64748b',
                            callback: function(value) {
                                return new Intl.NumberFormat('id-ID', {
                                    maximumSignificantDigits: 3
                                }).format(value);
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            },
                            color: '#64748b'
                        }
                    }
                }
            }
        });
    </script>
@endsection
