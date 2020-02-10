<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Movimientocuenta extends Model
{
    protected $guarded = [];

    public function ctacte()
    {
        return $this->belongsTo('App\Cuentacorriente', 'ctacte_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
