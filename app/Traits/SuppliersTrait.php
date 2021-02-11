<?php

namespace App\Traits;

use App\Supplier;
use Carbon\Carbon;
use App\Traits\ContactosTrait;

trait SuppliersTrait
{
    public static function index($request)
    {
        $suppliers = Supplier::orderBy('razonsocial', 'asc')->take($request->get('limit', null))->get();

        return [
            'proveedores' => $suppliers,
            'total' => Supplier::count(),
        ];
    }

    public static function store($request)
    {
        $data = $request->validated();

        $supplier = Supplier::create($data);

        ContactosTrait::crearContactos($supplier, $request);

        return $supplier;
    }

    public static function update($request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $data = $request->validated();

        $supplier->update($data);

        ContactosTrait::editarContactos($supplier, $request);

        return ['message' => 'actualizado'];
    }

    public static function show($id)
    {
        $supplier = Supplier::find($id);
        $contactos = ContactosTrait::contactos($supplier);
        $remitos = $supplier->remitos;
        // foreach ($remitos as $remito) {
        //     $fecha = new Carbon($remito->fecha);
        //     $remito->fecha = $fecha->format('d-m-Y');
        // }

        return ['proveedor' => $supplier, 'remitos' => $remitos, 'contactos' => $contactos];
    }
}
