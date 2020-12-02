<?php

namespace App\Exports;

use App\Venta;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;

class SheetVentasExport implements FromQuery, WithTitle, ShouldAutoSize, WithMapping, WithHeadings, WithStyles, WithColumnFormatting, WithEvents, WithCustomStartCell
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
        return Venta::query()->whereBetween('created_at', ['2020-10-31', '2020-12-30']);
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

        $sheet->getStyle('A2:K2')->applyFromArray($auxStyles);
        $sheet->getStyle('A2:K99')->applyFromArray($styleArray);
    }

    public function startCell(): string
    {
        return 'A2';
    }

    public static function beforeSheet(BeforeSheet $event)
    {
        $event->sheet->mergeCells('A1:K1');
        $event->sheet->setCellValue('A1', self::$esto->desde->format('d-m-Y') . ' | ' . self::$esto->hasta->format('d-m-Y'));
    }
}
