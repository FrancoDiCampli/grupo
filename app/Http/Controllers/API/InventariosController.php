<?php

namespace App\Http\Controllers\API;

use App\Supplier;
use App\Inventario;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\InventariosAdmin;

class InventariosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:airlock');

        $this->middleware('scope:inventarios-index')->only('index');
        $this->middleware('scope:inventarios-store')->only('store');
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

    // Consignacion de Inventarios para Distribuidores

    public function nuevo(Request $request)
    {
        // return $request;
        $inventario = InventariosAdmin::altaInventario($request);
        InventariosAdmin::movimientoAlta($inventario, $request);
    }

    public function devolucion(Request $request)
    {
        $data = $request->datos;
        $origen  =  InventariosAdmin::decrementarInventario($data);
        $data['cantidadlitros'] = $origen->articulo->litros * $data['cantidad'];
        InventariosAdmin::movimientoBajaDevolucion($data);

        $inventario = $origen->articulo->inventarios->first();
        $destino = Inventario::findOrFail($inventario['id']);

        $data['inventario_id'] = $destino->id;
        $data['origen'] = $origen->id;

        InventariosAdmin::incrementarInventario($data);
        InventariosAdmin::movimientoAltaDevolucion($data);
    }

    // CREA INVENTARIO DE NO EXISTIR, CASO CONTRARIO LO ACTUALIZA
    public function store(Request $request)
    {
        $data = $request;

        if (!array_key_exists('dependencia', $request->toArray())) {
            $actualizar = Inventario::where('articulo_id', $data['articulo_id'])->get()->first();

            if ($actualizar) {
                return InventariosAdmin::actualizarCasaCentral($actualizar, $data);
            } else {
                $inventario = InventariosAdmin::altaInventario($data);
                InventariosAdmin::movimientoAlta($inventario, $data);
            }
        } else {
            $actualizar = Inventario::where('dependencia', $data['dependencia'])->where('articulo_id', $data['articulo_id'])->get()->first();
            // return $actualizar;
            if ($actualizar) {

                InventariosAdmin::actualizarInventario($data, $actualizar);
                InventariosAdmin::decrementarInventario($data);

                InventariosAdmin::movimientoIncrementoConsignacion($data);
                InventariosAdmin::movimientoBajaConsignacion($data);
            } else {
                $inventario = InventariosAdmin::altaConsignacion($data);
                InventariosAdmin::movimientoAltaConsignacion($inventario, $data);

                InventariosAdmin::decrementarInventario($data);
                InventariosAdmin::movimientoBajaConsignacion($data);
            };

            return (['message' => 'guardado']);
        }
    }
}
