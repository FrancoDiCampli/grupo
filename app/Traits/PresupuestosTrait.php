<?php

namespace App\Traits;

use App\Venta;
use App\Cliente;
use Carbon\Carbon;
use App\Presupuesto;
use App\Traits\ConfiguracionTrait;
use Illuminate\Support\Facades\DB;

trait PresupuestosTrait
{
    public static function index($request)
    {
        if (auth()->user()->role->role != 'vendedor') {
            $pres = Presupuesto::orderBy('comprobanteadherido', 'DESC')->whereNull('numventa')->take($request->get('limit', null))->get();
        } else {
            $pres = Presupuesto::orderBy('comprobanteadherido', 'DESC')->whereNull('numventa')
                ->where('user_id', auth()->user()->id)->take($request->get('limit', null))
                ->get();
        }

        $presupuestos = collect();

        foreach ($pres as $pre) {
            $venta = Venta::find($pre->numventa);
            $venta ? $pre['venta'] = true : $pre['venta'] = false;
            $fecha = new Carbon($pre->fecha);
            $pre->fecha = $fecha->format('d-m-Y');
            $pre->cliente = Cliente::withTrashed()->find($pre->cliente_id);;
            $pre = collect($pre);
            $presupuestos->push($pre);
        }

        return response()->json([
            'presupuestos' => $presupuestos,
            'ultimo' => $presupuestos->first(),
            'total' => Presupuesto::whereNull('numventa')->count(),
        ]);
    }

    public static function store($request)
    {
        $request->validate(
            [
                'pedidoadherido' => 'required|unique:presupuestos,comprobanteadherido,NULL,id,deleted_at,NULL'
            ],
            [
                'pedidoadherido.unique' => 'El valor del campo Nota de pedido adherida Nº ya está en uso.',
            ]
        );

        $atributos = $request;
        $cliente = Cliente::find($atributos['cliente_id']);
        $atributos['cuit'] = $cliente->documentounico;

        $presupuesto = DB::transaction(function () use ($atributos, $request) {
            // ALMACENAMIENTO DE PRESUPUESTO
            $presupuesto = static::crearPresupuesto($atributos);

            if ($atributos->confirmacion) {
                static::confirmarVenta($atributos, $request, $presupuesto);
            }

            // ALMACENAMIENTO DE DETALLES
            $det = static::detallesPresupuesto($request->get('detalles'), $presupuesto);

            $presupuesto->articulos()->attach($det);

            return $presupuesto;
        });
        return response()->json($presupuesto->id);
    }

    public static function confirmarVenta($atributos, $request, $presupuesto)
    {
        $request->validate(
            [
                'remitoadherido' => 'required|unique:ventas,comprobanteadherido,NULL,id,deleted_at,NULL'
            ],
            [
                'remitoadherido.unique' => 'El valor del campo Remito adherido Nº ya está en uso.',
            ]
        );

        $atributos['comprobanteadherido'] = $request['remitoadherido'];
        $atributos['tipoComprobante'] = 'REMITO X';
        $atributos['condicionventa'] = 'CUENTA CORRIENTE';
        $numventa = Venta::all()->last() ? Venta::all()->last()->id : 0;
        $atributos['numventa'] = $numventa + 1;

        $venta = DB::transaction(function () use ($atributos, $request) {
            $venta = static::crearVenta($atributos);

            $det = static::detallesVentas($request->get('detalles'), $venta);

            $venta->articulos()->attach($det);

            return $venta;
        });

        // CREACION DE CUENTA CORRIENTE
        if ($presupuesto->cuit != 0) {
            DB::transaction(function () use ($venta) {
                CuentasCorrientesTrait::crearCuenta($venta);
            });
        }

        $presupuesto->numventa = $venta->id;
        $presupuesto->update();
    }

