<?php

namespace App\Traits;

use Illuminate\Http\Request;
use App\Marca;

trait MarcasTrait
{
    public static function index($request)
    {
        $marcas = Marca::orderBy('id', 'ASC')->take($request->get('limit', null))->get();
        return [
            'marcas' => $marcas,
            'total' => Marca::count(),
        ];
    }

    public static function store($request)
    {
        $data = $request->validate(['marca' => 'required|unique:marcas|max:190']);
        $data['marca'] = ucwords($data['marca']);
        $marca = Marca::create($data);
        return $marca;
    }

    public static function marcas($marca)
    {
        $brand = Marca::where('marca', $marca)->get();
        if (count($brand) > 0) {
            return $brand[0]->id;
        } else {
            $request = new Request(['marca' => $marca]);
            $nuevaMarca = static::store($request);
            return  $nuevaMarca->id;
        }
    }
}
