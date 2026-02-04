<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            $path = $request->file('file')->store('documents', 'public');

            Document::create([
                'title' => $request->title,
                'description' => $request->description,
                'file_path' => $path,
                'mime_type' => $request->file('file')->getClientMimeType(),
            ]);

            return redirect()->route('documents.index')->with('success', 'Dokumen berhasil diupload!');
        }

        return back()->with('error', 'Gagal mengupload file.');
    }

    public function show(Document $document)
    {
        return view('documents.show', compact('document'));
    }

    public function destroy(Document $document)
    {
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()->route('documents.index')->with('success', 'Dokumen berhasil dihapus.');
    }

    public function backup()
    {
        $userId = auth()->id();

        // Initial Cache State for Global Indicator
        \Illuminate\Support\Facades\Cache::put('backup_progress_' . $userId, [
            'status' => 'queued',
            'percentage' => 0,
            'message' => 'Menyiapkan backup dokumen...'
        ], now()->addMinutes(5));

        \App\Jobs\BackupDocumentsJob::dispatch($userId);

        return redirect()->back()->with('success', 'Backup dokumen dimulai! Anda bisa melihat progress di indikator bawah.');
    }
}
