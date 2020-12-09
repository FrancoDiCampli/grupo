<?php

namespace App\Http\Controllers\API;

use App\Exports\AllArticulosExport;
use App\Exports\AllComprasExport;
use App\Traits\ChequesTrait;
use Illuminate\Http\Request;
use App\Exports\AllVentasExport;
use App\Exports\OnlyLitrosClienteExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Traits\EstadisticasVentasTrait;
use App\Traits\EstadisticasComprasTrait;
use App\Traits\EstadisticasDetallesVentasTrait;
use App\Traits\EstadisticasDetallesComprasTrait;
use App\Traits\EstadisticasLitrosClienteTrait;

class EstadisticasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:airlock');

        $this->middleware('scope:reportes');
    }

    public function chequeCobrado($id)
    {
        return ChequesTrait::chequeCobrado($id);
    }

    public function cartera(Request $request)
    {
        return ChequesTrait::cartera($request);
    }

    public function ventas(Request $request)
    {
        return EstadisticasVentasTrait::ventas($request);
    }

    public function compras(Request $request)
    {
        return EstadisticasComprasTrait::compras($request);
    }

    public function detallesVentas(Request $request)
    {
        return EstadisticasDetallesVentasTrait::detallesVentas($request);
    }

    public function detallesCompras(Request $request)
    {
        return EstadisticasDetallesComprasTrait::detallesCompras($request);
    }

    public function ventasExcel(Request $request)
    {
        $export = new AllVentasExport($request->desde, $request->hasta);
        return Excel::download($export, 'reportesVentas.xlsx');
    }

    public function articulosExcel(Request $request)
    {
        $export = new AllArticulosExport($request->desde, $request->hasta);
        return Excel::download($export, 'reportesArticulos.xlsx');
    }

    public function comprasExcel(Request $request)
    {
        $export = new AllComprasExport($request->desde, $request->hasta);
        return Excel::download($export, 'reportesCompras.xlsx');
    }

    public function litrosCliente(Request $request)
    {
        $export = new OnlyLitrosClienteExport($request->desde, $request->hasta, $request->id);
        return Excel::download($export, 'reportesLitrosCliente.xlsx');
    }

    public function ventasClientesArticulos(Request $request)
    {
        return EstadisticasLitrosClienteTrait::litrosCliente($request);
    }
}
