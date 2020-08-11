<?php

namespace App\Traits;

use App\Cliente;
use App\Entrega;
use App\Factura;
use App\Traits\VentasTrait;
use App\Traits\ComprasTrait;
use App\Traits\RecibosTrait;
use App\Traits\DevolucionesTrait;
use App\Traits\PresupuestosTrait;
use Illuminate\Support\Facades\DB;
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

    public static function devoluciones($id)
    {
        $res = DevolucionesTrait::verDevolucion($id);
        $configuracion = $res['configuracion'];
        $devolucion = $res['devolucion'];
        $detalles = $res['detalles'];
        $dependencia = $res['dependencia'];
        $pdf = app('dompdf.wrapper')->loadView('remitoDevolucionPDF', compact('configuracion', 'devolucion', 'detalles', 'dependencia'))->setPaper('A4');
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

    public static function resumenCuenta($request)
    {
        $resumen = ClientesTrait::resumenCuenta($request);
        $configuracion = ConfiguracionTrait::configuracion();
        $cliente = $resumen['cliente'];
        $desde = $resumen['desde'];
        $hasta = $resumen['hasta'];
        $cuentas = $resumen['cuentas'];
        $pagos = $resumen['pagos'];
        $debe = $resumen['debe'];
        $haber = $resumen['haber'];
        $saldoAnterior = $resumen['saldoAnterior'];
        $saldo = $resumen['saldo'];
        $pdf = app('dompdf.wrapper')->loadView('resumenCuentaPDF', compact('configuracion', 'cliente', 'desde', 'hasta', 'cuentas', 'pagos', 'debe', 'haber', 'saldoAnterior', 'saldo'))->setPaper('A4');
        return $pdf->download();
    }

    public static function entregas($id)
    {
        $configuracion = ConfiguracionTrait::configuracion();
        $entrega = Entrega::find($id);
        $cliente = Cliente::withTrashed()->find($entrega->cliente_id);
        $detalles = DB::table('articulo_entrega')->where('entrega_id', $entrega->id)->get();

        $pdf = app('dompdf.wrapper')->loadView('entregasPDF', compact('configuracion', 'entrega', 'detalles', 'cliente'))->setPaper('A4');
        return $pdf->download();
    }

    public static function facturas($id)
    {
        $configuracion = ConfiguracionTrait::configuracion();
        $factura = Factura::find($id);
        $cliente = Cliente::withTrashed()->find($factura->cliente_id);
        $detalles = DB::table('articulo_factura')->where('factura_id', $factura->id)->get();

        $pdf = app('dompdf.wrapper')->loadView('facturasPDF', compact('configuracion', 'factura', 'detalles', 'cliente'))->setPaper('A4');
        return $pdf->download();
    }
}
