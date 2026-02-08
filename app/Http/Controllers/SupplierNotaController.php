<?php

namespace App\Http\Controllers;

use App\Models\SupplierNota;
use App\Models\Supplier;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class SupplierNotaController extends Controller
{
    public function index(Request $request)
    {
        $query = SupplierNota::with('supplier');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nota_number', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('transaction_date', $request->date);
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        $notas = $query->latest('transaction_date')->paginate(10);
        $suppliers = Supplier::orderBy('name')->get();

        // Stats for Dashboard Feel
        $stats = [
            'total_count' => SupplierNota::count(),
            'total_amount' => SupplierNota::sum('total_amount'),
            'today_count' => SupplierNota::whereDate('created_at', today())->count(),
        ];

        return view('supplier-notas.index', compact('notas', 'suppliers', 'stats'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        return view('supplier-notas.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required',
            'nota_number' => 'required|string|max:255',
            'transaction_date' => 'required|date',
            'total_amount' => 'required|numeric|min:0',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'description' => 'nullable|string',
        ]);

        $supplierId = $request->supplier_id;

        // Check if supplierId is a valid existing ID
        if (is_numeric($supplierId)) {
            $exists = Supplier::where('id', $supplierId)->exists();
            if (!$exists) {
                // If it's numeric but doesn't exist as ID, treat it as a name (though rare)
                $supplier = Supplier::firstOrCreate(['name' => $supplierId]);
                $supplierId = $supplier->id;
            }
            // else it's a valid ID, we're good
        } else {
            // It's a string name (Select2 tags)
            $supplier = Supplier::firstOrCreate(['name' => $supplierId]);
            $supplierId = $supplier->id;
        }

        $file = $request->file('file');
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('supplier-notas', $filename, 'public');

        $nota = SupplierNota::create([
            'supplier_id' => $supplierId,
            'nota_number' => $request->nota_number,
            'transaction_date' => $request->transaction_date,
            'total_amount' => $request->total_amount,
            'file_path' => $path,
            'description' => $request->description,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
            'action' => 'create',
            'model_type' => 'SupplierNota',
            'model_id' => $nota->id,
            'description' => "Menambahkan nota supplier #{$nota->nota_number} dari {$nota->supplier->name}",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('supplier-notas.index')->with('success', 'Nota supplier berhasil diunggah.');
    }

    public function show(SupplierNota $supplierNota)
    {
        return view('supplier-notas.show', compact('supplierNota'));
    }

    public function destroy(SupplierNota $supplierNota)
    {
        // Delete file
        if (Storage::disk('public')->exists($supplierNota->file_path)) {
            Storage::disk('public')->delete($supplierNota->file_path);
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
            'action' => 'delete',
            'model_type' => 'SupplierNota',
            'model_id' => $supplierNota->id,
            'description' => "Menghapus nota supplier #{$supplierNota->nota_number} dari {$supplierNota->supplier->name}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $supplierNota->delete();

        return back()->with('success', 'Nota supplier berhasil dihapus.');
    }

    public function download(SupplierNota $supplierNota)
    {
        if (!Storage::disk('public')->exists($supplierNota->file_path)) {
            abort(404);
        }

        return Storage::disk('public')->download($supplierNota->file_path);
    }
}
