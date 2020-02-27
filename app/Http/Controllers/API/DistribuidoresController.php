<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Traits\DistribuidoresTrait;
use Illuminate\Http\Request;

class DistribuidoresController extends Controller
{
    public function index()
    {
        return DistribuidoresTrait::index();
    }
}
