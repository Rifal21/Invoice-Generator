<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Expense;
use App\Models\Salary;
use App\Models\Product;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        // 1. Calculate Sales and Gross Profit (HPP)
        $invoices = Invoice::whereBetween('date', [$startDate, $endDate])
            ->with(['items.product'])
            ->get();

        $totalSales = 0;
        $totalHpp = 0;

        foreach ($invoices as $invoice) {
            foreach ($invoice->items as $item) {
                $totalSales += $item->total;
                $hppPerUnit = $item->purchase_price > 0 ? $item->purchase_price : ($item->product ? $item->product->purchase_price : 0);
                $totalHpp += ($hppPerUnit * $item->quantity);
            }
        }

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
        // Note: Assets are not bound by date, but by current state
        $totalAssetValue = Product::select(DB::raw('SUM(stock * purchase_price) as total_value'))->first()->total_value ?? 0;
        $totalStockCount = Product::sum('stock');

        // Categorized Expenses for Chart/Summary
        $expensesByCategory = Expense::whereBetween('date', [$startDate, $endDate])
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->get();

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
            'endDate'
        ));
    }
}
