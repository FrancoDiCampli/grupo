<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AllVentasExport implements WithMultipleSheets
{
    public $desde;
    public $hasta;

    public function __construct($desde, $hasta)
    {
        $this->desde = new Carbon($desde);
        $this->hasta = new Carbon($hasta);
    }

    public function sheets(): array
    {
        $sheets = [
            new SheetVentasExport($this->desde, $this->hasta),
            new SheetVendedoresExport($this->desde, $this->hasta),
            new SheetClientesExport($this->desde, $this->hasta),
            new SheetLitrosClientesExport($this->desde, $this->hasta, 2)
        ];

        return $sheets;
    }
}
