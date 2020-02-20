<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transferencia extends Model
{
    protected $guarded = [];

    protected $casts = [
        'pesos' => 'decimal:2',
        'dolares' => 'decimal:2',
        'cotizacion' => 'decimal:2',
    ];

    public function getEmisorAttribute($value)
    {
        return ucwords($value);
    }

    public function getBancoAttribute($value)
    {
        return ucwords($value);
    }
}
