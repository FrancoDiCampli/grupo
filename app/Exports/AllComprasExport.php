<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AllComprasExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        $sheets = [
            new ReportesExport(),
            new ArticulosExport(),
        ];

        return $sheets;
    }
}
