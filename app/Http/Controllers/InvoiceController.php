<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoices = Invoice::all();
        return view('invoices.index', compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::all();
        return view('invoices.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'customer_name' => 'required|string',
            'items' => 'required|array',
            'items.*.product_id' => 'required',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.unit' => 'required|string',
        ]);

        $invoice_number = 'INV-' . Carbon::parse($request->date)->format('Ymd') . '-' . str_pad(Invoice::max('id') + 1, 3, '0', STR_PAD_LEFT);
        $invoice = Invoice::create([
            'invoice_number' => $invoice_number,
            'date' => $request->date,
            'customer_name' => $request->customer_name,
            'total_amount' => 0, // Will calculate below
        ]);

        $totalAmount = 0;

        foreach ($request->items as $item) {
            $productId = $item['product_id'];
            $product = null;

            if (is_numeric($productId)) {
                $product = Product::find($productId);
            }

            if (!$product) {
                // Create new product
                $category = \App\Models\Category::firstOrCreate(['name' => 'Lain-lain']);
                $product = Product::create([
                    'category_id' => $category->id,
                    'name' => $productId, // The name was passed as product_id (from Select2 tags)
                    'price' => $item['price'],
                    'unit' => $item['unit'],
                ]);
            }

            $total = $item['price'] * $item['quantity'];
            $totalAmount += $total;

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'price' => $item['price'],
                'total' => $total,
            ]);
        }

        $invoice->update(['total_amount' => $totalAmount]);

        return redirect()->route('invoices.index')->with('success', 'Invoice created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        return view('invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        $products = Product::all();
        return view('invoices.edit', compact('invoice', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'date' => 'required|date',
            'customer_name' => 'required|string',
            'items' => 'required|array',
            'items.*.product_id' => 'required',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.unit' => 'required|string',
        ]);

        $invoice->update([
            'date' => $request->date,
            'customer_name' => $request->customer_name,
        ]);

        // Delete existing items
        $invoice->items()->delete();

        $totalAmount = 0;

        foreach ($request->items as $item) {
            $productId = $item['product_id'];
            $product = null;

            if (is_numeric($productId)) {
                $product = Product::find($productId);
            }

            if (!$product) {
                // Create new product
                $category = \App\Models\Category::firstOrCreate(['name' => 'Lain-lain']);
                $product = Product::create([
                    'category_id' => $category->id,
                    'name' => $productId,
                    'price' => $item['price'],
                    'unit' => $item['unit'],
                ]);
            }

            $total = $item['price'] * $item['quantity'];
            $totalAmount += $total;

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'price' => $item['price'],
                'total' => $total,
            ]);
        }

        $invoice->update(['total_amount' => $totalAmount]);

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully.');
    }
    public function exportPdf(Invoice $invoice)
    {
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        return $pdf->download($invoice->invoice_number . '.pdf');
    }

    public function exportExcel(Invoice $invoice)
    {
        return Excel::download(new \App\Exports\InvoiceExport($invoice), $invoice->invoice_number . '.xlsx');
    }
}
