<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Recibo extends Model
{
    protected $guarded = [];

    protected $casts = [
        'total' => 'decimal:2'
    ];

    public function pagos()
    {
        return $this->belongsToMany('App\Pago')->withTimestamps();
    }
}
