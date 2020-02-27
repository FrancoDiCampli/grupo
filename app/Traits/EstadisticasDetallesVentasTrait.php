<?php

namespace App\Traits;

use App\Articulo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait EstadisticasDetallesVentasTrait
{
    public static function detallesVentas($request)
    {
        $products = collect();
        $productos = [];
        $ventasProductos = [];

        $detallesVentas = DB::table('articulo_venta')->orderBy('created_at', 'ASC')->get();
        if (count($detallesVentas) > 0) {
            $inicio = $detallesVentas->first();
            $ultima = $detallesVentas->last();
            $from = $request->get('desde', $inicio->created_at);
            $to = $request->get('hasta', $ultima->created_at);
        } else {
            $from = $request->get('desde', now());
            $to = $request->get('hasta', now());
        }
        $desde = new Carbon($from);
        $hasta = new Carbon($to);

        // $detallesVentas = DB::table('articulo_factura')->orderBy('created_at', 'ASC')->get();

        $detallesVentas = DB::table('articulo_venta')
            // ->whereDate('created_at', '>=', $desde->format('Y-m-d'))
            // ->whereDate('created_at', '<=', $hasta->format('Y-m-d'))
            ->take($request->get('limit', null))
            ->orderBy('created_at', 'ASC')->get();


        // Productos
        foreach ($detallesVentas as $detalle) {
            $detalle->articulo_id;
            $product = Articulo::withTrashed()->find($detalle->articulo_id);
            $products->push($product);
        }
        $auxProductos = $products->unique();
        foreach ($auxProductos as $article) {
            $det = DB::table('articulo_venta')->where('articulo_id', $article->id)->get();
            array_push($productos, $article);
            array_push($ventasProductos, $det);
        }

        $coleccion  = collect();
        $nuevo = null;
        foreach ($ventasProductos as $venta) {
            if (count($venta) > 1) {
                $suma = $venta->sum('cantidad');
                foreach ($venta as $ven) {
                    $nuevo = $ven;
                }
                $nuevo->cantidad = $suma;
                $coleccion->push($nuevo);
            } else {
                foreach ($venta as $ven) {
                    $nuevo = $ven;
                }
                $coleccion->push($nuevo);
            }
        }

        $columnsProductos = ['producto', 'totalVendido'];
        $rowsProductos = collect();
        $total = 0;
        $prod = null;
        for ($i = 0; $i < count($ventasProductos); $i++) {
            $otro = $ventasProductos[$i];
            foreach ($otro as $a) {
                $prod = Articulo::withTrashed()->find($a->articulo_id);
                $total = $a->cantidad;
            }
            $rowsProductos->push([
                'producto' => $prod->articulo,
                'totalVendido' =>  $total,
            ]);
            $total = 0;
        };
        $ventasProductosChart = collect();
        $ventasProductosChart->put('columns', $columnsProductos);
        $ventasProductosChart->put('rows', $rowsProductos);

        $detalles = [
            'productos' =>  $productos,
            'ventasProductos' =>  $coleccion,
            'ventasProductosChart' => $ventasProductosChart
        ];

        return ['detalles' => $detalles];
    }
}
