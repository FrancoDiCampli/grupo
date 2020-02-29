<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Traits\ConfiguracionTrait;

class PreferencesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:airlock');
    }

    public function getConfig()
    {
        return ConfiguracionTrait::configuracion();
    }
}
