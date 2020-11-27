<?php

namespace App\Exports;

use App\Exports\ReportesExport;
use App\Exports\ArticulosExport;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AllExport implements WithMultipleSheets
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
