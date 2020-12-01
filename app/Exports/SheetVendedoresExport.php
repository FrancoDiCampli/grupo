<?php

namespace App\Exports;

use App\User;
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

class SheetVendedoresExport implements FromQuery, WithTitle, ShouldAutoSize, WithMapping, WithHeadings, WithStyles, WithColumnFormatting, WithEvents
{
    use Exportable, RegistersEventListeners;

    public function query()
    {
        return User::query()->whereIn('role_id', [1, 2, 3]);
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->ventas->count(),
            $user->ventas->sum('total')
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            'Vendedor',
            'Cant. Ventas',
            'Total',
        ];
    }

    public function title(): string
    {
        return 'Vendedores';
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

        $sheet->getStyle('A1:D1')->applyFromArray($auxStyles);
        $sheet->getStyle('A1:D99')->applyFromArray($styleArray);
    }
}
