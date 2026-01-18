<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProfitController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $query = Invoice::whereBetween('date', [$startDate, $endDate]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        $invoices = $query->with(['items.product'])->orderBy('date', 'desc')->get();

        $totalSales = 0;
        $totalHpp = 0;

        foreach ($invoices as $invoice) {
            $invoiceSales = 0;
            $invoiceHpp = 0;

            foreach ($invoice->items as $item) {
                $invoiceSales += $item->total;
                // Use purchase_price from invoice item, fall back to product if not set
                $hppPerUnit = $item->purchase_price > 0 ? $item->purchase_price : ($item->product ? $item->product->purchase_price : 0);
                $invoiceHpp += ($hppPerUnit * $item->quantity);
            }

            $invoice->sales = $invoiceSales;
            $invoice->hpp = $invoiceHpp;
            $invoice->profit = $invoiceSales - $invoiceHpp;

            $totalSales += $invoiceSales;
            $totalHpp += $invoiceHpp;
        }

        $totalProfit = $totalSales - $totalHpp;

        return view('profit.index', compact(
            'invoices',
            'totalSales',
            'totalHpp',
            'totalProfit',
            'startDate',
            'endDate'
        ));
    }
}
