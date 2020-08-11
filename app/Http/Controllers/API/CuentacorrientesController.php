<?php

namespace App\Http\Controllers\API;

use App\Cuentacorriente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Traits\CuentasCorrientesTrait;

class CuentacorrientesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:airlock');
        $this->middleware('scope:cuentascorrientes-index')->only('pagar');
    }

    // PAGOS PARCIALES O TOTALES 
    public function pagar(Request $request)
    {
        try {
            DB::transaction(function() use($request){
                return CuentasCorrientesTrait::pagar($request);
            });
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function recargar(Request $request)
    {
        $cuenta = Cuentacorriente::find($request->get('id'));
        $cuenta->saldo = $request->get('nuevoSaldo');
        $cuenta->update();
    }
}
