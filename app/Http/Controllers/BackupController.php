<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

class BackupController extends Controller
{
    public function index()
    {
        $isConnected = false;
        try {
            $drive = new \App\Services\GoogleDriveService();
            $isConnected = $drive->isConnected();
        } catch (\Exception $e) {
            // Ignore (maybe credentials missing)
        }

        $backups = \App\Models\Backup::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $customers = \App\Models\Customer::orderBy('name')->get();

        return view('backup.index', compact('isConnected', 'backups', 'customers'));
    }

    public function process(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|in:monthly,weekly,database,products,custom',
                'month' => 'required_if:type,monthly|nullable|integer|min:1|max:12',
                'year' => 'required_if:type,monthly,weekly|nullable|integer|min:2000|max:' . (date('Y') + 1),
                'week' => 'nullable|integer|min:1|max:53',
                'start_date' => 'required_if:type,weekly,custom|nullable|date',
                'end_date' => 'required_if:type,weekly,custom|nullable|date|after_or_equal:start_date',
                'customer_id' => 'nullable|exists:customers,id',
            ]);

            $type = $request->type;
            $month = $request->month;
            $year = $request->year;
            $week = $request->week;
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            $customerId = $request->customer_id;
            $userId = auth()->id();

            // Check credentials existence
            if (!file_exists(storage_path('app/google/credentials.json'))) {
                return back()->with('error', 'BACKUP GAGAL: File credentials.json (OAuth Client ID) tidak ditemukan.');
            }

            // Create Backup Record Log
            $backup = \App\Models\Backup::create([
                'user_id' => $userId,
                'type' => $type,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'period_month' => $month,
                'period_week' => $week,
                'period_year' => $year,
                'status' => 'queued',
                'details' => 'Queued for processing'
            ]);

            // Dispatch Job
            \App\Jobs\ProcessGoogleDriveBackup::dispatch($month, $year, $userId, $backup->id, $type, $week, $startDate, $endDate, $customerId);

            // Log Activity
            $periodDesc = "Manual Backup";
            if ($type === 'database') {
                $periodDesc = "Full Database " . date('d/m/Y H:i');
            } elseif ($type === 'products') {
                $periodDesc = "Data Produk " . date('d/m/Y H:i');
            } elseif ($type === 'weekly') {
                $periodDesc = Carbon::parse($startDate)->format('d/m/Y') . " - " . Carbon::parse($endDate)->format('d/m/Y');
            } elseif ($year && $month) {
                $periodDesc = Carbon::createFromDate($year, $month, 1)->format('F Y');
            }

            \App\Models\ActivityLog::create([
                'user_id' => $userId,
                'user_name' => auth()->user()->name ?? 'Unknown',
                'action' => 'backup_drive_init',
                'model_type' => 'Backup',
                'model_id' => $backup->id,
                'description' => "Initiated Cloud Backup ($type): " . $periodDesc,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            // Clear previous cache or set initial
            \Illuminate\Support\Facades\Cache::put('backup_progress_' . $userId, [
                'status' => 'queued',
                'percentage' => 0,
                'message' => 'Menambahkan ke antrian...'
            ], 3600);

            if ($request->ajax()) {
                return response()->json(['status' => 'success', 'message' => 'Backup dimulai di latar belakang.']);
            }

            return back()->with('success', 'Backup sedang berjalan di latar belakang! Silakan tunggu indikator selesai.');
        } catch (\Exception $e) {
            Log::error("Cloud Backup Error: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            if ($request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan internal: ' . $e->getMessage()], 500);
            }

            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function checkProgress()
    {
        $userId = auth()->id();
        $progress = \Illuminate\Support\Facades\Cache::get('backup_progress_' . $userId, [
            'status' => 'idle',
            'percentage' => 0,
            'message' => ''
        ]);

        return response()->json($progress);
    }

    public function connect()
    {
        try {
            $driveService = new \App\Services\GoogleDriveService();
            return redirect($driveService->getAuthUrl());
        } catch (\Exception $e) {
            return redirect()->route('backup.index')->with('error', 'Gagal membuat URL Auth: ' . $e->getMessage());
        }
    }

    public function callback(Request $request)
    {
        if (!$request->has('code')) {
            return redirect()->route('backup.index')->with('error', 'Gagal koneksi: Code tidak ditemukan.');
        }

        try {
            $driveService = new \App\Services\GoogleDriveService();
            $driveService->authenticate($request->code);
            return redirect()->route('backup.index')->with('success', 'Koneksi ke Google Drive Berhasil! Silakan coba backup lagi.');
        } catch (\Throwable $e) {
            return redirect()->route('backup.index')->with('error', 'Gagal autentikasi: ' . $e->getMessage());
        }
    }

    public function history()
    {
        $backups = \App\Models\Backup::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('backup.history', compact('backups'));
    }

    public function downloadDatabase()
    {
        // Only allow Rifal Kurniawan to download the database
        if (auth()->user()->name !== 'Rifal Kurniawan') {
            abort(403);
        }

        $dbConfig = config('database.connections.mysql');
        $host = $dbConfig['host'];
        $database = $dbConfig['database'];
        $username = $dbConfig['username'];
        $password = $dbConfig['password'];

        $fileName = 'db_backup_' . $database . '_' . date('Y-m-d_H-i-s') . '.sql';
        $filePath = storage_path('app/' . $fileName);

        // Command to export database
        // We use --no-tablespaces to avoid permission issues in some environments
        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s --no-tablespaces %s > %s',
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($host),
            escapeshellarg($database),
            escapeshellarg($filePath)
        );

        $output = [];
        $returnVar = null;
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            // Log error for debugging
            Log::error("Database Backup Failed: " . implode("\n", $output));
            return back()->with('error', 'Gagal membuat backup database. Pastikan mysqldump terinstall.');
        }

        // Log the activity
        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
            'action' => 'download_database',
            'model_type' => 'Database',
            'model_id' => 0,
            'description' => "Downloaded Full Database Backup: $fileName",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return response()->download($filePath)->deleteFileAfterSend(true);
    }
    public function importDatabase(Request $request)
    {
        // Only allow Rifal Kurniawan to import the database
        if (auth()->user()->name !== 'Rifal Kurniawan') {
            abort(403);
        }

        $request->validate([
            'db_file' => 'required|file',
            'password' => 'required|string',
        ]);

        // Verify password
        if (!Hash::check($request->password, auth()->user()->getAuthPassword())) {
            return back()->with('error', 'PASSWORD SALAH: Akses ditolak untuk operasi berbahaya.');
        }

        $result = $this->runImport($request->file('db_file')->getRealPath());

        if ($result['status'] === 'success') {
            // Log the activity
            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name,
                'action' => 'import_database',
                'model_type' => 'Database',
                'model_id' => 0,
                'description' => "Imported Database from file: " . $request->file('db_file')->getClientOriginalName(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return back()->with('success', 'Database berhasil diimpor!');
        }

        return back()->with('error', $result['message']);
    }

    private function runImport($filePath)
    {
        try {
            $dbConfig = config('database.connections.mysql');
            $host = $dbConfig['host'];
            $database = $dbConfig['database'];
            $username = $dbConfig['username'];
            $password = $dbConfig['password'];

            // Command to import database
            $command = sprintf(
                'mysql --user=%s --password=%s --host=%s %s < %s',
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($host),
                escapeshellarg($database),
                escapeshellarg($filePath)
            );

            $output = [];
            $returnVar = null;
            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                Log::error("Database Import Failed: " . implode("\n", $output));
                return ['status' => 'error', 'message' => 'Gagal mengimpor database.'];
            }

            return ['status' => 'success'];
        } catch (\Exception $e) {
            Log::error("Database Import Exception: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()];
        }
    }

    public function pushSync(Request $request)
    {
        // Security check
        if (auth()->user()->name !== 'Rifal Kurniawan') {
            abort(403);
        }

        $request->validate(['password' => 'required']);
        if (!Hash::check($request->password, auth()->user()->getAuthPassword())) {
            return response()->json(['status' => 'error', 'message' => 'Password salah!'], 403);
        }

        $targetUrl = env('SYNC_TARGET_URL');
        $token = env('SYNC_SECRET_TOKEN', 'default_sync_token_123');

        if (!$targetUrl) {
            return response()->json(['status' => 'error', 'message' => 'URL target sinkronisasi belum diatur di .env (SYNC_TARGET_URL)'], 400);
        }

        try {
            // 1. Export local DB
            $dbConfig = config('database.connections.mysql');
            $database = $dbConfig['database'];
            $fileName = 'sync_push_temp_' . date('YmdHis') . '.sql';
            $filePath = storage_path('app/' . $fileName);

            $dumpCommand = sprintf(
                'mysqldump --user=%s --password=%s --host=%s --no-tablespaces %s > %s',
                escapeshellarg($dbConfig['username']),
                escapeshellarg($dbConfig['password']),
                escapeshellarg($dbConfig['host']),
                escapeshellarg($database),
                escapeshellarg($filePath)
            );

            exec($dumpCommand, $output, $returnVar);

            if ($returnVar !== 0) {
                return response()->json(['status' => 'error', 'message' => 'Gagal membuat dump database local.'], 500);
            }

            // 2. Send to target
            $response = Http::timeout(180)->attach(
                'db_file', File::get($filePath), $fileName
            )->post($targetUrl . '/api/receive-sync', [
                'sync_token' => $token,
            ]);

            // 3. Cleanup
            File::delete($filePath);

            if ($response->successful()) {
                \App\Models\Backup::create([
                    'user_id' => auth()->id(),
                    'type' => 'database',
                    'status' => 'completed',
                    'details' => "PUSH SYNC: Mengirim data ke $targetUrl"
                ]);

                \App\Models\ActivityLog::create([
                    'user_id' => auth()->id(),
                    'user_name' => auth()->user()->name,
                    'action' => 'push_sync',
                    'model_type' => 'Database',
                    'model_id' => 0,
                    'description' => "Pushed Database Sync to: $targetUrl",
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);

                return response()->json(['status' => 'success', 'message' => 'Push Sync Berhasil! Data di server target telah diperbaharui.']);
            }

            return response()->json(['status' => 'error', 'message' => 'Server target menolak sinkronisasi: ' . ($response->json()['message'] ?? 'Unknown Error')], 500);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal sinkronisasi: ' . $e->getMessage()], 500);
        }
    }

    public function pullSync(Request $request)
    {
        if (auth()->user()->name !== 'Rifal Kurniawan') {
            abort(403);
        }

        $request->validate(['password' => 'required']);
        if (!Hash::check($request->password, auth()->user()->getAuthPassword())) {
            return response()->json(['status' => 'error', 'message' => 'Password salah!'], 403);
        }

        $targetUrl = env('SYNC_TARGET_URL');
        $token = env('SYNC_SECRET_TOKEN', 'default_sync_token_123');

        if (!$targetUrl) {
            return response()->json(['status' => 'error', 'message' => 'URL target sinkronisasi belum diatur di .env (SYNC_TARGET_URL)'], 400);
        }

        try {
            // 1. Request SMART data from target (JSON format)
            $response = Http::timeout(300)->post($targetUrl . '/api/provide-sync', [
                'sync_token' => $token,
                'mode' => 'smart'
            ]);

            if (!$response->successful()) {
                return response()->json(['status' => 'error', 'message' => 'Server target gagal menyediakan data smart sync.'], 500);
            }

            $data = $response->json();
            $allStats = [];
            $syncLog = [];

            // 2. Perform Smart Merge (Upsert) for each model
            \DB::beginTransaction();
            try {
                foreach ($data['models'] as $modelName => $records) {
                    if (empty($records)) continue;

                    $fullModelPath = "App\\Models\\$modelName";
                    if (!class_exists($fullModelPath)) continue;

                    $model = new $fullModelPath;
                    $table = $model->getTable();
                    $fillable = array_merge(['id'], $model->getFillable());
                    
                    // We use database Query Builder for faster batch upsert
                    // This will update existing IDs and insert new ones
                    \DB::table($table)->upsert($records, ['id'], array_diff($fillable, ['id']));
                    
                    $count = count($records);
                    $allStats[$modelName] = $count;
                    $syncLog[] = "Merged $count records for $modelName";
                }
                \DB::commit();
            } catch (\Exception $e) {
                \DB::rollBack();
                throw $e;
            }

            // 3. Finalize Logging
            \App\Models\Backup::create([
                'user_id' => auth()->id(),
                'type' => 'database',
                'status' => 'completed',
                'details' => "SMART PULL SYNC (Merge Mode) dari $targetUrl. " . implode(', ', $syncLog)
            ]);

            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name,
                'action' => 'pull_sync_smart',
                'model_type' => 'Database',
                'model_id' => 0,
                'description' => "Smart Merge Pull from $targetUrl",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'status' => 'success', 
                'message' => 'Smart Sync Berhasil! Data telah digabungkan tanpa menghapus data lokal.',
                'stats' => $allStats
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal Smart Sync: ' . $e->getMessage()], 500);
        }
    }

    public function provideSync(Request $request)
    {
        $token = env('SYNC_SECRET_TOKEN', 'default_sync_token_123');

        if ($request->sync_token !== $token) {
            return response()->json(['status' => 'error', 'message' => 'Token sinkronisasi tidak valid!'], 403);
        }

        // SMART MODE: Send JSON for selective merging
        if ($request->mode === 'smart') {
            try {
                $models = [
                    'User', 'Category', 'Product', 'Customer', 'Invoice', 'InvoiceItem', 
                    'Expense', 'Attendance', 'Salary', 'Supplier', 'SupplierNota',
                    'DeliveryOrder', 'DeliveryOrderItem', 'StockHistory', 'Document'
                ];
                
                $data = [];
                foreach ($models as $m) {
                    $fullClass = "App\\Models\\$m";
                    if (class_exists($fullClass)) {
                        $data[$m] = $fullClass::all()->toArray();
                    }
                }
                
                return response()->json([
                    'status' => 'success',
                    'models' => $data,
                    'timestamp' => now()
                ]);
            } catch (\Exception $e) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }
        }

        // FULL MODE (Default): Send SQL Dump
        try {
            $dbConfig = config('database.connections.mysql');
            $database = $dbConfig['database'];
            $fileName = 'provided_sync_' . date('YmdHis') . '.sql';
            $filePath = storage_path('app/' . $fileName);

            $dumpCommand = sprintf(
                'mysqldump --user=%s --password=%s --host=%s --no-tablespaces %s > %s',
                escapeshellarg($dbConfig['username']),
                escapeshellarg($dbConfig['password']),
                escapeshellarg($dbConfig['host']),
                escapeshellarg($database),
                escapeshellarg($filePath)
            );

            exec($dumpCommand, $output, $returnVar);

            if ($returnVar !== 0) {
                return response()->json(['status' => 'error', 'message' => 'Gagal membuat dump database untuk permintaan pull.'], 500);
            }

            // Calculate stats to show what's being pulled
            $stats = [
                'Invoices' => \App\Models\Invoice::count(),
                'Customers' => \App\Models\Customer::count(),
                'Products' => \App\Models\Product::count(),
                'Attendances' => \App\Models\Attendance::count(),
                'Users' => \App\Models\User::count(),
                'Timestamp' => now()->format('d M Y H:i:s')
            ];

            return response()->download($filePath)
                ->withHeaders(['X-Sync-Stats' => json_encode($stats)])
                ->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal menyediakan data: ' . $e->getMessage()], 500);
        }
    }

    public function receiveSync(Request $request)
    {
        $token = env('SYNC_SECRET_TOKEN', 'default_sync_token_123');

        if ($request->sync_token !== $token) {
            return response()->json(['status' => 'error', 'message' => 'Token sinkronisasi tidak valid!'], 403);
        }

        if (!$request->hasFile('db_file')) {
            return response()->json(['status' => 'error', 'message' => 'File database tidak ditemukan!'], 400);
        }

        $result = $this->runImport($request->file('db_file')->getRealPath());

        if ($result['status'] === 'success') {
            // Log to database for history/audit
            $admin = \App\Models\User::where('name', 'Rifal Kurniawan')->first();
            
            \App\Models\Backup::create([
                'user_id' => $admin ? $admin->id : 1, // Default to 1 if Rifal not found
                'type' => 'database',
                'status' => 'completed',
                'details' => 'SINKRONISASI OTOMATIS: Diterima dari server pengirim.'
            ]);

            \App\Models\ActivityLog::create([
                'user_id' => $admin ? $admin->id : 1,
                'user_name' => 'SYSTEM (Sync)',
                'action' => 'receive_sync',
                'model_type' => 'Database',
                'model_id' => 0,
                'description' => "Received and imported automated database sync.",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            Log::info("Auto Sync received and imported successfully.");
            return response()->json(['status' => 'success', 'message' => 'Database Sync Berhasil di-import secara otomatis.']);
        }

        return response()->json(['status' => 'error', 'message' => $result['message']], 500);
    }
}
