<?php

namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ProductExport implements WithMultipleSheets
{
    private $type;

    public function __construct($type = 'client')
    {
        $this->type = $type;
    }

    public function sheets(): array
    {
        $sheets = [];

        $categories = Category::with(['products' => function ($query) {
            // If internal, we might want to eager load supplier too
            $query->with('supplier');
        }])->get();

        foreach ($categories as $category) {
            if ($category->products->count() > 0) {
                $sheets[] = new CategoryProductSheet($category, $this->type);
            }
        }

        return $sheets;
    }
}
