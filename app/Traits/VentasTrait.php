<?php

namespace App\Traits;

use App\Venta;
use App\Cliente;
use App\Articulo;
use App\Formapago;
use Carbon\Carbon;
use App\Inventario;
use App\Traits\ArticulosTrait;
use App\Traits\FormasDePagoTrait;
use App\Traits\ConfiguracionTrait;
use Illuminate\Support\Facades\DB;
use App\Traits\CuentasCorrientesTrait;

trait VentasTrait
{
    public static function index($request)
    {
        if (auth()->user()->role->role != 'vendedor') {
            if ($request->fec) {
                $fec = $request->fec;
                $facs = Venta::whereDate('created_at', $fec)
                    ->orderBy('id', 'DESC')
                    ->get();
            } else {
                $facs = Venta::orderBy('id', 'DESC')
                    ->get();
            }
        } else {
            if ($request->fec) {
                $fec = $request->fec;
                $facs = Venta::whereDate('created_at', $fec)
                    ->orderBy('id', 'DESC')
                    ->where('user_id', auth()->user()->id)
                    ->get();
            } else {
                $facs = Venta::orderBy('id', 'DESC')
                    ->where('user_id', auth()->user()->id)
                    ->get();
            }
        }

        $facturas = collect();

        foreach ($facs as $fac) {
            $fecha = new Carbon($fac->fecha);
            $fac->fecha = $fecha->format('d-m-Y');
            $fac->cliente = Cliente::withTrashed()->find($fac->cliente_id);
            // $fac->forma;
            // $pagos = FormasDePagoTrait::verPagos($fac);
            // $fac->cuenta ? $fac->cuenta->pagos : null;
            // $fac = collect($fac);
            // $fac->put('pagos', $pagos);

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
        $atributos = $request;
        $cliente = Cliente::find($atributos['cliente_id']);
        $atributos['cuit'] = $cliente->documentounico;
        $atributos['condicionventa'] = $atributos['condicionventa'];
        // if ($atributos['condicionventa'] == 'CONTADO') {
            $atributos['pagada'] = false;
            $atributos['subtotalPesos'] = ($atributos['subtotal'] * 1) * ($atributos['cotizacion'] * 1);
            $atributos['totalPesos'] = ($atributos['total'] * 1) * ($atributos['cotizacion'] * 1);
        // } else {
        //     $atributos['pagada'] = false;
        //     $atributos['subtotalPesos'] = null;
        //     $atributos['totalPesos'] = null;
        //     $atributos['cotizacion'] = null;
        //     $atributos['fechaCotizacion'] = null;
        // }

        // ALMACENAMIENTO DE FACTURA
        $factura = static::crearVenta($atributos);

        // Metodos de pago
        // if ($atributos['pagada'] && $atributos['pagos']) {
        //     foreach ($atributos['pagos'] as $pay) {
        //         $ref = FormasDePagoTrait::formaPago($pay, $cliente, $diferencia = null);
        //         $forma = Formapago::create([
        //             'referencia' => $ref
        //         ]);
        //         $auxiliar[] = $forma->id;
        //     }
        //     $factura->formasPago()->attach($auxiliar);
        // }

        // ALMACENAMIENTO DE DETALLES
        $det = static::detallesVentas($request->get('detalles'), $atributos, $factura);

        $factura->articulos()->attach($det);
        // CREACION DE CUENTA CORRIENTE
        if (($factura->pagada == false) && ($cliente->id != 1)) {
            CuentasCorrientesTrait::crearCuenta($factura);
        }
        $aux = collect($det);

        // DESCUENTA LOS INVENTARIOS
        // static::actualizarInventarios($aux, $factura);

        return $factura->id;
    }

    public static function show($id)
    {
        return static::verVenta($id);
    }

    public static function actualizarInventarios($aux, $factura)
    {
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

                MovimientosTrait::crearMovimiento('VENTA', $aux[$i]['cantidad'], ($aux[$i]['cantidad'] * $art->litros), $article[0], $factura);
            } else {
                $factura->articulos()->detach();
                $factura->forceDelete();
            }

            if ($article[0]['cantidad'] == 0) {
                $arti = Articulo::find($article[0]->articulo_id);
                ArticulosTrait::crearNotificacion($arti);
            }

            unset($article);
        }
    }

    public static function detallesVentas($details, $atributos, $factura)
    {
        foreach ($details as $detail) {
            // if ($atributos['condicionventa'] == 'CUENTA CORRIENTE') {
            //     $detail['subtotalPesos'] = null;
            //     $detail['cotizacion'] = null;
            //     $detail['fechaCotizacion'] = null;
            // }
            $detalles = array(
                'codarticulo' => $detail['codarticulo'],
                'articulo' => $detail['articulo'],
                'cantidad' => $detail['cantidad'],
                'cantidadLitros' => $detail['cantidadLitros'],
                'medida' => $detail['medida'],
                'preciounitario' => $detail['precio'],
                'subtotalPesos' => $detail['subtotalDolares'] * $detail['cotizacion'],
                'subtotal' => $detail['subtotalDolares'],
                'cotizacion' => $detail['cotizacion'],
                'fechaCotizacion' => $detail['fechaCotizacion'],
                'articulo_id' => $detail['id'],
                'venta_id' => $factura->id,
                'created_at' => now()->format('Ymd'),
            );
            $det[] = $detalles;
        }

        return $det;
    }

    public static function crearVenta($atributos)
    {
        return Venta::create([
            "cuit" => $atributos['cuit'], //cliente
            "tipocomprobante" => $atributos['tipoComprobante'],
            "numventa" => $atributos['numventa'],
            "comprobanteadherido" => $atributos['remitoadherido'],
            // "fecha" => now()->format('Ymd'),
            'fecha' => $atributos['fecha'],
            'observaciones' => $atributos['observaciones'],
            "bonificacion" => $atributos['bonificacion'] * 1,
            "recargo" => $atributos['recargo'] * 1,
            "pagada" => $atributos['pagada'],
            // "referencia" => $referencia,
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
    }

    public static function verVenta($id)
    {
        $configuracion = ConfiguracionTrait::configuracion();
        $factura = Venta::find($id);
        // $formas = $factura->formasPago;
        // $aux = FormasDePagoTrait::verPagosVenta($formas);
        $fecha = new Carbon($factura->fecha);
        $fechaCot = new Carbon($factura->fechaCotizacion);
        $factura->fecha = $fecha->format('d-m-Y');
        $factura->fechaCotizacion = $fechaCot->format('d-m-Y');
        $cliente = Cliente::withTrashed()->find($factura->cliente_id);
        $detalles = DB::table('articulo_venta')->where('venta_id', $factura->id)->get();
        return [
            'configuracion' => $configuracion,
            'factura' => $factura,
            'detalles' => $detalles,
            'cliente' => $cliente,
            // 'forma' => $aux
        ];
    }

    public static function facturar($request)
    {
        $factura = collect();
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

                $detsFact = DB::table('articulo_factura')->where('articulo_venta_id', $det->pivot->id)->get();

                $cant = 0;
                $cantLitros = 0;

                if ($detsFact) {
                    $cant = $detsFact->each(function ($item) {
                        $item->cantidad++;
                    });
                    $cantLitros = $detsFact->each(function ($item) {
                        $item->cantidadLitros++;
                    });
                }

                $det->pivot['cantFac'] = $cant;
                $det->pivot['cantLitrosFac'] = $cantLitros;
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
        $venta = Venta::findOrFail($id);
        $sePuede = true;

        if (!$venta->numfactura) {
            if ($venta->cuenta) {
                if (count($venta->cuenta->pagos) == 0) {
                    $sePuede = true;
                } else {
                    $sePuede = false;
                }
            } else {
                $sePuede = true;
            }
        } else {
            $sePuede = false;
        }

        if ($sePuede) {
            if ($venta->cuenta) {
                $venta->cuenta->movimientos->each->delete();
                $venta->cuenta->delete();
            }

            $detalles = collect($venta->articulos);
            $pivot = collect();
            $inventarios = collect();
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
                $aux = $aux->where('numcomprobante', $venta->id);
                foreach ($aux as $a) {
                    $inventario = $a->inventario;
                    $inventario->cantidad = $inventario->cantidad + $a->cantidad;
                    $inventario->cantidadlitros = $inventario->cantidadlitros + $a->cantidadlitros;
                    $inventario->save();

                    MovimientosTrait::crearMovimiento('ANULACION', $a->cantidad, $a->cantidadlitros, $inventario, $venta);
                }
            }
            $venta->articulos()->detach();
            $venta->forceDelete();
            return ['msg' => 'Venta Anulada'];
        } else {
            return ['msg' => 'No es posible anular la venta'];
        }
    }
}
