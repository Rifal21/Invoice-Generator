<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InvoiceCalendarController extends Controller
{
    public function index(Request $request)
    {
        return view('invoices.calendar');
    }

    public function events(Request $request)
    {
        $start = Carbon::parse($request->start);
        $end = Carbon::parse($request->end);

        $invoices = Invoice::with('items')->whereBetween('date', [$start, $end])
            ->select('id', 'invoice_number', 'customer_name', 'total_amount', 'date')
            ->get();

        $events = $invoices->groupBy(function($inv) {
            return Carbon::parse($inv->date)->format('Y-m-d');
        })->map(function ($dayInvoices, $date) {
            $count = $dayInvoices->count();
            $total = $dayInvoices->sum('total_amount');
            
            return [
                'id' => $date, // Group ID by date
                'title' => "$count Invoice (Rp " . number_format($total, 0, ',', '.') . ")",
                'start' => $date,
                'allDay' => true,
                'extendedProps' => [
                    'invoices' => $dayInvoices->map(function($inv) {
                        return [
                            'id' => $inv->id,
                            'number' => $inv->invoice_number,
                            'customer' => $inv->customer_name,
                            'total' => "Rp " . number_format($inv->total_amount, 0, ',', '.'),
                            'url' => route('invoices.show', $inv->id),
                            'items' => $inv->items->map(function($item) {
                                return [
                                    'name' => $item->product_name,
                                    'qty' => rtrim(rtrim(number_format($item->quantity, 2, ',', '.'), '0'), ','),
                                    'unit' => $item->unit,
                                    'price' => "Rp " . number_format($item->price, 0, ',', '.'),
                                    'total' => "Rp " . number_format($item->total, 0, ',', '.')
                                ];
                            })
                        ];
                    })
                ],
                'backgroundColor' => '#4f46e5',
                'borderColor' => '#4338ca',
            ];
        })->values();

        return response()->json($events);
    }
}
