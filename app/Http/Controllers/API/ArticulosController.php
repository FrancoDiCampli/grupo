<?php

namespace App\Http\Controllers\API;

use App\Articulo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArticulo;
use App\Http\Requests\UpdateArticulo;
use App\Traits\ArticulosTrait;
use App\Traits\EstadisticasLitrosClienteTrait;

class ArticulosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:airlock');

        $this->middleware('scope:articulos-index')->only('index');
        $this->middleware('scope:articulos-show')->only('show');
        $this->middleware('scope:articulos-store')->only('store');
        $this->middleware('scope:articulos-update')->only('update');
        $this->middleware('scope:articulos-destroy')->only('destroy');
    }

    public function index(Request $request)
    {
        return EstadisticasLitrosClienteTrait::litrosCliente();
        return ArticulosTrait::index($request);
    }

    public function store(StoreArticulo $request)
    {
        return ArticulosTrait::store($request);
    }

    public function update(UpdateArticulo $request, $id)
    {
        return ArticulosTrait::update($request, $id);
    }

    public function destroy($id)
    {
        $articulo = Articulo::find($id);
        $articulo->delete();

        return ['message' => 'eliminado'];
    }

    public function show($id)
    {
        return ArticulosTrait::show($id);
    }

    public function restaurar($id)
    {
        Articulo::withTrashed()->find($id)->restore();
        return Articulo::find($id);
    }
}
