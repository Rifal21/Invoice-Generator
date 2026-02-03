<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceRecapController extends Controller
{
    /**
     * Get monthly invoice recap with full details
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMonthlyRecap(Request $request)
    {
        // 1. Determine Month & Year (Default to current if not provided)
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        // Security: Basic Token Check (or implement Sanctum properly later)
        // For now, let's keep it open or require a simple key in header if desired
        // But since n8n will access it, we will return standard JSON.

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();

        // 2. Fetch Invoices with Relationships
        $invoices = Invoice::with(['items', 'items.product'])
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'asc')
            ->get();

        // 3. Calculate Summary
        $summary = [
            'period' => $startDate->format('F Y'),
            'month' => $month,
            'year' => $year,
            'total_invoices' => $invoices->count(),
            'total_revenue' => (float) $invoices->sum('total_amount'),
            'total_items_sold' => (float) $invoices->sum(function ($invoice) {
                return $invoice->items->sum('quantity');
            }),
            'generated_at' => Carbon::now()->toIso8601String(),
        ];

        // 4. Format Data for n8n
        // n8n often prefers "flat" arrays for table processing, but hierarchical is better for Invoice detail PDF.
        // We will provide a structured response.

        return response()->json([
            'success' => true,
            'summary' => $summary,
            'data' => $invoices->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    // Parse date carefully
                    'date' => Carbon::parse($invoice->date)->format('Y-m-d'),
                    'customer_name' => $invoice->customer_name,
                    'total_amount' => (float) $invoice->total_amount,
                    'discount' => (float) $invoice->discount,
                    'items_count' => $invoice->items->count(),
                    'items' => $invoice->items->map(function ($item) {
                        return [
                            'product_name' => $item->product_name,
                            'quantity' => (float) $item->quantity,
                            'unit' => $item->unit,
                            'price' => (float) $item->price,
                            'total' => (float) $item->total,
                        ];
                    }),
                ];
            }),
        ]);
    }
}
