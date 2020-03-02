<?php

namespace App\Traits;

use App\Venta;
use App\Cliente;
use App\Factura;
use Carbon\Carbon;

trait FacturasTrait
{
    public static function index($request)
    {
        if (auth()->user()->role->role == 'superAdmin' || auth()->user()->role->role == 'administrador') {
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
            $fac->cliente = Cliente::withTrashed()->find($fac->cliente_id);
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

    public static function store($request)
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

    public static function show($id)
    {
        $configuracion = ConfiguracionTrait::configuracion();
        $factura = Factura::find($id);
        $detalles = collect();
        foreach ($factura->ventas as $venta) {
            foreach ($venta->articulos as $det) {
                $detalles->push($det['pivot']);
            }
        }
        $fecha = new Carbon($factura->fecha);
        $factura->fecha = $fecha->format('d-m-Y');
        $cliente = Cliente::withTrashed()->find($factura->cliente_id);

        return [
            'configuracion' => $configuracion,
            'factura' => $factura,
            'detalles' => $detalles,
            'cliente' => $cliente
        ];
    }
}
