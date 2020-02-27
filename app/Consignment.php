<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Consignment extends Model
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
            ->withPivot('codarticulo', 'articulo', 'medida', 'presentacion', 'cantidad', 'cantidadLitros', 'preciounitario', 'subtotal', 'subtotalPesos', 'cotizacion', 'fechaCotizacion', 'articulo_id', 'consignacion_id')
            ->withTimestamps();
    }

    // public function dependencia()
    // {
    //     return $this->belongsTo('App\User');
    // }
}
