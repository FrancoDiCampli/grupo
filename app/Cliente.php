<?php

namespace App;

use App\User;
use App\Saldo;
use App\Venta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
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
    public function getCondicionivaAttribute($value)
    {
        return ucwords($value);
    }
    public function getObservacionesAttribute($value)
    {
        return ucfirst($value);
    }

    public function scopeBuscar($query, $request)
    {
        $cliente = $request->get('buscarCliente');

        if (strlen($cliente)) {
            return $query
                ->orWhere('razonsocial', 'LIKE', "$cliente%")
                ->orWhere('documentounico', 'LIKE', "$cliente%");
        }
    }

    public function facturas()
    {
        return $this->hasMany(Venta::class);
    }

    public function ctacte()
    {
        return $this->hasManyThrough(
            'App\Cuentacorriente',
            'App\Venta'
        );
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function haber()
    {
        return $this->hasOne(Saldo::class);
    }
}
