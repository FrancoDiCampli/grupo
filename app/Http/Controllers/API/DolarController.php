<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\DolarTrait;

class DolarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:airlock');
    }

    public function consultar()
    {
        $res = DolarTrait::consultar();

        if ($res != 502) {
            return $res;
        } else return DolarTrait::alternativa();
    }

    public function setCotizacion(Request $request)
    {
        return DolarTrait::setCotizacion($request);
    }
}
