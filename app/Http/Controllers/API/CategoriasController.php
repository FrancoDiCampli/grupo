<?php

namespace App\Http\Controllers\API;

use App\Categoria;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoriasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $categorias = Categoria::orderBy('id', 'ASC')
            ->buscar($request);
        return [
            'categorias' => $categorias->take($request->get('limit', null))->get(),
            'total' => $categorias->count()
        ];
    }

    public function show($id)
    {
        return Categoria::find($id);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['categoria' => 'required|unique:categorias|max:190']);
        $data['categoria'] = ucwords($data['categoria']);
        $categoria = Categoria::create($data);
        return $categoria->id;
    }
}
