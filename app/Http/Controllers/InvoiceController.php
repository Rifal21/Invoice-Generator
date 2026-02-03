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
            'items.*.unit' => 'required|string',
            'items.*.description' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0',
        ]);
        $invoice_number = 'INV-' . Carbon::parse($request->date)->format('Ymd') . '-' . str_pad(Invoice::max('id') + 1, 3, '0', STR_PAD_LEFT) . '-' . $request->tipe;
        $invoice = Invoice::create([
            'invoice_number' => $invoice_number,
            'date' => $request->date,
            'customer_name' => $request->customer_name,
            'discount' => $request->input('discount', 0),
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

        $discount = $request->input('discount', 0);
        $totalAmount -= $discount;

        $invoice->update([
            'total_amount' => $totalAmount,
            'discount' => $discount
        ]);

        $filters = $request->input('filters', []);
        return redirect()->route('invoices.index', $filters)->with('success', 'Invoice created successfully.');
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
            'invoice_number' => 'required|string|unique:invoices,invoice_number,' . $invoice->id,
            'date' => 'required|date',
            'customer_name' => 'required|string',
            'items' => 'required|array',
            'items.*.product_id' => 'required',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.purchase_price' => 'nullable|numeric|min:0',
            'items.*.unit' => 'required|string',
            'items.*.description' => 'nullable|string',
            'discount' => 'nullable|numeric|min:0',
        ]);

        $invoice->update([
            'invoice_number' => $request->invoice_number,
            'date' => $request->date,
            'customer_name' => $request->customer_name,
            'discount' => $request->input('discount', 0),
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

        $discount = $request->input('discount', 0);
        $totalAmount -= $discount;

        $invoice->update([
            'total_amount' => $totalAmount,
            // discount already updated
        ]);

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

                // 1.5 Kirim Pesan Pembatas (Header)
                $headerMessage = "━━━━━━━━━━━━━━━━━━━━━━\n" .
                    "🆕 *KIRIM DATA INVOICE*\n" .
                    "👤 *Pelanggan:* {$invoice->customer_name}\n" .
                    "📄 *No. Inv:* `{$invoice->invoice_number}`\n" .
                    "📅 *Tanggal:* " . \Carbon\Carbon::parse($invoice->date)->format('d M Y') . "\n" .
                    "━━━━━━━━━━━━━━━━━━━━━━";

                Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $headerMessage,
                    'parse_mode' => 'Markdown',
                ]);

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

                    $invoice->update(['telegram_sent_at' => now()]);
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
            return back()->with('warning', "Berhasil mengirim {$successCount} data ke Group, namun gagal mengirim {$errorCount} data.");
        }

        return back()->with('success', "Berhasil mengirim {$successCount} Invoice & Laporan Laba Rugi ke Group Telegram.");
    }

    public function sendToCustomer(Request $request)
    {
        $request->validate([
            'invoice_ids' => 'required|array',
            'invoice_ids.*' => 'exists:invoices,id'
        ]);

        $token = env('TELEGRAM_BOT_TOKEN');

        if (!$token) {
            return back()->with('error', 'Konfigurasi Token Telegram belum diatur di .env');
        }

        $invoices = Invoice::with(['items.product'])->whereIn('id', $request->invoice_ids)->get();
        $successCount = 0;
        $errorCount = 0;
        $noChatIdCount = 0;

        foreach ($invoices as $invoice) {
            $customer = \App\Models\Customer::where('name', $invoice->customer_name)->first();

            if (!$customer || !$customer->telegram_chat_id) {
                $noChatIdCount++;
                continue;
            }

            try {
                // Generate PDF Invoice
                $pdfInvoice = Pdf::loadView('invoices.pdf', compact('invoice'));
                $invoiceContent = $pdfInvoice->output();
                $invoiceFilename = $invoice->invoice_number . ' - ' . $invoice->customer_name . '.pdf';

                $caption = "📄 *INVOICE ANDA*\n\n" .
                    "📌 *No:* `{$invoice->invoice_number}`\n" .
                    "👤 *Pelanggan:* {$invoice->customer_name}\n" .
                    "📅 *Tanggal:* " . \Carbon\Carbon::parse($invoice->date)->format('d M Y') . "\n" .
                    "💰 *Total:* *Rp " . number_format($invoice->total_amount, 0, ',', '.') . "*\n\n" .
                    "Terima kasih telah berlangganan!";

                $response = Http::attach('document', $invoiceContent, $invoiceFilename)
                    ->post("https://api.telegram.org/bot{$token}/sendDocument", [
                        'chat_id' => $customer->telegram_chat_id,
                        'caption' => $caption,
                        'parse_mode' => 'Markdown',
                    ]);

                if ($response->successful()) {
                    $invoice->update(['telegram_sent_at' => now()]);
                    $successCount++;
                } else {
                    $errorCount++;
                    Log::error("Telegram Personal Sent Error: " . $response->body());
                }
            } catch (\Exception $e) {
                $errorCount++;
                Log::error("Telegram Customer Send Exception: " . $e->getMessage());
            }
        }

        $message = "Berhasil mengirim {$successCount} invoice ke pelanggan.";
        if ($noChatIdCount > 0) {
            $message .= " {$noChatIdCount} pelanggan tidak memiliki Chat ID.";
        }
        if ($errorCount > 0) {
            $message .= " {$errorCount} gagal terkirim.";
        }

        return back()->with($errorCount > 0 ? 'warning' : 'success', $message);
    }

    public function sendToWhatsApp(Request $request)
    {
        $request->validate([
            'invoice_ids' => 'required|array',
            'invoice_ids.*' => 'exists:invoices,id'
        ]);

        $apiUrl = env('WHATSAPP_API_URL');
        $apiKey = env('WHATSAPP_API_KEY');
        $instance = env('WHATSAPP_SESSION', 'default');

        // if (!$apiUrl || !$apiKey) {
        //     return back()->with('error', 'Konfigurasi WhatsApp API URL atau Key belum diatur di .env');
        // }

        $invoices = Invoice::with(['items.product'])->whereIn('id', $request->invoice_ids)->get();
        $successCount = 0;
        $errorCount = 0;
        $noPhoneCount = 0;

        // Pastikan folder public ada
        if (!file_exists(public_path('invoice-files'))) {
            mkdir(public_path('invoice-files'), 0777, true);
        }

        foreach ($invoices as $invoice) {
            $customer = \App\Models\Customer::where('name', $invoice->customer_name)->first();

            if (!$customer || !$customer->phone) {
                $noPhoneCount++;
                continue;
            }

            // Bersihkan nomor telepon
            $phone = preg_replace('/[^0-9]/', '', $customer->phone);
            if (str_starts_with($phone, '0')) {
                $phone = '62' . substr($phone, 1);
            }
            $chatId = $phone . '@c.us';

            try {
                // 1. Generate & Save PDF
                $pdfInvoice = Pdf::loadView('invoices.pdf', compact('invoice'));
                $pdfName = 'invoice-' . str_replace('/', '-', $invoice->invoice_number) . '.pdf';
                $pdfPath = public_path('invoice-files/' . $pdfName);
                $pdfInvoice->save($pdfPath);

                $downloadLink = url('invoice-files/' . $pdfName);

                // 2. Convert First Page to Image (JPG) using Imagick
                $imageName = 'invoice-' . str_replace('/', '-', $invoice->invoice_number) . '.jpg';
                $imagePath = public_path('invoice-files/' . $imageName);
                $imageBase64 = null;

                if (class_exists('Imagick')) {
                    try {
                        $imagick = new \Imagick();
                        $imagick->setResolution(150, 150);
                        $imagick->readImage($pdfPath . '[0]');
                        $imagick->setImageFormat('jpg');
                        $imagick->writeImage($imagePath);
                        $imagick->clear();
                        $imagick->destroy();

                        $imageBase64 = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($imagePath));
                    } catch (\Exception $imgErr) {
                        Log::error("Imagick Error: " . $imgErr->getMessage());
                    }
                } else {
                    Log::warning("Imagick extension not found. Sending PDF link only.");
                }

                // Format Tanggal Indonesia (Opsional: pakai Carbon)
                $date = \Carbon\Carbon::parse($invoice->date)->format('d-m-Y');

                $caption = "🛒 *INVOICE TAGIHAN*\n\n" .
                    "Halo *{$customer->name}*,\n" .
                    "Berikut detail transaksi Anda:\n\n" .
                    "📅 Tanggal : {$date}\n" .
                    "🧾 No. Faktur : `{$invoice->invoice_number}`\n" .
                    "💰 Total : *Rp " . number_format($invoice->total_amount, 0, ',', '.') . "*\n\n" .
                    "----------------------------------\n" .
                    "📄 *Silakan unduh dokumen PDF disini:*\n" .
                    $downloadLink . "\n\n" .
                    "----------------------------------\n" .
                    "Terima kasih telah berbelanja! 🙏";

                // 3. Kirim ke WAHA (TEXT ONLY - WAHA Core Friendly)
                // Kita setup payload Teks saja agar gratis dan tidak kena limit Plus version
                $payload = [
                    'session' => $instance,
                    'chatId' => $chatId,
                    'text' => $caption
                ];
                $endpoint = "{$apiUrl}/api/sendText";

                $response = Http::withHeaders([
                    'X-Api-Key' => $apiKey,
                    'Content-Type' => 'application/json'
                ])->post($endpoint, $payload);

                if ($response->successful()) {
                    $invoice->update(['whatsapp_sent_at' => now()]);
                    $successCount++;
                    // Optional: Hapus file setelah kirim agar hemat storage
                    // unlink($pdfPath); unlink($imagePath); 
                } else {
                    $errorCount++;
                    Log::error("WAHA Send Error ({$invoice->invoice_number}): " . $response->body());
                }
            } catch (\Exception $e) {
                $errorCount++;
                Log::error("Invoice WA Exception: " . $e->getMessage());
            }
        }

        $message = "Berhasil mengirim {$successCount} Invoice.";
        if ($noPhoneCount > 0) $message .= " {$noPhoneCount} tanpa nomor.";
        if ($errorCount > 0) $message .= " {$errorCount} gagal.";

        return back()->with($errorCount > 0 ? 'warning' : 'success', $message);
    }

    public function sendToWhapi(Request $request)
    {
        $request->validate([
            'invoice_ids' => 'required|array',
            'invoice_ids.*' => 'exists:invoices,id'
        ]);

        $whapiToken = 'QYBkexNHUc5ALtBa7KIyelEHAQWioYPP';
        $whapiUrl = 'https://gate.whapi.cloud/messages';

        $invoices = Invoice::with(['items.product'])->whereIn('id', $request->invoice_ids)->get();
        $successCount = 0;
        $errorCount = 0;

        foreach ($invoices as $invoice) {
            $customer = \App\Models\Customer::where('name', $invoice->customer_name)->first();
            if (!$customer || !$customer->phone) continue;

            $phone = preg_replace('/[^0-9]/', '', $customer->phone);
            if (str_starts_with($phone, '0')) $phone = '62' . substr($phone, 1);

            // Format ID Whapi
            $chatId = $phone . '@s.whatsapp.net';

            try {
                $pdfInvoice = Pdf::loadView('invoices.pdf', compact('invoice'));
                $pdfContent = base64_encode($pdfInvoice->output());
                $filename = 'Invoice-' . str_replace('/', '-', $invoice->invoice_number) . '.pdf';

                $date = \Carbon\Carbon::parse($invoice->date)->format('d-m-Y');

                $itemDetails = "";
                foreach ($invoice->items as $item) {
                    $qty = rtrim(rtrim(number_format($item->quantity, 2, ',', '.'), '0'), ',');
                    $itemDetails .= "- {$item->product_name} ({$qty} {$item->unit}) : *Rp " . number_format($item->total, 0, ',', '.') . "*\n";
                }

                $caption = " *INVOICE KOPERASI JR*\n\n" .
                    "Halo *{$customer->name}*,\n" .
                    "Berikut adalah rincian transaksi Anda:\n\n" .
                    "📅 Tanggal : {$date}\n" .
                    "🧾 No. Invoice : `{$invoice->invoice_number}`\n\n" .
                    "*Rincian Barang:*\n" .
                    $itemDetails . "\n" .
                    "💰 *Total Tagihan : Rp " . number_format($invoice->total_amount, 0, ',', '.') . "*\n\n" .
                    "Terima kasih atas kunjungan Anda! 🙏";

                // Kirim Dokumen via Whapi
                $response = Http::withHeaders([
                    'Authorization' => "Bearer {$whapiToken}",
                    'Content-Type' => 'application/json'
                ])->post("{$whapiUrl}/document", [
                    'to' => $chatId,
                    'media' => "data:application/pdf;base64,{$pdfContent}",
                    'filename' => $filename,
                    'caption' => $caption
                ]);

                if ($response->successful()) {
                    $invoice->update(['whatsapp_sent_at' => now()]);
                    $successCount++;
                } else {
                    $errorCount++;
                    Log::error("Whapi Error ({$invoice->invoice_number}): " . $response->body());
                }
            } catch (\Exception $e) {
                $errorCount++;
                Log::error("Whapi Exception: " . $e->getMessage());
            }
        }

        return back()->with($errorCount > 0 ? 'warning' : 'success', "Whapi: {$successCount} terkirim, {$errorCount} gagal.");
    }
}
