<?php

namespace App\Traits;

use App\Venta;
use App\Cliente;
use App\Articulo;
use Carbon\Carbon;
use App\Inventario;
use App\Movimiento;
use App\Cuentacorriente;
use App\Movimientocuenta;
use App\Traits\FormasDePagoTrait;
use App\Traits\ConfiguracionTrait;
use Illuminate\Support\Facades\DB;
use App\Traits\ArticulosNotificacionesTrait;

trait VentasTrait
{
    public static function index($request)
    {
        if (auth()->user()->role->role != 'vendedor') {
            if ($request->fec) {
                $fec = $request->fec;
                $facs = Venta::whereDate('created_at', $fec)
                    ->orderBy('id', 'DESC')
                    ->buscar($request)->get();
            } else {
                $facs = Venta::orderBy('id', 'DESC')
                    ->buscar($request)->get();
            }
        } else {
            if ($request->fec) {
                $fec = $request->fec;
                $facs = Venta::whereDate('created_at', $fec)
                    ->orderBy('id', 'DESC')
                    ->where('user_id', auth()->user()->id)
                    ->buscar($request)->get();
            } else {
                $facs = Venta::orderBy('id', 'DESC')
                    ->where('user_id', auth()->user()->id)
                    ->buscar($request)->get();
            }
        }

        $facturas = collect();

        foreach ($facs as $fac) {
            $fecha = new Carbon($fac->fecha);
            $fac->fecha = $fecha->format('d-m-Y');
            $fac->cliente = Cliente::withTrashed()->find($fac->cliente_id);
            $fac->forma;
            $pagos = FormasDePagoTrait::verPagos($fac);
            $fac = collect($fac);
            $fac->put('pagos', $pagos);
            $facturas->push($fac);
        }
        $eliminadas = Venta::onlyTrashed()->get();

        foreach ($eliminadas as $eliminada) {
            $dateFac = new Carbon($eliminada->fecha);
            $eliminada->fecha = $dateFac->format('d-m-Y');
            $eliminada->cliente;
        }

        if ($facturas->count() <= $request->get('limit')) {
            return [
                'ventas' => $facturas,
                'ultima' => $facturas->first(),
                'total' => $facturas->count(),
                'eliminadas' => $eliminadas
            ];
        } else {
            return [
                'ventas' => $facturas->take($request->get('limit', null)),
                'ultima' => $facturas->first(),
                'total' => $facturas->count(),
                'eliminadas' => $eliminadas
            ];
        }
    }

    public static function store($request)
    {
        // return $request;
        $atributos = $request;
        $cliente = Cliente::find($atributos['cliente_id']);
        $atributos['cuit'] = $cliente->documentounico;
        $atributos['condicionventa'] = $atributos['condicionventa'];
        if ($atributos['condicionventa'] == 'CONTADO') {
            $atributos['pagada'] = true;
            $atributos['subtotalPesos'] = ($atributos['subtotal'] * 1) * ($atributos['cotizacion'] * 1);
        } else {
            $atributos['pagada'] = false;
            $atributos['subtotalPesos'] = null;
            $atributos['totalPesos'] = null;
            $atributos['cotizacion'] = null;
            $atributos['fechaCotizacion'] = null;
        }

        // $config = ConfiguracionTrait::configuracion();

        if ($atributos['pagada']) {
            foreach ($atributos['pagos'] as $pay) {
                $referencia[] = FormasDePagoTrait::formaPago($pay, $cliente, $diferencia = null);
            }
        } else $referencia = null;

        // ALMACENAMIENTO DE FACTURA
        $factura = Venta::create([
            "cuit" => $atributos['cuit'], //cliente
            "tipocomprobante" => $atributos['tipoComprobante'],
            "numventa" => $atributos['numventa'],
            "comprobanteadherido" => $atributos['comprobanteadherido'],
            "fecha" => now()->format('Ymd'),
            'observaciones' => $atributos['observaciones'],
            "bonificacion" => $atributos['bonificacion'] * 1,
            "recargo" => $atributos['recargo'] * 1,
            "pagada" => $atributos['pagada'],
            "referencia" => $referencia,
            "condicionventa" => $atributos['condicionventa'],
            "subtotal" => $atributos['subtotal'],
            "total" => $atributos['total'],
            "subtotalPesos" => $atributos['subtotalPesos'],
            "totalPesos" => $atributos['totalPesos'],
            'cotizacion' => $atributos['cotizacion'],
            'fechaCotizacion' => $atributos['fechaCotizacion'],
            "cliente_id" => $atributos['cliente_id'],
            "user_id" => auth()->user()->id,
        ]);
        // ALMACENAMIENTO DE DETALLES
        foreach ($request->get('detalles') as $detail) {
            if ($atributos['condicionventa'] == 'CUENTA CORRIENTE') {
                $detail['subtotalPesos'] = null;
                $detail['cotizacion'] = null;
                $detail['fechaCotizacion'] = null;
            }
            $detalles = array(
                'codarticulo' => $detail['codarticulo'],
                'articulo' => $detail['articulo'],
                'cantidad' => $detail['cantidad'],
                'cantidadLitros' => $detail['cantidadLitros'],
                'medida' => $detail['medida'],
                // 'bonificacion' => 0,
                // 'alicuota' => 0,
                'preciounitario' => $detail['precio'],
                'subtotalPesos' => $detail['subtotalPesos'],
                'subtotal' => $detail['subtotalDolares'],
                'cotizacion' => $detail['cotizacion'],
                'fechaCotizacion' => $detail['fechaCotizacion'],
                'articulo_id' => $detail['id'],
                'venta_id' => $factura->id,
                'created_at' => now()->format('Ymd'),
            );
            $det[] = $detalles;
        }
        $factura->articulos()->attach($det);
        // CREACION DE CUENTA CORRIENTE
        if (($factura->pagada == false) && ($cliente->id != 1)) {
            $cuenta = Cuentacorriente::create([
                'venta_id' => $factura->id,
                'importe' => $factura->total,
                'saldo' => $factura->total,
                'alta' => now(),
                'estado' => 'ACTIVA'
            ]);
            Movimientocuenta::create([
                'ctacte_id' => $cuenta->id,
                'tipo' => 'ALTA',
                'fecha' => now(),
                'user_id' => auth()->user()->id,
                'importe' => $cuenta->importe
            ]);
        }
        $aux = collect($det);

        // DESCUENTA LOS INVENTARIOS
        for ($i = 0; $i < count($aux); $i++) {
            if (auth()->user()->role->role == 'superAdmin' || auth()->user()->role->role == 'administrador') {
                $article = Inventario::where('dependencia', null)
                    ->where('cantidad', '>', 0)
                    ->where('articulo_id', $aux[$i]['articulo_id'])->get();
            } else {
                $article = Inventario::where('dependencia', auth()->user()->id)
                    ->where('cantidad', '>', 0)
                    ->where('articulo_id', $aux[$i]['articulo_id'])->get();
            }

            if ($aux[$i]['cantidad'] <= $article[0]['cantidad']) {
                $art = Articulo::find($aux[$i]['articulo_id']);
                $article[0]['cantidad'] -= $aux[$i]['cantidad'];
                $article[0]['cantidadLitros'] = $article[0]['cantidad'] * $art->litros;
                $article[0]->save();
                Movimiento::create([
                    'inventario_id' => $article[0]->id,
                    'tipo' => 'VENTA',
                    'cantidadlitros' => ($aux[$i]['cantidad'] * $art->litros),
                    'cantidad' => $aux[$i]['cantidad'],
                    'fecha' => now(),
                    'numcomprobante' => $factura->id,
                    'user_id' => auth()->user()->id
                ]);
            } else {
                $factura->articulos()->detach();
                $factura->forceDelete();
            }

            if ($article[0]['cantidad'] == 0) {
                // Probando notificaciones
                $arti = Articulo::find($article[0]->articulo_id);
                ArticulosNotificacionesTrait::crearNotificacion($arti);
                // ------------
            }

            unset($article);
        }
        return $factura->id;
    }

