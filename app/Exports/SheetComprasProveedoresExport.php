<?php

namespace App\Exports;

use App\Supplier;
use App\User;
use Carbon\Carbon;
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
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class SheetComprasProveedoresExport implements FromQuery, WithTitle, ShouldAutoSize, WithMapping, WithHeadings, WithStyles, WithColumnFormatting, WithEvents, WithCustomStartCell
{
    use Exportable, RegistersEventListeners;

    public $desde;
    public $hasta;
    public static $esto;

    public function __construct($desde, $hasta)
    {
        $this->desde = new Carbon($desde);
        $this->hasta = new Carbon($hasta);
        self::$esto = $this;
    }

    public function query()
    {
        return Supplier::query();
    }

    public function map($proveedor): array
    {
        return [
            $proveedor->id,
            $proveedor->razonsocial,
            $proveedor->remitos->count(),
            $proveedor->remitos->sum('total')
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
            'Proeedor',
            'Cant. Compras',
            'Total',
        ];
    }

    public function title(): string
    {
        return 'Proveedores';
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

        $sheet->getStyle('A2:D2')->applyFromArray($auxStyles);
        $sheet->getStyle('A2:D99')->applyFromArray($styleArray);
    }

    public function startCell(): string
    {
        return 'A2';
    }

    public static function beforeSheet(BeforeSheet $event)
    {
        $event->sheet->mergeCells('A1:D1');
        $event->sheet->setCellValue('A1', self::$esto->desde->format('d-m-Y') . ' | ' . self::$esto->hasta->format('d-m-Y'));
    }
}
