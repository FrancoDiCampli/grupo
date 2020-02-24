<?php

namespace App\Http\Controllers\API;

use App\Venta;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\VentasTrait;

class VentasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:airlock');

        $this->middleware('scope:ventas-index')->only('index');
        $this->middleware('scope:ventas-show')->only('show');
        $this->middleware('scope:ventas-store')->only('store');
        $this->middleware('scope:ventas-destroy')->only('destroy');
    }

    public function index(Request $request)
    {
        return VentasTrait::index($request);
    }

    public function store(Request $request)
    {
        return VentasTrait::store($request);
    }

    public function update(Request $request, $id)
    {
        $factura = Venta::find($id);
        if ($request->get('pagada')) {
            $factura->pagada = $request->get('pagada');
        }
        return $factura->id;
    }

    public function facturar(Request $request)
    {
        return VentasTrait::facturar($request);
    }

    // ANULACION DE FACTURA
    public function destroy($id)
    {
        return VentasTrait::anular($id);
    }

    public function show($id)
    {
        return VentasTrait::show($id);
    }

    public function restaurar($id)
    {
        Venta::withTrashed()->find($id)->restore();
        return Venta::find($id);
    }
}
