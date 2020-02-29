<?php

namespace App;

use App\User;
use App\Factura;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venta extends Model
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
        'referencia' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function factura()
    {
        return $this->belongsToMany(Factura::class);
    }

    public function articulos()
    {
        return $this->belongsToMany('App\Articulo')
            ->withPivot('codarticulo', 'articulo', 'medida', 'cantidad', 'cantidadLitros', 'preciounitario', 'subtotal', 'subtotalPesos', 'cotizacion', 'fechaCotizacion', 'articulo_id', 'venta_id')
            ->withTimestamps()->withTrashed();
    }

    public function cuenta()
    {
        return $this->hasOne('App\Cuentacorriente');
    }

    public function cliente()
    {
        return $this->belongsTo('App\Cliente')->withTrashed();
    }

    public function vendedor()
    {
        return $this->belongsTo('App\User');
    }

    public function scopeBuscar($query, $request)
    {
        $factura = $request->get('buscarFactura');

        if (strlen($factura)) {
            return $query->where('cuit', 'LIKE', "$factura%");
        }
    }
}
