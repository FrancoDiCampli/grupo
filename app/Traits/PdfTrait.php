<?php

namespace App\Traits;

use App\Traits\VentasTrait;
use App\Traits\ComprasTrait;
use App\Traits\RecibosTrait;
use App\Traits\PresupuestosTrait;
use App\Traits\ConsignacionesTrait;

trait PdfTrait
{
    public static function ventas($id)
    {
        $res = VentasTrait::verVenta($id);
        $configuracion = $res['configuracion'];
        $factura = $res['factura'];
        $detalles = $res['detalles'];
        $cliente = $res['cliente'];
        $pdf = app('dompdf.wrapper')->loadView('remitosPDF', compact('configuracion', 'factura', 'detalles', 'cliente'))->setPaper('A4');
        return $pdf->download();
    }

    public static function presupuestos($id)
    {
        $res = PresupuestosTrait::verPresupuesto($id);
        $configuracion = $res['configuracion'];
        $presupuesto = $res['presupuesto'];
        $detalles = $res['detalles'];
        $cliente = $res['cliente'];
        $pdf = app('dompdf.wrapper')->loadView('presupuestosPDF', compact('configuracion', 'presupuesto', 'detalles', 'cliente'))->setPaper('A4');
        return $pdf->download();
    }

    public static function compras($id)
    {
        $res = ComprasTrait::verCompra($id);
        $configuracion = $res['configuracion'];
        $remito = $res['remito'];
        $detalles = $res['detalles'];
        $proveedor = $res['proveedor'];
        $pdf = app('dompdf.wrapper')->loadView('comprasPDF', compact('configuracion', 'remito', 'detalles', 'proveedor'))->setPaper('A4');
        return $pdf->download();
    }

    public static function consignaciones($id)
    {
        $res = ConsignacionesTrait::verConsignacion($id);
        $configuracion = $res['configuracion'];
        $consignacion = $res['consignacion'];
        $detalles = $res['detalles'];
        $dependencia = $res['dependencia'];
        $pdf = app('dompdf.wrapper')->loadView('remitoConsignacionPDF', compact('configuracion', 'consignacion', 'detalles', 'dependencia'))->setPaper('A4');
        return $pdf->download();
    }

    public static function recibos($id)
    {
        $res = RecibosTrait::verRecibo($id);
        $configuracion = $res['configuracion'];
        $recibo = $res['recibo'];
        $pagos = $res['pagos'];
        $cliente = $res['cliente'];
        $pdf = app('dompdf.wrapper')->loadView('recibosPDF', compact('configuracion', 'recibo', 'pagos', 'cliente'))->setPaper('A4');
        return $pdf->download();
    }
}
