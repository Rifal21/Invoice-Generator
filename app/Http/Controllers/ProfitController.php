<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use Barryvdh\DomPDF\Facade\Pdf;

class ProfitController extends Controller
{
    private function calculateProfitData($startDate, $endDate, $search = null)
    {
        // 1. Calculate Period Totals using SQL (Fast)
        $totalsQuery = DB::table('invoice_items')
            ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->leftJoin('products', 'products.id', '=', 'invoice_items.product_id')
            ->whereBetween('invoices.date', [$startDate, $endDate]);

        if ($search) {
            $totalsQuery->where(function ($q) use ($search) {
                $q->where('invoices.invoice_number', 'like', "%{$search}%")
                    ->orWhere('invoices.customer_name', 'like', "%{$search}%");
            });
        }

        $periodTotals = $totalsQuery->selectRaw('
            SUM(invoice_items.total) as total_sales,
            SUM(
                CASE 
                    WHEN invoice_items.purchase_price > 0 THEN invoice_items.purchase_price 
                    WHEN products.purchase_price IS NOT NULL THEN products.purchase_price 
                    ELSE 0 
                END * invoice_items.quantity
            ) as total_hpp
        ')->first();

        $totalSales = $periodTotals->total_sales ?? 0;
        $totalHpp = $periodTotals->total_hpp ?? 0;
        $totalProfit = $totalSales - $totalHpp;

        // 2. Fetch Paginated Invoices (Efficient)
        $query = Invoice::whereBetween('date', [$startDate, $endDate]);
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        // Use pagination to avoid loading everything at once
        $invoices = $query->with(['items.product'])->orderBy('date', 'desc')->paginate(25)->withQueryString();

        // 3. Enrich current page invoices with calculated profit data
        foreach ($invoices as $invoice) {
            $invoiceSales = 0;
            $invoiceHpp = 0;

            foreach ($invoice->items as $item) {
                $invoiceSales += $item->total;
                $hppPerUnit = $item->purchase_price > 0 ? $item->purchase_price : ($item->product ? $item->product->purchase_price : 0);
                $invoiceHpp += ($hppPerUnit * $item->quantity);
            }

            $invoice->sales = $invoiceSales;
            $invoice->hpp = $invoiceHpp;
            $invoice->profit = $invoiceSales - $invoiceHpp;
        }

        return compact('invoices', 'totalSales', 'totalHpp', 'totalProfit');
    }

    public function exportAllPdf(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        $search = $request->input('search');

        $data = $this->calculateProfitData($startDate, $endDate, $search);
        $data['startDate'] = $startDate;
        $data['endDate'] = $endDate;

        $pdf = Pdf::loadView('profit.pdf-all', $data);
        return $pdf->stream('Laporan_Laba_Rugi_' . $startDate . '_to_' . $endDate . '.pdf');
    }

    public function exportInvoicePdf($id)
    {
        $invoice = Invoice::with(['items.product'])->findOrFail($id);

        $invoiceSales = 0;
        $invoiceHpp = 0;

        foreach ($invoice->items as $item) {
            $invoiceSales += $item->total;
            $hppPerUnit = $item->purchase_price > 0 ? $item->purchase_price : ($item->product ? $item->product->purchase_price : 0);
            $invoiceHpp += ($hppPerUnit * $item->quantity);

            // Add calculated values to each item for display
            $item->hpp_per_unit = $hppPerUnit;
            $item->total_hpp = $hppPerUnit * $item->quantity;
        }

        $invoice->sales = $invoiceSales;
        $invoice->hpp = $invoiceHpp;
        $invoice->profit = $invoiceSales - $invoiceHpp;

        $pdf = Pdf::loadView('profit.pdf-invoice', compact('invoice'));
        return $pdf->stream('Laba_Rugi_' . $invoice->invoice_number . '.pdf');
    }
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());
        $search = $request->input('search');

        $data = $this->calculateProfitData($startDate, $endDate, $search);
        $data['startDate'] = $startDate;
        $data['endDate'] = $endDate;

        return view('profit.index', $data);
    }
}
