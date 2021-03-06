<?php

namespace App\Traits;

use App\Venta;
use App\Cliente;
use App\Entrega;
use App\Articulo;
use Carbon\Carbon;
use App\Inventario;
use App\Traits\ConfiguracionTrait;
use Illuminate\Support\Facades\DB;
use App\Traits\CuentasCorrientesTrait;

trait VentasTrait
{
    public static function index($request)
    {
        if (auth()->user()->role->role != 'vendedor') {
            $facs = Venta::take($request->get('limit', null))
                ->orderBy('comprobanteadherido', 'DESC')
                ->get();
        } else {
            $facs = Venta::take($request->get('limit', null))
                ->orderBy('comprobanteadherido', 'DESC')
                ->where('user_id', auth()->user()->id)
                ->get();
        }

        $facturas = collect();

        foreach ($facs as $fac) {
            // $fecha = new Carbon($fac->fecha);
            // $fac->fecha = $fecha->format('d-m-Y');
            $fac->cliente = Cliente::withTrashed()->find($fac->cliente_id);
            // $fac->forma;
            // $pagos = FormasDePagoTrait::verPagos($fac);
            // $fac->cuenta ? $fac->cuenta->pagos : null;
            // $fac = collect($fac);
            // $fac->put('pagos', $pagos);

            foreach ($fac->articulos as $det) {
                if (auth()->user()->role->role == 'superAdmin' || auth()->user()->role->role == 'administrador') {
                    $article = Inventario::where('dependencia', null)
                        ->where('cantidad', '>', 0)
                        ->where('articulo_id', $det['id'])
                        ->first();
                } else {
                    $article = Inventario::where('dependencia', auth()->user()->id)
                        ->where('cantidad', '>', 0)
                        ->where('articulo_id', $det['id'])
                        ->first();
                }

                $article ? $det['pivot']['disponible'] = $article->cantidad : $det['pivot']['disponible'] = 0;

                $detsFact = DB::table('articulo_factura')->where('articulo_venta_id', $det['pivot']->id)->get();
                $detsEntr = DB::table('articulo_entrega')->where('articulo_venta_id', $det['pivot']->id)->get();

                $detsFact->sum('cantidad') < $det['pivot']->cantidad ? $fac['todofacturado'] = false : $fac['todofacturado'] = true;
                $det['pivot']['cantidadfacturado'] = $detsFact->sum('cantidad');

                $detsEntr->sum('cantidad') < $det['pivot']->cantidad ? $fac['todoentregado'] = false : $fac['todoentregado'] = true;
                $det['pivot']['cantidadentregado'] = $detsEntr->sum('cantidad');

                $det['pivot']['litros'] = $det->litros;

                if ($fac->cliente_id <> 1) {
                    count($fac->cuenta->pagos) > 0 ? $fac['hasPagos'] = true : $fac['hasPagos'] = false;
                    count($fac->entregas) > 0 ? $fac['hasEntregas'] = true : $fac['hasEntregas'] = false;
                    count($fac->facturas) > 0 ? $fac['hasFacturas'] = true : $fac['hasFacturas'] = false;
                }
            }

            $facturas->push($fac);
        }
        $eliminadas = Venta::onlyTrashed()->get();

        foreach ($eliminadas as $eliminada) {
            // $dateFac = new Carbon($eliminada->fecha);
            // $eliminada->fecha = $dateFac->format('d-m-Y');
            $eliminada->cliente;
        }

        return [
            'ventas' => $facturas,
            'ultima' => Venta::all(['id', 'numventa'])->last(),
            'total' => Venta::count(),
            'eliminadas' => $eliminadas
        ];
    }

    public static function store($request)
    {
        $request->validate(
            [
                'remitoadherido' => 'required|unique:ventas,comprobanteadherido,NULL,id,deleted_at,NULL'
            ],
            [
                'remitoadherido.unique' => 'El valor del campo Remito adherido N?? ya est?? en uso.',
            ]
        );

        try {
            return DB::transaction(function () use ($request) {
                $atributos = $request;
                $cliente = Cliente::find($atributos['cliente_id']);
                $atributos['cuit'] = $cliente->documentounico;
                $atributos['condicionventa'] = $atributos['condicionventa'];
                $atributos['subtotalPesos'] = ($atributos['subtotal'] * 1) * ($atributos['cotizacion'] * 1);
                $atributos['totalPesos'] = ($atributos['total'] * 1) * ($atributos['cotizacion'] * 1);

                // ALMACENAMIENTO DE FACTURA
                $factura = static::crearVenta($atributos);

                // Metodos de pago
                // if ($atributos['pagos']) {
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
                $det = static::detallesVentas($request->get('detalles'), $factura);

                $factura->articulos()->attach($det);
                // CREACION DE CUENTA CORRIENTE
                if ($cliente->documentounico != 0) {
                    CuentasCorrientesTrait::crearCuenta($factura);
                }
                return $factura->id;
            });
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function show($id)
    {
        return static::verVenta($id);
    }

    public static function detallesVentas($details, $factura)
    {
        foreach ($details as $detail) {
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
                'created_at' => now()->format('Y-m-d'),
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
            'fecha' => $atributos['fecha'],
            'observaciones' => $atributos['observaciones'],
            "bonificacion" => $atributos['bonificacion'] * 1,
            "recargo" => $atributos['recargo'] * 1,
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
        // $fecha = new Carbon($factura->fecha);
        // $fechaCot = new Carbon($factura->fechaCotizacion);
        // $factura->fecha = $fecha->format('d-m-Y');
        // $factura->fechaCotizacion = $fechaCot->format('d-m-Y');
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

    // Reestablecer inventarios e iva de cuentas corrientes

    public static function delete($id)
    {
        if (auth()->user()->role->role == 'superAdmin' || auth()->user()->role->role == 'administrador') {
            $venta = Venta::findOrFail($id);
            $sePuede = true;

            if (!$venta->numfactura) {
                if ($venta->cuenta) {
                    count($venta->cuenta->pagos) == 0 ? $sePuede = true : $sePuede = false;
                } else {
                    $sePuede = true;
                }
            } else {
                $sePuede = false;
            }

            if ($sePuede) {
                DB::transaction(function () use ($venta) {
                    if ($venta->cuenta) {
                        $venta->cuenta->movimientos->each->delete();
                        $venta->cuenta->delete();
                    }

                    // Eliminar entregas
                    if ($venta->entregas) {
                        static::reestablecerInventarios($venta->entregas);
                    }

                    // Eliminar facturas
                    if ($venta->facturas) {
                        static::descontarIVA($venta->facturas);
                    }

                    $venta->pedido->numventa = null;
                    $venta->pedido->touch();
                    // $venta->articulos()->detach();
                    $venta->delete();
                });
                return response()->json('Venta Anulada');
            } else {
                return response()->json('No es posible anular la venta');
            }
        } else abort(403, 'Acceso Denegado');
    }

    public static function reestablecerInventarios($entregas)
    {
        foreach ($entregas as $item) {
            $entrega = Entrega::findOrFail($item->id);

            $detalles = collect($entrega->articulos);
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

            EntregasTrait::reestablecerInventarios($inventarios, $entrega);

            $entrega->articulos()->detach();
            $entrega->delete();
        }
    }

    public static function descontarIVA($facturas)
    {
        foreach ($facturas as $factura) {
            CuentasCorrientesTrait::descontarIVA($factura->cliente, $factura->iva);
            $factura->articulos()->detach();
            $factura->ventas()->detach();
            $factura->delete();
        }
    }
}
