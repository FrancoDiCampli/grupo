<?php

namespace App\Http\Controllers\API;

use App\Categoria;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\CategoriasTrait;

class CategoriasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:airlock');
    }

    public function index(Request $request)
    {
        return CategoriasTrait::index($request);
    }

    public function show($id)
    {
        return Categoria::find($id);
    }

    public function store(Request $request)
    {
        return CategoriasTrait::store($request);
    }
}
