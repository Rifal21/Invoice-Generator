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

        // 4. Top 5 Produk Terlaris Bulan Ini
        $topProducts = InvoiceItem::join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
            ->whereBetween('invoices.date', [$startOfMonth, $endOfMonth])
            ->select('product_name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(invoice_items.total) as total_revenue'))
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
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
            'topProducts',
            'recentInvoices'
        ));
    }
}
