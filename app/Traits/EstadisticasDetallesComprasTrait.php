<?php

namespace App\Traits;

use App\Articulo;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait EstadisticasDetallesComprasTrait
{
    public static function detallesCompras($request)
    {
        $products = collect();
        $productos = [];
        $comprasProductos = [];

        $detallesCompras = DB::table('articulo_compra')->orderBy('created_at', 'ASC')->get();
        if (count($detallesCompras) > 0) {
            $inicio = $detallesCompras->first();
            $ultima = $detallesCompras->last();
            $from = $request->get('desde', $inicio->created_at);
            $to = $request->get('hasta', $ultima->created_at);
        } else {
            $from = $request->get('desde', now());
            $to = $request->get('hasta', now());
        }
        $desde = new Carbon($from);
        $hasta = new Carbon($to);

        // $detallesCompras = DB::table('articulo_factura')->orderBy('created_at', 'ASC')->get();

        $detallesCompras = DB::table('articulo_compra')
            // ->whereDate('created_at', '>=', $desde->format('Y-m-d'))
            // ->whereDate('created_at', '<=', $hasta->format('Y-m-d'))
            ->take($request->get('limit', null))
            ->orderBy('created_at', 'ASC')->get();


        // Productos
        foreach ($detallesCompras as $detalle) {
            $detalle->articulo_id;
            $product = Articulo::withTrashed()->find($detalle->articulo_id);
            $products->push($product);
        }
        $auxProductos = $products->unique();
        foreach ($auxProductos as $article) {
            $det = DB::table('articulo_compra')->where('articulo_id', $article->id)->get();
            array_push($productos, $article);
            array_push($comprasProductos, $det);
        }

        $coleccion  = collect();
        $nuevo = null;
        foreach ($comprasProductos as $compra) {
            if (count($compra) > 1) {
                $suma = $compra->sum('cantidad');
                foreach ($compra as $com) {
                    $nuevo = $com;
                }
                $nuevo->cantidad = $suma;
                $coleccion->push($nuevo);
            } else {
                foreach ($compra as $com) {
                    $nuevo = $com;
                }
                $coleccion->push($nuevo);
            }
        }

        $columnsProductos = ['producto', 'totalComprado'];
        $rowsProductos = collect();
        $total = 0;
        $prod = null;
        for ($i = 0; $i < count($comprasProductos); $i++) {
            $otro = $comprasProductos[$i];
            foreach ($otro as $a) {
                $prod = Articulo::withTrashed()->find($a->articulo_id);
                $total = $a->cantidad;
            }
            $rowsProductos->push([
                'producto' => $prod->articulo,
                'totalComprado' =>  $total,
            ]);
            $total = 0;
        };
        $comprasProductosChart = collect();
        $comprasProductosChart->put('columns', $columnsProductos);
        $comprasProductosChart->put('rows', $rowsProductos);

        $detalles = [
            'productos' =>  $productos,
            'comprasProductos' =>  $coleccion,
            'comprasProductosChart' => $comprasProductosChart
        ];

        return ['detalles' => $detalles];
    }
}
