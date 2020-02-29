<?php

namespace App\Traits;

use App\Saldo;
use App\Cheque;
use App\Credito;
use App\Efectivo;
use App\Transferencia;

trait FormasDePagoTrait
{
    public static function formaPago($pay, $cliente, $diferencia)
    {
        $tipo = $pay['tipo'];
        switch ($tipo) {
            case 'Efectivo':
                $efectivo = Efectivo::create([
                    'pesos' => $pay['pesos'] * 1,
                    'dolares' => $pay['dolares'] * 1,
                    'fecha' => now()->format('Ymd'),
                    'fecha_cotizacion' => $pay['fecha_cotizacion'],
                    'cotizacion' => $pay['cotizacion'],
                ]);
                if ($cliente) {
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
                if ($cliente) {
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
                if ($cliente) {
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
                if ($cliente) {
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
                }
                return 'TB' . $transferencia->id;
                break;
        }
    }

    public static function verPagos($objeto)
    {
        $aux = collect();
        if ($objeto->referencia) {
            $refs = collect($objeto->referencia);
            foreach ($refs as $ref) {
                $referencia = str_split($ref);
                $cadena = $referencia[0] . $referencia[1];
                $numero = null;

                for ($i = 2; $i < count($referencia); $i++) {
                    $numero = $numero . $referencia[$i];
                }

                switch ($cadena) {
                    case 'EF':
                        $aux->push(['Efectivo', Efectivo::find($numero)]);
                        break;

                    case 'CH':
                        $aux->push(['Cheque', Cheque::find($numero)]);
                        break;

                    case 'TB':
                        $aux->push(['Transferencia', Transferencia::find($numero)]);
                        break;
                }
            }
            return $aux;
        }
    }
}
