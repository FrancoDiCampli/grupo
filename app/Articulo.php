<?php

namespace App;

use App\Venta;
use App\Compra;
use App\Presupuesto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Articulo extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'precio' => 'decimal:2',
    ];

    public function getArticuloAttribute($value)
    {
        return ucwords($value);
    }
    public function getDescripcionAttribute($value)
    {
        return ucfirst($value);
    }
    public function getMedidaAttribute($value)
    {
        return ucwords($value);
    }

    public function categoria()
    {
        return $this->belongsTo('App\Categoria', 'categoria_id');
    }

    public function marca()
    {
        return $this->belongsTo('App\Marca', 'marca_id');
    }

    public function stock()
    {
        return $this->hasMany('App\Inventario')
            ->selectRaw('SUM(cantidad) as total')
            ->addSelect('articulo_id')
            ->groupBy('articulo_id');
    }

    public function inventarios()
    {
        return $this->hasMany('App\Inventario', 'articulo_id');
    }

    public function facturas()
    {
        return $this->belongsToMany(Venta::class)
            ->withPivot('codprov', 'codarticulo', 'articulo', 'medida', 'cantidad', 'litros', 'bonificacion', 'alicuota', 'preciounitario', 'subtotal')
            ->withTimestamps();
    }

    public function presupuestos()
    {
        return $this->belongsToMany(Presupuesto::class)
            ->withPivot('codprov', 'codarticulo', 'articulo', 'medida', 'cantidad', 'litros', 'bonificacion', 'alicuota', 'preciounitario', 'subtotal')
            ->withTimestamps();
    }

    public function remitos()
    {
        return $this->belongsToMany(Compra::class)
            ->withPivot('lote', 'cantidad', 'medida', 'preciounitario', 'subtotal')
            ->withTimestamps();
    }

    public function scopeBuscar($query, $request)
    {
        $articulo = $request->get('buscarArticulo');
        $articulos = collect();

        if (strlen($articulo)) {
            return $query->orWhere('codarticulo', 'LIKE', "$articulo%")
                ->orWhere('articulo', 'LIKE', "$articulo%");
        }
    }
}
