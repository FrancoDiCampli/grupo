<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PreferencesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getConfig()
    {
        $jsonString = file_get_contents(base_path('config.json'));
        $preferences = json_decode($jsonString, true);

        return $preferences;
    }
}
