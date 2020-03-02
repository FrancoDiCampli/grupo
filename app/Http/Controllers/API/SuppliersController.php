<?php

namespace App\Http\Controllers\API;

use App\Supplier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupplier;
use App\Http\Requests\UpdateSupplier;
use App\Traits\SuppliersTrait;

class SuppliersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:airlock');

        $this->middleware('scope:suppliers-index')->only('index');
        $this->middleware('scope:suppliers-show')->only('show');
        $this->middleware('scope:suppliers-store')->only('store');
        $this->middleware('scope:suppliers-update')->only('update');
        $this->middleware('scope:suppliers-destroy')->only('destroy');
    }

    public function index(Request $request)
    {
        return SuppliersTrait::index($request);
    }

    public function store(StoreSupplier $request)
    {
        return SuppliersTrait::store($request);
    }

    public function update(UpdateSupplier $request, $id)
    {
        return SuppliersTrait::update($request, $id);
    }

    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return ['message' => 'eliminado'];
    }

    public function show($id)
    {
        return SuppliersTrait::show($id);
    }

    public function restaurar($id)
    {
        Supplier::withTrashed()->find($id)->restore();
        return Supplier::find($id);
    }
}
