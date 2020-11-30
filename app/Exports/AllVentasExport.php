<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AllVentasExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        $sheets = [
            new SheetVentasExport(),
            new SheetVendedoresExport(),
            new SheetClientesExport(),
        ];

        return $sheets;
    }
}