    public static function show($id)
    {
        return static::verVenta($id);
    }

    public static function verVenta($id)
    {
        $configuracion = ConfiguracionTrait::configuracion();
        $factura = Venta::find($id);
        $fecha = new Carbon($factura->fecha);
        $factura->fecha = $fecha->format('d-m-Y');
        $cliente = Cliente::withTrashed()->find($factura->cliente_id);
        $detalles = DB::table('articulo_venta')->where('venta_id', $factura->id)->get();
        return [
            'configuracion' => $configuracion,
            'factura' => $factura,
            'detalles' => $detalles,
            'cliente' => $cliente
        ];
    }

    public static function facturar($request)
    {
        $factura = collect();
        // if (array_key_exists('cliente', $request->id)) {
        //     $cliente = Cliente::findOrFail($request->id['cliente']);
        //     $factura->put('cliente', $cliente);
        // }
        $ids = $request->id['seleccionadas'];

        $details = collect();
        $arregloIds = array();
        $numeros = "";
        $sub = 0;
        $tot = 0;
        for ($i = 0; $i < count($ids); $i++) {
            $aux = Venta::find($ids[$i]);
            array_push($arregloIds, $ids[$i]);
            foreach ($aux->articulos as $det) {
                $details->push($det);
            }
            $sub += $aux->subtotal;
            $tot += $aux->total;
            if ($i == 0) {
                $numeros = $numeros  . $aux->id;
            } elseif ($i < count($ids)) {
                $numeros = $numeros . ', ' . $aux->id;
            }
        }
        $factura->put('detalles', $details);
        $factura->put('subtotal', $sub);
        $factura->put('total', $tot);
        $factura->put('ids', $numeros);
        $factura->put('ventas', $arregloIds);

        return $factura;
    }

    public static function anular($id)
    {
        $factura = Venta::findOrFail($id);
        $detalles = collect($factura->articulos);
        $pivot = collect();
        $inventarios = collect();
        $factura->delete();
        foreach ($detalles as $art) {
            $pivot = $pivot->push($art->pivot);
        }
        foreach ($pivot as $piv) {
            $art = Articulo::findOrFail($piv->articulo_id);
            $aux = collect($art->inventarios);
            foreach ($aux as $a) {
                $inventarios = $inventarios->push($a);
            }
        }
        // SE REESTABLECE LA CANTIDAD EN LOS INVENTARIOS
        unset($aux);
        foreach ($inventarios as $inv) {
            $aux = collect($inv->movimientos);
            $aux = $aux->where('numcomprobante', $factura->id);
            foreach ($aux as $a) {
                $inventario = $a->inventario;
                $inventario->cantidad = $inventario->cantidad + $a->cantidad;
                $inventario->save();
                Movimiento::create([
                    'inventario_id' => $inventario->id,
                    'tipo' => 'ANULACION',
                    'cantidad' => $a->cantidad,
                    'cantidadlitros' => $a->cantidadlitros,
                    'fecha' => now(),
                    'numcomprobante' => $factura->id,
                    'user_id' => auth()->user()->id
                ]);
            }
        }
        return ['msg' => 'Factura Anulada'];
    }
}
