<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ProductExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Product::with('category')->get();
    }

    public function headings(): array
    {
        return [
            'Nama Barang',
            'Kategori',
            'Harga',
            'Satuan',
            'Deskripsi',
        ];
    }

    public function map($product): array
    {
        return [
            $product->name,
            $product->category ? $product->category->name : '',
            $product->price,
            $product->unit,
            $product->description,
        ];
    }
}
