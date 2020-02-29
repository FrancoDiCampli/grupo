<?php

namespace App\Traits;

use App\Recibo;
use Carbon\Carbon;
use App\Traits\FormasDePagoTrait;
use App\Traits\ConfiguracionTrait;

trait RecibosTrait
{
    public static function verRecibo($id)
    {
        $configuracion = ConfiguracionTrait::configuracion();
        $recibo = Recibo::find($id);
        $fecha = new Carbon($recibo->fecha);
        $recibo->fecha = $fecha->format('d-m-Y');
        $pagos = $recibo->pagos;
        $cliente = null;
        $cotizacion = null;
        $fechaCotizacion = null;
        foreach ($pagos as $pay) {
            $fecha = new Carbon($pay->fecha);
            $pay->fecha = $fecha->format('d-m-Y');
            $pay['cuenta'] = $pay->ctacte;
            $pay['factura'] = $pay->ctacte->factura;
            $pays = FormasDePagoTrait::verPagos($pay);
            $pay['forma'] = $pays->flatten();
            $cliente = $pay->ctacte->factura->cliente;
            $cotizacion = $pay['forma'][1]['cotizacion'];
            $fechaCotizacion = $pay['forma'][1]['fecha_cotizacion'];
        }
        $recibo['cotizacion'] = $cotizacion;
        $recibo['fecha_cotizacion'] = $fechaCotizacion;

        return [
            'configuracion' => $configuracion,
            'recibo' => $recibo,
            'cliente' => $cliente,
            'pagos' => $pagos
        ];
    }
}
