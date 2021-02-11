<?php

namespace App\Traits;

use App\Cliente;
use App\Entrega;
use App\Articulo;
use Carbon\Carbon;
use App\Inventario;
use App\Traits\ArticulosTrait;
use App\Traits\MovimientosTrait;
use Illuminate\Support\Facades\DB;

trait EntregasTrait
{
    public static function index($request)
    {
        if (auth()->user()->role->role != 'vendedor') {
            $ents = Entrega::orderBy('comprobanteadherido', 'DESC')->get();
        } else {
            $ents = Entrega::orderBy('comprobanteadherido', 'DESC')
                ->where('user_id', auth()->user()->id)
                ->get();
        }

        $entregas = collect();

        foreach ($ents as $ent) {
            // $fecha = new Carbon($ent->fecha);
            // $ent->fecha = $fecha->format('d-m-Y');
            $ent->cliente = Cliente::withTrashed()->find($ent->cliente_id);;
            $ent = collect($ent);
            $entregas->push($ent);
        }

        if ($entregas->count() <= $request->get('limit')) {
            return [
                'entregas' => $entregas,
                'ultimo' => Entrega::all(['id', 'numentrega'])->last(),
                'total' => $entregas->count(),
            ];
        } else {
            return [
                'entregas' => $entregas->take($request->get('limit', null)),
                'ultimo' => Entrega::all(['id', 'numentrega'])->last(),
                'total' => $entregas->count(),
            ];
        }
    }

    public static function store($request)
    {
        $request->validate(
            [
                'comprobanteadherido' => 'required|unique:entregas,comprobanteadherido'
            ],
            [
                'comprobanteadherido.unique' => 'El valor del campo Entrega adherida Nº ya está en uso.',
            ]
        );

        $atributos = $request;
        $cliente = Cliente::find($atributos['cliente_id']);
        $atributos['cuit'] = $cliente->documentounico;

        try {
            return DB::transaction(function () use ($atributos, $request) {
                // ALMACENAMIENTO DE ENTREGA
                $entrega = static::crearEntrega($atributos);

                // ALMACENAMIENTO DE DETALLES
                $det = static::detallesEntrega($request->get('detalles'), $entrega);

                $entrega->articulos()->attach($det);

                static::actualizarInventarios($det, $entrega);

                return $entrega->id;
            });
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function detallesEntrega($details, $entrega)
    {
        foreach ($details as $detail) {
            $detalles = array(
                'codarticulo' => $detail['codarticulo'],
                'articulo' => $detail['articulo'],
                'cantidad' => $detail['entregando'],
                'cantidadLitros' => $detail['litros'] * $detail['entregando'],
                'medida' => $detail['medida'],
                'articulo_id' => $detail['articulo_id'],
                'entrega_id' => $entrega->id,
                'articulo_venta_id' => $detail['id']
            );
            $det[] = $detalles;
        }
        return $det;
    }

    public static function crearEntrega($atributos)
    {
        $configuracion = ConfiguracionTrait::configuracion();
        $numentrega = Entrega::all()->last() ? Entrega::all()->last()->id : 0;

        return Entrega::create([
            "ptoventa" => $configuracion['puntoventa'],
            "numentrega" => $numentrega + 1,
            "comprobanteadherido" => $atributos['comprobanteadherido'],
            "cuit" => $atributos['cuit'],
            "fecha" => now()->format('Ymd'),
            "observaciones" => $atributos['observaciones'],
            "cliente_id" => $atributos['cliente_id'],
            "user_id" => auth()->user()->id,
            "venta_id" => $atributos['venta_id'],
        ]);
    }

    public static function show($id)
    {
        $configuracion = ConfiguracionTrait::configuracion();
        $entrega = Entrega::find($id);
        $detalles = collect();
        foreach ($entrega->articulos as $det) {
            $detalles->push($det['pivot']);
        }
        $fecha = new Carbon($entrega->fecha);
        $entrega->fecha = $fecha->format('d-m-Y');
        $cliente = Cliente::withTrashed()->find($entrega->cliente_id);
        return [
            'configuracion' => $configuracion,
            'entrega' => $entrega,
            'detalles' => $detalles,
            'cliente' => $cliente
        ];
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

                MovimientosTrait::crearMovimiento('ENTREGA', $aux[$i]['cantidad'], ($aux[$i]['cantidad'] * $art->litros), $article[0], $factura);
            } else {
                $factura->articulos()->detach();
                $factura->delete();
            }

            if ($article[0]['cantidad'] == 0 || $article[0]['cantidad'] <= $article[0]->articulo->stockminimo) { // REVISAR NOTIFICACIONES
                $arti = Articulo::find($article[0]->articulo_id);
                ArticulosTrait::crearNotificacion($arti);
            }

            unset($article);
        }
    }

    public static function delete($id)
    {
        if (auth()->user()->role->role == 'superAdmin' || auth()->user()->role->role == 'administrador') {
            $entrega = Entrega::findOrFail($id);

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
            // SE REESTABLECE LA CANTIDAD EN LOS INVENTARIOS
            try {
                DB::transaction(function () use ($inventarios, $entrega) {
                    static::reestablecerInventarios($inventarios, $entrega);
                    $entrega->articulos->map(function ($detalle) {
                        return auth()->user()->notifications()
                            ->where('data->action', 'articulos/show/' . $detalle['id'])
                            ->whereRead_at(null)
                            ->delete();
                    });
                    $entrega->articulos()->detach();
                    $entrega->delete();
                });
                return response()->json('Entrega Anulada');
            } catch (\Throwable $th) {
                throw $th;
            }
        } else abort(403, 'Acceso Denegado');
    }

    public static function reestablecerInventarios($inventarios, $entrega)
    {
        foreach ($inventarios as $inv) {
            $aux = collect($inv->movimientos);
            $aux = $aux->where('numcomprobante', $entrega->id);
            foreach ($aux as $a) {
                $inventario = $a->inventario;
                $inventario->cantidad = $inventario->cantidad + $a->cantidad;
                $inventario->cantidadlitros = $inventario->cantidadlitros + $a->cantidadlitros;
                $inventario->save();

                MovimientosTrait::crearMovimiento('ANULACION', $a->cantidad, $a->cantidadlitros, $inventario, $entrega);
            }
        }
    }
}
