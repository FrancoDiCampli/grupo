<?php

namespace App;

use App\Venta;
use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    protected $guarded = [];

    public function ventas()
    {
        return $this->belongsToMany(Venta::class)->withTimestamps();
    }

    public function cliente()
    {
        return $this->belongsTo('App\Cliente');
    }
}
