<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $guarded = [];

    public function getCategoriaAttribute($value)
    {
        return ucwords($value);
    }

    public function articulos()
    {
        return $this->hasMany('App\Articulo');
    }

    public function scopeBuscarExacto($query, $request)
    {
        $categoria = $request->get('buscarExactoCategoria');

        if (strlen($categoria)) {
            return $query->where('categoria', $categoria);
        }
    }
}
