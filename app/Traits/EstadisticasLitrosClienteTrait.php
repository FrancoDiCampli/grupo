<?php

namespace App\Traits;

use App\Cliente;
use App\Articulo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait EstadisticasLitrosClienteTrait
{
    public static function litrosCliente($request)
    {
        $cliente = Cliente::find($request->id);
        $ventasIds = collect();
        $desde = new Carbon($request->desde);
        $hasta = new Carbon($request->hasta);

        $cliente->facturas->map(function ($venta) use ($ventasIds) {
            $ventasIds->push($venta->id);
        });

        $detalles = DB::table('articulo_venta')->whereIn('venta_id', $ventasIds)->whereDate('created_at', '>=', $desde->format('Y-m-d'))->whereDate('created_at', '<=', $hasta->format('Y-m-d'))->get();

        $columns = ['articulo', 'litrosVendidos'];
        $rows = collect();

        $detalles->groupBy('articulo_id')->map(function ($det) use ($rows) {
            $aux = collect();
            $articulo = Articulo::withTrashed()->find($det->first()->articulo_id);
            $aux->put('articulo', $articulo->articulo);
            $aux->put('litros', $det->sum('cantidadLitros'));
            $rows->push($aux);
        });

        return [
            'columns' => $columns,
            'rows' => $rows
        ];
    }
}
