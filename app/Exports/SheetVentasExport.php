<?php

namespace App\Exports;

use App\Venta;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;

class SheetVentasExport implements FromQuery, WithTitle, ShouldAutoSize, WithMapping, WithHeadings, WithStyles, WithColumnFormatting, WithEvents
{
    use Exportable, RegistersEventListeners;

    public function query()
    {
        return Venta::query()->whereBetween('created_at', ['2020-10-31', '2020-11-30']);
    }

    public function map($venta): array
    {
        $aux = new Carbon($venta->created_at);
        return [
            $venta->numventa,
            $venta->tipocomprobante,
            $venta->comprobanteadherido,
            $venta->cuit,
            $venta->cliente->razonsocial,
            $venta->bonificacion,
            $venta->recargo,
            $venta->subtotal,
            $venta->total,
            $aux->format('d-m-Y'),
            $venta->user->name,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_PERCENTAGE_00,
            'G' => NumberFormat::FORMAT_PERCENTAGE_00,
            'H' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            'I' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            'Tipo Comprobante',
            'Comprobante Adherido',
            'CUIT',
            'Cliente',
            'Bonificación',
            'Recargo',
            'Subtotal',
            'Total',
            'Fecha',
            'User',
        ];
    }

    public function title(): string
    {
        return 'Ventas';
    }

    public function styles(Worksheet $sheet)
    {
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'font' => [
                'size' => 12,
                'name' => 'Arial'
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center'
            ],
        ];

        $auxStyles = [
            'font' => [
                'bold' => true,
                'size' => 12,
                'name' => 'Arial'
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center'
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                'rotation' => 90,
                'startColor' => [
                    'argb' => 'FFFFFC19',
                ],
                'endColor' => [
                    'argb' => 'FFFFFC19',
                ],
            ],
        ];

        $sheet->getStyle('A1:K1')->applyFromArray($auxStyles);
        $sheet->getStyle('A1:K99')->applyFromArray($styleArray);
    }
}
