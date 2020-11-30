<?php

namespace App\Http\Controllers\API;

use App\Traits\ChequesTrait;
use Illuminate\Http\Request;
use App\Exports\AllVentasExport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Traits\EstadisticasVentasTrait;
use App\Traits\EstadisticasComprasTrait;
use App\Traits\EstadisticasDetallesVentasTrait;
use App\Traits\EstadisticasDetallesComprasTrait;

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

    public function ventasExcel()
    {
        $export = new AllVentasExport();
        return Excel::download($export, 'reportesVentas.xlsx');
    }
}
