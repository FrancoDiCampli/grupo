<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AllComprasExport implements WithMultipleSheets
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
            new SheetComprasExport($this->desde, $this->hasta),
            new SheetComprasProveedoresExport($this->desde, $this->hasta)
        ];

        return $sheets;
    }
}
