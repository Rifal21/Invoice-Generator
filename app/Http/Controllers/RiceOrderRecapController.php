<?php

namespace App\Http\Controllers;

use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RiceOrderRecapController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $items = InvoiceItem::whereHas('invoice', function ($query) use ($startDate, $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        })
            ->where(function ($query) {
                $query->where('product_name', 'like', '%Beras%')
                    ->orWhereHas('product.category', function ($q) {
                        $q->where('name', 'like', '%Beras%');
                    });
            })
            ->select(
                'product_name',
                DB::raw('SUM(quantity) as total_quantity'),
                'unit',
                DB::raw('SUM(total) as total_amount')
            )
            ->groupBy('product_name', 'unit')
            ->orderBy('total_quantity', 'desc')
            ->get();

        // Also get individual transactions for detail
        $details = InvoiceItem::with('invoice')
            ->whereHas('invoice', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            })
            ->where(function ($query) {
                $query->where('product_name', 'like', '%Beras%')
                    ->orWhereHas('product.category', function ($q) {
                        $q->where('name', 'like', '%Beras%');
                    });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('rice-orders.recap', compact('items', 'details', 'startDate', 'endDate'));
    }

    public function exportPdf(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Group by customer for the PDF
        $customerGroups = InvoiceItem::with('invoice')
            ->whereHas('invoice', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate]);
            })
            ->where(function ($query) {
                $query->where('product_name', 'like', '%Beras%')
                    ->orWhereHas('product.category', function ($q) {
                        $q->where('name', 'like', '%Beras%');
                    });
            })
            ->get()
            ->groupBy(function ($item) {
                return $item->invoice->customer_name;
            });

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('rice-orders.recap-pdf', [
            'customerGroups' => $customerGroups,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream("Rekap-Beras-{$startDate}-{$endDate}.pdf");
    }
}
