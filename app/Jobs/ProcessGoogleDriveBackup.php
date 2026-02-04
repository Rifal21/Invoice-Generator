<?php

namespace App\Jobs;

use App\Models\Invoice;
use App\Models\Backup;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ProcessGoogleDriveBackup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $month;
    protected $year;
    protected $userId;
    protected $backupId;

    public $timeout = 7200; // 2 hours

    public function __construct($month, $year, $userId, $backupId = null)
    {
        $this->month = $month;
        $this->year = $year;
        $this->userId = $userId;
        $this->backupId = $backupId;
    }

    public function handle()
    {
        $cacheKey = 'backup_progress_' . $this->userId;

        // Retrieve Backup Record
        $backup = $this->backupId ? Backup::find($this->backupId) : null;
        if ($backup) {
            $backup->update(['status' => 'processing', 'details' => 'Memulai proses backup...']);
        }

        Cache::put($cacheKey, [
            'status' => 'starting',
            'percentage' => 0,
            'message' => 'Menyiapkan data backup...'
        ], 3600);

        try {
            $driveService = new \App\Services\GoogleDriveService();
            if (!$driveService->isConnected()) {
                throw new \Exception('Google Drive tidak terhubung.');
            }

            $startDate = Carbon::createFromDate($this->year, $this->month, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($this->year, $this->month, 1)->endOfMonth();

            // Fetch Invoices
            $invoices = Invoice::with(['items.product'])
                ->whereBetween('date', [$startDate, $endDate])
                ->get();

            $total = $invoices->count();

            if ($total === 0) {
                Cache::put($cacheKey, ['status' => 'completed', 'percentage' => 100, 'message' => 'Tidak ada invoice bulan ini.']);
                if ($backup) {
                    $backup->update([
                        'status' => 'completed',
                        'total_invoices' => 0,
                        'details' => 'Tidak ada invoice untuk periode ini.'
                    ]);
                }
                return;
            }

            // Folder Setup
            $rootBackupFolderId = $driveService->createFolder('Koperasi JR Backups');
            $periodFolderName = $startDate->format('F Y');
            $periodFolderId = $driveService->createFolder($periodFolderName, $rootBackupFolderId);

            // 1. Invoices Folder
            $invoicesBaseFolderId = $driveService->createFolder('Invoices', $periodFolderId);

            // 2. Laporan Folder
            $reportsBaseFolderId = $driveService->createFolder('Laporan Laba Rugi', $periodFolderId);

            $count = 0;

            // --- PROCESSING INVOICES ---
            foreach ($invoices as $invoice) {
                try {
                    $percentage = round((($count + 1) / $total) * 80); // 0-80% for Invoices
                    Cache::put($cacheKey, [
                        'status' => 'processing',
                        'percentage' => $percentage,
                        'message' => "Mengupload Invoice #{$invoice->invoice_number}..."
                    ], 3600);

                    // Structure: Invoices > Date > Customer
                    $dateFolderName = Carbon::parse($invoice->date)->format('d-m-Y');
                    $dateFolderId = $driveService->createFolder($dateFolderName, $invoicesBaseFolderId);

                    $customerFolderName = preg_replace('/[^A-Za-z0-9 _-]/', '', $invoice->customer_name);
                    if (empty($customerFolderName)) $customerFolderName = "Customer-" . $invoice->id;
                    $customerFolderId = $driveService->createFolder($customerFolderName, $dateFolderId);

                    // Generate PDF
                    $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
                    $fileName = preg_replace('/[^A-Za-z0-9 _-]/', '', $invoice->invoice_number) . '.pdf';
                    $tempPath = storage_path('app/temp_job_' . $invoice->id . '.pdf');
                    file_put_contents($tempPath, $pdf->output());

                    $driveService->uploadFile($tempPath, $fileName, $customerFolderId);

                    if (file_exists($tempPath)) unlink($tempPath);
                    $count++;
                } catch (\Exception $e) {
                    Log::error("Backup Invoice Error #{$invoice->id}: " . $e->getMessage());
                }
            }

            // --- PROCESSING REPORTS (Per Customer) ---
            Cache::put($cacheKey, ['status' => 'finalizing', 'percentage' => 85, 'message' => 'Memproses Laporan Laba Rugi...'], 3600);
            if ($backup) $backup->update(['details' => 'Membuat laporan PDF...']);

            // Group by Customer
            $grouped = $invoices->groupBy('customer_name');
            $reportCount = 0;
            $totalGroups = $grouped->count();

            foreach ($grouped as $customerName => $customerInvoices) {
                $reportCount++;
                $percentage = 85 + round(($reportCount / $totalGroups) * 15); // 85-100%
                Cache::put($cacheKey, [
                    'status' => 'processing',
                    'percentage' => $percentage,
                    'message' => "Membuat Laporan: $customerName"
                ], 3600);

                // Calculate Data for this Customer
                $totalSales = 0;
                $totalHpp = 0;

                foreach ($customerInvoices as $inv) {
                    $invSales = 0;
                    $invHpp = 0;
                    foreach ($inv->items as $item) {
                        $invSales += $item->total;
                        $hppPerUnit = $item->purchase_price > 0
                            ? $item->purchase_price
                            : ($item->product ? $item->product->purchase_price : 0);

                        $invHpp += ($hppPerUnit * $item->quantity);
                    }
                    $inv->sales = $invSales;
                    $inv->hpp = $invHpp;
                    $inv->profit = $invSales - $invHpp;

                    $totalSales += $inv->sales;
                    $totalHpp += $inv->hpp;
                }

                $totalProfit = $totalSales - $totalHpp;

                // Create PDF using 'profit.pdf-all'
                $data = [
                    'invoices' => $customerInvoices,
                    'totalSales' => $totalSales,
                    'totalHpp' => $totalHpp,
                    'totalProfit' => $totalProfit,
                    'startDate' => $startDate->toDateString(),
                    'endDate' => $endDate->toDateString()
                ];

                $pdfReport = Pdf::loadView('profit.pdf-all', $data);

                $cleanName = preg_replace('/[^A-Za-z0-9 _-]/', '', $customerName);
                if (empty($cleanName)) $cleanName = "CustomerReport";
                $reportFileName = "LabaRugi_{$cleanName}.pdf";

                $tempReportPath = storage_path('app/report_' . $cleanName . '_' . time() . '.pdf');
                file_put_contents($tempReportPath, $pdfReport->output());

                $custReportFolderId = $driveService->createFolder($cleanName, $reportsBaseFolderId);
                $driveService->uploadFile($tempReportPath, $reportFileName, $custReportFolderId);

                if (file_exists($tempReportPath)) unlink($tempReportPath);
            }

            // Finish
            Cache::put($cacheKey, [
                'status' => 'completed',
                'percentage' => 100,
                'message' => "Backup Selesai! {$count} invoice & {$totalGroups} laporan pelanggan tersimpan."
            ], 3600);

            if ($backup) {
                $backup->update([
                    'status' => 'completed',
                    'total_invoices' => $count,
                    'details' => "Backup Success. {$count} Invoices. {$totalGroups} Reports."
                ]);
            }

            \App\Models\ActivityLog::create([
                'user_id' => $this->userId,
                'user_name' => 'System Job',
                'action' => 'backup_drive_job',
                'model_type' => 'Backup',
                'model_id' => $backup ? $backup->id : 0,
                'description' => "Backup Complete: {$startDate->format('F Y')}",
                'ip_address' => '127.0.0.1',
                'user_agent' => 'QueueWorker',
            ]);
        } catch (\Exception $e) {
            Log::error("Backup Job Fatal: " . $e->getMessage());
            Cache::put($cacheKey, [
                'status' => 'error',
                'percentage' => 0,
                'message' => "Error: " . $e->getMessage()
            ], 3600);

            if ($backup) {
                $backup->update([
                    'status' => 'failed',
                    'details' => "Error: " . $e->getMessage()
                ]);
            }

            throw $e;
        }
    }
}
