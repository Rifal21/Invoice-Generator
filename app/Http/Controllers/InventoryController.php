<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockHistory;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $products = $query->paginate(15);
        $categories = Category::all();

        return view('inventory.index', compact('products', 'categories'));
    }

    public function adjustStock(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|numeric',
            'type' => 'required|in:in,out',
            'description' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $product) {
            $quantity = $request->quantity;
            $type = $request->type;

            if ($type == 'in') {
                $product->increment('stock', $quantity);
            } else {
                $product->decrement('stock', $quantity);
            }

            StockHistory::create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'type' => $type,
                'reference' => 'Manual Adjustment',
                'description' => $request->description,
            ]);
        });

        return redirect()->back()->with('success', 'Stok berhasil diperbarui.');
    }

    public function history(Product $product)
    {
        $histories = $product->stockHistories()->latest()->paginate(20);
        return view('inventory.history', compact('product', 'histories'));
    }
}
