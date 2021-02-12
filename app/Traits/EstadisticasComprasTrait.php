<?php

namespace App\Traits;

use App\Compra;
use App\Supplier;
use Carbon\Carbon;

trait EstadisticasComprasTrait
{
    public static function compras($request)
    {
        $remitos = Compra::orderBy('fechaCompra', 'ASC')->get();

        if (count($remitos) > 0) {
            $inicio = $remitos->first();
            $ultima = $remitos->last();
            $from = $request->get('desde', $inicio->fechaCompra);
            $to = $request->get('hasta', $ultima->fechaCompra);
        } else {
            $from = $request->get('desde', now()->format('Y-m-d'));
            $to = $request->get('hasta', now()->format('Y-m-d'));
        }

        $desde = new Carbon($from);
        $hasta = new Carbon($to);
        $comprasFechas = [];
        $fecs = collect();
        $suppliers = collect();
        $proveedores = [];
        $comprasProveedores = [];

        $remitos = Compra::whereDate('fechaCompra', '>=', $desde->format('Y-m-d'))
            ->whereDate('fechaCompra', '<=', $hasta->format('Y-m-d'))
            ->orderBy('fechaCompra', 'ASC')
            ->take($request->get('limit', null))
            ->get();

        foreach ($remitos as $remito) {
            $supplier = Supplier::withTrashed()->find($remito->supplier_id);
            $suppliers->push($supplier);
        }

        $remitos2 = Compra::whereDate('fechaCompra', '>=', $desde->format('Y-m-d'))->whereDate('fechaCompra', '<=', $hasta->format('Y-m-d'))->orderBy('fechaCompra', 'ASC')->get();
        foreach ($remitos2 as $remito) {
            $fecs->push($remito->fechaCompra);
        }

        // Fechas
        $auxFechas = $fecs->unique();
        foreach ($auxFechas as $value) {
            $remis = Compra::whereDate('fechaCompra', $value)->get();
            array_push($comprasFechas, $remis);
        };
        $columnsFechas = ['fecha', 'total'];
        $rowsFechas = collect();
        $total = 0;
        for ($i = 0; $i < count($comprasFechas); $i++) {
            $otro = $comprasFechas[$i];
            foreach ($otro as $a) {
                $total += $a->total;
            }
            $fecha = $comprasFechas[$i][0]->fechaCompra;
            $fechaNew = new Carbon($fecha);
            $rowsFechas->push([
                'fecha' =>
                $fechaNew->format('d-m-Y'),
                'total' => $total
            ]);
            $total = 0;
        };
        $comprasFechasChart = collect();
        $comprasFechasChart->put('columns', $columnsFechas);
        $comprasFechasChart->put('rows', $rowsFechas);
        // Fin Fechas

        // Proveedores
        $auxProveedores = $suppliers->unique();
        foreach ($auxProveedores as $key) {
            $rems = Compra::where('user_id', $key->id)
                ->whereDate('fechaCompra', '>=', $desde->format('Y-m-d'))
                ->whereDate('fechaCompra', '<=', $hasta->format('Y-m-d'))
                ->orderBy('fechaCompra', 'ASC')
                ->get();
            array_push($proveedores, $key);
            array_push($comprasProveedores, $rems);
        }
        $columnsProveedores = ['vendedor', 'totalVendido'];
        $rowsProveedores = collect();
        $total = 0;
        for ($i = 0; $i < count($comprasProveedores); $i++) {
            $otro = $comprasProveedores[$i];
            foreach ($otro as $a) {
                $total += $a->total;
            }
            $rowsProveedores->push([
                'vendedor' => $proveedores[$i]->razonsocial,
                'totalVendido' =>  $total
            ]);
            $total = 0;
        };
        $comprasProveedores = collect();
        $comprasProveedores->put('columns', $columnsProveedores);
        $comprasProveedores->put('rows', $rowsProveedores);
        // Fin Proveedores

        $compras = [
            'fechas' => ['desde' => $desde, 'hasta' => $hasta],
            'comprasFecha' => $remitos,
            'comprasFechasChart' => $comprasFechasChart,
            'proveedores' => $proveedores,
            'comprasProveedores' => $comprasProveedores,
            'total' => count(Compra::all()),
        ];

        return ['compras' => $compras];
    }
}
