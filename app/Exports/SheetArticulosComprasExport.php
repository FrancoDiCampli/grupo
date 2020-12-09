<?php

namespace App\Exports;

use App\Articulo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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

class SheetArticulosComprasExport implements FromCollection, WithTitle, ShouldAutoSize, WithMapping, WithHeadings, WithStyles, WithColumnFormatting, WithEvents, WithCustomStartCell
{
    use Exportable, RegistersEventListeners;

    public $cliente;
    public $desde;
    public $hasta;
    public static $esto;

    public function __construct($desde, $hasta)
    {
        $this->desde = new Carbon($desde);
        $this->hasta = new Carbon($hasta);
        self::$esto = $this;
    }

    public function collection()
    {
        $detalles = DB::table('articulo_compra')->whereDate('created_at', '>=', $this->desde->format('Y-m-d'))->whereDate('created_at', '<=', $this->hasta->format('Y-m-d'))->get();

        $articulos = collect();

        $detalles->groupBy('articulo_id')->map(function ($det) use ($articulos) {
            $aux = collect();
            $articulo = Articulo::withTrashed()->find($det->first()->articulo_id);
            $aux->put('articulo', $articulo->articulo);
            $aux->put('unidades', $det->sum('cantidad'));
            $aux->put('litros', $det->sum('cantidadlitros'));
            $articulos->push($aux);
        });

        return $articulos;
    }

    public function map($article): array
    {
        return [
            [
                $article['articulo'],
                $article['unidades'],
                $article['litros'],
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
            'Articulo',
            'Total Unidades',
            'Total Litros',
        ];
    }

    public function title(): string
    {
        return 'Compras por Articulo';
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

        $sheet->getStyle('A2:C2')->applyFromArray($auxStyles);
        $sheet->getStyle('A2:C99')->applyFromArray($styleArray);
    }

    public function startCell(): string
    {
        return 'A2';
    }

    public static function beforeSheet(BeforeSheet $event)
    {
        $event->sheet->mergeCells('A1:C1');
        $event->sheet->setCellValue('A1', self::$esto->desde->format('d-m-Y') . ' | ' . self::$esto->hasta->format('d-m-Y'));
    }
}
