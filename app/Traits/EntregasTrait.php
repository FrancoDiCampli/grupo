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
            $ents = Entrega::orderBy('id', 'DESC')->get();
        } else {
            $ents = Entrega::orderBy('id', 'DESC')
                ->where('user_id', auth()->user()->id)
                ->get();
        }

        $entregas = collect();

        foreach ($ents as $ent) {
            $fecha = new Carbon($ent->fecha);
            $ent->fecha = $fecha->format('d-m-Y');
            $ent->cliente = Cliente::withTrashed()->find($ent->cliente_id);;
            $ent = collect($ent);
            $entregas->push($ent);
        }

        if ($entregas->count() <= $request->get('limit')) {
            return [
                'entregas' => $entregas,
                'ultimo' => $entregas->first(),
                'total' => $entregas->count(),
            ];
        } else {
            return [
                'entregas' => $entregas->take($request->get('limit', null)),
                'ultimo' => $entregas->first(),
                'total' => $entregas->count(),
            ];
        }
    }

    public static function store($request)
    {
        return $request;

        $atributos = $request;
        $cliente = Cliente::find($atributos['cliente_id']);
        $atributos['cuit'] = $cliente->documentounico;

        DB::transaction(function () use ($atributos, $request) {
            // ALMACENAMIENTO DE ENTREGA
            $entrega = static::crearEntrega($atributos);

            // ALMACENAMIENTO DE DETALLES
            $det = static::detallesEntrega($request->get('detalles'), $entrega);

            $entrega->articulos()->attach($det);

            static::actualizarInventarios($det, $entrega);

            return $entrega->id;
        });
    }

    public static function detallesEntrega($details, $entrega)
    {
        foreach ($details as $detail) {
            $detalles = array(
                'codarticulo' => $detail['codarticulo'],
                'articulo' => $detail['articulo'],
                'cantidad' => $detail['cantidad'],
                'cantidadLitros' => $detail['cantidadLitros'],
                'medida' => $detail['medida'],
                // 'preciounitario' => $detail['precio'],
                // 'subtotalPesos' => null,
                // 'subtotal' => $detail['subtotalDolares'],
                // 'cotizacion' => null,
                // 'fechaCotizacion' => null,
                'articulo_id' => $detail['id'],
                'entrega_id' => $entrega->id,
                'articulo_venta_id' => $detail['articulo_venta_id']
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
            // "numentrega" => $atributos['numentrega'],
            "numentrega" => $numentrega + 1,
            "cuit" => $atributos['cuit'],
            "fecha" => now()->format('Ymd'),
            // "bonificacion" => $atributos['bonificacion'] * 1,
            // "recargo" => $atributos['recargo'] * 1,
            // "subtotal" => $atributos['subtotal'],
            // "total" => $atributos['total'],
            // "subtotalPesos" => $atributos['subtotalPesos'],
            // "totalPesos" => $atributos['totalPesos'],
            // 'cotizacion' => $atributos['cotizacion'],
            // 'fechaCotizacion' => $atributos['fechaCotizacion'],
            "observaciones" => $atributos['observaciones'],
            "cliente_id" => $atributos['cliente_id'],
            "user_id" => auth()->user()->id
        ]);
    }

    public static function show($id)
    {
        return $entrega = Entrega::find($id);
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
                $factura->forceDelete();
            }

            if ($article[0]['cantidad'] == 0) {
                $arti = Articulo::find($article[0]->articulo_id);
                ArticulosTrait::crearNotificacion($arti);
            }

            unset($article);
        }
    }

    public static function delete($id)
    {
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
        unset($aux);
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
        $entrega->articulos()->detach();
        $entrega->forceDelete();
        return ['msg' => 'Entrega Anulada'];
    }
}
