<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Recibo extends Model
{
    protected $guarded = [];

    public function pagos()
    {
        return $this->belongsToMany('App\Pago')->withTimestamps();
    }
}
