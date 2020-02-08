<?php

namespace App\Http\Controllers\API;

use App\Negocio;
use Carbon\Carbon;
use App\Movimiento;
use App\Movimientocuenta;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MovimientosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:airlock');

        $this->middleware('scope:movimientos-index')->only('index');
    }

    public function index(Request $request)
    {
        $movimientos = Movimiento::orderBy('id', 'ASC')->get();
        $movimientosCuentas = Movimientocuenta::orderBy('id', 'ASC')->get();

        if (count($movimientos) > 0) {
            $inicio = $movimientos->first();
            $ultima = $movimientos->last();
            $from = $request->get('desde', $inicio->fecha);
            $to = $request->get('hasta', $ultima->fecha);
        } else {
            $from = $request->get('desde', now());
            $to = $request->get('hasta', now());
        }

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

        if ($request->negocio_id <> 0) {
            $negocio = Negocio::find($request->negocio_id);

            $auxMovimientos = $negocio->movimientos->where('fecha', '>=', $desde->format('Y-m-d H:i'))->where('fecha', '<=', $hasta->format('Y-m-d H:i'));
            $auxMovimientos1 = $auxMovimientos->sortByDesc('fecha');
            $movimientos = $auxMovimientos1->values()->all();

            $movCuentas = collect();
            foreach ($negocio->clientes as $cliente) {
                foreach ($cliente->ctacte as $cuenta) {
                    $movCuentas->push($cuenta->movimientos);
                }
            }
            $auxCuentas = $movCuentas->flatten();
            $auxCuentas2 = $auxCuentas->where('fecha', '>=', $desde->format('Y-m-d H:i'))->where('fecha', '<=', $hasta->format('Y-m-d H:i'));
            $auxCuentas3 = $auxCuentas2->sortByDesc('fecha');
            $movimientosCuentas = $auxCuentas3->values()->all();
        } else {
            $movimientos = Movimiento::whereDate('fecha', '>=', $desde->format('Y-m-d H:i'))->whereDate('fecha', '<=', $hasta->format('Y-m-d H:i'))->orderBy('fecha', 'DESC')->get();

            $movimientosCuentas = Movimientocuenta::whereDate('fecha', '>=', $desde->format('Y-m-d H:i'))->whereDate('fecha', '<=', $hasta->format('Y-m-d H:i'))->orderBy('fecha', 'DESC')->get();
        }

        $arreglo = collect();
        foreach ($movimientos as $move) {
            $fecha = new Carbon($move->fecha);
            $coleccion = collect();
            $coleccion->put('fecha', $fecha->format('d-m-Y H:i'));
            $coleccion->put('tipo', $move->tipo);
            $coleccion->put('cantidad', $move->cantidad);
            $coleccion->put('user', $move->user);
            $coleccion->put('negocio', $move->user->negocio);
            $coleccion->put('inventario', $move->load('inventario.articulo'));

            $arreglo->push($coleccion);
        }

        $arregloCuentas = collect();
        foreach ($movimientosCuentas as $move) {
            $fecha = new Carbon($move->fecha);
            $coleccion = collect();
            $coleccion->put('fecha', $fecha->format('d-m-Y H:i'));
            $coleccion->put('tipo', $move->tipo);
            $coleccion->put('importe', $move->importe);
            $coleccion->put('user', $move->user);
            $coleccion->put('negocio', $move->user->negocio);
            $coleccion->put('cuenta', $move->load('ctacte.factura.cliente'));

            $arregloCuentas->push($coleccion);
        }

        return ['articulos' => $arreglo->take($request->get('limit', null)), 'cuentas' => $arregloCuentas->take($request->get('limit', null))];
    }
}
