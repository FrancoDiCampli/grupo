<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Traits\DolarTrait;

class DolarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:airlock');
    }

    public function consultar()
    {
        return DolarTrait::consultar();
        // return DolarTrait::alternativa();
    }
}
