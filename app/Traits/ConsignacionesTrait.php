<?php

namespace App\Traits;

use Error;
use App\User;
use App\Articulo;
use Carbon\Carbon;
use App\Inventario;
use App\Consignment;
use App\Traits\ArticulosTrait;
use App\Traits\InventariosAdmin;
use App\Traits\ConfiguracionTrait;
use Exception;
use Illuminate\Support\Facades\DB;

trait ConsignacionesTrait
{
    public static function index($request)
    {
        if (auth()->user()->role->role != 'vendedor') {
            $consignaciones = Consignment::take($request->get('limit', null))
                ->orderBy('comprobanteadherido', 'DESC')
                ->get();
        } else {
            $consignaciones = Consignment::take($request->get('limit', null))
                ->orderBy('comprobanteadherido', 'DESC')
                ->where('dependencia', auth()->user()->id)
                ->get();
        }

        foreach ($consignaciones as $consig) {
            // $fecha = new Carbon($consig->fecha);
            // $consig->fecha = $fecha->format('d-m-Y');
            $consig->dependencia = User::find($consig->dependencia);
        }

        return [
            'consignaciones' => $consignaciones->take($request->get('limit', null)),
            'total' => $consignaciones->count()
        ];
    }

    public static function store($request)
    {
        $aux = static::moverInventarios($request);
        if ($aux) {
            static::storeConsignaciones($request);
            return ['Consignación exitosa'];
        } else return new Error();
    }

    public static function show($id)
    {
        return static::verConsignacion($id);
    }

    public static function verConsignacion($id)
    {
        $configuracion = ConfiguracionTrait::configuracion();
        $consignacion = Consignment::find($id);
        $dependencia = User::find($consignacion->dependencia);
        // $fecha = new Carbon($consignacion->fecha);
        // $consignacion->fecha = $fecha->format('d-m-Y');
        $detalles = DB::table('articulo_consignment')->where('consignment_id', $consignacion->id)->get();

        return [
            'configuracion' => $configuracion,
            'consignacion' => $consignacion,
            'detalles' => $detalles,
            'dependencia' => $dependencia
        ];
    }

    public static function storeConsignaciones($request)
    {
        $request->validate([
            'fecha' => 'required',
            'comprobanteadherido' => 'required',
            'fecha' => 'required',
            'subtotal' => 'required',
            'total' => 'required',
        ]);

        $data = [
            'tipo' => $request->tipo,
            'fecha' => $request->fecha,
            'comprobanteadherido' => $request->comprobanteadherido,
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
                'subtotalPesos' => null,
                'cotizacion' => null,
                'fechaCotizacion' => null,
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
        $aux = true;
        $contador = 0;
        DB::beginTransaction();
        foreach ($detalles as $detalle) {
            if ($aux) {
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
                    'observaciones' => $request->observaciones
                ];

                if ($data['cantidad'] <= $origen['cantidad']) {
                    if ($actualizar) {
                        $data['inventario_id'] = $actualizar['id'];
                        InventariosAdmin::actualizarInventario($data, $actualizar);
                        $data['inventario_id'] = $origen['id'];
                        $inventory = InventariosAdmin::decrementarInventario($data);
                        if ($inventory['cantidad'] == 0) {
                            $arti = Articulo::find($inventory['articulo_id']);
                            ArticulosTrait::crearNotificacion($arti);
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
                            $arti = Articulo::find($inventory['articulo_id']);
                            ArticulosTrait::crearNotificacion($arti);
                        }
                        $data['inventario_id'] = $origen['id'];
                        InventariosAdmin::movimientoBajaConsignacion($data);
                    };
                } else {
                    $aux = false;
                    $contador++;
                }
            }
        }
        if ($contador == 0) {
            DB::commit();
        } else {
            DB::rollback();
            abort(400, 'Stock Insuficiente');
        }
        return $aux;
    }
}
