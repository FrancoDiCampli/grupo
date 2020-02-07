<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contacto extends Model
{
    protected $guarded = [];

    public function getNombreAttribute($value)
    {
        return ucwords($value);
    }

    public function getCargoAttribute($value)
    {
        return ucwords($value);
    }
}
