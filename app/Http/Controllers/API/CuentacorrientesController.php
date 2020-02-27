<?php

namespace App\Http\Controllers\API;

use App\Pago;
use App\Venta;
use App\Recibo;
use App\Cuentacorriente;
use App\Movimientocuenta;
use Illuminate\Http\Request;
use App\Traits\FormasDePagoTrait;
use App\Http\Controllers\Controller;

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
        $request = collect($request);
        $aux = array();
        $total = 0;


        foreach ($request['pagos'] as $res) {
            $cuenta = Cuentacorriente::findOrFail($res['cuenta_id']);
            $cliente = $cuenta->factura->cliente;
            if ($res['pagos'] != null) {
                foreach ($res['pagos'] as $pay) {
                    // PAGO PARCIAL
                    if ($pay['dolares'] < $cuenta->saldo) {
                        $cuenta->saldo = $cuenta->saldo - $pay['dolares'];
                        $cuenta->ultimopago = now()->format('Ymd');
                        $cuenta->update();
                        $total = $total + $pay['dolares'];

                        if (Pago::all()->last() == null) {
                            $jsonString = file_get_contents(base_path('config.json'));
                            $config = json_decode($jsonString, true);
                            $numpago = $config['numpago'] + 1;
                        } else $numpago = Pago::all()->last()->numpago + 1;

                        $nuevoPago = Pago::create([
                            'ctacte_id' => $cuenta->id,
                            'importe' => $pay['dolares'],
                            'fecha' => now()->format('Ymd'),
                            'numpago' => $numpago,
                            'referencia' => FormasDePagoTrait::formaPago($pay, $cliente, $diferencia = null) // ACA ESTA EL PROBLEMA
                        ]);

                        array_push($aux, $nuevoPago->id);
                        $movimiento = Movimientocuenta::create([
                            'ctacte_id' => $cuenta->id,
                            'tipo' => 'PAGO PARCIAL',
                            'fecha' => now(),
                            'importe' => $pay['dolares'],
                            'user_id' => auth()->user()->id
                        ]);
                    } else if ($pay['dolares'] == $cuenta->saldo) {
                        // PAGO TOTAL
                        $cuenta->saldo = 0;
                        $factura = Venta::find($cuenta->venta_id);
                        $cuenta->ultimopago = now()->format('Ymd');
                        $cuenta->estado = 'CANCELADA';
                        $cuenta->update();
                        $factura->pagada = true;
                        $factura->update();
                        $total = $total + $pay['dolares'];

                        if (Pago::all()->last() == null) {
                            $jsonString = file_get_contents(base_path('config.json'));
                            $config = json_decode($jsonString, true);
                            $numpago = $config['numpago'] + 1;
                        } else $numpago = Pago::all()->last()->numpago + 1;

                        $nuevoPago = Pago::create([
                            'ctacte_id' => $cuenta->id,
                            'importe' => $pay['dolares'],
                            'fecha' => now()->format('Ymd'),
                            'numpago' => $numpago,
                            'referencia' => FormasDePagoTrait::formaPago($pay, $cliente, $diferencia = null) // ACA ESTA EL PROBLEMA
                        ]);
                        array_push($aux, $nuevoPago->id);
                        $movimiento = Movimientocuenta::create([
                            'ctacte_id' => $cuenta->id,
                            'tipo' => 'PAGO TOTAL',
                            'fecha' => now(),
                            'importe' => $pay['dolares'],
                            'user_id' => auth()->user()->id
                        ]);
                    } elseif ($pay['dolares'] > $cuenta->saldo) {
                        $diferencia = $pay['dolares'] - $cuenta->saldo;
                        $total = $total + $pay['dolares'];
                        $cuenta->saldo = 0;
                        $factura = Venta::find($cuenta->venta_id);
                        $cuenta->ultimopago = now()->format('Ymd');
                        $cuenta->estado = 'CANCELADA';
                        $cuenta->update();
                        $factura->pagada = true;
                        $factura->update();

                        if (Pago::all()->last() == null) {
                            $jsonString = file_get_contents(base_path('config.json'));
                            $config = json_decode($jsonString, true);
                            $numpago = $config['numpago'] + 1;
                        } else $numpago = Pago::all()->last()->numpago + 1;

                        $nuevoPago = Pago::create([
                            'ctacte_id' => $cuenta->id,
                            'importe' => $pay['dolares'],
                            'fecha' => now()->format('Ymd'),
                            'numpago' => $numpago,
                            'referencia' => FormasDePagoTrait::formaPago($pay, $cliente, $diferencia) // ACA ESTA EL PROBLEMA
                        ]);

                        array_push($aux, $nuevoPago->id);
                        $movimiento = Movimientocuenta::create([
                            'ctacte_id' => $cuenta->id,
                            'tipo' => 'PAGO TOTAL',
                            'fecha' => now(),
                            'importe' => $pay['dolares'],
                            'user_id' => auth()->user()->id
                        ]);
                    }
                }
            }
        }
        // ALMACENAMIENTO DE RECIBO
        if (Recibo::all()->last() == null) {
            $jsonString = file_get_contents(base_path('config.json'));
            $config = json_decode($jsonString, true);
            $numrecibo = $config['numrecibo'] + 1;
        } else $numrecibo = Recibo::all()->last()->numrecibo + 1;
        $recibo = Recibo::create([
            'fecha' => now()->format('Ymd'),
            'total' => $total,
            'numrecibo' => $numrecibo
        ]);
        $recibo->pagos()->attach($aux);
        return $recibo->id;
    }

    public function recargar(Request $request)
    {
        $cuenta = Cuentacorriente::find($request->get('id'));
        $cuenta->saldo = $request->get('nuevoSaldo');
        $cuenta->update();
    }
}
