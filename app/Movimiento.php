<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Movimiento extends Model
{
    protected $guarded = [];

    public function inventario()
    {
        return $this->belongsTo('App\Inventario');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
