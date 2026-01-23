<?php

namespace App\Http\Controllers;

use App\Models\VehicleRentalInvoice;
use App\Models\VehicleRentalItem;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class VehicleRentalInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = VehicleRentalInvoice::query();

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        $invoices = $query->orderBy('date', 'desc')->orderBy('invoice_number', 'desc')->paginate(10)->withQueryString();

        return view('vehicle-rentals.index', compact('invoices'));
    }

    public function create()
    {
        return view('vehicle-rentals.create');
    }

    public function getNextNumber(Request $request)
    {
        $date = $request->get('date', date('Y-m-d'));
        $carbonDate = Carbon::parse($date);
        $datePrefix = $carbonDate->format('dmy');

        $lastInvoice = VehicleRentalInvoice::where('invoice_number', 'like', $datePrefix . '-%')
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastSequence = (int) substr($lastInvoice->invoice_number, -3);
            $newSequence = str_pad($lastSequence + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newSequence = '001';
        }

        return response()->json(['invoice_number' => $datePrefix . '-' . $newSequence]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'customer_name' => 'required|string',
            'items' => 'required|array',
            'items.*.description' => 'required|string',
            'items.*.start_date' => 'nullable|date',
            'items.*.end_date' => 'nullable|date',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit' => 'required|string',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $carbonDate = Carbon::parse($request->date);
        $datePrefix = $carbonDate->format('dmy');
        $lastInvoice = VehicleRentalInvoice::where('invoice_number', 'like', $datePrefix . '-%')
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastSequence = (int) substr($lastInvoice->invoice_number, -3);
            $newSequence = str_pad($lastSequence + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newSequence = '001';
        }
        $invoice_number = $datePrefix . '-' . $newSequence;

        $invoice = VehicleRentalInvoice::create([
            'invoice_number' => $invoice_number,
            'date' => $request->date,
            'customer_name' => $request->customer_name,
            'total_amount' => 0,
        ]);

        $totalAmount = 0;

        foreach ($request->items as $item) {
            $total = $item['price'] * $item['quantity'];
            $totalAmount += $total;

            VehicleRentalItem::create([
                'vehicle_rental_invoice_id' => $invoice->id,
                'description' => $item['description'],
                'start_date' => $item['start_date'] ?? null,
                'end_date' => $item['end_date'] ?? null,
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'price' => $item['price'],
                'total' => $total,
            ]);
        }

        $invoice->update(['total_amount' => $totalAmount]);

        return redirect()->route('vehicle-rentals.index')->with('success', 'Invoice sewa kendaraan berhasil dibuat.');
    }

    public function show(VehicleRentalInvoice $vehicleRental)
    {
        $vehicleRental->load('items');
        return view('vehicle-rentals.show', compact('vehicleRental'));
    }

    public function edit(VehicleRentalInvoice $vehicleRental)
    {
        $vehicleRental->load('items');
        return view('vehicle-rentals.edit', compact('vehicleRental'));
    }

    public function update(Request $request, VehicleRentalInvoice $vehicleRental)
    {
        $request->validate([
            'date' => 'required|date',
            'customer_name' => 'required|string',
            'items' => 'required|array',
            'items.*.description' => 'required|string',
            'items.*.start_date' => 'nullable|date',
            'items.*.end_date' => 'nullable|date',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit' => 'required|string',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $vehicleRental->update([
            'date' => $request->date,
            'customer_name' => $request->customer_name,
        ]);

        $vehicleRental->items()->delete();

        $totalAmount = 0;

        foreach ($request->items as $item) {
            $total = $item['price'] * $item['quantity'];
            $totalAmount += $total;

            VehicleRentalItem::create([
                'vehicle_rental_invoice_id' => $vehicleRental->id,
                'description' => $item['description'],
                'start_date' => $item['start_date'] ?? null,
                'end_date' => $item['end_date'] ?? null,
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'price' => $item['price'],
                'total' => $total,
            ]);
        }

        $vehicleRental->update(['total_amount' => $totalAmount]);

        return redirect()->route('vehicle-rentals.index')->with('success', 'Invoice sewa kendaraan berhasil diperbarui.');
    }

    public function destroy(VehicleRentalInvoice $vehicleRental)
    {
        $vehicleRental->delete();
        return back()->with('success', 'Invoice sewa kendaraan berhasil dihapus.');
    }

    public function exportPdf(VehicleRentalInvoice $vehicleRental)
    {
        $vehicleRental->load('items');
        $invoice = $vehicleRental; // For compatibility with the template
        $pdf = Pdf::loadView('vehicle-rentals.pdf', compact('invoice'));
        return $pdf->stream($invoice->invoice_number . ' - Sewa Kendaraan.pdf');
    }
}
