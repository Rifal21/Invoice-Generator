<?php

namespace App\Jobs;

use App\Models\Document;
use App\Models\ActivityLog;
use App\Services\GoogleDriveService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class BackupDocumentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userId;

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function handle(GoogleDriveService $drive)
    {
        $cacheKey = 'backup_progress_' . $this->userId;

        try {
            // Initial Status
            Cache::put($cacheKey, [
                'status' => 'processing',
                'percentage' => 5,
                'message' => 'Menghubungkan ke Google Drive...'
            ], now()->addMinutes(10));

            if (!$drive->isConnected()) {
                throw new \Exception("Google Drive belum terhubung.");
            }

            $count = Document::count();
            if ($count === 0) {
                Cache::put($cacheKey, [
                    'status' => 'completed',
                    'percentage' => 100,
                    'message' => 'Tidak ada dokumen untuk dibackup.'
                ], now()->addMinutes(5));
                return;
            }

            // 1. Create Main Backup Folder
            $mainFolderId = $drive->createFolder('Koperasi JR Backups');

            // 2. Create Specific Document Folder with Timestamp
            $folderName = 'Legalitas_' . now()->format('Y-m-d_H-i');
            $targetFolderId = $drive->createFolder($folderName, $mainFolderId);

            $documents = Document::all();
            $uploaded = 0;
            $total = $documents->count();

            foreach ($documents as $index => $doc) {
                // Update Progress (10% to 90%)
                $percentage = round((($index) / $total) * 80) + 10;

                Cache::put($cacheKey, [
                    'status' => 'processing',
                    'percentage' => $percentage,
                    'message' => "Mengupload: {$doc->title}..."
                ], now()->addMinutes(5));

                // Determine File Path (Check Local Secure then Public Fallback)
                $localPath = storage_path('app/' . $doc->file_path);
                $publicPath = storage_path('app/public/' . $doc->file_path);

                $filePath = null;
                if (file_exists($localPath)) {
                    $filePath = $localPath;
                } elseif (file_exists($publicPath)) {
                    $filePath = $publicPath;
                }

                if ($filePath) {
                    // Title as filename, sanitized
                    $fileName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $doc->title) . '.pdf';

                    $drive->uploadFile($filePath, $fileName, $targetFolderId);
                    $uploaded++;
                }
            }

            // Final Success
            Cache::put($cacheKey, [
                'status' => 'completed',
                'percentage' => 100,
                'message' => "Backup Selesai! $uploaded dokumen berhasil disimpan."
            ], now()->addMinutes(10));

            // Log Success
            ActivityLog::create([
                'user_id' => $this->userId,
                'action' => 'backup_documents',
                'model_type' => 'Document',
                'description' => "Berhasil backup $uploaded dokumen ke Google Drive ($folderName).",
                'ip_address' => 'System',
                'user_agent' => 'QueueWorker',
            ]);
        } catch (\Exception $e) {
            Log::error("Backup Documents Failed: " . $e->getMessage());

            Cache::put($cacheKey, [
                'status' => 'error',
                'percentage' => 0,
                'message' => "Gagal: " . $e->getMessage()
            ], now()->addMinutes(10));

            ActivityLog::create([
                'user_id' => $this->userId,
                'action' => 'backup_documents_failed',
                'model_type' => 'Document',
                'description' => "Gagal backup dokumen: " . $e->getMessage(),
                'ip_address' => 'System',
                'user_agent' => 'QueueWorker',
            ]);

            throw $e;
        }
    }
}
