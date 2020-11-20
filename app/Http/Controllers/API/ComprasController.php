<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\ComprasTrait;

class ComprasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:airlock');

        $this->middleware('scope:compras-index')->only('index');
        $this->middleware('scope:compras-show')->only('show');
        $this->middleware('scope:compras-store')->only('store');
        $this->middleware('scope:compras-destroy')->only('destroy');
    }

    public function index(Request $request)
    {
        return ComprasTrait::index($request);
    }

    public function store(Request $request)
    {
        return ComprasTrait::store($request);
    }

    public function show($id)
    {
        return ComprasTrait::show($id);
    }

    public function destroy($id)
    {
        return ComprasTrait::delete($id);
    }
}
