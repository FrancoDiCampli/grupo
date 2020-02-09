<?php

namespace App\Http\Controllers\API;

use App\Marca;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MarcasController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:airlock');
    }

    public function index(Request $request)
    {
        $marcas = Marca::orderBy('id', 'ASC')
            ->buscar($request);
        return [
            'marcas' => $marcas->take($request->get('limit', null))->get(),
            'total' => $marcas->count(),
        ];
    }

    public function show($id)
    {
        return Marca::find($id);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['marca' => 'required|unique:marcas|max:190']);
        $data['marca'] = ucwords($data['marca']);
        $marca = Marca::create($data);
        return $marca->id;
    }
}
