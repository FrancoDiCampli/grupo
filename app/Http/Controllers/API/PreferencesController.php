<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

class PreferencesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:airlock');
    }

    public function getConfig()
    {
        $jsonString = file_get_contents(base_path('config.json'));
        $preferences = json_decode($jsonString, true);

        return $preferences;
    }
}
