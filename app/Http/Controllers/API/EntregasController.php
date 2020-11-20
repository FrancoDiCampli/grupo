<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Traits\EntregasTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class EntregasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:airlock');

        $this->middleware('scope:entregas-index')->only('index');
        $this->middleware('scope:entregas-show')->only('show');
        $this->middleware('scope:entregas-store')->only('store');
        $this->middleware('scope:entregas-destroy')->only('destroy');
    }

    public function index(Request $request)
    {
        return EntregasTrait::index($request);
    }

    public function store(Request $request)
    {
        return EntregasTrait::store($request);
    }

    public function show($id)
    {
        return EntregasTrait::show($id);
    }

    public function destroy($id)
    {
        return EntregasTrait::delete($id);
    }
}
