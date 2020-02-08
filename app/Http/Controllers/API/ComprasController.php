<?php

namespace App\Http\Controllers\API;

use App\Compra;
use App\Articulo;
use App\Supplier;
use Carbon\Carbon;
use App\Inventario;
use App\Movimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ComprasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:airlock');

        $this->middleware('scope:compras-index')->only('index');
        $this->middleware('scope:compras-show')->only('show');
        $this->middleware('scope:compras-store')->only('store');
    }

    public function index(Request $request)
    {
        if ($request->fec) {
            $fec = $request->fec;
            $rems = Compra::whereDate('created_at', $fec)->orderBy('id', 'DESC')->with('proveedor')->get();
        } else {
            $rems = Compra::orderBy('id', 'DESC')->with('proveedor')->get();
        }

        $remitos = collect();

        foreach ($rems as $rem) {
            $fecha = new Carbon($rem->fecha);
            $rem->fecha = $fecha->format('d-m-Y');
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

    public function store(Request $request)
    {
        $atributos = $request;

        // $detalle = array();
        // array_push($detalle, $atributos->detalle);

        // $proveedor = Supplier::find($atributos['supplier_id']);

        // ALMACENAMIENTO DE REMITO
        $remito = Compra::create([
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

        // $total = 0;

        // ALMACENAMIENTO DE DETALLES
        foreach ($request->get('detalles') as $detail) {
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
                'lote' => $detail['lote'],
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

        $remito->articulos()->attach($det);

        // ACTUALIZA LA CANTIDAD DE LOS INVENTARIOS SI EXISTEN
        foreach ($request->get('detalles') as $detail) {
            $article = Inventario::where('articulo_id', '=', $detail['articulo_id'])
                ->where('lote', '=', $detail['lote'])->get()->first();

            $data = [
                'inventario_id' => '',
                'tipo' => $detail['movimiento'],
                'cantidad' => $detail['cantidad'],
                'cantidadlitros' => $detail['cantidad'] * $detail['totalLitros'],
                // 'observaciones' => $detail['observaciones'],
                'fecha' => now(),
                'numcomprobante' => $remito->id,
                "user_id" => auth()->user()->id
            ];

            // CREA INVENTARIOS SI NO EXISTEN
            if ($article != null) {
                $article->cantidad = $article->cantidad + $detail['cantidad'];
                $article->cantidadlitros = $article->cantidadlitros + $detail['totalLitros'];
                $article->save();
                $data['inventario_id'] = $article->id;
            } else {
                $att['articulo_id'] = $detail['articulo_id'];
                $att['supplier_id'] = $remito->supplier_id;
                $att['cantidad'] = $detail['cantidad'];
                $att['cantidadlitros'] = $detail['cantidad'] * $detail['totalLitros'];
                $att['negocio_id'] = $detail['negocio_id'];
                $att['lote'] = $detail['lote'];
                $arti = Inventario::create($att);
                $data['inventario_id'] = $arti->id;
            }

            Movimiento::create($data);
        }

        return $remito->id;
    }

    public function show($id)
    {
        // RETORNA LOS DETALLES DE UNA COMPRA
        $jsonString = file_get_contents(base_path('config.json'));
        $configuracion = json_decode($jsonString, true);
        $remito = Compra::find($id);
        $fecha = new Carbon($remito->fecha);
        $remito->fecha = $fecha->format('d-m-Y');
        $proveedor = Supplier::find($remito->supplier_id);
        $detalles = DB::table('articulo_compra')->where('compra_id', $remito->id)->get();

        return [
            'configuracion' => $configuracion,
            'remito' => $remito,
            'detalles' => $detalles,
            'proveedor' => $proveedor
        ];
    }
}
