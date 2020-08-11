<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Traits\PdfTrait;
use Illuminate\Http\Request;

class PdfController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:airlock');
    }

    public function ventasPDF($id)
    {
        return PdfTrait::ventas($id);
    }

    public function presupuestosPDF($id)
    {
        return PdfTrait::presupuestos($id);
    }

    public function comprasPDF($id)
    {
        return PdfTrait::compras($id);
    }

    public function consignacionesPDF($id)
    {
        return PdfTrait::consignaciones($id);
    }

    public function devolucionesPDF($id)
    {
        return PdfTrait::devoluciones($id);
    }

    public function recibosPDF($id)
    {
        return PdfTrait::recibos($id);
    }

    public function resumenCuentaPDF(Request $request)
    {
        return PdfTrait::resumenCuenta($request);
    }

    public function entregasPDF($id)
    {
        return PdfTrait::entregas($id);
    }

    public function facturasPDF($id)
    {
        return PdfTrait::facturas($id);
    }
}
