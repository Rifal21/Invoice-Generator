<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\StockHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->where('stock', '>', 0)->get();
        $categories = \App\Models\Category::all();
        return view('pos.index', compact('products', 'categories'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string',
            'order_type' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        return DB::transaction(function () use ($request) {
            $totalAmount = 0;

            // Generate invoice number - Using the selected type code
            $typeCode = $request->order_type; // e.g., BSH, KR, KRBSBM, OPR
            $invoice_number = 'INV-' . date('Ymd') . '-' . str_pad(Invoice::max('id') + 1, 3, '0', STR_PAD_LEFT) . '-' . $typeCode;

            $invoice = Invoice::create([
                'invoice_number' => $invoice_number,
                'date' => Carbon::now(),
                'customer_name' => $request->customer_name,
                'total_amount' => 0, // Update later
            ]);

            foreach ($request->items as $item) {
                $product = Product::lockForUpdate()->find($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Stok tidak mencukupi untuk produk: {$product->name}");
                }

                $itemTotal = $product->price * $item['quantity'];
                $totalAmount += $itemTotal;

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'unit' => $product->unit,
                    'price' => $product->price,
                    'purchase_price' => $product->purchase_price ?? 0,
                    'total' => $itemTotal,
                ]);

                // Update stock
                $product->decrement('stock', $item['quantity']);

                // Record history
                StockHistory::create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'type' => 'out',
                    'reference' => $invoice_number,
                    'description' => 'Penjualan POS',
                ]);
            }

            $invoice->update(['total_amount' => $totalAmount]);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil.',
                'invoice_id' => $invoice->id,
                'redirect' => route('invoices.show', $invoice->id)
            ]);
        });
    }
}
