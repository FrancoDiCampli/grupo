<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    protected $guarded = [];

    public function getObservacionesAttribute($value)
    {
        return ucfirst($value);
    }

    public function articulo()
    {
        return $this->belongsTo('App\Articulo', 'articulo_id')->withTrashed();
    }

    public function movimientos()
    {
        return $this->hasMany('App\Movimiento');
    }

    public function proveedor()
    {
        return $this->belongsTo('App\Supplier', 'supplier_id')->withTrashed();
    }

    public function scopeBuscar($query, $request)
    {
        $lote = $request->get('lote');
        $articulo_id = $request->get('articulo_id');

        if (strlen($lote)) {
            return $query->where('lote', $lote)->where('articulo_id', $articulo_id);
        }
    }
}
