<?php

namespace App\Http\Controllers;

use App\Models\KitchenIncentive;
use App\Models\KitchenIncentiveItem;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class KitchenIncentiveController extends Controller
{
    public function index()
    {
        $invoices = KitchenIncentive::with('items')->latest()->get();
        return view('kitchen-incentives.index', compact('invoices'));
    }

    public function create()
    {
        $count = KitchenIncentive::whereDate('date', now())->count() + 1;
        $suggestedNumber = 'INV-' . now()->format('ymd') . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        $customers = \App\Models\Customer::orderBy('name')->get();
        return view('kitchen-incentives.create', compact('suggestedNumber', 'customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric',
            'items.*.unit' => 'required|string',
            'items.*.duration_text' => 'nullable|string',
            'items.*.price' => 'required|numeric',
        ]);

        // Generate Invoice Number
        // Pattern: INV-YYMMDD-XXX based on INPUT DATE, not today
        $datePart = Carbon::parse($request->date)->format('ymd');
        // Count existing for that date
        $count = KitchenIncentive::whereDate('date', $request->date)->count() + 1;
        $sequence = str_pad($count, 3, '0', STR_PAD_LEFT);
        $invoiceNumber = "INV-{$datePart}-{$sequence}";

        // Ensure uniqueness
        while (KitchenIncentive::where('invoice_number', $invoiceNumber)->exists()) {
            $count++;
            $sequence = str_pad($count, 3, '0', STR_PAD_LEFT);
            $invoiceNumber = "INV-{$datePart}-{$sequence}";
        }

        $customer = \App\Models\Customer::find($request->customer_id);

        $kitchenIncentive = KitchenIncentive::create([
            'invoice_number' => $invoiceNumber,
            'date' => $request->date,
            'customer_id' => $request->customer_id,
            'recipient_name' => $customer->name, // Keep for fallback or historical? Or rely on rel.
            'total_amount' => 0,
        ]);

        $totalAmount = 0;

        foreach ($request->items as $item) {
            $total = $item['quantity'] * $item['price'];
            $totalAmount += $total;

            KitchenIncentiveItem::create([
                'kitchen_incentive_id' => $kitchenIncentive->id,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'duration_text' => $item['duration_text'],
                'price' => $item['price'],
                'total_price' => $total,
            ]);
        }

        $kitchenIncentive->update(['total_amount' => $totalAmount]);

        return redirect()->route('kitchen-incentives.index')->with('success', 'Invoice berhasil dibuat.'); // Changed redirect to index
    }

    public function show(KitchenIncentive $kitchenIncentive)
    {
        return view('kitchen-incentives.show', compact('kitchenIncentive'));
    }

    public function edit(KitchenIncentive $kitchenIncentive)
    {
        $kitchenIncentive->load('items');
        $customers = \App\Models\Customer::orderBy('name')->get();
        return view('kitchen-incentives.edit', compact('kitchenIncentive', 'customers'));
    }

    public function update(Request $request, KitchenIncentive $kitchenIncentive)
    {
        $request->validate([
            'date' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric',
            'items.*.unit' => 'required|string',
            'items.*.duration_text' => 'nullable|string',
            'items.*.price' => 'required|numeric',
        ]);

        $customer = \App\Models\Customer::find($request->customer_id);

        $kitchenIncentive->update([
            'date' => $request->date,
            'customer_id' => $request->customer_id,
            'recipient_name' => $customer->name,
        ]);

        // Delete old items
        $kitchenIncentive->items()->delete();

        $totalAmount = 0;

        foreach ($request->items as $item) {
            $total = $item['quantity'] * $item['price'];
            $totalAmount += $total;

            KitchenIncentiveItem::create([
                'kitchen_incentive_id' => $kitchenIncentive->id,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'duration_text' => $item['duration_text'],
                'price' => $item['price'],
                'total_price' => $total,
            ]);
        }

        $kitchenIncentive->update(['total_amount' => $totalAmount]);

        return redirect()->route('kitchen-incentives.index')->with('success', 'Invoice berhasil diperbarui.');
    }

    public function exportPdf(KitchenIncentive $kitchenIncentive)
    {
        $pdf = Pdf::loadView('kitchen-incentives.show', compact('kitchenIncentive'));
        $pdf->setPaper('a4', 'portrait');
        return $pdf->download($kitchenIncentive->invoice_number . ' _ ' . ($kitchenIncentive->items->first()->duration_text ?? '') . ' _ ' . $kitchenIncentive->customer->name . '.pdf');
    }

    public function destroy(KitchenIncentive $kitchenIncentive)
    {
        $kitchenIncentive->delete();
        return back()->with('success', 'Invoice deleted successfully.');
    }
}
