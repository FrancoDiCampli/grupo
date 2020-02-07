<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Presupuesto extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cliente()
    {
        return $this->belongsTo('App\Cliente');
    }

    public function articulos()
    {
        return $this->belongsToMany('App\Articulo')
            ->withPivot('codarticulo', 'articulo', 'medida', 'cantidad', 'cantidadLitros', 'preciounitario', 'subtotal')
            ->withTimestamps();
    }

    public function detalles()
    {
        return $this->belongsToMany('App\Articulo');
    }

    public function scopeBuscar($query, $request)
    {
        $presupuesto = $request->get('buscarPresupuesto');

        if (strlen($presupuesto)) {
            return $query->where('cuit', 'LIKE', "%$presupuesto%");
        }
    }
}
