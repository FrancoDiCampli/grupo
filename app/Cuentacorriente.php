<?php

namespace App;

use App\Venta;
use Illuminate\Database\Eloquent\Model;

class Cuentacorriente extends Model
{
    protected $guarded = [];

    protected $casts = [
        'importe' => 'decimal:2',
        'saldo' => 'decimal:2',
    ];

    public function factura()
    {
        return $this->hasOne(Venta::class, 'id', 'venta_id');
    }

    public function pagos()
    {
        return $this->hasMany('App\Pago', 'ctacte_id');
    }

    public function movimientos()
    {
        return $this->hasMany('App\Movimientocuenta', 'ctacte_id', 'id');
    }
}
