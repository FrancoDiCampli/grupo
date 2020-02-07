<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cheque extends Model
{
    protected $guarded = [];

    public function getEmisorAttribute($value)
    {
        return ucwords($value);
    }

    public function getBancoAttribute($value)
    {
        return ucwords($value);
    }

    public function getEstadoAttribute($value)
    {
        return ucwords($value);
    }
}