    public static function detallesVentas($details, $factura)
    {
        foreach ($details as $detail) {
            // return $detail;
            $detalles = array(
                'codarticulo' => $detail['codarticulo'],
                'articulo' => $detail['articulo'],
                'cantidad' => $detail['cantidad'],
                'cantidadLitros' => $detail['cantidadLitros'],
                'medida' => $detail['medida'],
                'preciounitario' => $detail['precio'],
                'subtotalPesos' => $detail['subtotalDolares'] * $factura['cotizacion'],
                'subtotal' => $detail['subtotalDolares'],
                'cotizacion' => $factura['cotizacion'],
                'fechaCotizacion' => $factura['fechaCotizacion'],
                'articulo_id' => $detail['articulo_id'] ?? $detail['id'],
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

    public static function detallesPresupuesto($details, $presupuesto)
    {
        foreach ($details as $detail) {
            $detalles = array(
                'codarticulo' => $detail['codarticulo'],
                'articulo' => $detail['articulo'],
                'cantidad' => $detail['cantidad'],
                'cantidadLitros' => $detail['cantidadLitros'],
                'medida' => $detail['medida'],
                'preciounitario' => $detail['precio'],
                'subtotalPesos' => $detail['subtotalDolares'] * $presupuesto['cotizacion'],
                'subtotal' => $detail['subtotalDolares'],
                'cotizacion' => $presupuesto['cotizacion'],
                'fechaCotizacion' => $presupuesto['fechaCotizacion'],
                'articulo_id' => $detail['id'],
                'presupuesto_id' => $presupuesto->id,
            );
            $det[] = $detalles;
        }
        return $det;
    }

    public static function crearPresupuesto($atributos)
    {
        $configuracion = ConfiguracionTrait::configuracion();

        return Presupuesto::create([
            "ptoventa" => $configuracion['puntoventa'],
            "numpresupuesto" => $atributos['numpedido'],
            "comprobanteadherido" => $atributos['pedidoadherido'],
            "cuit" => $atributos['cuit'],
            "fecha" => $atributos['fecha'],
            "bonificacion" => $atributos['bonificacion'] * 1,
            "recargo" => $atributos['recargo'] * 1,
            "subtotal" => $atributos['subtotal'],
            "total" => $atributos['total'],
            "subtotalPesos" => $atributos['subtotal'] * $atributos['cotizacion'],
            "totalPesos" => $atributos['total'] * $atributos['cotizacion'],
            'cotizacion' => $atributos['cotizacion'],
            'fechaCotizacion' => $atributos['fechaCotizacion'],
            "observaciones" => $atributos['observaciones'],
            "cliente_id" => $atributos['cliente_id'],
            "user_id" => auth()->user()->id
        ]);
    }

    public static function show($id)
    {
        $res = static::verPresupuesto($id);
        $articulos = collect($res['presupuesto']->articulos);
        $detallesVenta = collect();
        foreach ($articulos as $art) {
            $stock = $art->inventarios->sum('cantidad');
            if ($stock > 0) {
                $detallesVenta->push($art->pivot);
            }
        }
        $res['detallesVenta'] = $detallesVenta;

        return response()->json($res);
    }

    public static function verPresupuesto($id)
    {
        $configuracion = ConfiguracionTrait::configuracion();
        $presupuesto = Presupuesto::find($id);
        $cliente = Cliente::withTrashed()->find($presupuesto->cliente_id);
        $detalles = DB::table('articulo_presupuesto')->where('presupuesto_id', $presupuesto->id)->get();

        return [
            'configuracion' => $configuracion,
            'presupuesto' => $presupuesto,
            'detalles' => $detalles,
            'cliente' => $cliente
        ];
    }

    public static function vender($request)
    {
        $presupuesto = Presupuesto::find($request->id);
        $pre = Presupuesto::find($request->id);

        $presupuesto['tipoComprobante'] = 'REMITO X';
        $numventa = Venta::all()->last() ? Venta::all()->last()->id : 0;
        $presupuesto['numventa'] = $numventa + 1;
        $presupuesto['condicionventa'] = 'CUENTA CORRIENTE';
        $presupuesto['remitoadherido'] = $request->remitoadherido;

        $request->validate(
            [
                'remitoadherido' => 'required|unique:ventas,comprobanteadherido,NULL,id,deleted_at,NULL'
            ],
            [
                'remitoadherido.unique' => 'El valor del campo Remito adherido Nº ya está en uso.',
            ]
        );

        $venta = VentasTrait::crearVenta($presupuesto);

        // CREACION DE CUENTA CORRIENTE
        if ($presupuesto->cuit != 0) {
            CuentasCorrientesTrait::crearCuenta($venta);
        }

        foreach ($presupuesto->articulos as $item) {
            $detail = $item['pivot'];

            $detalles = array(
                'codarticulo' => $detail['codarticulo'],
                'articulo' => $detail['articulo'],
                'cantidad' => $detail['cantidad'],
                'cantidadLitros' => $detail['cantidadLitros'],
                'medida' => $detail['medida'],
                'preciounitario' => $detail['preciounitario'],
                'subtotalPesos' => ($detail['preciounitario'] * $detail['cantidadLitros']) * $detail['cotizacion'],
                'subtotal' => $detail['preciounitario'] * $detail['cantidadLitros'],
                'cotizacion' =>  $detail['cotizacion'],
                'fechaCotizacion' => $detail['fechaCotizacion'],
                'articulo_id' => $detail['articulo_id'],
                'venta_id' => $venta->id,
                'created_at' => now()->format('Ymd'),
            );
            $det[] = $detalles;
        }

        $venta->articulos()->attach($det);

        $pre->numventa = $venta->id;
        $pre->save();

        return response()->json('Listo');
    }

    public static function update($request, $id)
    {
        $request->validate(
            [
                'pedidoadherido' => 'required|unique:presupuestos,comprobanteadherido,' . $id . ',id,deleted_at,NULL'
            ],
            [
                'pedidoadherido.unique' => 'El valor del campo Nota de pedido adherida Nº ya está en uso.',
            ]
        );

        try {
            DB::transaction(function () use ($request, $id) {
                $presupuesto = Presupuesto::find($id);
                $cliente = Cliente::find($request['cliente_id']);

                $presupuesto->update([
                    "comprobanteadherido" => $request['pedidoadherido'],
                    "cuit" => $cliente->documentounico,
                    "fecha" => $request['fecha'],
                    "bonificacion" => $request['bonificacion'] * 1,
                    "recargo" => $request['recargo'] * 1,
                    "subtotal" => $request['subtotal'],
                    "total" => $request['total'],
                    "subtotalPesos" => $request['subtotal'] * $request['cotizacion'],
                    "totalPesos" => $request['totalPesos'],
                    'cotizacion' => $request['cotizacion'],
                    'fechaCotizacion' => $request['fechaCotizacion'],
                    "observaciones" => $request['observaciones'],
                    "cliente_id" => $request['cliente_id'],
                    "user_id" => auth()->user()->id
                ]);

                $dets = collect($presupuesto->articulos->map(function ($item) {
                    return $item['pivot'];
                }));
                $coleccion = collect();

                foreach ($request->detalles as $item) {
                    if (array_key_exists('inventarios', $item)) {
                        DB::table('articulo_presupuesto')
                            ->insert([
                                'codarticulo' => $item['codarticulo'],
                                'articulo' => $item['articulo'],
                                'medida' => $item['medida'],
                                'cantidad' => $item['cantidad'],
                                'cantidadLitros' => $item['cantidadLitros'],
                                'preciounitario' => $item['precio'],
                                'subtotal' => $item['subtotalDolares'],
                                'subtotalPesos' => $item['subtotalDolares'] * $item['cotizacion'],
                                'cotizacion' => $item['cotizacion'],
                                'fechaCotizacion' => $item['fechaCotizacion'],
                                'articulo_id' => $item['id'],
                                'presupuesto_id' => $presupuesto->id,
                                'created_at' => now(),
                                'updated_at' => now()
                            ]);
                    } else {
                        $coleccion->push($item);
                        DB::table('articulo_presupuesto')
                            ->where('id', $item['id'])
                            ->update([
                                'cantidad' => $item['cantidad'],
                                'preciounitario' => $item['precio'],
                                'subtotal' => $item['subtotalDolares'],
                                'subtotalPesos' => $item['subtotalDolares'] * $item['cotizacion'],
                                'cotizacion' => $item['cotizacion'],
                                'fechaCotizacion' => $item['fechaCotizacion'],
                                'updated_at' => now()
                            ]);
                    }
                }

                $keys = $coleccion->keyBy('id');
                $aux = $keys->keys();

                $eliminar = $dets->whereNotIn('id', $aux);

                $eliminar->map(function ($item) {
                    DB::table('articulo_presupuesto')->where('id', $item['id'])->delete();
                });

                $atributos = $request;
                $atributos['cuit'] = $cliente->documentounico;

                if ($atributos->confirmacion) {
                    static::confirmarVenta($atributos, $request, $presupuesto);
                }
            });
            return response()->json('Actualizado');
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function delete($id)
    {
        $pedido = Presupuesto::find($id);

        if ($pedido->numventa == null) {
            DB::transaction(function () use ($pedido) {
                $pedido->articulos()->detach();
                $pedido->delete();
            });
            return  response()->json('Pedido Eliminado');
        } else return  response()->json('No es posible eliminar el pedido');
    }
}
