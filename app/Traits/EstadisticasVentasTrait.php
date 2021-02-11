<?php

namespace App\Traits;

use App\User;
use App\Venta;
use App\Cliente;
use Carbon\Carbon;

trait EstadisticasVentasTrait
{
    public static function ventas($request)
    {
        // Traer todas la facturas y los detalles
        $facturas = Venta::orderBy('fecha', 'ASC')->get();
        // Establecer la fecha desde y hasta
        if (count($facturas) > 0) {
            $inicio = $facturas->first();
            $ultima = $facturas->last();
            $from = $request->get('desde', $inicio->fecha);
            $to = $request->get('hasta', $ultima->fecha);
        } else {
            $from = $request->get('desde', now());
            $to = $request->get('hasta', now());
        }

        $desde = new Carbon($from);
        $hasta = new Carbon($to);
        $ventasFecha = [];
        $fecs = collect();
        $vendedores = [];
        $ventasVendedores = [];
        $sellers = collect();
        $ventasClientes = [];
        $clientes = [];
        $clients = collect();

        // Buscar las facturas entre las fechas
        $facturas = Venta::whereDate('fecha', '>=', $desde->format('Ymd'))->whereDate('fecha', '<=', $hasta->format('Ymd'))->orderBy('fecha', 'ASC')->take($request->get('limit', null))->get();

        foreach ($facturas as $factura) {
            $seller = User::find($factura->user_id);
            $sellers->push($seller);
            $client = Cliente::withTrashed()->find($factura->cliente_id);
            $clients->push($client);
        }

        $facturas2 = Venta::whereDate('fecha', '>=', $desde->format('Ymd'))->whereDate('fecha', '<=', $hasta->format('Ymd'))->orderBy('fecha', 'ASC')->get();
        foreach ($facturas2 as $factura) {
            $fecs->push($factura->fecha);
        }

        // Fechas
        $auxFechas = $fecs->unique();
        foreach ($auxFechas as $value) {
            $factus = Venta::whereDate('fecha', $value)->get();
            array_push($ventasFecha, $factus);
        };
        $columnsFechas = ['fecha', 'total'];
        $rowsFechas = collect();
        $total = 0;
        for ($i = 0; $i < count($ventasFecha); $i++) {
            $otro = $ventasFecha[$i];
            foreach ($otro as $a) {
                $total += $a->total;
            }
            $fecha = $ventasFecha[$i][0]->fecha;
            $fechaNew = new Carbon($fecha);
            $rowsFechas->push([
                'fecha' =>
                $fechaNew->format('d-m-Y'),
                'total' => $total
            ]);
            $total = 0;
        };
        $ventasFechasChart = collect();
        $ventasFechasChart->put('columns', $columnsFechas);
        $ventasFechasChart->put('rows', $rowsFechas);
        // Fin Fechas

        // Vendedores
        $auxVendedores = $sellers->unique();
        foreach ($auxVendedores as $key) {
            $facs = Venta::where('user_id', $key->id)
                ->whereDate('fecha', '>=', $desde->format('Ymd'))
                ->whereDate('fecha', '<=', $hasta->format('Ymd'))
                ->orderBy('fecha', 'ASC')
                ->get();
            array_push($vendedores, $key);
            array_push($ventasVendedores, $facs);
        }
        $columnsVendedores = ['vendedor', 'totalVendido'];
        $rowsVendedores = collect();
        $total = 0;
        for ($i = 0; $i < count($ventasVendedores); $i++) {
            $otro = $ventasVendedores[$i];
            foreach ($otro as $a) {
                $total += $a->total;
            }
            $rowsVendedores->push([
                'vendedor' => $vendedores[$i]->name,
                'totalVendido' =>  $total
            ]);
            $total = 0;
        };
        $ventasVendedores = collect();
        $ventasVendedores->put('columns', $columnsVendedores);
        $ventasVendedores->put('rows', $rowsVendedores);
        // Fin Vendedores

        // Clientes
        $auxClientes = $clients->unique();
        foreach ($auxClientes as $aux) {
            $facturs = Venta::where('cliente_id', $aux->id)
                ->whereDate('fecha', '>=', $desde->format('Ymd'))
                ->whereDate('fecha', '<=', $hasta->format('Ymd'))
                ->orderBy('fecha', 'ASC')->get();
            array_push($clientes, $aux);
            array_push($ventasClientes, $facturs);
        }
        $columnsClientes = ['cliente', 'totalComprado'];
        $rowsClientes = collect();
        $total = 0;
        for ($i = 0; $i < count($ventasClientes); $i++) {
            $otro = $ventasClientes[$i];
            foreach ($otro as $a) {
                $total += $a->total;
            }
            $rowsClientes->push([
                'cliente' => $clientes[$i]->razonsocial,
                'totalComprado' =>  $total
            ]);
            $total = 0;
        };
        $ventasClientes = collect();
        $ventasClientes->put('columns', $columnsClientes);
        $ventasClientes->put('rows', $rowsClientes);
        // Fin Clientes

        $ventas = [
            'fechas' => ['desde' => $desde, 'hasta' => $hasta],
            'ventasFecha' => $facturas,
            'ventasFechaChart' => $ventasFechasChart,
            'total' => count(Venta::all()),
            'vendedores' => $vendedores,
            'ventasVendedores' => $ventasVendedores,
            'clientes' => $clientes,
            'ventasClientes' => $ventasClientes
        ];

        return ['ventas' => $ventas];
    }
}
