<?php

namespace App\Http\Controllers\API;

use App\Venta;
use App\Traits\VentasTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class VentasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:airlock');

        $this->middleware('scope:remitos-index')->only('index');
        $this->middleware('scope:remitos-show')->only('show');
        $this->middleware('scope:remitos-store')->only('store');
        $this->middleware('scope:remitos-destroy')->only('destroy');
        $this->middleware('scope:remitos-facturar')->only('facturar');
    }

    public function index(Request $request)
    {
        return VentasTrait::index($request);
    }

    public function store(Request $request)
    {
        return VentasTrait::store($request);
    }

    public function facturar(Request $request)
    {
        return VentasTrait::facturar($request);
    }

    public function show($id)
    {
        return VentasTrait::show($id);
    }

    public function destroy($id)
    {
        return VentasTrait::delete($id);
    }

    public function restaurar($id)
    {
        Venta::withTrashed()->find($id)->restore();
        return Venta::find($id);
    }
}
