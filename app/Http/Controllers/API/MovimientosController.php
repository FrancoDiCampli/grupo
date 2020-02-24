<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Traits\MovimientosTrait;
use App\Http\Controllers\Controller;

class MovimientosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:airlock');

        $this->middleware('scope:movimientos-index')->only('index');
    }

    public function index(Request $request)
    {
        $moves = MovimientosTrait::movimientos($request);

        $cuentas = MovimientosTrait::movimientosCuenta($request);

        return ['articulos' => $moves, 'cuentas' => $cuentas];
    }
}
