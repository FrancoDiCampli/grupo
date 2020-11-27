<?php

namespace App\Exports;

use App\Venta;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithTitle;

class ReportesExport implements FromCollection, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithProperties, WithTitle
{
    public function collection()
    {
        $from = new Carbon('2020-10-31');
        $to = now();
        return Venta::whereDate('created_at', '>=', $from->format('Y-m-d'))->whereDate('created_at', '<=', $to->addDay()->format('Y-m-d'))->get(['tipocomprobante', 'cuit', 'fecha', 'bonificacion', 'recargo', 'subtotal', 'total']);
    }

    public function headings(): array
    {
        return ["tipo comprobante", "cuit", "fecha", 'bonificacion', 'recargo', 'subtotal', 'total'];
    }

    public function columnFormats(): array
    {
        return [
            'C' => 'd-m-Y',
            'D' => NumberFormat::FORMAT_NUMBER_00,
            'E' => NumberFormat::FORMAT_NUMBER_00,
            'F' => NumberFormat::FORMAT_NUMBER_00,
            'G' => NumberFormat::FORMAT_NUMBER_00,
        ];
    }

    public function properties(): array
    {
        return [
            'creator'        => 'Controller POS',
            'lastModifiedBy' => 'Controller POS',
            'title'          => 'Reportes Ventas',
            'description'    => 'Reportes Ventas',
            'subject'        => 'Reportes Ventas',
            'keywords'       => 'Ventas,Reportes',
            'category'       => 'Ventas',
            'manager'        => 'Controller POS',
            'company'        => 'Controller POS',
        ];
    }

    public function title(): string
    {
        return 'Ventas';
    }
}
