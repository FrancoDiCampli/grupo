<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Traits\EntregasTrait;
use Illuminate\Http\Request;

class EntregasController extends Controller
{
    public function index(Request $request)
    {
        return EntregasTrait::index($request);
    }

    public function store(Request $request)
    {
        return EntregasTrait::store($request);
    }

    public function destroy($id)
    {
        return EntregasTrait::delete($id);
    }
}
