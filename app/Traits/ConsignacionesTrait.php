<?php

namespace App\Traits;

use App\User;
use App\Venta;
use App\Articulo;
use Carbon\Carbon;
use App\Inventario;
use App\Consignment;
use App\Traits\InventariosAdmin;
use App\Traits\ArticulosNotificacionesTrait;

trait ConsignacionesTrait
{
    public static function index()
    {
        $consignaciones = Consignment::orderBy('id', 'DESC')->get();

        foreach ($consignaciones as $consig) {
            $fecha = new Carbon($consig->fecha);
            $consig->fecha = $fecha->format('d-m-Y');
            $consig->dependencia = User::find($consig->dependencia);
        }

        return ['consignaciones' => $consignaciones];
    }

    public static function store($request)
    {
        $consignacion = ConsignacionesTrait::storeConsignaciones($request);
        $mover = ConsignacionesTrait::moverInventarios($request);
        if ($mover) {
            if ($request->tipo != 'TRANSFERENCIA') {
                $venta = ConsignacionesTrait::ventaConsignaciones($request);
                $consignacion->numventa = $venta->id;
                $consignacion->save();
            }
        } else {
            $consignacion->articulos()->detach();
            $consignacion->forceDelete();
        }
    }

    public static function show($id)
    {
        return $consignacion = Consignment::find($id);
    }

    public static function storeConsignaciones($request)
    {
        $data = [
            'tipo' => $request->tipo,
            'fecha' => now(),
            'observaciones' => $request->observaciones,
            'bonificacion' => $request->bonificacion,
            'recargo' => $request->recargo,
            'subtotal' => $request->subtotal,
            'total' => $request->total,
            'subtotalPesos' => $request->subtotalPesos,
            'totalPesos' => $request->totalPesos,
            'cotizacion' => $request->cotizacion,
            'fechaCotizacion' => $request->fechaCotizacion,
            'dependencia' => $request->vendedor_id,
            'user_id' => auth()->user()->id
        ];

        $consignacion = Consignment::create($data);

        foreach ($request->detalles as $detail) {
            $det = [
                'codarticulo' => $detail['codarticulo'],
                'articulo' => $detail['articulo'],
                'medida' => $detail['medida'],
                'litros' => $detail['litros'],
                'cantidad' => $detail['cantidad'],
                'cantidadLitros' => $detail['cantidadLitros'],
                'preciounitario' => $detail['precio'],
                'subtotal' => $detail['subtotalDolares'],
                'subtotalPesos' => $detail['subtotalPesos'],
                'cotizacion' => $detail['cotizacion'],
                'fechaCotizacion' => $detail['fechaCotizacion'],
                'articulo_id' => $detail['id'],
                'consignment_id' => $consignacion->id,
            ];

            $detalles[] = $det;
        }

        $consignacion->articulos()->attach($detalles);

        return $consignacion;
    }

    public static function ventaConsignaciones($request)
    {
        if ($request->tipo != 'TRANSFERENCIA') {
            $data = [
                'tipocomprobante' => 'VENTA X CONSIGNACION',
                'numventa' => 0,
                'cuit' => 0,
                'fecha' => now(),
                'observaciones' => $request->observaciones,
                'bonificacion' => $request->get('bonificacion', 0),
                'recargo' => $request->get('recargo', 0),
                'pagada' => true,
                'condicionventa' => 'CONTADO',
                'subtotal' => $request->subtotal,
                'total' => $request->total,
                'subtotalPesos' => $request->subtotalPesos,
                'totalPesos' => $request->totalPesos,
                'cotizacion' => $request->cotizacion,
                'fechaCotizacion' => $request->fechaCotizacion,
                'numfactura' => 0,
                'cliente_id' => 1,
                // 'dependencia' => $request->vendedor_id,
                'user_id' => auth()->user()->id
            ];

            $venta = Venta::create($data);

            foreach ($request->detalles as $detail) {
                $det = array(
                    'codarticulo' => $detail['codarticulo'],
                    'articulo' => $detail['articulo'],
                    'cantidad' => $detail['cantidad'],
                    'cantidadLitros' => $detail['cantidadLitros'],
                    'medida' => $detail['medida'],
                    'preciounitario' => $detail['precio'],
                    'subtotalPesos' => $detail['subtotalPesos'],
                    'subtotal' => $detail['subtotalDolares'],
                    'cotizacion' => $detail['cotizacion'],
                    'fechaCotizacion' => $detail['fechaCotizacion'],
                    'articulo_id' => $detail['id'],
                    'venta_id' => $venta->id,
                );

                $detalles[] = $det;
            }

            $venta->articulos()->attach($detalles);

            return $venta;
        }
    }

    public static function moverInventarios($request)
    {
        $detalles = $request->detalles;
        $dependencia_id = $request->vendedor_id;

        foreach ($detalles as $detalle) {
            $articulo_id = $detalle['id'];
            $litros = $detalle['litros'];
            $cantidad = $detalle['cantidad'];
            $origen = Inventario::find($detalle['inventarios']['id']);
            $actualizar = Inventario::where('articulo_id', $articulo_id)->where('dependencia', $dependencia_id)->get()->first();

            $data = [
                'cantidad' => $cantidad,
                'cantidadlitros' => $cantidad * $litros,
                'articulo_id' => $articulo_id,
                'dependencia' => $dependencia_id,
            ];

            if ($data['cantidad'] <= $origen['cantidad']) {
                if ($actualizar) {
                    $data['inventario_id'] = $actualizar['id'];
                    InventariosAdmin::actualizarInventario($data, $actualizar);
                    $data['inventario_id'] = $origen['id'];
                    $inventory = InventariosAdmin::decrementarInventario($data);
                    if ($inventory['cantidad'] == 0) {
                        // Probando notificaciones
                        $arti = Articulo::find($inventory['articulo_id']);
                        ArticulosNotificacionesTrait::crearNotificacion($arti);
                        // ------------
                    }

                    $data['inventario_id'] = $actualizar['id'];
                    InventariosAdmin::movimientoIncrementoConsignacion($data);
                    $data['inventario_id'] = $origen['id'];
                    InventariosAdmin::movimientoBajaConsignacion($data);
                } else {
                    $inventario = InventariosAdmin::altaConsignacion($data);
                    $data['inventario_id'] = $origen['id'];
                    InventariosAdmin::movimientoAltaConsignacion($inventario, $data);

                    $inventory = InventariosAdmin::decrementarInventario($data);
                    if ($inventory['cantidad'] == 0) {
                        // Probando notificaciones
                        $arti = Articulo::find($inventory['articulo_id']);
                        ArticulosNotificacionesTrait::crearNotificacion($arti);
                        // ------------
                    }
                    $data['inventario_id'] = $origen['id'];
                    InventariosAdmin::movimientoBajaConsignacion($data);
                };
                return true;
            } else {
                return false;
            }
        }
    }
}
