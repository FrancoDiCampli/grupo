<?php

namespace App\Traits;

use App\Cliente;

trait DistribuidoresTrait
{
    public static function index()
    {
        return $distribuidores = Cliente::where('distribuidor', true)->get();
    }
}
