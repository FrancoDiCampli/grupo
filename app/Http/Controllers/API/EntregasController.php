<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Traits\EntregasTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class EntregasController extends Controller
{
    public function index(Request $request)
    {
        return EntregasTrait::index($request);
    }

    public function store(Request $request)
    {
        try {
            DB::transaction(function() use($request){
                return EntregasTrait::store($request);
            });
        } catch (\Throwable $th) {
            throw $th;
        }
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
