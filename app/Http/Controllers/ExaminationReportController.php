<?php

namespace App\Http\Controllers;

use App\Models\ExaminationReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

use App\Models\Customer;

class ExaminationReportController extends Controller
{
    public function index()
    {
        $reports = ExaminationReport::with(['user', 'customer'])->latest()->get();
        return view('examination-reports.index', compact('reports'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        return view('examination-reports.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'report_date' => 'required|date',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:pdf,jpg,png,jpeg,gif|max:10240', // Max 10MB
        ]);

        if ($request->file('file')) {
            $path = $request->file('file')->store('examination-reports', 'local');

            ExaminationReport::create([
                'customer_id' => $request->customer_id,
                'report_date' => $request->report_date,
                'description' => $request->description,
                'file_path' => $path,
                'user_id' => auth()->id(),
            ]);

            return redirect()->route('examination-reports.index')->with('success', 'Laporan pemeriksaan berhasil disimpan!');
        }

        return back()->with('error', 'Gagal memproses file laporan.');
    }

    public function show(ExaminationReport $examinationReport)
    {
        return view('examination-reports.show', compact('examinationReport'));
    }

    public function stream(ExaminationReport $examinationReport)
    {
        $path = storage_path('app/' . $examinationReport->file_path);

        if (!file_exists($path)) {
            // Check other potential locations (like private disk)
            $path = storage_path('app/private/' . $examinationReport->file_path);
            if (!file_exists($path)) {
                abort(404, 'File laporan tidak ditemukan di server.');
            }
        }

        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $mime = 'application/pdf';
        if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
            $mime = 'image/' . ($extension === 'jpg' ? 'jpeg' : $extension);
        }

        return response()->file($path, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . $examinationReport->title . '.' . $extension . '"',
        ]);
    }

    public function destroy(ExaminationReport $examinationReport)
    {
        if (Storage::disk('local')->exists($examinationReport->file_path)) {
            Storage::disk('local')->delete($examinationReport->file_path);
        }

        $examinationReport->delete();

        return redirect()->route('examination-reports.index')->with('success', 'Laporan pemeriksaan berhasil dihapus.');
    }
}
