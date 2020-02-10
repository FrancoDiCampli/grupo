<?php

namespace App\Http\Controllers\API;

use App\Articulo;
use App\Supplier;
use App\Inventario;
use App\Movimiento;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInventario;

class InventariosController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:airlock');

        // $this->middleware('scope:inventarios-index')->only('index');
        // $this->middleware('scope:inventarios-store')->only('store');
    }

    public function index(Request $request)
    {
        $inventories = Inventario::buscar($request)->get();
        $inventarios = collect();
        foreach ($inventories as $inventory) {
            $proveedor = Supplier::find($inventory->supplier_id);
            $inventory = collect($inventory);
            $inventory->put('supplier', $proveedor);
            $inventarios->push($inventory);
        }
        return $inventarios->take(1);
    }

    // CREA INVENTARIO DE NO EXISTIR, CASO CONTRARIO LO ACTUALIZA
    public function store(StoreInventario $request)
    {
        $data = $request->validated();
        $mov = new Movimiento;
        $actualizar = Inventario::where('lote', $data['lote'])->where('articulo_id', $data['articulo_id'])->get()->first();

        if ($actualizar) {
            if ($request['movimiento'] == 'INCREMENTO') {
                $actualizar->cantidad = $actualizar->cantidad + $data['cantidad'];
                $actualizar->cantidadlitros = $actualizar->cantidadlitros + $data['cantidadlitros'];
                $mov->tipo = 'INCREMENTO';
            } else if ($request['movimiento'] == 'MODIFICACION') {
                $actualizar->cantidad =  $data['cantidad'];
                $actualizar->cantidadlitros =  $data['cantidadlitros'];
                $mov->tipo = 'MODIFICACION';
            } else {
                $actualizar->cantidad = $actualizar->cantidad - $data['cantidad'];
                $actualizar->cantidadlitros = $actualizar->cantidadlitros - $data['cantidadlitros'];
                $mov->tipo = $request['movimiento'];
            }
            $actualizar->save();
            $mov->inventario_id = $actualizar->id;
        } else {
            $inventario = Inventario::create($data);
            $mov->tipo = 'ALTA';
            $mov->inventario_id = $inventario->id;
        }

        $mov->observaciones = $request->get('observaciones');
        $mov->cantidad = $data['cantidad'];
        $mov->cantidadlitros = $data['cantidadlitros'];
        $mov->fecha = now();
        $mov->user_id = auth()->user()->id;
        $mov->save();

        return (['message' => 'guardado']);
    }
}
