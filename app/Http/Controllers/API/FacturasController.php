<?php

namespace App\Http\Controllers\API;

use App\Venta;
use App\Cliente;
use App\Factura;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\FacturasTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

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
        try {
            DB::transaction(function () use ($request) {
                return FacturasTrait::store($request);
            });
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function show($id)
    {
        return FacturasTrait::show($id);
    }

    public function destroy($id)
    {
        return FacturasTrait::delete($id);
    }
}
