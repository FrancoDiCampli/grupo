<?php

namespace App\Exports;

use App\Cliente;
use App\Articulo;
use Carbon\Carbon;
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

    public $cliente;
    public $desde;
    public $hasta;
    public static $esto;

    public function __construct($desde, $hasta, $id)
    {
        $this->desde = new Carbon($desde);
        $this->hasta = new Carbon($hasta);
        $this->cliente = Cliente::find($id);
        self::$esto = $this;
    }

    public function collection()
    {
        $ventasIds = collect();
        $this->cliente->facturas->map(function ($venta) use ($ventasIds) {
            $ventasIds->push($venta->id);
        });
        $detalles = DB::table('articulo_venta')->whereIn('venta_id', $ventasIds)->whereDate('created_at', '>=', $this->desde->format('Y-m-d'))->whereDate('created_at', '<=', $this->hasta->format('Y-m-d'))->get();

        $articulos = collect();

        $detalles->groupBy('articulo_id')->map(function ($det) use ($articulos) {
            $aux = collect();
            $articulo = Articulo::withTrashed()->find($det->first()->articulo_id);
            $aux->put('articulo', $articulo->articulo);
            $aux->put('unidades', $det->sum('cantidad'));
            $aux->put('litros', $det->sum('cantidadLitros'));
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
            'Unidades',
            'Total Litros',
        ];
    }

    public function title(): string
    {
        return 'Litros por Cliente';
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

        $sheet->getStyle('A3:C3')->applyFromArray($auxStyles);
        $sheet->getStyle('A3:C99')->applyFromArray($styleArray);
    }

    public function startCell(): string
    {
        return 'A3';
    }

    public static function beforeSheet(BeforeSheet $event)
    {
        $event->sheet->mergeCells('A1:C1');
        $event->sheet->setCellValue('A1', self::$esto->desde->format('d-m-Y') . ' | ' . self::$esto->hasta->format('d-m-Y'));
        $event->sheet->setCellValue('A2', 'Cliente:');
        $event->sheet->setCellValue('B2', self::$esto->cliente->razonsocial);
        $event->sheet->setCellValue('C2', 'CUIT:');
        $event->sheet->setCellValue('D2', self::$esto->cliente->documentounico);
    }
}
