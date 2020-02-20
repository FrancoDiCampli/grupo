<?php

namespace App;

use App\Cheque;
use App\Efectivo;
use App\Transferencia;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $guarded = [];

    protected $casts = [
        'importe' => 'decimal:2',
    ];

    public function ctacte()
    {
        return $this->belongsTo('App\Cuentacorriente', 'ctacte_id');
    }

    public function recibo()
    {
        return $this->belongsToMany('App\Recibo')->withTimestamps();
    }

    public function forma()
    {
        $referencia = str_split($this->referencia);
        $cadena = $referencia[0] . $referencia[1];
        $numero = null;

        for ($i = 2; $i < count($referencia); $i++) {
            $numero = $numero . $referencia[$i];
        }

        switch ($cadena) {
            case 'EF':
                return ['Efectivo', Efectivo::find($numero)];
                break;

            case 'CH':
                return ['Cheque', Cheque::find($numero)];
                break;

            case 'TB':
                return ['Transferencia', Transferencia::find($numero)];
                break;
        }
    }
}
