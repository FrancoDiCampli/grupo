<?php

namespace App\Traits;

use App\Recibo;
use Carbon\Carbon;
use App\Traits\FormasDePagoTrait;
use App\Traits\ConfiguracionTrait;

trait RecibosTrait
{
    public static function crearRecibo($total)
    {
        if (Recibo::all()->last() == null) {
            $config = ConfiguracionTrait::configuracion();
            $numrecibo = $config['numrecibo'] + 1;
        } else $numrecibo = Recibo::all()->last()->numrecibo + 1;

        return  Recibo::create([
            'fecha' => now()->format('Y-m-d'),
            'total' => $total,
            'numrecibo' => $numrecibo
        ]);
    }

    public static function verRecibo($id)
    {
        $configuracion = ConfiguracionTrait::configuracion();
        $recibo = Recibo::find($id);
        // $fecha = new Carbon($recibo->fecha);
        // $recibo->fecha = $fecha->format('d-m-Y');
        $pagos = $recibo->pagos;
        $cliente = null;
        $cotizacion = null;
        $fechaCotizacion = null;
        foreach ($pagos as $pay) {
            // $fecha = new Carbon($pay->fecha);
            // $pay->fecha = $fecha->format('d-m-Y');
            $pay['cuenta'] = $pay->ctacte;
            $pay['factura'] = $pay->ctacte->factura;
            $pays = FormasDePagoTrait::verPagos([$pay]); // el parametro debe ser array
            $pay['forma'] = $pays->flatten();
            $cliente = $pay->ctacte->factura->cliente;
            $cotizacion = $pay['forma'][1]['cotizacion'];
            $fechaCotizacion = $pay['forma'][1]['fecha_cotizacion'];
        }
        $recibo['cotizacion'] = $cotizacion;
        $fechaCot = new Carbon($fechaCotizacion);
        // $recibo['fecha_cotizacion'] = $fechaCot->format('d-m-Y');
        $recibo['fecha_cotizacion'] = $fechaCot;

        return [
            'configuracion' => $configuracion,
            'recibo' => $recibo,
            'cliente' => $cliente,
            'pagos' => $pagos
        ];
    }
}
