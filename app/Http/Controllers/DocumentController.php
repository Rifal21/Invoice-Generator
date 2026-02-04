<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = Document::latest()->get();
        return view('documents.index', compact('documents'));
    }

    public function create()
    {
        return view('documents.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:pdf|max:10240', // Max 10MB
        ]);

        if ($request->file('file')) {
            // Store in LOCAL storage. Depending on Laravel config, this might be storage/app or storage/app/private
            $path = $request->file('file')->store('document', 'local');

            Document::create([
                'title' => $request->title,
                'description' => $request->description,
                'file_path' => $path,
                'mime_type' => $request->file('file')->getClientMimeType(),
            ]);

            return redirect()->route('documents.index')->with('success', 'Dokumen berhasil diupload secara aman!');
        }

        return back()->with('error', 'Gagal mengupload file.');
    }

    public function show(Document $document)
    {
        return view('documents.show', compact('document'));
    }

    public function stream(Document $document)
    {
        $paths = [
            // 1. Try Local Storage (Standard storage/app/documents/...)
            storage_path('app/' . $document->file_path),

            // 2. Try Private Storage (Laravel 11 structure: storage/app/private/documents/...)
            storage_path('app/private/' . $document->file_path),

            // 3. Try Public Storage (Old Files: storage/app/public/documents/...)
            storage_path('app/public/' . $document->file_path),

            // 4. Try User Suggested Path (private/document singular?)
            storage_path('app/private/document/' . basename($document->file_path)),
        ];

        $path = null;
        foreach ($paths as $p) {
            // Log::info("Checking Path: $p"); // Uncomment for verbose logging
            if (file_exists($p)) {
                $path = $p;
                break;
            }
        }

        if (!$path) {
            Log::error("Doc {$document->id} NOT FOUND. Checked: " . implode(', ', $paths));
            abort(404, 'File dokumen tidak ditemukan di server.');
        }

        return response()->file($path, [
            'Content-Type' => $document->mime_type ?? 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $document->title . '.pdf"',
            'X-Robots-Tag' => 'noindex, nofollow',
        ]);
    }

    public function destroy(Document $document)
    {
        // Manual deletion attempts since we have multiple potential paths
        $deleted = false;

        $paths = [
            'documents' => $document->file_path, // storage/app
            'public/' . $document->file_path, // storage/app/public
            'private/' . $document->file_path, // storage/app/private
            'private/document/' . basename($document->file_path) // User custom
        ];

        // Try using Storage facade for standard disks
        if (Storage::disk('local')->exists($document->file_path)) {
            Storage::disk('local')->delete($document->file_path);
            $deleted = true;
        } elseif (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
            $deleted = true;
        }

        // If not deleted by Facade, try raw unlink on found path
        if (!$deleted) {
            // Logic similar to stream search
            $possiblePaths = [
                storage_path('app/' . $document->file_path),
                storage_path('app/private/' . $document->file_path),
                storage_path('app/private/document/' . basename($document->file_path)),
            ];
            foreach ($possiblePaths as $p) {
                if (file_exists($p)) {
                    @unlink($p);
                    $deleted = true;
                }
            }
        }

        $document->delete();

        return redirect()->route('documents.index')->with('success', 'Dokumen berhasil dihapus.');
    }

    public function backup()
    {
        $userId = auth()->id();

        // Initial Cache State for Global Indicator
        Cache::put('backup_progress_' . $userId, [
            'status' => 'queued',
            'percentage' => 0,
            'message' => 'Menyiapkan backup dokumen...'
        ], now()->addMinutes(5));

        \App\Jobs\BackupDocumentsJob::dispatch($userId);

        return redirect()->back()->with('success', 'Backup dokumen dimulai! Anda bisa melihat progress di indikator bawah.');
    }
}
