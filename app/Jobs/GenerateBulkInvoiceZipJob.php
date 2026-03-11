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
            $zipName = 'Invoice_Koperasi_JR_' . now()->format('d F Y') . '_' . substr($this->exportId, 0, 8) . '.zip';
            
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
}
