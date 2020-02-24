<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
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
        return ConsignacionesTrait::store($request);
    }

    public function show($id)
    {
        return ConsignacionesTrait::show($id);
    }
}
