<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
}
