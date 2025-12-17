<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ProductImport implements ToModel, WithStartRow, WithBatchInserts, WithChunkReading
{
    private $categories;

    public function __construct()
    {
        // Load all categories into memory to avoid N+1 queries
        // Key is name, Value is ID
        $this->categories = Category::pluck('id', 'name')->toArray();
    }

    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Skip if name is empty
        if (empty($row[0])) {
            return null;
        }

        // Handle Category
        $categoryName = $row[1] ?? 'Uncategorized';

        // Check if category exists in cache to avoid query
        if (isset($this->categories[$categoryName])) {
            $categoryId = $this->categories[$categoryName];
        } else {
            // Create new category and update cache
            $category = Category::firstOrCreate(['name' => $categoryName]);
            $categoryId = $category->id;
            $this->categories[$categoryName] = $categoryId;
        }

        // Handle Price
        $priceString = $row[2] ?? '0';
        $price = preg_replace('/[^0-9]/', '', $priceString);

        return new Product([
            'name'        => $row[0],
            'category_id' => $categoryId,
            'price'       => (int) $price,
            'unit'        => $row[3] ?? null,
            'description' => $row[4] ?? null,
        ]);
    }

    public function batchSize(): int
    {
        return 1000;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
