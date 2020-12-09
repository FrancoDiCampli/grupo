<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class OnlyLitrosClienteExport implements WithMultipleSheets
{
    public $desde;
    public $hasta;
    public $id;

    public function __construct($desde, $hasta, $id)
    {
        $this->desde = new Carbon($desde);
        $this->hasta = new Carbon($hasta);
        $this->id = $id;
    }

    public function sheets(): array
    {
        $sheets = [
            new SheetLitrosClientesExport($this->desde, $this->hasta, $this->id)
        ];

        return $sheets;
    }
}
