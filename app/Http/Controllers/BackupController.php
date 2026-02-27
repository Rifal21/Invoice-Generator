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
use Illuminate\Support\Facades\DB;

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
                $errorMessage = 'Server target gagal menyediakan data smart sync.';
                try {
                    $errorJson = $response->json();
                    if (isset($errorJson['message'])) {
                        $errorMessage .= ' Detail: ' . $errorJson['message'];
                    }
                } catch (\Exception $e) {
                    $errorMessage .= ' (Status: ' . $response->status() . ')';
                }
                return response()->json(['status' => 'error', 'message' => $errorMessage], 500);
            }

            $data = $response->json();
            $allStats = [];
            $syncLog = [];
            $idMap = []; // [ModelName => [RemoteID => LocalID]]

            // Define Model Metadata for Intelligence
            $modelMeta = [
                'User' => ['nk' => ['email'], 'fk' => []],
                'Category' => ['nk' => ['name'], 'fk' => []],
                'Supplier' => ['nk' => ['name'], 'fk' => []],
                'Customer' => ['nk' => ['name'], 'fk' => []],
                'AttendanceSetting' => ['nk' => [], 'singleton' => true],
                'Document' => ['nk' => ['title'], 'fk' => []],
                'Product' => ['nk' => ['name'], 'fk' => ['category_id' => 'Category', 'supplier_id' => 'Supplier']],
                'Invoice' => ['nk' => ['invoice_number'], 'fk' => []],
                'InvoiceItem' => ['nk' => ['invoice_id', 'product_name', 'quantity', 'total'], 'fk' => ['invoice_id' => 'Invoice', 'product_id' => 'Product']],
                'Expense' => ['nk' => ['description', 'amount', 'date'], 'fk' => ['category_id' => 'Category']],
                'Attendance' => ['nk' => ['user_id', 'date'], 'fk' => ['user_id' => 'User', 'approved_by' => 'User']],
                'Salary' => ['nk' => ['user_id', 'period'], 'fk' => ['user_id' => 'User']],
                'RiceDelivery' => ['nk' => ['nota_number'], 'fk' => []],
                'RiceDeliveryItem' => ['nk' => ['rice_delivery_id', 'description', 'total'], 'fk' => ['rice_delivery_id' => 'RiceDelivery']],
                'DediInvoice' => ['nk' => ['invoice_number'], 'fk' => []],
                'DediInvoiceItem' => ['nk' => ['dedi_invoice_id', 'item_name', 'quantity'], 'fk' => ['dedi_invoice_id' => 'DediInvoice']],
                'KitchenIncentive' => ['nk' => ['invoice_number'], 'fk' => ['customer_id' => 'Customer']],
                'KitchenIncentiveItem' => ['nk' => ['kitchen_incentive_id', 'description', 'total_price'], 'fk' => ['kitchen_incentive_id' => 'KitchenIncentive']],
                'VehicleRentalInvoice' => ['nk' => ['invoice_number'], 'fk' => []],
                'VehicleRentalItem' => ['nk' => ['vehicle_rental_invoice_id', 'description', 'total'], 'fk' => ['vehicle_rental_invoice_id' => 'VehicleRentalInvoice']],
            ];

            // 2. Perform Intelligent Merge
            ini_set('memory_limit', '1024M');
            ini_set('max_execution_time', 600);
            
            DB::beginTransaction();
            try {
                // Iterate through models in defined order (to satisfy dependencies)
                $syncOrder = array_keys($modelMeta);
                
                foreach ($syncOrder as $modelName) {
                    if (!isset($data['models'][$modelName]) || empty($data['models'][$modelName])) continue;
                    
                    $records = $data['models'][$modelName];
                    $meta = $modelMeta[$modelName];
                    $fullModelPath = "App\\Models\\$modelName";
                    
                    if (!class_exists($fullModelPath)) continue;
                    
                    $modelInstance = new $fullModelPath;
                    $tableName = $modelInstance->getTable();
                    $tableColumns = DB::getSchemaBuilder()->getColumnListing($tableName);
                    $primaryKey = $modelInstance->getKeyName() ?: 'id';
                    
                    $processedCount = 0;
                    
                    foreach ($records as $remoteRecord) {
                        $remoteId = $remoteRecord[$primaryKey] ?? null;
                        
                        // 1. Clean and filter record
                        $recordData = array_intersect_key($remoteRecord, array_flip($tableColumns));
                        
                        // Fix date formats
                        foreach ($recordData as $key => $value) {
                            if (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', $value)) {
                                $recordData[$key] = \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s');
                            }
                        }
                        
                        // 2. Translate Foreign Keys
                        foreach ($meta['fk'] ?? [] as $fkColumn => $parentModel) {
                            if (isset($recordData[$fkColumn]) && isset($idMap[$parentModel][$recordData[$fkColumn]])) {
                                $recordData[$fkColumn] = $idMap[$parentModel][$recordData[$fkColumn]];
                            }
                        }
                        
                        // 3. Find if record exists locally
                        $localRecord = null;
                        
                        // Search by Natural Key first
                        if (!empty($meta['nk'] ?? [])) {
                            $query = DB::table($tableName);
                            foreach ($meta['nk'] as $nkCol) {
                                $query->where($nkCol, $recordData[$nkCol] ?? null);
                            }
                            $localRecord = $query->first();
                        }
                        
                        // Singleton handling
                        if (!$localRecord && ($meta['singleton'] ?? false)) {
                            $localRecord = DB::table($tableName)->first();
                        }
                        
                        // Match by Remote ID as last resort if NK missing or if the NK points to same ID anyway
                        if (!$localRecord && $remoteId) {
                            $localRecord = DB::table($tableName)->where($primaryKey, $remoteId)->first();
                        }
                        
                        if ($localRecord) {
                            $localId = $localRecord->$primaryKey;
                            
                            // Check if "Exactly the Same" to avoid unnecessary writes
                            $isIdentical = true;
                            foreach ($recordData as $key => $val) {
                                if (in_array($key, [$primaryKey, 'created_at', 'updated_at'])) continue;
                                if ((string)($localRecord->$key ?? '') !== (string)($val ?? '')) {
                                    $isIdentical = false;
                                    break;
                                }
                            }
                            
                            if (!$isIdentical) {
                                DB::table($tableName)->where($primaryKey, $localId)->update(array_diff_key($recordData, array_flip([$primaryKey, 'created_at'])));
                            }
                            
                            $idMap[$modelName][$remoteId] = $localId;
                        } else {
                            // 4. Insert as New
                            $newRecord = $recordData;
                            unset($newRecord[$primaryKey]); // Let local DB assign new ID
                            
                            $newId = DB::table($tableName)->insertGetId($newRecord);
                            $idMap[$modelName][$remoteId] = $newId;
                        }
                        
                        $processedCount++;
                    }
                    
                    $allStats[$modelName] = $processedCount;
                    $syncLog[] = "Processed $processedCount records for $modelName";
                }
                
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Pull Sync Error at $modelName: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
                throw $e;
            }

            // 3. Finalize Logging
            \App\Models\Backup::create([
                'user_id' => auth()->id(),
                'type' => 'database',
                'status' => 'completed',
                'details' => "SMART MERGE PULL dari $targetUrl. Data telah digabungkan dengan pengecekan duplikasi."
            ]);

            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name,
                'action' => 'pull_sync_smart',
                'model_type' => 'Database',
                'model_id' => 0,
                'description' => "Smart Merge Pull from $targetUrl with ID mapping.",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'status' => 'success', 
                'message' => 'Smart Sync Berhasil! Data telah digabungkan tanpa duplikasi (mapping ID otomatis).',
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
                    'Expense', 'Attendance', 'AttendanceSetting', 'Salary', 'Supplier', 'SupplierNota',
                    'DeliveryOrder', 'DeliveryOrderItem', 'StockHistory', 'Document',
                    'RiceDelivery', 'RiceDeliveryItem', 'DediInvoice', 'DediInvoiceItem',
                    'KitchenIncentive', 'KitchenIncentiveItem', 'VehicleRentalInvoice', 'VehicleRentalItem'
                ];
                
                $data = [];
                foreach ($models as $m) {
                    $fullClass = "App\\Models\\$m";
                    if (class_exists($fullClass)) {
                        $model = new $fullClass;
                        // Use DB::table to bypass Eloquent hidden attributes (like password)
                        // This ensures all data is sent for the sync
                        $data[$m] = DB::table($model->getTable())->get()->map(function($item) {
                            return (array) $item;
                        })->toArray();
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
