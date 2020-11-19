<?php

namespace App\Http\Controllers\API;

use App\Presupuesto;
use Illuminate\Http\Request;
use App\Traits\PresupuestosTrait;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class PresupuestosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:airlock');

        $this->middleware('scope:presupuestos-index')->only('index');
        $this->middleware('scope:presupuestos-show')->only('show');
        $this->middleware('scope:presupuestos-store')->only('store');
        $this->middleware('scope:presupuestos-destroy')->only('destroy');
    }

    public function index(Request $request)
    {
        return PresupuestosTrait::index($request);
    }

    public function store(Request $request)
    {
        return PresupuestosTrait::store($request);
    }

    public function show($id)
    {
        return PresupuestosTrait::show($id);
    }

    public function destroy($id)
    {
        return PresupuestosTrait::delete($id);
    }

    public function restaurar($id)
    {
        Presupuesto::withTrashed()->find($id)->restore();
        return Presupuesto::find($id);
    }

    public function vender(Request $request)
    {
        return PresupuestosTrait::vender($request);
    }

    public function update(Request $request, $id)
    {
        return PresupuestosTrait::update($request, $id);
    }
}
