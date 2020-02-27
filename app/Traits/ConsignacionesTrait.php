<?php

namespace App\Traits;

use App\User;
use App\Venta;
use App\Articulo;
use Carbon\Carbon;
use App\Inventario;
use App\Consignment;
use Illuminate\Http\Request;
use App\Traits\InventariosAdmin;
use App\Traits\ArticulosNotificacionesTrait;

trait ConsignacionesTrait
{
    public static function index($request)
    {
        $consignaciones = Consignment::orderBy('id', 'DESC')->get();

        foreach ($consignaciones as $consig) {
            $fecha = new Carbon($consig->fecha);
            $consig->fecha = $fecha->format('d-m-Y');
            $consig->dependencia = User::find($consig->dependencia);
        }

        return [
            'consignaciones' => $consignaciones->take($request->get('limit', null)),
            'total' => $consignaciones->count()
        ];
    }

    public static function store($request)
    {
        ConsignacionesTrait::storeConsignaciones($request);
        ConsignacionesTrait::moverInventarios($request);
    }

    public static function show($id)
    {
        $consignacion = Consignment::find($id);
        $detalles = $consignacion->articulos;
        $dependencia = User::find($consignacion->dependencia);

        return [
            'consignacion' => $consignacion,
            'dependencia' => $dependencia,
            'detalles' => $detalles
        ];
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
