<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Traits\DevolucionesTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class GiveBacksController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:airlock');

        $this->middleware('scope:devoluciones-index')->only('index');
        $this->middleware('scope:devoluciones-store')->only(['store', 'inventariosVendedor']);
        $this->middleware('scope:devoluciones-show')->only('show');
    }

    public function index(Request $request)
    {
        return DevolucionesTrait::index($request);
    }

    public function store(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                return DevolucionesTrait::store($request);
            });
        } catch (\Throwable $th) {
            throw $th;
        }
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
