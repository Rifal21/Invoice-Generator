<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Invoice::query();

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

        if ($request->filled('customer')) {
            $query->where('customer_name', $request->customer);
        }

        // Sorting
        $sortField = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSortFields = ['invoice_number', 'date', 'customer_name', 'total_amount', 'created_at'];

        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'created_at';
        }

        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        $query->orderBy($sortField, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 10);
        if ($perPage === 'all') {
            $invoices = $query->with('items')->get();
        } else {
            $invoices = $query->with('items')->paginate(is_numeric($perPage) ? $perPage : 10)->withQueryString();
        }

        $customers = \App\Models\Customer::orderBy('name')->pluck('name');

        // Calculate total amount of filtered results
        $totalAmountFiltered = $query->sum('total_amount');
        // Total on current page
        $totalAmountPage = $invoices->sum('total_amount');

        return view('invoices.index', compact('invoices', 'customers', 'totalAmountFiltered', 'totalAmountPage'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::with('supplier')->get();
        $customers = \App\Models\Customer::all();
        return view('invoices.create', compact('products', 'customers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'customer_name' => 'required|string',
            'tipe' => 'required|string',
            'items' => 'required|array',
            'items.*.product_id' => 'required',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.purchase_price' => 'nullable|numeric|min:0',
            'items.*.unit' => 'required|string',
            'items.*.description' => 'nullable|string',
        ]);
        $invoice_number = 'INV-' . Carbon::parse($request->date)->format('Ymd') . '-' . str_pad(Invoice::max('id') + 1, 3, '0', STR_PAD_LEFT) . '-' . $request->tipe;
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
                    'purchase_price' => $item['purchase_price'] ?? 0,
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
                'purchase_price' => $item['purchase_price'] ?? 0,
                'total' => $total,
                'description' => $item['description'] ?? null,
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
        $products = Product::with('supplier')->get();
        $customers = \App\Models\Customer::all();
        return view('invoices.edit', compact('invoice', 'products', 'customers'));
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
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.purchase_price' => 'nullable|numeric|min:0',
            'items.*.unit' => 'required|string',
            'items.*.description' => 'nullable|string',
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
                    'purchase_price' => $item['purchase_price'] ?? 0,
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
                'purchase_price' => $item['purchase_price'] ?? 0,
                'total' => $total,
                'description' => $item['description'] ?? null,
            ]);
        }

        $invoice->update(['total_amount' => $totalAmount]);

        $filters = $request->input('filters', []);
        return redirect()->route('invoices.index', $filters)->with('success', 'Invoice updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return back()->with('success', 'Invoice deleted successfully.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'invoice_ids' => 'required|array',
            'invoice_ids.*' => 'exists:invoices,id'
        ]);

        Invoice::whereIn('id', $request->invoice_ids)->delete();

        return back()->with('success', 'Invoices deleted successfully.');
    }
    public function exportPdf(Invoice $invoice)
    {
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        return $pdf->download($invoice->invoice_number . ' - ' . $invoice->customer_name . '.pdf');
    }

    public function printMultiPdf(Request $request)
    {
        if (!$request->has('invoice_ids')) {
            return redirect()->route('invoices.index')->with('error', 'Silakan pilih minimal satu invoice.');
        }

        $request->validate([
            'invoice_ids' => 'required|array',
            'invoice_ids.*' => 'exists:invoices,id'
        ]);

        $invoices = Invoice::with('items')->whereIn('id', $request->invoice_ids)
            ->orderBy('date', 'asc')
            ->orderBy('invoice_number', 'asc')
            ->get();

        $pdf = Pdf::loadView('invoices.multi-pdf', compact('invoices'));
        $fileName = 'Gabungan invoice ' . $invoices->first()->customer_name . ' - ' . now()->format('d-m-Y') . '.pdf';
        return $pdf->download($fileName);
    }

    public function bulkExportPdf(Request $request)
    {
        if (!$request->has('invoice_ids')) {
            return redirect()->route('invoices.index')->with('error', 'Silakan pilih minimal satu invoice.');
        }

        $request->validate([
            'invoice_ids' => 'required|array',
            'invoice_ids.*' => 'exists:invoices,id'
        ]);

        $invoices = Invoice::with('items')->whereIn('id', $request->invoice_ids)->get();

        // Group and sum items by type and product name
        $groupedItems = [];
        foreach ($invoices as $invoice) {
            $type = 'LAINNYA';
            if (str_contains($invoice->invoice_number, 'BSH')) $type = 'BASAHAN SISWA';
            elseif (str_contains($invoice->invoice_number, 'KRBSBM')) $type = 'KERINGAN BUMIL BUSUI';
            elseif (str_contains($invoice->invoice_number, 'KR')) $type = 'KERINGAN SISWA';
            elseif (str_contains($invoice->invoice_number, 'OPR')) $type = 'OPERASIONAL';

            foreach ($invoice->items as $item) {
                if (!isset($groupedItems[$type])) {
                    $groupedItems[$type] = [];
                }

                $productKey = $item->product_name . '|' . $item->unit . '|' . $item->description;
                if (!isset($groupedItems[$type][$productKey])) {
                    $groupedItems[$type][$productKey] = [
                        'product_name' => $item->product_name,
                        'quantity' => 0,
                        'unit' => $item->unit,
                        'description' => $item->description,
                        'product' => $item->product
                    ];
                }

                $groupedItems[$type][$productKey]['quantity'] += $item->quantity;
            }
        }

        // Convert to objects for easier access in blade
        foreach ($groupedItems as $type => $products) {
            $groupedItems[$type] = array_map(function ($p) {
                return (object) $p;
            }, array_values($products));
        }

        // Sort grouped items by preferred order
        $order = [
            'BASAHAN SISWA' => 1,
            'KERINGAN SISWA' => 2,
            'KERINGAN BUMIL BUSUI' => 3,
            'OPERASIONAL' => 4,
            'LAINNYA' => 5
        ];

        uksort($groupedItems, function ($a, $b) use ($order) {
            $posA = $order[$a] ?? 99;
            $posB = $order[$b] ?? 99;
            return $posA <=> $posB;
        });

        $pdf = Pdf::loadView('invoices.bulk-pdf', compact('groupedItems', 'invoices'));
        return $pdf->download('Laporan_Pemeriksaan_Bahan_Makanan_' . $invoices->first()->customer_name . '_' . $invoices->first()->created_at->format('d F Y') . '.pdf');
    }

    public function exportExcel(Invoice $invoice)
    {
        return Excel::download(new \App\Exports\InvoiceExport($invoice), $invoice->invoice_number . '.xlsx');
    }

    public function sendToTelegram(Request $request)
    {
        $request->validate([
            'invoice_ids' => 'required|array',
            'invoice_ids.*' => 'exists:invoices,id'
        ]);

        $token = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

        if (!$token || !$chatId) {
            return back()->with('error', 'Konfigurasi Telegram (Token atau Chat ID) belum diatur di .env');
        }

        $invoices = Invoice::with(['items.product'])->whereIn('id', $request->invoice_ids)->get();
        $successCount = 0;
        $errorCount = 0;

        foreach ($invoices as $invoice) {
            try {
                // 1. Persiapkan Data Laba Rugi (Sama seperti di ProfitController)
                $invoiceSales = 0;
                $invoiceHpp = 0;

                foreach ($invoice->items as $item) {
                    $invoiceSales += $item->total;
                    $hppPerUnit = $item->purchase_price > 0 ? $item->purchase_price : ($item->product ? $item->product->purchase_price : 0);
                    $invoiceHpp += ($hppPerUnit * $item->quantity);

                    // Tambahkan data ke item untuk ditampilkan di PDF Profit
                    $item->hpp_per_unit = $hppPerUnit;
                    $item->total_hpp = $hppPerUnit * $item->quantity;
                }

                $invoice->sales = $invoiceSales;
                $invoice->hpp = $invoiceHpp;
                $invoice->profit = $invoiceSales - $invoiceHpp;

                // 2. Generate PDF Invoice
                $pdfInvoice = Pdf::loadView('invoices.pdf', compact('invoice'));
                $invoiceContent = $pdfInvoice->output();
                $invoiceFilename = $invoice->invoice_number . ' - ' . $invoice->customer_name . '.pdf';

                // 3. Generate PDF Laba Rugi
                $pdfProfit = Pdf::loadView('profit.pdf-invoice', compact('invoice'));
                $profitContent = $pdfProfit->output();
                $profitFilename = 'Laba Rugi - ' . $invoice->invoice_number . '.pdf';

                // 4. Buat Caption yang Informatif
                $caption = "📄 *INVOICE & LABA RUGI*\n\n" .
                    "📌 *Info:* `{$invoice->invoice_number}`\n" .
                    "👤 *Pelanggan:* {$invoice->customer_name}\n" .
                    "📅 *Tanggal:* " . \Carbon\Carbon::parse($invoice->date)->format('d M Y') . "\n\n" .
                    "💰 *Ringkasan Keuangan:*\n" .
                    "• Total Jual: *Rp " . number_format($invoice->total_amount, 0, ',', '.') . "*\n" .
                    "• Total HPP: *Rp " . number_format($invoiceHpp, 0, ',', '.') . "*\n" .
                    "• Laba Bersih: *" . ($invoice->profit >= 0 ? '🟢' : '🔴') . " Rp " . number_format($invoice->profit, 0, ',', '.') . "*\n\n" .
                    "Laporan detail terlampir di bawah ini 👇";

                // 5. Kirim Invoice PDF
                $response1 = Http::attach('document', $invoiceContent, $invoiceFilename)
                    ->post("https://api.telegram.org/bot{$token}/sendDocument", [
                        'chat_id' => $chatId,
                        'caption' => $caption,
                        'parse_mode' => 'Markdown',
                    ]);

                // 6. Kirim Laba Rugi PDF
                if ($response1->successful()) {
                    Http::attach('document', $profitContent, $profitFilename)
                        ->post("https://api.telegram.org/bot{$token}/sendDocument", [
                            'chat_id' => $chatId,
                            'caption' => "📊 Laporan Laba Rugi untuk `{$invoice->invoice_number}`",
                            'parse_mode' => 'Markdown',
                        ]);
                    $successCount++;
                } else {
                    $errorCount++;
                    Log::error("Telegram Send Error: " . $response1->body());
                }
            } catch (\Exception $e) {
                $errorCount++;
                Log::error("Telegram Invoice Send Exception: " . $e->getMessage());
            }
        }

        if ($errorCount > 0) {
            return back()->with('warning', "Berhasil mengirim {$successCount} data, namun gagal mengirim {$errorCount} data ke Telegram.");
        }

        return back()->with('success', "Berhasil mengirim {$successCount} Invoice & Laporan Laba Rugi ke Telegram.");
    }
}
