<?php

namespace App\Jobs;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class GenerateBulkInvoiceZipJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes
    protected $invoiceIds;
    protected $exportId;
    protected $filters;

    /**
     * Create a new job instance.
     */
    public function __construct(array $invoiceIds, string $exportId, array $filters = [])
    {
        $this->invoiceIds = $invoiceIds;
        $this->exportId = $exportId;
        $this->filters = $filters;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("GenerateBulkInvoiceZipJob started ({$this->exportId})");
        try {
            Cache::put("zip_export_{$this->exportId}_status", 'processing', 3600);
            Cache::put("zip_export_{$this->exportId}_progress", 0, 3600);

            $query = Invoice::query()->with('items');

            if (!empty($this->invoiceIds)) {
                $query->whereIn('id', $this->invoiceIds);
            } else {
                // Re-apply filters if no specific IDs
                if (($this->filters['filter_type'] ?? '') == 'month' && isset($this->filters['month'], $this->filters['year'])) {
                    $query->whereYear('date', $this->filters['year'])
                          ->whereMonth('date', $this->filters['month']);
                } elseif (isset($this->filters['start_date'], $this->filters['end_date'])) {
                    $query->whereBetween('date', [$this->filters['start_date'], $this->filters['end_date']]);
                }

                if (!empty($this->filters['customer_name'])) {
                    $query->where('customer_name', $this->filters['customer_name']);
                }
            }

            $invoices = $query->orderBy('date', 'asc')->orderBy('invoice_number', 'asc')->get();
            $total = $invoices->count();

            if ($total === 0) {
                Cache::put("zip_export_{$this->exportId}_status", 'failed', 3600);
                Cache::put("zip_export_{$this->exportId}_error", 'Tidak ada invoice yang ditemukan.', 3600);
                return;
            }

            $zip = new ZipArchive();
            $zipName = $this->generateZipFileName();
            
            $tempDir = storage_path('app/public/temp_zips');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0777, true);
            }
            
            $zipPath = $tempDir . '/' . $zipName;

            if ($zip->open($zipPath, ZipArchive::CREATE) !== TRUE) {
                throw new \Exception("Gagal membuat file ZIP di server.");
            }

            foreach ($invoices as $index => $invoice) {
                try {
                    $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
                    $pdfContent = $pdf->output();
                    
                    if (!empty($pdfContent)) {
                        $customerFolder = $invoice->customer_name ?: 'Tanpa Pelanggan';
                        $customerFolder = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '-', $customerFolder);
                        
                        $dateFolder = Carbon::parse($invoice->date)->format('Y-m-d');
                        $fileName = str_replace(['/', '\\'], '-', $invoice->invoice_number) . '.pdf';
                        
                        $zip->addFromString($customerFolder . '/' . $dateFolder . '/' . $fileName, $pdfContent);
                    }
                } catch (\Exception $e) {
                    Log::error("Error generating invoice {$invoice->invoice_number}: " . $e->getMessage());
                }

                // Update progress
                $progress = round((($index + 1) / $total) * 100);
                Cache::put("zip_export_{$this->exportId}_progress", $progress, 3600);
            }

            $zip->close();
            Log::info("GenerateBulkInvoiceZipJob completed ({$this->exportId}). Zip saved to: " . $zipPath);

            // Send to Telegram
            $this->sendToTelegram($zipPath, $zipName, $total);

            if (!file_exists($zipPath)) {
                throw new \Exception("File ZIP hilang setelah proses selesai.");
            }

            Cache::put("zip_export_{$this->exportId}_status", 'completed', 3600);
            Cache::put("zip_export_{$this->exportId}_filename", $zipName, 3600);
            Cache::put("zip_export_{$this->exportId}_progress", 100, 3600);

        } catch (\Exception $e) {
            Log::error("Bulk ZIP Export Job Failed ({$this->exportId}): " . $e->getMessage());
            Cache::put("zip_export_{$this->exportId}_status", 'failed', 3600);
            Cache::put("zip_export_{$this->exportId}_error", $e->getMessage(), 3600);
        }
    }

    /**
     * Send the generated ZIP file to Telegram.
     */
    private function sendToTelegram(string $zipPath, string $zipName, int $totalCount): void
    {
        try {
            $token = env('TELEGRAM_BOT_TOKEN');
            $chatId = env('TELEGRAM_CHAT_ID');

            if (!$token || !$chatId) {
                Log::warning("Telegram token or Chat ID not found for Bulk ZIP export.");
                return;
            }

            $caption = "📦 *PENGUNDUHAN MASSAL ZIP SELESAI*\n\n" .
                "📄 *Nama File:* `{$zipName}`\n" .
                "📑 *Jumlah Invoice:* {$totalCount}\n" .
                "⏰ *Waktu:* " . now()->format('d M Y H:i:s') . "\n\n" .
                "File ZIP telah berhasil dibuat dan siap diunduh oleh admin.";

            Http::attach('document', file_get_contents($zipPath), $zipName)
                ->post("https://api.telegram.org/bot{$token}/sendDocument", [
                    'chat_id' => $chatId,
                    'caption' => $caption,
                    'parse_mode' => 'Markdown',
                ]);
            
            Log::info("Bulk ZIP file sent to Telegram successfully ({$this->exportId})");
        } catch (\Exception $e) {
            Log::error("Failed to send Bulk ZIP to Telegram: " . $e->getMessage());
        }
    }

    /**
     * Generate a descriptive ZIP filename based on filters.
     */
    private function generateZipFileName(): string
    {
        $prefix = 'Invoices_Koperasi_JR';
        $customer = !empty($this->filters['customer_name']) ? '_' . str_replace([' ', '/', '\\'], '_', $this->filters['customer_name']) : '';
        
        $details = '';
        if (!empty($this->invoiceIds)) {
            $details = '_Terpilih_' . now()->format('d-m-Y_His');
        } else {
            $type = $this->filters['filter_type'] ?? 'bulk';
            if ($type === 'month') {
                $months = [
                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
                    7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                ];
                $monthName = $months[(int)$this->filters['month']] ?? 'Bulan';
                $details = '_Bulanan_' . $monthName . '_' . $this->filters['year'];
            } elseif ($type === 'week' || $type === 'custom') {
                $start = Carbon::parse($this->filters['start_date'])->format('d-m-Y');
                $end = Carbon::parse($this->filters['end_date'])->format('d-m-Y');
                $label = ($type === 'week') ? 'Mingguan' : 'Custom';
                $details = '_' . $label . '_' . $start . '_sd_' . $end;
            } else {
                $details = '_' . now()->format('d-m-Y_His');
            }
        }

        // Add a small part of exportId to ensure uniqueness if the same filter used twice in same second
        return $prefix . $customer . $details . '_' . substr($this->exportId, 0, 5) . '.zip';
    }
}
