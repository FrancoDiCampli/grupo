<?php

namespace App\Exports;

use App\Cliente;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;

class SheetLitrosClientesExport implements FromCollection, WithTitle, ShouldAutoSize, WithMapping, WithHeadings, WithStyles, WithColumnFormatting, WithEvents
{
    use Exportable, RegistersEventListeners;

    public function collection()
    {
        $clientes = Cliente::all();

        foreach ($clientes as $cliente) {
            $ventasIds = collect();
            $cliente->facturas->map(function ($venta) use ($ventasIds) {
                $ventasIds->push($venta->id);
            });
            $cliente['detalles'] = DB::table('articulo_venta')->whereIn('venta_id', $ventasIds->all())->get();
        }

        return $clientes;
    }

    public function map($cliente): array
    {
        return [
            [
                $cliente->id,
                $cliente->documentounico,
                $cliente->razonsocial,
                $cliente->facturas->count(),
                $cliente['detalles']->sum('cantidadLitros')
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
            '#',
            'CUIT',
            'Razon Social',
            'Cant. Ventas',
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

        $sheet->getStyle('A1:E1')->applyFromArray($auxStyles);
        $sheet->getStyle('A1:E99')->applyFromArray($styleArray);
    }
}
