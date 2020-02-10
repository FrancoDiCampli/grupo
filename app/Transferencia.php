<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transferencia extends Model
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
}
