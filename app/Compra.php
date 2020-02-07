<?php

namespace App;

use App\User;
use App\Supplier;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $guarded = [];

    public function proveedor()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, ' user_id');
    }

    public function articulos()
    {
        return $this->belongsToMany('App\Articulo')
            ->withPivot('codprov', 'codarticulo', 'articulo', 'medida', 'cantidad', 'litros', 'bonificacion', 'alicuota', 'preciounitario', 'subtotal', 'lote')->withTimestamps();
    }
}
