<?php

namespace App\Http\Controllers\API;

use App\Consignment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\ConsignacionesTrait;
use App\User;
use Carbon\Carbon;

class ConsignmentsController extends Controller
{
    public function index()
    {
        $consignaciones = Consignment::orderBy('id', 'DESC')->get();

        foreach ($consignaciones as $consig) {
            $fecha = new Carbon($consig->fecha);
            $consig->fecha = $fecha->format('d-m-Y');
            $consig->dependencia = User::find($consig->dependencia);
        }

        return ['consignaciones' => $consignaciones];
    }

    public function store(Request $request)
    {
        $consignacion = ConsignacionesTrait::storeConsignaciones($request);
        $mover = ConsignacionesTrait::moverInventarios($request);
        if ($mover) {
            if ($request->tipo != 'TRANSFERENCIA') {
                $venta = ConsignacionesTrait::ventaConsignaciones($request);
                $consignacion->numventa = $venta->id;
                $consignacion->save();
            }
        } else {
            $consignacion->articulos()->detach();
            $consignacion->forceDelete();
        }
    }

    public function show($id)
    {
        return $consignacion = Consignment::find($id);
    }
}
