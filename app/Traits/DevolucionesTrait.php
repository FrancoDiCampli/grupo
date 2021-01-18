<?php

namespace App\Traits;

use Error;
use App\User;
use App\GiveBack;
use Carbon\Carbon;
use App\Inventario;
use App\Traits\InventariosAdmin;
use App\Traits\ConfiguracionTrait;
use Illuminate\Support\Facades\DB;

trait DevolucionesTrait
{
    public static function index($request)
    {
        // $devoluciones = GiveBack::orderBy('id', 'DESC')->get();

        if (auth()->user()->role->role != 'vendedor') {
            $devoluciones = GiveBack::take($request->get('limit', null))
                ->orderBy('id', 'DESC')
                ->get();
        } else {
            $devoluciones = GiveBack::take($request->get('limit', null))
                ->orderBy('id', 'DESC')
                ->where('dependencia', auth()->user()->id)
                ->get();
        }

        foreach ($devoluciones as $devolu) {
            $fecha = new Carbon($devolu->fecha);
            $devolu->fecha = $fecha->format('d-m-Y');
            $devolu->dependencia = User::find($devolu->dependencia);
        }

        return [
            'devoluciones' => $devoluciones->take($request->get('limit', null)),
            'total' => $devoluciones->count()
        ];
    }

    public static function store($request)
    {
        $aux = static::moverInventario($request);
        if ($aux) {
            static::storeDevoluciones($request);
            return ['Devolución exitosa'];
        } else return new Error();
    }

    public static function show($id)
    {
        return static::verDevolucion($id);
    }

    public static function storeDevoluciones($request)
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

        $devolucion = GiveBack::create($data);

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
                'give_back_id' => $devolucion->id,
            ];

            $detalles[] = $det;
        }

        $devolucion->articulos()->attach($detalles);

        return $devolucion;
    }

    public static function moverInventario($request)
    {
        $detalles = $request->detalles;
        $aux = true;
        $contador = 0;
        DB::beginTransaction();
        foreach ($detalles as $detalle) {
            if ($aux) {
                $data = [
                    'inventario_id' => $detalle['inventarios']['id'],
                    'cantidad' => $detalle['cantidad'],
                    'cantidadlitros' => $detalle['litros']
                ];

                if ($detalle['cantidad'] <= $detalle['inventarios']['cantidad'] && $detalle['litros'] <= $detalle['inventarios']['cantidadlitros']) {
                    $origen  =  InventariosAdmin::decrementarInventario($data);
                    $data['cantidadlitros'] = $origen->articulo->litros * $data['cantidad'];
                    InventariosAdmin::movimientoBajaDevolucion($data);

                    $inventario = $origen->articulo->inventarios->first();
                    $destino = Inventario::findOrFail($inventario['id']);

                    $data['inventario_id'] = $destino->id;
                    $data['origen'] = $origen->id;

                    InventariosAdmin::incrementarInventario($data);
                    InventariosAdmin::movimientoAltaDevolucion($data);
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
        }
        return $aux;
    }

    public static function inventariosVendedor($request)
    {
        $articulos = collect();

        $inventarios = Inventario::where('dependencia', $request->dependencia)->get();

        $inventarios->each(function ($inven) use ($articulos) {
            $stock = $inven->cantidadlitros;
            $unidades = $inven->cantidad;
            $inv = collect($inven);
            $art = collect($inven->articulo);
            $art->put('inventarios', $inv);
            $art->put('stock', $stock);
            $art->put('unidades', $unidades);
            $articulos->push($art);
        });

        return $articulos;
    }

    public static function verDevolucion($id)
    {
        $configuracion = ConfiguracionTrait::configuracion();
        $devolucion = GiveBack::find($id);
        $dependencia = User::find($devolucion->dependencia);
        $fecha = new Carbon($devolucion->fecha);
        $devolucion->fecha = $fecha->format('d-m-Y');
        $detalles = DB::table('articulo_give_back')->where('give_back_id', $devolucion->id)->get();

        return [
            'configuracion' => $configuracion,
            'devolucion' => $devolucion,
            'detalles' => $detalles,
            'dependencia' => $dependencia
        ];
    }
}
