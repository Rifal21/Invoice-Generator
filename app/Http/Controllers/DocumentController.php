<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

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
            // Store in LOCAL storage (secure, not accessible via public URL)
            $path = $request->file('file')->store('documents', 'local');

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
        // 1. Try Local Storage (New Secure Files)
        $path = storage_path('app/' . $document->file_path);

        // 2. Fallback to Public Storage (Old Files)
        if (!file_exists($path)) {
            $path = storage_path('app/public/' . $document->file_path);
        }

        if (!file_exists($path)) {
            abort(404, 'File dokumen tidak ditemukan di server.');
        }

        return response()->file($path, [
            'Content-Type' => $document->mime_type ?? 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $document->title . '.pdf"',
            'X-Robots-Tag' => 'noindex, nofollow', // Prevent indexing
        ]);
    }

    public function destroy(Document $document)
    {
        // Try deleting from local
        if (Storage::disk('local')->exists($document->file_path)) {
            Storage::disk('local')->delete($document->file_path);
        }
        // Try deleting from public (cleanup old files)
        elseif (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
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
