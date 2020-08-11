<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\ConsignacionesTrait;
use App\Http\Controllers\Controller;

class ConsignmentsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:airlock');
    }

    public function index(Request $request)
    {
        return ConsignacionesTrait::index($request);
    }

    public function store(Request $request)
    {
        try {
            DB::transaction(function() use($request){
                return ConsignacionesTrait::store($request);
            });
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function show($id)
    {
        return ConsignacionesTrait::show($id);
    }
}
