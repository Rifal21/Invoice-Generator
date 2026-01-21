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
        $query = Invoice::whereBetween('date', [$startDate, $endDate]);

        if ($search) {
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
