<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $perPage = $request->input('per_page', 10);
        $products = $query->paginate($perPage)->withQueryString();
        $categories = Category::all();
        $suppliers = Supplier::all();

        return view('products.index', compact('products', 'categories', 'suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $suppliers = Supplier::all();
        return view('products.create', compact('categories', 'suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'price' => 'required|numeric',
            'purchase_price' => 'nullable|numeric|min:0',
            'unit' => 'required|string',
            'stock' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable', // file or base64 string
        ]);

        $data = $request->all();

        if ($request->has('image') && $request->image) {
            // Check if base64
            if (preg_match('/^data:image\/(\w+);base64,/', $request->image, $type)) {
                $image_data = substr($request->image, strpos($request->image, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, gif
                $image_data = base64_decode($image_data);
                $image_name = 'product_' . time() . '.' . $type;
                \Storage::disk('public')->put('products/' . $image_name, $image_data);
                $data['image'] = 'products/' . $image_name;
            } elseif ($request->hasFile('image')) {
                $file = $request->file('image');
                $path = $file->store('products', 'public');
                $data['image'] = $path;
            }
        }

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        if (request()->wantsJson()) {
            return response()->json($product->load(['category', 'supplier']));
        }
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        $suppliers = Supplier::all();
        return view('products.edit', compact('product', 'categories', 'suppliers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'price' => 'required|numeric',
            'purchase_price' => 'nullable|numeric|min:0',
            'unit' => 'required|string',
            'stock' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable',
        ]);

        $data = $request->all();

        if ($request->has('image') && $request->image) {
            // Check if base64
            if (preg_match('/^data:image\/(\w+);base64,/', $request->image, $type)) {
                $image_data = substr($request->image, strpos($request->image, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, gif
                $image_data = base64_decode($image_data);
                $image_name = 'product_' . time() . '.' . $type;
                \Storage::disk('public')->put('products/' . $image_name, $image_data);
                $data['image'] = 'products/' . $image_name;
            } elseif ($request->hasFile('image')) {
                $file = $request->file('image');
                $path = $file->store('products', 'public');
                $data['image'] = $path;
            }
        } else {
            // Keep old image if no new one sent
            unset($data['image']);
        }

        $product->update($data);

        return redirect()->route('products.index', $request->query())->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->back()->with('success', 'Product deleted successfully.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:products,id',
        ]);

        Product::whereIn('id', $request->ids)->delete();

        return redirect()->back()->with('success', count($request->ids) . ' products deleted successfully.');
    }

    public function quickUpdate(Request $request, Product $product)
    {
        $data = $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'supplier_id' => 'sometimes|nullable|exists:suppliers,id',
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'purchase_price' => 'sometimes|nullable|numeric|min:0',
            'stock' => 'sometimes|numeric|min:0',
            'unit' => 'sometimes|string',
            'description' => 'sometimes|nullable|string',
        ]);

        // Ensure empty string is null for supplier_id
        if (array_key_exists('supplier_id', $data) && $data['supplier_id'] === "") {
            $data['supplier_id'] = null;
        }

        $product->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Data updated successfully',
            'category_name' => $product->category->name,
            'supplier_name' => $product->supplier ? $product->supplier->name : '-',
            'product' => $product
        ]);
    }

    public function deleteImage(Product $product)
    {
        if ($product->image) {
            \Storage::disk('public')->delete($product->image);
            $product->update(['image' => null]);
            return response()->json(['success' => true, 'message' => 'Image deleted successfully.']);
        }
        return response()->json(['success' => false, 'message' => 'No image to delete.']);
    }

    public function import(Request $request)
    {
        set_time_limit(300); // Increase time limit to 5 minutes

        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        Excel::import(new \App\Imports\ProductImport, $request->file('file'));

        return redirect()->route('products.index')->with('success', 'Products imported successfully.');
    }

    public function export(Request $request)
    {
        $type = $request->query('type', 'client'); // default to client
        $fileName = ($type === 'internal' ? 'internal_' : '') . 'products_' . now()->format('d F Y') . '.xlsx';
        return Excel::download(new \App\Exports\ProductExport($type), $fileName);
    }
}
