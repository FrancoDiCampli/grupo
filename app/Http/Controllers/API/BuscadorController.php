<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\BuscadorTrait;

class BuscadorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:airlock');
    }

    public function buscando(Request $request)
    {
        return BuscadorTrait::buscando($request);
    }
}
