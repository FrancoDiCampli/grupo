<?php

namespace App\Http\Controllers\API;

use App\Venta;
use App\Cliente;
use App\Factura;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FacturasController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:airlock');

        // $this->middleware('scope:facturas-index')->only('index');
        // $this->middleware('scope:facturas-show')->only('show');
        // $this->middleware('scope:facturas-store')->only('store');
    }

    public function index(Request $request)
    {
        if (auth()->user()->role_id == 1 || auth()->user()->role_id == 2) {
            $facs = Factura::orderBy('id', 'DESC')
                ->get();
        } else {
            $facs = Factura::orderBy('id', 'DESC')
                ->where('user_id', auth()->user()->id)
                ->get();
        }

        $facturas = collect();

        foreach ($facs as $fac) {
            $fecha = new Carbon($fac->fecha);
            $fac->fecha = $fecha->format('d-m-Y');
            $fac->cliente;
            $fac = collect($fac);
            $facturas->push($fac);
        }

        if ($facturas->count() <= $request->get('limit')) {
            return [
                'facturas' => $facturas,
                'ultima' => $facturas->first(),
                'total' => $facturas->count()
            ];
        } else {
            return [
                'facturas' => $facturas->take($request->get('limit', null)),
                'ultima' => $facturas->first(),
                'total' => $facturas->count()
            ];
        }
    }

    public function store(Request $request)
    {
        $cliente = Cliente::findOrFail($request['cliente_id']);

        $nueva = Factura::create([
            "cuit" => $cliente->documentounico, //cliente
            "tipocomprobante" => $request['tipocomprobante'],
            "numfactura" => $request['numfactura'],
            "comprobanteadherido" => $request['comprobanteadherido'],
            "fecha" => now()->format('Ymd'),
            'observaciones' => $request['observaciones'],
            "bonificacion" => $request['bonificacion'] * 1,
            "recargo" => $request['recargo'] * 1,
            "condicionventa" => $request['condicionventa'],
            "subtotal" => $request['subtotal'],
            "total" => $request['total'],
            "subtotalPesos" => ($request['subtotal'] * 1) * ($request['cotizacion'] * 1),
            "totalPesos" => $request['totalPesos'],
            'cotizacion' => $request['cotizacion'],
            'fechaCotizacion' => $request['fechaCotizacion'],
            "cliente_id" => $request['cliente_id'],
            "user_id" => auth()->user()->id,
        ]);

        $nueva->ventas()->attach($request->ventas);

        foreach ($request['ventas'] as $ven) {
            $venta = Venta::findOrFail($ven);
            $venta->numfactura = $nueva->id;
            $venta->save();
        }

        return ['msg' => 'factura creada'];
    }

    public function show($id)
    {
        $jsonString = file_get_contents(base_path('config.json'));
        $configuracion = json_decode($jsonString, true);
        $factura = Factura::find($id);
        $detalles = collect();
        foreach ($factura->ventas as $venta) {
            foreach ($venta->articulos as $det) {
                $detalles->push($det['pivot']);
            }
        }
        $fecha = new Carbon($factura->fecha);
        $factura->fecha = $fecha->format('d-m-Y');
        $cliente = Cliente::find($factura->cliente_id);

        return [
            'configuracion' => $configuracion,
            'factura' => $factura,
            'detalles' => $detalles,
            'cliente' => $cliente
        ];
    }
}
