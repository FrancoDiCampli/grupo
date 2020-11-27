<?php

namespace App\Exports;

use App\Articulo;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithTitle;

class ArticulosExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithTitle
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Articulo::get(['articulo', 'precio', 'litros']);
    }

    public function headings(): array
    {
        return ["articulo", "precio", 'litros'];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_NUMBER_00,
        ];
    }

    public function title(): string
    {
        return 'Articulos';
    }
}
