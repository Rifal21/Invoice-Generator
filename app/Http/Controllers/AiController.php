<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Inventory;
use App\Services\AiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AiController extends Controller
{
    protected $aiService;

    public function __construct(AiService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function index()
    {
        return view('ai.index');
    }

    public function analyze(Request $request)
    {
        try {
            $request->validate([
                'prompt' => 'required|string|max:500'
            ]);

            // Gather Data Context
            $context = $this->gatherContext();

            // Call AI Service
            $response = $this->aiService->analyze($request->prompt, $context);

            return response()->json([
                'success' => true,
                'response' => \Illuminate\Support\Str::markdown($response)
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('AI Analysis Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    private function gatherContext()
    {
        // Basic stats for AI to understand the business state
        // Refactored to use existing columns in KJRS database
        return [
            'overview' => [
                'total_products' => Product::count(),
                'total_customers' => Customer::count(),
                'total_invoices' => Invoice::count(),
                'total_revenue' => Invoice::sum('total_amount'),
            ],
            'products_status' => Product::select('name', 'stock', 'unit')
                ->where('stock', '<=', 10)
                ->get()
                ->toArray(), // Low stock products
            'recent_invoices' => Invoice::latest()
                ->take(5)
                ->get()
                ->map(fn($i) => [
                    'number' => $i->invoice_number,
                    'customer' => $i->customer_name,
                    'total' => $i->total_amount,
                    'date' => $i->date
                ])->toArray(),
            'top_products' => DB::table('invoice_items')
                ->select('product_name', DB::raw('SUM(quantity) as total_sold'))
                ->groupBy('product_name')
                ->orderByDesc('total_sold')
                ->take(5)
                ->get()
                ->toArray(),
            'current_date' => now()->format('Y-m-d H:i:s'),
            'app_info' => 'Sistem Koperasi Jembar Rahayu Sejahtera (KJRS) - Invoice, Inventory, & Customer Management'
        ];
    }
}
