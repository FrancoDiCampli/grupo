<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Presupuesto extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'bonificacion' => 'decimal:2',
        'recargo' => 'decimal:2',
        'total' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'subtotalPesos' => 'decimal:2',
        'totalPesos' => 'decimal:2',
        'cotizacion' => 'decimal:2',
    ];

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
            ->withPivot('codarticulo', 'articulo', 'medida', 'cantidad', 'cantidadLitros', 'preciounitario', 'subtotal', 'subtotalPesos', 'cotizacion', 'fechaCotizacion', 'articulo_id', 'presupuesto_id')
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
