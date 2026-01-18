<?php

namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ProductExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        $sheets = [];

        $categories = Category::with('products')->get();

        foreach ($categories as $category) {
            if ($category->products->count() > 0) {
                $sheets[] = new CategoryProductSheet($category);
            }
        }

        return $sheets;
    }
}
