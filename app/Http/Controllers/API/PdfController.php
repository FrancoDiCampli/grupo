<?php

namespace App\Http\Controllers\API;

use App\Venta;
use App\Compra;
use App\Recibo;
use App\Cliente;
use App\Factura;
use App\Supplier;
use Carbon\Carbon;
use App\Presupuesto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class PdfController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:airlock');
    }

    public function ventasPDF($id)
    {
        $jsonString = file_get_contents(base_path('config.json'));
        $configuracion = json_decode($jsonString, true);
        $factura = Venta::find($id);
        $fecha = new Carbon($factura->fecha);
        $factura->fecha = $fecha->format('d-m-Y');
        $cliente = Cliente::find($factura->cliente_id);
        $detalles = DB::table('articulo_venta')->where('venta_id', $factura->id)->get();
        $pdf = app('dompdf.wrapper')->loadView('remitosPDF', compact('configuracion', 'factura', 'detalles', 'cliente'))->setPaper('A4');
        return $pdf->download();
    }

    public function presupuestosPDF($id)
    {
        $jsonString = file_get_contents(base_path('config.json'));
        $configuracion = json_decode($jsonString, true);
        $presupuesto = Presupuesto::find($id);
        $fecha = new Carbon($presupuesto->fecha);
        $presupuesto->fecha = $fecha->format('d-m-Y');
        $vencimiento = new Carbon($presupuesto->vencimiento);
        $presupuesto->vencimiento = $vencimiento->format('d-m-Y');
        $cliente = Cliente::find($presupuesto->cliente_id);
        $detalles = DB::table('articulo_presupuesto')->where('presupuesto_id', $presupuesto->id)->get();
        $pdf = app('dompdf.wrapper')->loadView('presupuestosPDF', compact('configuracion', 'presupuesto', 'detalles', 'cliente'))->setPaper('A4');
        return $pdf->download();
    }

    public function comprasPDF($id)
    {
        $jsonString = file_get_contents(base_path('config.json'));
        $configuracion = json_decode($jsonString, true);
        $remito = Compra::find($id);
        $fecha = new Carbon($remito->fecha);
        $remito->fecha = $fecha->format('d-m-Y');
        $proveedor = Supplier::find($remito->supplier_id);
        $detalles = DB::table('articulo_compra')->where('compra_id', $remito->id)->get();
        $pdf = app('dompdf.wrapper')->loadView('comprasPDF', compact('configuracion', 'remito', 'detalles', 'proveedor'))->setPaper('A4');
        return $pdf->download();
    }

    public function recibosPDF($id)
    {
        $jsonString = file_get_contents(base_path('config.json'));
        $configuracion = json_decode($jsonString, true);
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
            $pay['forma'] = $pay->forma();
            $cliente = $pay->ctacte->factura->cliente;
            $cotizacion = $pay['forma'][1]['cotizacion'];
            $fechaCotizacion = $pay['forma'][1]['fecha_cotizacion'];
        }
        $recibo['cotizacion'] = $cotizacion;
        $recibo['fecha_cotizacion'] = $fechaCotizacion;
        $pdf = app('dompdf.wrapper')->loadView('recibosPDF', compact('configuracion', 'recibo', 'pagos', 'cliente'))->setPaper('A4');
        return $pdf->download();
    }
}
