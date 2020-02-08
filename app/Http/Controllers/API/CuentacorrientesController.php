<?php

namespace App\Http\Controllers\API;

use App\Pago;
use App\Saldo;
use App\Venta;
use App\Cheque;
use App\Recibo;
use App\Credito;
use App\Efectivo;
use App\Transferencia;
use App\Cuentacorriente;
use App\Movimientocuenta;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CuentacorrientesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:airlock');
        $this->middleware('scope:cuentascorrientes-pagar')->only('pagar');
    }

    // PAGOS PARCIALES O TOTALES 
    public function pagar(Request $request)
    {
        $request = collect($request);
        $aux = array();
        $total = 0;

        foreach ($request as $res) {
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
                            'referencia' => $this->formaPago($pay, $cliente, $diferencia = null)
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
                            'referencia' => $this->formaPago($pay, $cliente, $diferencia = null)
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
                            'referencia' => $this->formaPago($pay, $cliente, $diferencia)
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

    public function formaPago($pay, $cliente, $diferencia)
    {
        $esta = $pay['tipo'];

        switch ($esta) {
            case 'Efectivo':
                $efectivo = Efectivo::create([
                    'pesos' => $pay['pesos'] * 1,
                    'dolares' => $pay['dolares'] * 1,
                    'fecha' => now()->format('Ymd'),
                    'fecha_cotizacion' => $pay['fecha_cotizacion'],
                    'cotizacion' => $pay['cotizacion'],
                ]);
                if ($cliente->haber) {
                    if ($diferencia != null) {
                        $haber = Saldo::findOrFail($cliente->haber->id);
                        $haber->haber = $haber->haber + $diferencia;
                        $haber->save();
                    }
                } else {
                    if ($diferencia != null) {
                        Saldo::create([
                            'cliente_id' => $cliente->id,
                            'haber' => $diferencia
                        ]);
                    }
                }
                return 'EF' . $efectivo->id;
                break;

            case 'Haber':
                $haber = Credito::create([
                    'pesos' => $pay['pesos'] * 1,
                    'dolares' => $pay['dolares'] * 1,
                    'fecha' => now()->format('Ymd'),
                    'fecha_cotizacion' => $pay['fecha_cotizacion'],
                    'cotizacion' => $pay['cotizacion'],
                ]);
                if ($cliente->haber) {
                    if ($diferencia != null) {
                        $haber = Saldo::findOrFail($cliente->haber->id);
                        $haber->haber = $diferencia;
                        $haber->save();
                    } else {
                        $haber = Saldo::findOrFail($cliente->haber->id);
                        $haber->haber = 0;
                        $haber->save();
                    }
                } else {
                    if ($diferencia != null) {
                        Saldo::create([
                            'cliente_id' => $cliente->id,
                            'haber' => $diferencia
                        ]);
                    }
                }
                return 'HA' . $haber->id;
                break;

            case 'Cheque':
                if ($pay['fechacobro'] == $pay['fecharecibido']) {
                    $pay['estado'] = 'COBRADO';
                } else {
                    $pay['estado'] = 'POR COBRAR';
                }
                if ($pay['observaciones'] == null) {
                    $pay['observaciones'] = null;
                }
                $cheque = Cheque::create([
                    'banco' => $pay['banco'],
                    'cotizacion' => $pay['cotizacion'] * 1,
                    'dolares' => $pay['dolares'] * 1,
                    'cuit' => $pay['cuit'] * 1,
                    'emisor' => $pay['emisor'],
                    'estado' => $pay['estado'],
                    'fecha_cotizacion' => $pay['fecha_cotizacion'],
                    'fechacobro' => $pay['fechacobro'],
                    'fecharecibido' => $pay['fecharecibido'],
                    'numero' => $pay['numero'] * 1,
                    'pesos' => $pay['pesos'] * 1,
                    'observaciones' => $pay['observaciones'],
                ]);
                if ($cliente->haber) {
                    if ($diferencia != null) {
                        $haber = Saldo::findOrFail($cliente->haber->id);
                        $haber->haber = $haber->haber + $diferencia;
                        $haber->save();
                    }
                } else {
                    if ($diferencia != null) {
                        Saldo::create([
                            'cliente_id' => $cliente->id,
                            'haber' => $diferencia
                        ]);
                    }
                }
                return 'CH' . $cheque->id;
                break;

            case 'Transferencia':
                $transferencia = Transferencia::create([
                    'banco' => $pay['banco'],
                    'cotizacion' => $pay['cotizacion'],
                    'dolares' => $pay['dolares'] * 1,
                    'cuit' => $pay['cuit'],
                    'emisor' => $pay['emisor'],
                    'fecha' => $pay['fecha'],
                    'fecha_cotizacion' => $pay['fecha_cotizacion'],
                    'numero' => $pay['numero'] * 1,
                    'pesos' => $pay['pesos'] * 1,
                    'observaciones' => $pay['observaciones'],
                ]);
                if ($cliente->haber) {
                    if ($diferencia != null) {
                        $haber = Saldo::findOrFail($cliente->haber->id);
                        $haber->haber = $haber->haber + $diferencia;
                        $haber->save();
                    }
                } else {
                    if ($diferencia != null) {
                        Saldo::create([
                            'cliente_id' => $cliente->id,
                            'haber' => $diferencia
                        ]);
                    }
                }
                return 'TB' . $transferencia->id;
                break;
        }
    }

    public function recargar(Request $request)
    {
        $cuenta = Cuentacorriente::find($request->get('id'));
        $cuenta->saldo = $request->get('nuevoSaldo');
        $cuenta->update();
    }
}
