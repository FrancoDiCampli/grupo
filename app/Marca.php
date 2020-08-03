<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    protected $guarded = [];

    public function getMarcaAttribute($value)
    {
        return ucwords($value);
    }

    public function articulos()
    {
        return $this->hasMany('App\Articulo');
    }

    public function scopeBuscarExacto($query, $request)
    {
        $marca = $request->get('buscarExactoMarca');

        if (strlen($marca)) {
            return $query->where('marca', $marca);
        }
    }
}
