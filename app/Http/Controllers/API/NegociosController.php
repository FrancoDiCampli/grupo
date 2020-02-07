<?php

namespace App\Http\Controllers\API;

use App\Cliente;
use App\Negocio;
use App\Contacto;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NegociosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $negocios = Negocio::orderBy('razonsocial', 'asc');

        return [
            'negocios' => $negocios->take($request->get('limit', null))->get(),
            'total' => $negocios->count(),
        ];
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'razonsocial' => 'required|unique:negocios|min:1|max:190',
            'cuit' => 'required|unique:negocios|min:11|max:11',
            'direccion' => 'required|min:1|max:190',
            'telefono' => 'required|min:6|max:13',
            'email' => 'required|email',
            'codigopostal' => 'required|min:4|max:4',
            'localidad' => 'required|min:1|max:190',
            'provincia' => 'required|min:1|max:190',
            'observaciones' => 'nullable'
        ]);

        $negocio = Negocio::create($data);

        if ($request['contactos']) {
            foreach ($request['contactos'] as $tel) {
                Contacto::create([
                    'nombre' => $tel['nombre'],
                    'numero' => $tel['numero'],
                    'email' => $tel['email'],
                    'cargo' => $tel['cargo'],
                    'referencia' => 'NE' . $negocio->id,
                ]);
            }
        }

        return $negocio;
    }

    public function show($id)
    {
        $negocio = Negocio::find($id);
        $contactos = $this->contactos($negocio);
        $inventarios = $negocio->inventarios;
        $negocio->inventarios->each->proveedor;
        $negocio->inventarios->each->articulo;
        $usuarios = $negocio->usuarios;
        $usuarios->each->role;
        $clientes = collect();
        $ventas = collect();

        foreach ($usuarios as $user) {
            $clientes->push(Cliente::where('user_id', $user->id)->get());
            $ventas->push($user->ventas);
        }

        return [
            'negocio' => $negocio,
            'contactos' => $contactos,
            'usuarios' => $usuarios,
            'clientes' => $clientes->flatten(),
            'inventarios' => $inventarios,
            'ventas' => $ventas->flatten(),
        ];
    }

    public function update(Request $request, $id)
    {
        $negocio = Negocio::findOrFail($id);

        $data = $request->validate([
            'razonsocial' => 'required|min:1|max:190|unique:negocios,razonsocial,' . $negocio->id,
            'cuit' => 'required|min:11|max:11|unique:negocios,cuit,' . $negocio->id,
            'direccion' => 'required|min:1|max:190',
            'telefono' => 'required|min:6|max:13',
            'email' => 'required|email',
            'codigopostal' => 'required|min:4|max:4',
            'localidad' => 'required|min:1|max:190',
            'provincia' => 'required|min:1|max:190',
            'observaciones' => 'nullable'
        ]);

        $negocio->update($data);

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
                    'referencia' => 'NE' . $negocio->id,
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
        $negocio = Negocio::findOrFail($id);
        $negocio->delete();

        return ['message' => 'eliminado'];
    }

    public function contactos($negocio)
    {
        $referencia = 'NE' . $negocio->id;
        return Contacto::where('referencia', $referencia)->get();
    }

    public function restaurar($id)
    {
        Negocio::withTrashed()->find($id)->restore();
        return Negocio::find($id);
    }
}
