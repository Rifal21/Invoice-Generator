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
    private $type;

    public function __construct($category, $type = 'client')
    {
        $this->category = $category;
        $this->type = $type;
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
        $headings = [
            'Nama Barang',
            'Harga Jual', // clarified
            'Satuan',
        ];

        if ($this->type === 'internal') {
            $headings[] = 'Harga Beli';
            $headings[] = 'Stok';
            $headings[] = 'Supplier';
        }

        $headings[] = 'Deskripsi';

        return $headings;
    }

    public function map($product): array
    {
        $row = [
            $product->name,
            $product->price,
            $product->unit,
        ];

        if ($this->type === 'internal') {
            $row[] = $product->purchase_price;
            $row[] = $product->stock;
            $row[] = $product->supplier ? $product->supplier->name : '-';
        }

        $row[] = $product->description;

        return $row;
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
        $formats = [
            'B' => '"Rp"#,##0', // Price is always B
        ];

        if ($this->type === 'internal') {
            $formats['D'] = '"Rp"#,##0'; // Purchase Price becomes D (Name, Price, Unit, PurchasePrice)
        }

        return $formats;
    }
}
