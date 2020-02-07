<?php

namespace App;

use App\Compra;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
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

    public function scopeBuscar($query, $request)
    {
        $proveedor = $request->get('buscarProveedor');

        if (strlen($proveedor)) {
            return $query
                ->orWhere('razonsocial', 'LIKE', "$proveedor%")
                ->orWhere('id', 'LIKE', "$proveedor%");
        }
    }

    public function remitos()
    {
        return $this->hasMany(Compra::class);
    }
}
