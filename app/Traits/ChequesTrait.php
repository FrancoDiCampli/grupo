<?php

namespace App\Traits;

use App\Cheque;
use Carbon\Carbon;

trait ChequesTrait
{
    public static function chequeCobrado($id)
    {
        $cheque = Cheque::findOrFail($id);
        $cheque->estado = 'COBRADO';
        $cheque->udpate();

        return ['message' => 'actualizado'];
    }

    public static function cartera()
    {
        $cheques = Cheque::all();
        $calendar = array();
        $table = array();
        $today = now();
        $diferencia = 0;
        $hoy = 0;
        $hoyCount = 0;
        $siete = 0;
        $sieteCount = 0;
        $quince = 0;
        $quinceCount = 0;
        $treinta = 0;
        $treintaCount = 0;
        $sesenta = 0;
        $sesentaCount = 0;
        $mas = 0;
        $masCount = 0;

        foreach ($cheques as $cheque) {
            if ($cheque->estado == 'POR COBRAR') {
                $color = 'orange';
            } else {
                $color = 'green';
            }
            array_push(
                $calendar,
                [
                    'name' => 'Cheque Nº.: ' . $cheque->numero,
                    'start' => $cheque->fecharecibido,
                    'end' => $cheque->fechacobro,
                    'color' => $color,
                    'title' => $cheque->estado
                ]
            );

            $date = new Carbon($cheque->fechacobro);
            $diferencia = $today->diffInDays($date);

            if ($diferencia == 0) {
                $hoy += $cheque->importe;
                $hoyCount++;
            } elseif ($diferencia > 0 && $diferencia < 8) {
                $siete += $cheque->importe;
                $sieteCount++;
            } elseif ($diferencia > 7 && $diferencia < 16) {
                $quince += $cheque->importe;
                $quinceCount++;
            } elseif ($diferencia > 15 && $diferencia < 31) {
                $treinta += $cheque->importe;
                $treintaCount++;
            } elseif ($diferencia > 30 && $diferencia < 61) {
                $sesenta += $cheque->importe;
                $sesentaCount++;
            } elseif ($diferencia > 60) {
                $mas += $cheque->importe;
                $masCount++;
            }
        }

        array_push($table, [
            'hoy' => $hoy,
            'hoyCount' => $hoyCount,
            'siete' => $siete,
            'sieteCount' => $sieteCount,
            'quince' => $quince,
            'quinceCount' => $quinceCount,
            'treinta' => $treinta,
            'treintaCount' => $treintaCount,
            'sesenta' => $sesenta,
            'sesentaCount' => $sesentaCount,
            'mas' => $mas,
            'masCount' => $masCount,
            'total' => $hoy + $siete + $quince + $treinta + $sesenta + $mas,
            'totalCount' => $hoyCount + $sieteCount + $quinceCount + $treintaCount + $sesentaCount + $masCount
        ]);

        return ['calendar' => $calendar, 'table' => $table];
    }
}
