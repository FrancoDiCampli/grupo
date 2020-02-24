<?php

namespace App\Http\Controllers\API;

use App\Articulo;
use App\Supplier;
use App\Inventario;
use App\Movimiento;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInventario;
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

    public function update(Request $request, $id)
    {
        $inventario = Inventario::findOrFail($id);

        $inventario->cantidad += $request['cantidad'];
        $inventario->cantidadlitros += $request['cantidadLitros'];
        $inventario->save();

        $movInc = Movimiento::create([
            'tipo' => 'INCREMENTO',
            'inventario_id' => $inventario->id,
            'cantidad' => $request['cantidad'],
            'cantidadlitros' => $request['cantidadLitros'],
            'fecha' => now(),
            'user_id' => auth()->user()->id
        ]);

        return 'actualizado';
    }
}
