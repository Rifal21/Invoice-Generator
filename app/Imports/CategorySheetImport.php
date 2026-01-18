<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeSheet;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class CategorySheetImport implements ToCollection, WithHeadingRow, WithEvents
{
    private $categoryId;
    private $sheetName;

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $this->sheetName = trim($event->getSheet()->getTitle());
                // Find category by name (case-insensitive)
                $category = Category::where('name', 'LIKE', $this->sheetName)->first();
                if (!$category) {
                    $category = Category::create(['name' => $this->sheetName]);
                }
                $this->categoryId = $category->id;
            },
        ];
    }

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Using slugs from WithHeadingRow: 'Nama Barang' -> 'nama_barang', 'Harga' -> 'harga'
            $name = isset($row['nama_barang']) ? trim((string)$row['nama_barang']) : null;

            if (empty($name)) {
                $name = isset($row['nama']) ? trim((string)$row['nama']) : null;
            }

            if (empty($name)) {
                continue;
            }

            $priceVal = $row['harga'] ?? 0;
            $price = 0;

            if (is_numeric($priceVal)) {
                $price = (float) $priceVal;
            } else if (is_string($priceVal)) {
                // Remove whitespace and currency symbols
                $filtered = preg_replace('/[^\d,.]/', '', $priceVal);

                if (str_contains($filtered, '.') && str_contains($filtered, ',')) {
                    $dotPos = strpos($filtered, '.');
                    $commaPos = strpos($filtered, ',');
                    if ($dotPos < $commaPos) {
                        // ID format: 1.500,00
                        $filtered = str_replace('.', '', $filtered);
                        $filtered = str_replace(',', '.', $filtered);
                    } else {
                        // US format: 1,500.00
                        $filtered = str_replace(',', '', $filtered);
                    }
                } elseif (str_contains($filtered, ',')) {
                    $parts = explode(',', $filtered);
                    if (count($parts) == 2 && strlen($parts[1]) == 3) {
                        $filtered = str_replace(',', '', $filtered);
                    } else {
                        $filtered = str_replace(',', '.', $filtered);
                    }
                } elseif (str_contains($filtered, '.')) {
                    $parts = explode('.', $filtered);
                    if (count($parts) == 2 && strlen($parts[1]) == 3) {
                        $filtered = str_replace('.', '', $filtered);
                    }
                }
                $price = (float) $filtered;
            }

            // Find existing product by name and category
            $product = Product::where('category_id', $this->categoryId)
                ->where('name', 'LIKE', $name)
                ->first();

            if ($product) {
                // Found existing -> UPDATE
                $product->price = $price;
                if (isset($row['satuan'])) {
                    $product->unit = trim((string)$row['satuan']);
                }
                if (isset($row['deskripsi'])) {
                    $product->description = trim((string)$row['deskripsi']);
                }
                $product->save();
            } else {
                // Not found -> CREATE
                Product::create([
                    'name'        => $name,
                    'category_id' => $this->categoryId,
                    'price'       => $price,
                    'unit'        => isset($row['satuan']) ? trim((string)$row['satuan']) : null,
                    'description' => isset($row['deskripsi']) ? trim((string)$row['deskripsi']) : null,
                ]);
            }
        }
    }
}
