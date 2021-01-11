<?php

namespace App\Http\Controllers\API;

use App\Traits\DolarTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DolarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:airlock');
    }

    public function consultar()
    {
        return DolarTrait::getCotizacion();
    }

    public function setCotizacion(Request $request)
    {
        return DolarTrait::setCotizacion($request);
    }
}
