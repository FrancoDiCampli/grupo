<?php

namespace App\Traits;

use App\Pago;
use App\Venta;
use App\Recibo;
use App\Cuentacorriente;
use App\Movimientocuenta;
use App\Traits\FormasDePagoTrait;
use App\Traits\ConfiguracionTrait;

trait CuentasCorrientesTrait
{
    public static function pagar($request)
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

                        $nuevoPago = Pago::create([
                            'ctacte_id' => $cuenta->id,
                            'importe' => $pay['dolares'],
                            'fecha' => now()->format('Ymd'),
                            'numpago' => static::nroPago(),
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

                        $nuevoPago = Pago::create([
                            'ctacte_id' => $cuenta->id,
                            'importe' => $pay['dolares'],
                            'fecha' => now()->format('Ymd'),
                            'numpago' => static::nroPago(),
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

                        $nuevoPago = Pago::create([
                            'ctacte_id' => $cuenta->id,
                            'importe' => $pay['dolares'],
                            'fecha' => now()->format('Ymd'),
                            'numpago' => static::nroPago(),
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
            $config = ConfiguracionTrait::configuracion();
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

    public static function nroPago()
    {
        if (Pago::all()->last() == null) {
            $config = ConfiguracionTrait::configuracion();
            return $config['numpago'] + 1;
        } else return Pago::all()->last()->numpago + 1;
    }
}
