<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\ConsignacionesTrait;

class ConsignmentsController extends Controller
{
    public function index()
    {
        ConsignacionesTrait::index();
    }

    public function store(Request $request)
    {
        ConsignacionesTrait::store($request);
    }

    public function show($id)
    {
        ConsignacionesTrait::show($id);
    }
}
