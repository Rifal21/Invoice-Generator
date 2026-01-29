<?php

namespace App\Http\Controllers;

use App\Models\DediInvoice;
use App\Models\DediInvoiceItem;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class DediInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = DediInvoice::with('items');

        if ($request->filled('search')) {
            $query->where('invoice_number', 'like', '%' . $request->search . '%')
                ->orWhere('customer_name', 'like', '%' . $request->search . '%');
        }

        $invoices = $query->latest()->paginate(10);
        return view('dedi_invoices.index', compact('invoices'));
    }

    public function create()
    {
        // Generate next invoice number? OR let user input?
        // Pattern: NOTA 015. simple increment?
        // Let's assume auto-generated for convenience but editable.
        // Get max ID
        $nextId = DediInvoice::max('id') + 1;
        $suggestedNumber = 'NOTA ' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

        return view('dedi_invoices.create', compact('suggestedNumber'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'customer_name' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit' => 'required|string',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $nextId = DediInvoice::max('id') + 1;
        $invoiceNumber = 'NOTA ' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

        $invoice = DediInvoice::create([
            'invoice_number' => $invoiceNumber,
            'date' => $request->date,
            'customer_name' => $request->customer_name,
            'total_amount' => 0,
        ]);

        $totalAmount = 0;

        foreach ($request->items as $item) {
            $total = $item['quantity'] * $item['price'];
            $totalAmount += $total;

            DediInvoiceItem::create([
                'dedi_invoice_id' => $invoice->id,
                'item_name' => $item['item_name'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'price' => $item['price'],
                'total_price' => $total,
            ]);
        }

        $invoice->update(['total_amount' => $totalAmount]);

        return redirect()->route('dedi-invoices.index')->with('success', 'Nota Faktur H Dedi berhasil dibuat.');
    }

    public function show(DediInvoice $dedi_invoice)
    {
        return view('dedi_invoices.show', compact('dedi_invoice'));
    }

    public function edit(DediInvoice $dedi_invoice)
    {
        $dedi_invoice->load('items');
        return view('dedi_invoices.edit', compact('dedi_invoice'));
    }

    public function update(Request $request, DediInvoice $dedi_invoice)
    {
        $request->validate([
            'invoice_number' => 'required|string|unique:dedi_invoices,invoice_number,' . $dedi_invoice->id,
            'date' => 'required|date',
            'customer_name' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit' => 'required|string',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $dedi_invoice->update([
            'invoice_number' => $request->invoice_number,
            'date' => $request->date,
            'customer_name' => $request->customer_name,
        ]);

        $dedi_invoice->items()->delete();

        $totalAmount = 0;
        foreach ($request->items as $item) {
            $total = $item['quantity'] * $item['price'];
            $totalAmount += $total;

            DediInvoiceItem::create([
                'dedi_invoice_id' => $dedi_invoice->id,
                'item_name' => $item['item_name'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'price' => $item['price'],
                'total_price' => $total,
            ]);
        }

        $dedi_invoice->update(['total_amount' => $totalAmount]);

        return redirect()->route('dedi-invoices.index')->with('success', 'Nota updated successfully.');
    }

    public function destroy(DediInvoice $dedi_invoice)
    {
        $dedi_invoice->delete();
        return redirect()->route('dedi-invoices.index')->with('success', 'Nota deleted successfully.');
    }

    public function exportPdf(DediInvoice $dedi_invoice)
    {
        $pdf = Pdf::loadView('dedi_invoices.pdf', compact('dedi_invoice'));
        // Set paper size if needed. Invoice implies maybe A4 or custom. A4 is safe.
        $pdf->setPaper('a4', 'portrait');
        return $pdf->stream('Nota_H_Dedi_' . $dedi_invoice->invoice_number . '.pdf');
    }
}
