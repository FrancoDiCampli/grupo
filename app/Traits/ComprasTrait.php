<?php

namespace App\Traits;

use App\Compra;
use App\Articulo;
use App\Supplier;
use Carbon\Carbon;
use App\Inventario;
use App\Traits\MovimientosTrait;
use App\Traits\ConfiguracionTrait;
use Illuminate\Support\Facades\DB;

trait ComprasTrait
{
    public static function index($request)
    {
        if ($request->fec) {
            $fec = $request->fec;
            $rems = Compra::whereDate('created_at', $fec)->orderBy('id', 'DESC')->get();
        } else {
            $rems = Compra::orderBy('id', 'DESC')->get();
        }

        $remitos = collect();

        foreach ($rems as $rem) {
            $fecha = new Carbon($rem->fecha);
            $rem->fecha = $fecha->format('d-m-Y');
            $rem->proveedor = Supplier::withTrashed()->find($rem->supplier_id);
            $rem = collect($rem);
            $remitos->push($rem);
        }

        if ($remitos->count() <= $request->get('limit')) {
            return [
                'remitos' => $remitos,
                'total' => $remitos->count(),
            ];
        } else {
            return [
                'remitos' => $remitos->take($request->get('limit', null)),
                'total' => $remitos->count(),
            ];
        }
    }

    public static function store($request)
    {
        $atributos = $request;

        // ALMACENAMIENTO DE REMITO
        $remito = static::crearCompra($atributos);

        // ALMACENAMIENTO DE DETALLES
        $det = static::detallesCompra($request->get('detalles'), $remito);

        $remito->articulos()->attach($det);

        // ACTUALIZA LA CANTIDAD DE LOS INVENTARIOS SI EXISTEN
        static::actualizarInventarios($request->get('detalles'), $remito);

        return $remito->id;
    }

    public static function show($id)
    {
        return static::verCompra($id);
    }

    public static function actualizarInventarios($detalles, $remito)
    {
        foreach ($detalles as $detail) {
            $article = Inventario::where('articulo_id', '=', $detail['articulo_id'])
                ->where('dependencia', null)->get()->first();

            $data = [
                'tipo' => $detail['movimiento'],
                'cantidad' => $detail['cantidad'],
                'cantidadlitros' => $detail['cantidad'] * $detail['litros'],
            ];

            // CREA INVENTARIOS SI NO EXISTEN
            if ($article != null) {
                $article->cantidad += $detail['cantidad'];
                $article->cantidadlitros += $detail['cantidad'] * $detail['litros'];
                $article->save();
                $data['inventario'] = $article;
            } else {
                $att['articulo_id'] = $detail['articulo_id'];
                $att['supplier_id'] = $remito->supplier_id;
                $att['cantidad'] = $detail['cantidad'];
                $att['cantidadlitros'] = $detail['cantidad'] * $detail['litros'];
                $arti = Inventario::create($att);
                $data['inventario'] = $arti;
            }

            MovimientosTrait::crearMovimiento($data['tipo'], $data['cantidad'], $data['cantidadlitros'], $data['inventario'], $remito);
        }
    }

    public static function detallesCompra($details, $remito)
    {
        foreach ($details as $detail) {
            $articulo = Articulo::find($detail['articulo_id'] * 1);
            $detalles = array(
                'codarticulo' => $detail['codarticulo'],
                'articulo' => $detail['articulo'],
                'cantidad' => $detail['cantidad'] * 1,
                'cantidadlitros' => $detail['totalLitros'],
                'medida' => $detail['medida'],
                'litros' => $detail['litros'],
                'preciounitario' => $detail['precio'] * 1,
                'subtotal' => $detail['subtotal'] * 1,
                'articulo_id' => $detail['articulo_id'],
                'compra_id' => $remito->id,
                'created_at' => now(),
            );
            // ACTUALIZA PRECIO DEL ARTICULO
            if ($detail['update']) {
                $articulo->precio = $detail['precio'];
                $articulo->update();
            }
            $det[] = $detalles;
        }
        return $det;
    }

    public static function crearCompra($atributos)
    {
        return Compra::create([
            "ptoventa" => $atributos['puntoventa'] * 1,
            "numcompra" => $atributos['numcompra'] * 1,
            "fecha" => now()->format('Ymd'),
            'fechaCompra' => $atributos['fecha'],
            "recargo" => $atributos['recargo'] * 1,
            "bonificacion" => $atributos['bonificacion'] * 1,
            "subtotal" => $atributos['subtotal'],
            "total" => $atributos['total'],
            'observaciones' => $atributos['observaciones'],
            "supplier_id" => $atributos['supplier_id'],
            "user_id" => auth()->user()->id,
        ]);
    }

    public static function verCompra($id)
    {
        $configuracion = ConfiguracionTrait::configuracion();
        $remito = Compra::find($id);
        $fecha = new Carbon($remito->fecha);
        $remito->fecha = $fecha->format('d-m-Y');
        $proveedor = Supplier::withTrashed()->find($remito->supplier_id);
        $detalles = DB::table('articulo_compra')->where('compra_id', $remito->id)->get();

        return [
            'configuracion' => $configuracion,
            'remito' => $remito,
            'detalles' => $detalles,
            'proveedor' => $proveedor
        ];
    }
}
