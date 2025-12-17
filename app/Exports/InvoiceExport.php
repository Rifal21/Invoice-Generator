<?php

namespace App\Exports;

use App\Models\Invoice;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InvoiceExport implements FromView, ShouldAutoSize, WithStyles, WithDrawings, WithColumnWidths
{
    protected $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function view(): View
    {
        return view('invoices.excel', [
            'invoice' => $this->invoice
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            // 1    => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 15,
            'C' => 15,
            'D' => 15,
            'E' => 10,
            'F' => 10,
            'G' => 15,
            'H' => 20,
        ];
    }

    public function drawings()
    {
        $drawings = [];

        if (file_exists(public_path('images/kopinvoice.png'))) {
            $drawing = new Drawing();
            $drawing->setName('Kop Invoice');
            $drawing->setDescription('Kop Invoice');
            $drawing->setPath(public_path('images/kopinvoice.png'));
            $drawing->setHeight(80);
            $drawing->setCoordinates('A2');
            $drawing->setOffsetX(5);
            $drawing->setOffsetY(5);
            $drawings[] = $drawing;
        }

        if (file_exists(public_path('images/ttd.png'))) {
            $drawing2 = new Drawing();
            $drawing2->setName('Signature');
            $drawing2->setDescription('Signature');
            $drawing2->setPath(public_path('images/ttd.png'));
            $drawing2->setHeight(60);

            // Calculate row for signature
            // Header rows: 10 (Header 5 + Spacer 1 + Customer 2 + Spacer 1 + Table Header 1)
            // Items: count
            // Totals: 4
            // Spacer: 1
            // Hormat Kami: 1
            // Signature Image Row: 17 + count

            $rowCount = 17 + $this->invoice->items->count();

            $drawing2->setCoordinates('G' . $rowCount);
            $drawing2->setOffsetX(20);
            $drawings[] = $drawing2;
        }

        return $drawings;
    }
}
