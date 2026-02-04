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

        return view('backup.index', compact('isConnected', 'backups'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:' . (date('Y') + 1),
        ]);

        $month = $request->month;
        $year = $request->year;
        $userId = auth()->id();

        // Check credentials existence
        if (!file_exists(storage_path('app/google/credentials.json'))) {
            return back()->with('error', 'BACKUP GAGAL: File credentials.json (OAuth Client ID) tidak ditemukan.');
        }

        // Create Backup Record Log
        $backup = \App\Models\Backup::create([
            'user_id' => $userId,
            'period_month' => $month,
            'period_year' => $year,
            'status' => 'queued',
            'details' => 'Queued for processing'
        ]);

        // Dispatch Job
        \App\Jobs\ProcessGoogleDriveBackup::dispatch($month, $year, $userId, $backup->id);

        // Log Activity
        \App\Models\ActivityLog::create([
            'user_id' => $userId,
            'user_name' => auth()->user()->name ?? 'Unknown',
            'action' => 'backup_drive_init',
            'model_type' => 'Backup',
            'model_id' => $backup->id,
            'description' => "Initiated Cloud Backup: " . Carbon::createFromDate($year, $month, 1)->format('F Y'),
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
}
