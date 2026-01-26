<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Expense;
use App\Models\Salary;
use App\Models\Product;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use Barryvdh\DomPDF\Facade\Pdf;

class FinancialReportController extends Controller
{
    private function getFinancialData($startDate, $endDate)
    {
        // 1. Calculate Sales and Gross Profit (HPP) using specialized SQL aggregations
        // Use join to directly sum data in the database
        $salesData = DB::table('invoice_items')
            ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->leftJoin('products', 'products.id', '=', 'invoice_items.product_id')
            ->whereBetween('invoices.date', [$startDate, $endDate])
            ->selectRaw('
                SUM(invoice_items.total) as total_sales,
                SUM(
                    CASE 
                        WHEN invoice_items.purchase_price > 0 THEN invoice_items.purchase_price 
                        WHEN products.purchase_price IS NOT NULL THEN products.purchase_price 
                        ELSE 0 
                    END * invoice_items.quantity
                ) as total_hpp
            ')
            ->first();

        $totalSales = $salesData->total_sales ?? 0;
        $totalHpp = $salesData->total_hpp ?? 0;
        $grossProfit = $totalSales - $totalHpp;

        // 2. Total Salaries
        $totalSalaries = Salary::whereBetween('start_date', [$startDate, $endDate])
            ->orWhereBetween('end_date', [$startDate, $endDate])
            ->sum('net_salary');

        // 3. Total Expenses (Operational)
        $totalOperationalExpenses = Expense::whereBetween('date', [$startDate, $endDate])->sum('amount');

        // 4. Net Profit
        $netProfit = $grossProfit - $totalSalaries - $totalOperationalExpenses;

        // 5. Assets (Current Stock Value)
        $totalAssetValue = Product::select(DB::raw('SUM(stock * purchase_price) as total_value'))->first()->total_value ?? 0;
        $totalStockCount = Product::sum('stock');

        // Categorized Expenses
        $expensesByCategory = Expense::whereBetween('date', [$startDate, $endDate])
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->get();

        return compact(
            'totalSales',
            'totalHpp',
            'grossProfit',
            'totalSalaries',
            'totalOperationalExpenses',
            'netProfit',
            'totalAssetValue',
            'totalStockCount',
            'expensesByCategory',
            'startDate',
            'endDate'
        );
    }

    public function exportPdf(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $data = $this->getFinancialData($startDate, $endDate);

        $pdf = Pdf::loadView('finance.pdf', $data);
        return $pdf->stream('Laporan_Keuangan_' . $startDate . '_to_' . $endDate . '.pdf');
    }

    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $data = $this->getFinancialData($startDate, $endDate);
        extract($data); // Extract variables for chart logic below

        // Chart Data: Daily Sales vs Expenses
        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
        $chartLabels = [];
        $chartSales = [];
        $chartExpenses = [];

        // Optimize: Group by date directly
        $salesByDate = Invoice::whereBetween('date', [$startDate, $endDate])
            ->selectRaw('date, SUM(total_amount) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $expensesByDate = Expense::whereBetween('date', [$startDate, $endDate])
            ->selectRaw('date, SUM(amount) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        foreach ($period as $date) {
            $dateString = $date->toDateString();
            // Format: 01 Jan
            $chartLabels[] = $date->format('d M');
            $chartSales[] = $salesByDate[$dateString] ?? 0;
            $chartExpenses[] = $expensesByDate[$dateString] ?? 0;
        }

        return view('finance.summary', compact(
            'totalSales',
            'totalHpp',
            'grossProfit',
            'totalSalaries',
            'totalOperationalExpenses',
            'netProfit',
            'totalAssetValue',
            'totalStockCount',
            'expensesByCategory',
            'startDate',
            'endDate',
            'chartLabels',
            'chartSales',
            'chartExpenses'
        ));
    }
}
