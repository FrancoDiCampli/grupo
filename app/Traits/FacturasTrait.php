<?php

namespace App\Traits;

use App\Articulo;
use App\Venta;
use App\Cliente;
use App\Factura;
use Carbon\Carbon;
use App\Traits\CuentasCorrientesTrait;

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
            $ventas = $fac->ventas->each->cuenta;
            $pagos = false;
            foreach ($ventas as $item) {
                if ($item->cuenta) {
                    $payments = $item->cuenta->pagos;
                    if (count($payments) > 0) {
                        $pagos = true;
                    }
                }
            }

            $fecha = new Carbon($fac->fecha);
            $fac->fecha = $fecha->format('d-m-Y');
            $fac->cliente = Cliente::withTrashed()->find($fac->cliente_id);
            $fac['pagos'] = $pagos;
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

        $factura = Factura::create([
            "cuit" => $cliente->documentounico, //cliente
            "tipocomprobante" => $request['tipocomprobante'],
            "numfactura" => $request['numfactura'],
            "comprobanteadherido" => $request['comprobanteadherido'],
            "fecha" => $request['fecha'],
            'observaciones' => $request['observaciones'],
            "bonificacion" => $request['bonificacion'] * 1,
            "recargo" => $request['recargo'] * 1,
            "condicionventa" => $request['condicionventa'],
            "subtotal" => $request['subtotal'],
            "iva" => $request['valorAgregado'],
            "total" => $request['total'],
            "subtotalPesos" => ($request['subtotal'] * 1) * ($request['cotizacion'] * 1),
            "totalPesos" => $request['totalPesos'],
            'cotizacion' => $request['cotizacion'],
            'fechaCotizacion' => $request['fechaCotizacion'],
            "cliente_id" => $request['cliente_id'],
            "user_id" => auth()->user()->id,
        ]);

        // $det = static::detallesFactura($request->detalles, $factura);

        foreach ($request->detalles as $item) {
            $ventas[] = $item['venta_id'];
        }

        // $factura->articulos()->attach($det);

        // $factura->ventas()->attach($ventas);

        // foreach ($request['ventas'] as $ven) {
        //     $venta = Venta::findOrFail($ven);
        //     $venta->numfactura = $nueva->id;
        //     $venta->save();
        // }

        // OJO ACA
        return CuentasCorrientesTrait::aplicarIVA($cliente, $request->valorAgregado);

        return ['msg' => 'factura creada'];
    }

    public static function detallesFactura($details, $factura)
    {
        foreach ($details as $detail) {
            $detalles = array(
                'codarticulo' => $detail['codarticulo'],
                'articulo' => $detail['articulo'],
                'cantidad' => $detail['cantidad'],
                'cantidadLitros' => $detail['cantidadLitros'],
                'medida' => $detail['medida'],
                'preciounitario' => $detail['preciounitario'],
                'bonificacion' => $detail['bonificacion'] ?? null,
                'recargo' => $detail['recargo'] ?? null,
                'subtotalPesos' => $detail['subtotalPesos'],
                'subtotal' => $detail['subtotal'],
                'cotizacion' => $detail['cotizacion'],
                'fechaCotizacion' => $detail['fechaCotizacion'],
                'articulo_id' => $detail['articulo_id'],
                'factura_id' => $factura->id,
                'created_at' => now(),
                'updated_at' => now(),
                'articulo_venta_id' => $detail['id']
            );
            $det[] = $detalles;
        }

        return $det;
    }

    public static function show($id)
    {
        $configuracion = ConfiguracionTrait::configuracion();
        $factura = Factura::find($id);
        $detalles = collect();
        foreach ($factura->articulos as $det) {
            $detalles->push($det['pivot']);
        }
        $fecha = new Carbon($factura->fecha);
        $factura->fecha = $fecha->format('d-m-Y');
        $fechaCot = new Carbon($factura->fechaCotizacion);
        $factura->fechaCotizacion = $fechaCot->format('d-m-Y');
        $cliente = Cliente::withTrashed()->find($factura->cliente_id);

        return [
            'configuracion' => $configuracion,
            'factura' => $factura,
            'detalles' => $detalles,
            'cliente' => $cliente
        ];
    }

    public static function delete($id)
    {
        $factura = Factura::find($id);

        foreach ($factura->ventas as $item) {
            if ($item->cuenta) {
                $pagos = $item->cuenta->pagos;
                count($pagos) > 0 ? $auxiliar = false : $auxiliar = true;
            }
        }

        if ($auxiliar) {
            $factura->articulos()->detach();
            $factura->delete();
            return ['msg' => 'Factura eliminada'];
        } else return ['msg' => 'No se pudo elimnar la factura'];
    }
}
