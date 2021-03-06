<?php

namespace App\Traits;

use App\Cheque;

trait ChequesTrait
{
    public static function chequeCobrado($id)
    {
        $cheque = Cheque::findOrFail($id);
        $cheque->estado = 'COBRADO';
        $cheque->save();

        return ['message' => 'actualizado'];
    }

    public static function cartera($request)
    {
        $data = Cheque::all();

        return [
            'cheques' => $data->take($request->get('limit', null)),
            'total' => $data->count(),
        ];
    }
}
