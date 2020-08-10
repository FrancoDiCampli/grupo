<?php

namespace App\Traits;

use App\Pago;
use App\Venta;
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

                        $referencia = FormasDePagoTrait::formaPago($pay, $cliente, $diferencia = null);
                        $nuevoPago = static::crearPago($cuenta, $pay['dolares'], $referencia);

                        array_push($aux, $nuevoPago->id);

                        static::crearMovimiento($cuenta, $tipo = 'PAGO PARCIAL', $pay['dolares']);
                    } else if ($pay['dolares'] == $cuenta->saldo) {
                        // PAGO TOTAL
                        $cuenta->saldo = 0;
                        $factura = Venta::find($cuenta->venta_id);
                        $cuenta->ultimopago = now()->format('Ymd');
                        $cuenta->estado = 'CANCELADA';
                        $cuenta->update();
                        // $factura->pagada = true;
                        $factura->update();
                        $total = $total + $pay['dolares'];

                        $referencia = FormasDePagoTrait::formaPago($pay, $cliente, $diferencia = null);
                        $nuevoPago = static::crearPago($cuenta, $pay['dolares'], $referencia);

                        array_push($aux, $nuevoPago->id);

                        static::crearMovimiento($cuenta, $tipo = 'PAGO TOTAL', $pay['dolares']);
                    } elseif ($pay['dolares'] > $cuenta->saldo) {
                        $diferencia = $pay['dolares'] - $cuenta->saldo;
                        $total = $total + $pay['dolares'];
                        $cuenta->saldo = 0;
                        $factura = Venta::find($cuenta->venta_id);
                        $cuenta->ultimopago = now()->format('Ymd');
                        $cuenta->estado = 'CANCELADA';
                        $cuenta->update();
                        // $factura->pagada = true;
                        $factura->update();

                        $referencia = FormasDePagoTrait::formaPago($pay, $cliente, $diferencia);
                        $nuevoPago = static::crearPago($cuenta, $pay['dolares'], $referencia);

                        array_push($aux, $nuevoPago->id);

                        static::crearMovimiento($cuenta, $tipo = 'PAGO TOTAL', $pay['dolares']);
                    }
                }
            }
        }
        // ALMACENAMIENTO DE RECIBO
        $recibo = RecibosTrait::crearRecibo($total);

        $recibo->pagos()->attach($aux);

        return $recibo->id;
    }

    public static function crearCuenta($factura)
    {
        $cuenta = Cuentacorriente::create([
            'venta_id' => $factura->id,
            'importe' => $factura->total,
            'saldo' => $factura->total,
            'alta' => now(),
            'estado' => 'ACTIVA'
        ]);
        Movimientocuenta::create([
            'ctacte_id' => $cuenta->id,
            'tipo' => 'ALTA',
            'fecha' => now(),
            'user_id' => auth()->user()->id,
            'importe' => $cuenta->importe
        ]);
    }

    public static function nroPago()
    {
        if (Pago::all()->last() == null) {
            $config = ConfiguracionTrait::configuracion();
            return $config['numpago'] + 1;
        } else return Pago::all()->last()->numpago + 1;
    }

    public static function crearPago($cuenta, $pago, $referencia)
    {
        $numPago = static::nroPago();

        return Pago::create([
            'ctacte_id' => $cuenta->id,
            'importe' => $pago,
            'fecha' => now()->format('Ymd'),
            'numpago' => $numPago,
            'referencia' => $referencia
        ]);
    }

    public static function crearMovimiento($cuenta, $tipo, $pago)
    {
        return Movimientocuenta::create([
            'ctacte_id' => $cuenta->id,
            'tipo' => $tipo,
            'fecha' => now(),
            'importe' => $pago,
            'user_id' => auth()->user()->id
        ]);
    }

    public static function aplicarIVA($ventas, $iva)
    {
        $cuenta = Cuentacorriente::whereIn('venta_id', $ventas)->where('estado', 'ACTIVA')->first();
        $cuenta->saldo += $iva;
        $cuenta->update();
        static::crearMovimiento($cuenta, 'IVA', $iva);

        // $cuentas = Cuentacorriente::whereIn('venta_id', $ventas)->get();

        // $cuentas->map(function($cuenta) use($iva){
        //     $cuenta->saldo += $iva;
        //     $cuenta->update();

        //     static::crearMovimiento($cuenta, 'IVA', $iva);
        // });
    }
}
