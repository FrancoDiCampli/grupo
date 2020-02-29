<?php

namespace App\Http\Controllers\API;

use App\Venta;
use App\Cliente;
use App\Factura;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\FacturasTrait;

class FacturasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:airlock');

        $this->middleware('scope:facturas-index')->only('index');
        $this->middleware('scope:facturas-show')->only('show');
        $this->middleware('scope:facturas-store')->only('store');
    }

    public function index(Request $request)
    {
        return FacturasTrait::index($request);
    }

    public function store(Request $request)
    {
        return FacturasTrait::store($request);
    }

    public function show($id)
    {
        return FacturasTrait::show($id);
    }
}
