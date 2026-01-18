<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class CategoryProductSheet implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle, WithColumnFormatting
{
    private $category;

    public function __construct($category)
    {
        $this->category = $category;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->category->products;
    }

    public function headings(): array
    {
        return [
            'Nama Barang',
            'Harga',
            'Satuan',
            'Deskripsi',
        ];
    }

    public function map($product): array
    {
        return [
            $product->name,
            $product->price,
            $product->unit,
            $product->description,
        ];
    }

    public function title(): string
    {
        // Excel sheet title limit is 31 characters
        // Forbidden characters: \ / ? * : [ ]
        $name = str_replace(['\\', '/', '?', '*', ':', '[', ']'], '', $this->category->name);
        return substr($name, 0, 31);
    }

    public function columnFormats(): array
    {
        return [
            'B' => '"Rp"#,##0',
        ];
    }
}
