<?php

namespace App\Http\Controllers\API;

use App\User;
use App\Venta;
use App\Cliente;
use App\Articulo;
use Carbon\Carbon;
use App\Inventario;
use App\Movimiento;
use App\Cuentacorriente;
use App\Distributor;
use App\Movimientocuenta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class VentasController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:airlock');

        // $this->middleware('scope:ventas-index')->only('index');
        // $this->middleware('scope:ventas-show')->only('show');
        // $this->middleware('scope:ventas-store')->only('store');
        // $this->middleware('scope:ventas-destroy')->only('destroy');
    }

    public function index(Request $request)
    {
        if (auth()->user()->role_id <> 3) {
            if ($request->fec) {
                $fec = $request->fec;
                $facs = Venta::whereDate('created_at', $fec)
                    ->orderBy('id', 'DESC')
                    ->buscar($request)->get();
            } else {
                $facs = Venta::orderBy('id', 'DESC')
                    ->buscar($request)->get();
            }
        } else {
            if ($request->fec) {
                $fec = $request->fec;
                $facs = Venta::whereDate('created_at', $fec)
                    ->orderBy('id', 'DESC')
                    ->where('user_id', auth()->user()->id)
                    ->buscar($request)->get();
            } else {
                $facs = Venta::orderBy('id', 'DESC')
                    ->where('user_id', auth()->user()->id)
                    ->buscar($request)->get();
            }
        }

        $facturas = collect();

        foreach ($facs as $fac) {
            $fecha = new Carbon($fac->fecha);
            $fac->fecha = $fecha->format('d-m-Y');
            $fac->cliente;
            $fac = collect($fac);
            $facturas->push($fac);
        }
        $eliminadas = Venta::onlyTrashed()->get();

        foreach ($eliminadas as $eliminada) {
            $dateFac = new Carbon($eliminada->fecha);
            $eliminada->fecha = $dateFac->format('d-m-Y');
            $eliminada->cliente;
        }

        if ($facturas->count() <= $request->get('limit')) {
            return [
                'facturas' => $facturas,
                'ultima' => $facturas->first(),
                'total' => $facturas->count(),
                'eliminadas' => $eliminadas
            ];
        } else {
            return [
                'facturas' => $facturas->take($request->get('limit', null)),
                'ultima' => $facturas->first(),
                'total' => $facturas->count(),
                'eliminadas' => $eliminadas
            ];
        }
    }

    public function store(Request $request)
    {
        // return $request;
        $atributos = $request;
        $cliente = Cliente::find($atributos['cliente_id']);
        $atributos['cuit'] = $cliente->documentounico;
        $atributos['condicionventa'] = $atributos['condicionventa'];
        if ($atributos['condicionventa'] == 'CONTADO') {
            $atributos['pagada'] = true;
            $atributos['subtotalPesos'] = ($atributos['subtotal'] * 1) * ($atributos['cotizacion'] * 1);
        } else {
            $atributos['pagada'] = false;
            $atributos['subtotalPesos'] = null;
            $atributos['totalPesos'] = null;
            $atributos['cotizacion'] = null;
            $atributos['fechaCotizacion'] = null;
        }
        // if ($atributos['tipo'] != 'REMITO X') {
        //     $atributos['codcomprobante'] = 11;
        //     $atributos['letracomprobante'] = 'C';
        // } else {
        //     $solicitarCAE = false;
        //     $atributos['codcomprobante'] = null;
        //     $atributos['letracomprobante'] = 'X';
        // }

        $jsonString = file_get_contents(base_path('config.json'));
        $config = json_decode($jsonString, true);

        // ALMACENAMIENTO DE FACTURA
        $factura = Venta::create([
            "cuit" => $atributos['cuit'], //cliente
            "tipocomprobante" => $atributos['tipoComprobante'],
            "numventa" => $atributos['numventa'],
            "comprobanteadherido" => $atributos['comprobanteadherido'],
            "fecha" => now()->format('Ymd'),
            'observaciones' => $atributos['observaciones'],
            "bonificacion" => $atributos['bonificacion'] * 1,
            "recargo" => $atributos['recargo'] * 1,
            "pagada" => $atributos['pagada'],
            "condicionventa" => $atributos['condicionventa'],
            "subtotal" => $atributos['subtotal'],
            "total" => $atributos['total'],
            "subtotalPesos" => $atributos['subtotalPesos'],
            "totalPesos" => $atributos['totalPesos'],
            'cotizacion' => $atributos['cotizacion'],
            'fechaCotizacion' => $atributos['fechaCotizacion'],
            "cliente_id" => $atributos['cliente_id'],
            "user_id" => auth()->user()->id,
        ]);
        // ALMACENAMIENTO DE DETALLES
        foreach ($request->get('detalles') as $detail) {
            if ($atributos['condicionventa'] == 'CUENTA CORRIENTE') {
                $detail['subtotalPesos'] = null;
                $detail['cotizacion'] = null;
                $detail['fechaCotizacion'] = null;
            }
            $detalles = array(
                'codarticulo' => $detail['codarticulo'],
                'articulo' => $detail['articulo'],
                'cantidad' => $detail['cantidad'],
                'cantidadLitros' => $detail['cantidadLitros'],
                'medida' => $detail['medida'],
                // 'bonificacion' => 0,
                // 'alicuota' => 0,
                'preciounitario' => $detail['precio'],
                'subtotalPesos' => $detail['subtotalPesos'],
                'subtotal' => $detail['subtotalDolares'],
                'cotizacion' => $detail['cotizacion'],
                'fechaCotizacion' => $detail['fechaCotizacion'],
                'articulo_id' => $detail['id'],
                'venta_id' => $factura->id,
                'created_at' => now()->format('Ymd'),
            );
            $det[] = $detalles;
        }
        $factura->articulos()->attach($det);
        // CREACION DE CUENTA CORRIENTE
        if (($factura->pagada == false) && ($cliente->id != 1)) {
            $cuenta = Cuentacorriente::create([
                'venta_id' => $factura->id,
                'importe' => $factura->total,
                'saldo' => $factura->total,
                'alta' => now(),
                'estado' => 'ACTIVA'
            ]);
            Movimientocuenta::create([
                'ctacte_id' => $cuenta->id,
                'tipo' => 'ALTA',
                'fecha' => now(),
                'user_id' => auth()->user()->id,
                'importe' => $cuenta->importe
            ]);
        }
        $aux = collect($det);
        // DESCUENTA LOS INVENTARIOS
        for ($i = 0; $i < count($aux); $i++) {
            $cond = true;
            $res = $aux[$i]['cantidad'];
            while ($cond) {
                $article = Inventario::where('cantidad', '>', 0)
                    ->where('articulo_id', $aux[$i]['articulo_id'])->get();
                if ($article[0]->cantidad < $res) {
                    $res = $aux[$i]['cantidad'] - $article[0]->cantidad;
                    Movimiento::create([
                        'inventario_id' => $article[0]->id,
                        'tipo' => 'VENTA',
                        'cantidadlitros' => $aux[$i]['cantidad'] * $aux[$i]['cantidadLitros'],
                        'cantidad' => $article[0]->cantidad,
                        'fecha' => now(),
                        'numcomprobante' => $factura->id,
                        'user_id' => auth()->user()->id
                    ]);
                    $article[0]->cantidad = 0;
                    $article[0]->cantidadlitros = 0;
                    if ($res <= 0) {
                        $cond = false;
                    }
                } else {
                    $article[0]->cantidad = $article[0]->cantidad - $res;
                    $article[0]->cantidadlitros = $article[0]->cantidadlitros - $aux[$i]['cantidadLitros'];
                    $cond = false;
                    Movimiento::create([
                        'inventario_id' => $article[0]->id,
                        'tipo' => 'VENTA',
                        'cantidad' => $res,
                        'cantidadlitros' => $aux[$i]['cantidad'] * $aux[$i]['cantidadLitros'],
                        'fecha' => now(),
                        'numcomprobante' => $factura->id,
                        'user_id' => auth()->user()->id
                    ]);
                }
                $article[0]->save();
                unset($article);
            }
        }
        return $factura->id;
    }

    public function update(Request $request, $id)
    {
        $factura = Venta::find($id);
        if ($request->get('pagada')) {
            $factura->pagada = $request->get('pagada');
        }
        return $factura->id;
    }

    public function facturar(Request $request)
    {
        $factura = collect();
        if (array_key_exists('cliente', $request->id)) {
            $cliente = Cliente::findOrFail($request->id['cliente']);
            $factura->put('cliente', $cliente);
        }
        $ids = $request->id['seleccionadas'];

        $details = collect();
        $arregloIds = array();
        $numeros = "";
        $sub = 0;
        $tot = 0;
        for ($i = 0; $i < count($ids); $i++) {
            $aux = Venta::find($ids[$i]);
            array_push($arregloIds, $ids[$i]);
            foreach ($aux->articulos as $det) {
                $details->push($det);
            }
            $sub += $aux->subtotal;
            $tot += $aux->total;
            if ($i == 0) {
                $numeros = $numeros  . $aux->id;
            } elseif ($i < count($ids)) {
                $numeros = $numeros . ', ' . $aux->id;
            }
        }
        $factura->put('detalles', $details);
        $factura->put('subtotal', $sub);
        $factura->put('total', $tot);
        $factura->put('ids', $numeros);
        $factura->put('ventas', $arregloIds);

        return $factura;
    }

    // ANULACION DE FACTURA
    public function destroy($id)
    {
        $factura = Venta::findOrFail($id);
        $detalles = collect($factura->articulos);
        $pivot = collect();
        $inventarios = collect();
        $factura->delete();
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
            $aux = $aux->where('numcomprobante', $factura->id);
            foreach ($aux as $a) {
                $inventario = $a->inventario;
                $inventario->cantidad = $inventario->cantidad + $a->cantidad;
                $inventario->save();
                Movimiento::create([
                    'inventario_id' => $inventario->id,
                    'tipo' => 'ANULACION',
                    'cantidad' => $a->cantidad,
                    'cantidadlitros' => $a->cantidadlitros,
                    'fecha' => now(),
                    'numcomprobante' => $factura->id,
                    'user_id' => auth()->user()->id
                ]);
            }
        }
        return ['msg' => 'Factura Anulada'];
    }

    public function show($id)
    {
        $jsonString = file_get_contents(base_path('config.json'));
        $configuracion = json_decode($jsonString, true);
        $factura = Venta::find($id);
        $fecha = new Carbon($factura->fecha);
        $factura->fecha = $fecha->format('d-m-Y');
        $cliente = Cliente::find($factura->cliente_id);
        $detalles = DB::table('articulo_venta')->where('venta_id', $factura->id)->get();

        return [
            'configuracion' => $configuracion,
            'factura' => $factura,
            'detalles' => $detalles,
            'cliente' => $cliente
        ];
    }

    public function restaurar($id)
    {
        Venta::withTrashed()->find($id)->restore();
        return Venta::find($id);
    }
}
