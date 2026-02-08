<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Ringkasan Hari Ini
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $todayRevenue = Invoice::whereDate('date', $today)->sum('total_amount');
        $monthRevenue = Invoice::whereBetween('date', [$startOfMonth, $endOfMonth])->sum('total_amount');
        $todayInvoices = Invoice::whereDate('date', $today)->count();
        $totalProducts = Product::count();

        // 2. Grafik Pendapatan 7 Hari Terakhir
        $chartData = [];
        $chartLabels = [];
        $date = Carbon::today()->subDays(6); // Start 6 days ago

        for ($i = 0; $i < 7; $i++) {
            $dayRevenue = Invoice::whereDate('date', $date)->sum('total_amount');
            $chartLabels[] = $date->format('d M');
            $chartData[] = $dayRevenue;
            $date->addDay();
        }

        // 3. Produk Stok Menipis (Limit 5)
        $lowStockProducts = Product::where('stock', '<=', 10)
            ->where('stock', '>', 0) // Optional: exclude out of stock if wanted, or keep them
            ->orderBy('stock', 'asc')
            ->limit(5)
            ->get();

        // 4. Top 5 Pelanggan Berdasarkan Pembelanjaan Bulan Ini
        $topCustomers = Invoice::whereBetween('date', [$startOfMonth, $endOfMonth])
            ->select('customer_name', DB::raw('COUNT(*) as total_invoices'), DB::raw('SUM(total_amount) as total_spend'))
            ->groupBy('customer_name')
            ->orderByDesc('total_spend')
            ->limit(5)
            ->get();

        // 5. Invoice Terbaru (Limit 5)
        $recentInvoices = Invoice::orderBy('created_at', 'desc')->limit(5)->get();

        return view('dashboard.index', compact(
            'todayRevenue',
            'monthRevenue',
            'todayInvoices',
            'totalProducts',
            'chartLabels',
            'chartData',
            'lowStockProducts',
            'topCustomers',
            'recentInvoices'
        ));
    }
}
