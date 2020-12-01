<?php

namespace App\Traits;

use App\Cliente;
use App\Articulo;
use Illuminate\Support\Facades\DB;

trait EstadisticasLitrosClienteTrait
{
    public static function litrosCliente()
    {
        $cliente = Cliente::find(2);
        $ventasIds = collect();

        $cliente->facturas->map(function ($venta) use ($ventasIds) {
            $ventasIds->push($venta->id);
        });

        $detalles = DB::table('articulo_venta')->whereIn('venta_id', $ventasIds)->whereDate('created_at', '>=', now()->subDay()->format('Y-m-d'))->whereDate('created_at', '<=', now()->addDay()->format('Y-m-d'))->get();

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
