<?php

namespace App\Traits;

use Carbon\Carbon;
use App\Movimiento;
use App\Movimientocuenta;

trait MovimientosTrait
{
    public static function movimientos($request)
    {
        $movimientos = Movimiento::orderBy('id', 'ASC')->get();

        if (count($movimientos) > 0) {
            $inicio = $movimientos->first();
            $ultima = $movimientos->last();
            $from = $request->get('desde', $inicio->fecha);
            $to = $request->get('hasta', $ultima->fecha);
        } else {
            $from = $request->get('desde', now());
            $to = $request->get('hasta', now());
        }

        $desde = new Carbon($from);
        $hasta = new Carbon($to);

        $movimientos = Movimiento::whereDate('fecha', '>=', $desde->format('Y-m-d H:i'))->whereDate('fecha', '<=', $hasta->format('Y-m-d H:i'))->orderBy('fecha', 'DESC')->get();

        $arreglo = collect();
        foreach ($movimientos as $move) {
            $fecha = new Carbon($move->fecha);
            $coleccion = collect();
            $coleccion->put('fecha', $fecha);
            $coleccion->put('tipo', $move->tipo);
            $coleccion->put('cantidad', $move->cantidad);
            $coleccion->put('user', $move->user);
            $coleccion->put('inventario', $move->load('inventario.articulo'));

            $arreglo->push($coleccion);
        }

        return $arreglo->take($request->get('limit', null));
    }

    public static function crearMovimiento(
        $tipo,
        $cantidad,
        $cantLitros,
        $inventario,
        $factura
    ) {
        Movimiento::create([
            'tipo' => $tipo,
            'cantidadlitros' => $cantLitros,
            'cantidad' => $cantidad,
            'fecha' => now(),
            'inventario_id' => $inventario->id,
            'numcomprobante' => $factura->id,
            'user_id' => auth()->user()->id
        ]);
    }

    public static function movimientosCuenta($request)
    {
        $movimientosCuentas = Movimientocuenta::orderBy('id', 'ASC')->get();

        if (count($movimientosCuentas) > 0) {
            $inicio = $movimientosCuentas->first();
            $ultima = $movimientosCuentas->last();
            $from = $request->get('desde', $inicio->fecha);
            $to = $request->get('hasta', $ultima->fecha);
        } else {
            $from = $request->get('desde', now());
            $to = $request->get('hasta', now());
        }

        $desde = new Carbon($from);
        $hasta = new Carbon($to);

        $movimientosCuentas = Movimientocuenta::whereDate('fecha', '>=', $desde->format('Y-m-d H:i'))->whereDate('fecha', '<=', $hasta->format('Y-m-d H:i'))->orderBy('fecha', 'DESC')->get();

        $arregloCuentas = collect();
        foreach ($movimientosCuentas as $move) {
            $fecha = new Carbon($move->fecha);
            $coleccion = collect();
            $coleccion->put('fecha', $fecha);
            $coleccion->put('tipo', $move->tipo);
            $coleccion->put('importe', $move->importe);
            $coleccion->put('user', $move->user);
            $coleccion->put('cuenta', $move->load('ctacte.factura.cliente'));

            $arregloCuentas->push($coleccion);
        }

        $arregloCuentas->take($request->get('limit', null));

        return $arregloCuentas->toArray();
    }
}
