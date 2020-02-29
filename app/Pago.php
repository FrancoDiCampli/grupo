<?php

namespace App;

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
}
