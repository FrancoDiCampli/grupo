<?php

namespace App\Http\Controllers\API;

use App\Contacto;
use App\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
        $suppliers = Supplier::orderBy('razonsocial', 'asc')
            ->buscar($request);

        return [
            'proveedores' => $suppliers->take($request->get('limit', null))->get(),
            'total' => $suppliers->count(),
        ];
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'razonsocial' => 'required|unique:suppliers|min:1|max:190',
            'cuit' => 'required|unique:suppliers|min:11|max:11',
            'direccion' => 'required|min:1|max:190',
            'telefono' => 'required|min:6|max:13',
            'email' => 'required|email',
            'codigopostal' => 'required|min:4|max:4',
            'localidad' => 'required|min:1|max:190',
            'provincia' => 'required|min:1|max:190',
            'observaciones' => 'nullable'
        ]);

        $supplier = Supplier::create($data);

        if ($request['contactos']) {
            foreach ($request['contactos'] as $tel) {
                Contacto::create([
                    'nombre' => $tel['nombre'],
                    'numero' => $tel['numero'],
                    'email' => $tel['email'],
                    'cargo' => $tel['cargo'],
                    'referencia' => 'PR' . $supplier->id,
                ]);
            }
        }

        return $supplier;
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);

        $data = $request->validate([
            'razonsocial' => 'required|min:1|max:190|unique:suppliers,razonsocial,' . $supplier->id,
            'cuit' => 'required|min:11|max:11|unique:suppliers,cuit,' . $supplier->id,
            'direccion' => 'required|min:1|max:190',
            'telefono' => 'required|min:6|max:13',
            'email' => 'required|email',
            'codigopostal' => 'required|min:4|max:4',
            'localidad' => 'required|min:1|max:190',
            'provincia' => 'required|min:1|max:190',
            'observaciones' => 'nullable'
        ]);

        $supplier->update($data);

        $nuevosContactos = $request['contactos'];
        $contactosEliminados = [];
        if ($request['eliminados']) {
            $contactosEliminados = $request['eliminados'];
        }

        // Agregar nuevos
        foreach ($nuevosContactos as $tel) {
            if (!array_key_exists('id', $tel)) {
                Contacto::create([
                    'nombre' => $tel['nombre'],
                    'numero' => $tel['numero'],
                    'email' => $tel['email'],
                    'cargo' => $tel['cargo'],
                    'referencia' => 'PR' . $supplier->id,
                ]);
            }
        }

        // Eliminar
        if (count($contactosEliminados) > 0) {
            foreach ($contactosEliminados as $tel) {
                Contacto::destroy($tel['id']);
            }
        }
    }

    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return ['message' => 'eliminado'];
    }

    public function contactos($supplier)
    {
        $referencia = 'PR' . $supplier->id;
        return Contacto::where('referencia', $referencia)->get();
    }

    public function show($id)
    {
        $supplier = Supplier::find($id);
        $contactos = $this->contactos($supplier);
        $remitos = $supplier->remitos;
        foreach ($remitos as $remito) {
            $fecha = new Carbon($remito->fecha);
            $remito->fecha = $fecha->format('d-m-Y');
        }

        return ['proveedor' => $supplier, 'remitos' => $remitos, 'contactos' => $contactos];
    }

    public function restaurar($id)
    {
        Supplier::withTrashed()->find($id)->restore();
        return Supplier::find($id);
    }
}
