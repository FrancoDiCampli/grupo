<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Saldo extends Model
{
    protected $guarded = [];

    protected $casts = [
        'haber' => 'decimal:2',
    ];
}
