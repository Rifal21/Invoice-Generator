<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;

class ProductImport implements WithMultipleSheets, SkipsUnknownSheets
{
    public function sheets(): array
    {
        /**
         * We handle up to 50 sheets dynamically by their index.
         * The CategorySheetImport will use the sheet name (BeforeSheet event)
         * to determine the correct category for each sheet.
         */
        $sheets = [];
        for ($i = 0; $i < 50; $i++) {
            $sheets[$i] = new CategorySheetImport();
        }
        return $sheets;
    }

    public function onUnknownSheet($sheetName)
    {
        // This is a fallback but with the index-based approach above it might not be needed
        return new CategorySheetImport();
    }
}
