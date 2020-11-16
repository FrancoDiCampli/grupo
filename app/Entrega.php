<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Entrega extends Model
{
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
        return $this->belongsTo('App\User');
    }

    public function articulos()
    {
        return $this->belongsToMany('App\Articulo')
            ->withPivot('codarticulo', 'articulo', 'medida', 'cantidad', 'cantidadLitros', 'articulo_id', 'articulo_venta_id')
            ->withTimestamps();
    }

    public function cliente()
    {
        return $this->belongsTo('App\Cliente');
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }
}
