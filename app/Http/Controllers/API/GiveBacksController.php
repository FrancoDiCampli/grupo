<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Traits\DevolucionesTrait;
use App\Http\Controllers\Controller;

class GiveBacksController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:airlock');
    }

    public function index(Request $request)
    {
        return DevolucionesTrait::index($request);
    }

    public function store(Request $request)
    {
        return DevolucionesTrait::store($request);
    }

    public function show($id)
    {
        return DevolucionesTrait::show($id);
    }

    public function inventariosVendedor(Request $request)
    {
        return DevolucionesTrait::inventariosVendedor($request);
    }
}
