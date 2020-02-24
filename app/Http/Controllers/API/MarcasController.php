<?php

namespace App\Http\Controllers\API;

use App\Marca;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\MarcasTrait;

class MarcasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:airlock');
    }

    public function index(Request $request)
    {
        return MarcasTrait::index($request);
    }

    public function show($id)
    {
        return Marca::find($id);
    }

    public function store(Request $request)
    {
        return MarcasTrait::store($request);
    }
}
