<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\Expense;
use App\Models\Salary;
use App\Models\Product;
use Illuminate\Console\Command;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendDailyRecap extends Command
{
    protected $signature = 'recap:daily';
    protected $description = 'Send daily financial recap to Telegram';

    public function handle()
    {
        $today = Carbon::today()->toDateString();
        $this->info("Generating all individual reports for {$today}...");

        $invoices = Invoice::with(['items.product'])->whereDate('date', $today)->get();

        if ($invoices->isEmpty()) {
            $this->info("No invoices found for today. Skipping.");
            return;
        }

        try {
            $token = env('TELEGRAM_BOT_TOKEN');
            $chatId = env('TELEGRAM_CHAT_ID');

            if (!$token || !$chatId) {
                $this->error("Telegram configuration not found in .env");
                return;
            }

            $this->info("Sending " . $invoices->count() . " invoices and their profit reports...");

            foreach ($invoices as $invoice) {
                // 1. Kalkulasi Laba Rugi (Sama seperti di Controller)
                $invoiceHpp = 0;
                foreach ($invoice->items as $item) {
                    $hppPerUnit = $item->purchase_price > 0 ? $item->purchase_price : ($item->product ? $item->product->purchase_price : 0);
                    $invoiceHpp += ($hppPerUnit * $item->quantity);
                    $item->hpp_per_unit = $hppPerUnit;
                    $item->total_hpp = $hppPerUnit * $item->quantity;
                }
                $invoice->sales = $invoice->total_amount;
                $invoice->hpp = $invoiceHpp;
                $invoice->profit = $invoice->sales - $invoiceHpp;

                // 1.5 Kirim Pesan Pembatas (Header)
                $headerMessage = "━━━━━━━━━━━━━━━━━━━━━━\n" .
                    "🆕 *DATA INVOICE BARU*\n" .
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

                // 4. Caption yang Informatif (Sama seperti manual)
                $caption = "📄 *INVOICE & LABA RUGI (OTOMATIS)*\n\n" .
                    "📌 *Info:* `{$invoice->invoice_number}`\n" .
                    "👤 *Pelanggan:* {$invoice->customer_name}\n" .
                    "📅 *Tanggal:* " . \Carbon\Carbon::parse($invoice->date)->format('d M Y') . "\n\n" .
                    "💰 *Ringkasan Keuangan:*\n" .
                    "• Total Jual: *Rp " . number_format($invoice->total_amount, 0, ',', '.') . "*\n" .
                    "• Total HPP: *Rp " . number_format($invoiceHpp, 0, ',', '.') . "*\n" .
                    "• Laba Bersih: *" . ($invoice->profit >= 0 ? '🟢' : '🔴') . " Rp " . number_format($invoice->profit, 0, ',', '.') . "*\n\n" .
                    "Laporan harian otomatis.";

                // 5. Kirim Invoice PDF
                $response = Http::attach('document', $invoiceContent, $invoiceFilename)
                    ->post("https://api.telegram.org/bot{$token}/sendDocument", [
                        'chat_id' => $chatId,
                        'caption' => $caption,
                        'parse_mode' => 'Markdown',
                    ]);

                // 6. Kirim Laba Rugi PDF jika sukses
                if ($response->successful()) {
                    Http::attach('document', $profitContent, $profitFilename)
                        ->post("https://api.telegram.org/bot{$token}/sendDocument", [
                            'chat_id' => $chatId,
                            'caption' => "📊 Laporan Laba Rugi untuk `{$invoice->invoice_number}`",
                            'parse_mode' => 'Markdown',
                        ]);
                }
            }

            $this->info("All individual reports sent successfully!");
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            Log::error("Daily Automate Send Error: " . $e->getMessage());
        }
    }

    private function getFinancialData($startDate, $endDate)
    {
        // 1. Calculate Sales and Gross Profit (HPP)
        $salesData = DB::table('invoice_items')
            ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->leftJoin('products', 'products.id', '=', 'invoice_items.product_id')
            ->whereBetween('invoices.date', [$startDate, $endDate])
            ->selectRaw('
                SUM(invoice_items.total) as total_sales,
                SUM(
                    CASE 
                        WHEN invoice_items.purchase_price > 0 THEN invoice_items.purchase_price 
                        WHEN products.purchase_price IS NOT NULL THEN products.purchase_price 
                        ELSE 0 
                    END * invoice_items.quantity
                ) as total_hpp
            ')
            ->first();

        $totalSales = $salesData->total_sales ?? 0;
        $totalHpp = $salesData->total_hpp ?? 0;
        $grossProfit = $totalSales - $totalHpp;

        // 2. Total Salaries
        $totalSalaries = Salary::whereBetween('start_date', [$startDate, $endDate])
            ->orWhereBetween('end_date', [$startDate, $endDate])
            ->sum('net_salary');

        // 3. Total Expenses (Operational)
        $totalOperationalExpenses = Expense::whereBetween('date', [$startDate, $endDate])->sum('amount');

        // 4. Net Profit
        $netProfit = $grossProfit - $totalSalaries - $totalOperationalExpenses;

        // 5. Assets (Current Stock Value)
        $totalAssetValue = Product::select(DB::raw('SUM(stock * purchase_price) as total_value'))->first()->total_value ?? 0;
        $totalStockCount = Product::sum('stock');

        // Categorized Expenses
        $expensesByCategory = Expense::whereBetween('date', [$startDate, $endDate])
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->get();

        return compact(
            'totalSales',
            'totalHpp',
            'grossProfit',
            'totalSalaries',
            'totalOperationalExpenses',
            'netProfit',
            'totalAssetValue',
            'totalStockCount',
            'expensesByCategory',
            'startDate',
            'endDate'
        );
    }
}
