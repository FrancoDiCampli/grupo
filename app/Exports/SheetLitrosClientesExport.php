<?php

namespace App\Exports;

use App\Cliente;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;

class SheetLitrosClientesExport implements FromCollection, WithTitle, ShouldAutoSize, WithMapping, WithHeadings, WithStyles, WithColumnFormatting, WithEvents, WithCustomStartCell
{
    use Exportable, RegistersEventListeners;

    protected static $cliente;

    public function collection()
    {
        self::$cliente = Cliente::find(2);

        $ventasIds = collect();
        self::$cliente->facturas->map(function ($venta) use ($ventasIds) {
            $ventasIds->push($venta->id);
        });
        return DB::table('articulo_venta')->whereIn('venta_id', $ventasIds)->get();
    }

    public function map($detalle): array
    {
        return [
            [
                $detalle->cantidad,
                $detalle->cantidadLitros
            ]
        ];
    }

    public function columnFormats(): array
    {
        return [
            // 'E' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
        ];
    }

    public function headings(): array
    {
        return [
            'Total Unidades',
            'Total Litros',
        ];
    }

    public function title(): string
    {
        return 'Litros por Clientes';
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

        $sheet->getStyle('A3:B3')->applyFromArray($auxStyles);
        $sheet->getStyle('A3:B99')->applyFromArray($styleArray);
    }

    public function startCell(): string
    {
        return 'A3';
    }

    public static function beforeSheet(BeforeSheet $event)
    {
        $event->sheet->setCellValue('A1', 'Desde:');
        $event->sheet->setCellValue('B1', now()->format('d-m-Y'));
        $event->sheet->setCellValue('C1', 'Hasta:');
        $event->sheet->setCellValue('D1', now()->addDay()->format('d-m-Y'));
        $event->sheet->setCellValue('A2', 'Cliente:');
        $event->sheet->setCellValue('B2', self::$cliente);
        $event->sheet->setCellValue('C2', 'CUIT:');
        $event->sheet->setCellValue('D2', self::$cliente);
    }
}
