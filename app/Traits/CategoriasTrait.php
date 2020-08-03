<?php

namespace App\Traits;

use App\Categoria;
use Illuminate\Http\Request;

trait CategoriasTrait
{
    public static function index($request)
    {
        $categorias = Categoria::orderBy('id', 'ASC');
        return [
            'categorias' => $categorias->take($request->get('limit', null))->get(),
            'total' => $categorias->count()
        ];
    }

    public static function store($request)
    {
        $data = $request->validate(['categoria' => 'required|unique:categorias|max:190']);
        $data['categoria'] = ucwords($data['categoria']);
        $categoria = Categoria::create($data);
        return $categoria;
    }

    public static function categorias($categoria)
    {
        $category = Categoria::where('categoria', $categoria)->get();
        if (count($category) > 0) {
            return $category[0]->id;
        } else {
            $request = new Request(['categoria' => $categoria]);
            $nuevaCategoria = static::store($request);
            return $nuevaCategoria->id;
        }
    }
}
