<?php

namespace App;

use App\Venta;
use Illuminate\Database\Eloquent\Model;

class Factura extends Model
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

    public function ventas()
    {
        return $this->belongsToMany(Venta::class)->withTimestamps();
    }

    public function cliente()
    {
        return $this->belongsTo('App\Cliente');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function articulos()
    {
        return $this->belongsToMany('App\Articulo');
    }
}
