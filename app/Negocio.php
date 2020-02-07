<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Negocio extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function getRazonsocialAttribute($value)
    {
        return ucwords($value);
    }
    public function getDireccionAttribute($value)
    {
        return ucwords($value);
    }
    public function getLocalidadAttribute($value)
    {
        return ucwords($value);
    }
    public function getProvinciaAttribute($value)
    {
        return ucwords($value);
    }
    public function getObservacionesAttribute($value)
    {
        return ucfirst($value);
    }

    public function inventarios()
    {
        return $this->hasMany('App\Inventario');
    }

    public function movimientos()
    {
        return $this->hasManyThrough(
            'App\Movimiento',
            'App\Inventario'
        );
    }

    public function clientes()
    {
        return $this->hasManyThrough(
            'App\Cliente',
            'App\User'
        );
    }

    public function usuarios()
    {
        return $this->hasMany('App\User');
    }
}
