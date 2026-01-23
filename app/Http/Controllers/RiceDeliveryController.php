<?php

namespace App\Http\Controllers;

use App\Models\RiceDelivery;
use App\Models\RiceDeliveryItem;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class RiceDeliveryController extends Controller
{
    public function index(Request $request)
    {
        $query = RiceDelivery::query();

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nota_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        $deliveries = $query->orderBy('date', 'desc')->orderBy('nota_number', 'desc')->paginate(10)->withQueryString();

        return view('rice-deliveries.index', compact('deliveries'));
    }

    public function create()
    {
        return view('rice-deliveries.create');
    }

    public function getNextNumber(Request $request)
    {
        $date = $request->get('date', date('Y-m-d'));
        $datePrefix = Carbon::parse($date)->format('Ymd');

        $count = RiceDelivery::whereDate('date', $date)->count();
        $nextNumber = 'NB-' . $datePrefix . '-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);

        return response()->json(['nota_number' => $nextNumber]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nota_number' => 'required|string|unique:rice_deliveries',
            'location' => 'required|string',
            'date' => 'required|date',
            'customer_name' => 'required|string',
            'items' => 'required|array',
            'items.*.quantity_string' => 'required|string',
            'items.*.description' => 'required|string',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $delivery = RiceDelivery::create([
            'nota_number' => $request->nota_number,
            'location' => $request->location,
            'date' => $request->date,
            'customer_name' => $request->customer_name,
            'total_amount' => 0,
        ]);

        $totalAmount = 0;

        foreach ($request->items as $item) {
            // Price is for the total quantity string usually in this type of nota? 
            // In the image: 800 kg @14,100 = 11,280,000. So quantity numeric is needed for calculation.
            // Let's assume price is per unit of the quantity string.
            // I'll extract number from quantity_string if possible, or just add a separate quantity field.
            // For now, I'll calculate total based on a hidden unit_price and quantity_numeric if I add them, 
            // but the user asked for "template seperti itu".
            // I'll add quantity_numeric to migration to make it easier to calculate.

            // Re-thinking: I'll just keep it simple and let user input the numeric parts.
            $qty_num = (float) filter_var($item['quantity_string'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $total = $item['price'] * $qty_num;
            $totalAmount += $total;

            RiceDeliveryItem::create([
                'rice_delivery_id' => $delivery->id,
                'quantity_string' => $item['quantity_string'],
                'description' => $item['description'],
                'price' => $item['price'],
                'total' => $total,
            ]);
        }

        $delivery->update(['total_amount' => $totalAmount]);

        return redirect()->route('rice-deliveries.index')->with('success', 'Nota pengiriman beras berhasil dibuat.');
    }

    public function show(RiceDelivery $riceDelivery)
    {
        $riceDelivery->load('items');
        return view('rice-deliveries.show', compact('riceDelivery'));
    }

    public function edit(RiceDelivery $riceDelivery)
    {
        $riceDelivery->load('items');
        return view('rice-deliveries.edit', compact('riceDelivery'));
    }

    public function update(Request $request, RiceDelivery $riceDelivery)
    {
        $request->validate([
            'nota_number' => 'required|string|unique:rice_deliveries,nota_number,' . $riceDelivery->id,
            'location' => 'required|string',
            'date' => 'required|date',
            'customer_name' => 'required|string',
            'items' => 'required|array',
            'items.*.quantity_string' => 'required|string',
            'items.*.description' => 'required|string',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $riceDelivery->update([
            'nota_number' => $request->nota_number,
            'location' => $request->location,
            'date' => $request->date,
            'customer_name' => $request->customer_name,
        ]);

        $riceDelivery->items()->delete();

        $totalAmount = 0;

        foreach ($request->items as $item) {
            $qty_num = (float) filter_var($item['quantity_string'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $total = $item['price'] * $qty_num;
            $totalAmount += $total;

            RiceDeliveryItem::create([
                'rice_delivery_id' => $riceDelivery->id,
                'quantity_string' => $item['quantity_string'],
                'description' => $item['description'],
                'price' => $item['price'],
                'total' => $total,
            ]);
        }

        $riceDelivery->update(['total_amount' => $totalAmount]);

        return redirect()->route('rice-deliveries.index')->with('success', 'Nota pengiriman beras berhasil diperbarui.');
    }

    public function destroy(RiceDelivery $riceDelivery)
    {
        $riceDelivery->delete();
        return back()->with('success', 'Nota pengiriman beras berhasil dihapus.');
    }

    public function exportPdf(RiceDelivery $riceDelivery)
    {
        $riceDelivery->load('items');
        $pdf = Pdf::loadView('rice-deliveries.pdf', ['delivery' => $riceDelivery]);
        return $pdf->stream('Nota ' . $riceDelivery->nota_number . '.pdf');
    }
}
