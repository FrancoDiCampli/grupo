<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Credito extends Model
{
    protected $guarded = [];

    protected $casts = [
        'pesos' => 'decimal:2',
        'dolares' => 'decimal:2',
        'cotizacion' => 'decimal:2',
    ];
}
