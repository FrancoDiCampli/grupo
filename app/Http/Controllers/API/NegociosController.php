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
        // $this->middleware('auth');

        // $this->middleware('scope:negocios-index')->only('index');
        // $this->middleware('scope:negocios-show')->only('show');
        // $this->middleware('scope:negocios-store')->only('store');
        // $this->middleware('scope:negocios-update')->only('update');
        // $this->middleware('scope:negocios-destroy')->only('destroy');
    }

    public function index(Request $request)
    {
        $negocios = Negocio::orderBy('nombre', 'asc');

        return [
            'negocios' => $negocios->take($request->get('limit', null))->get(),
            'total' => $negocios->count(),
        ];
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|unique:negocios|min:1|max:190',
            'direccion' => 'required|min:1|max:190',
            'codigopostal' => 'required|min:4|max:4',
            'localidad' => 'required|min:1|max:190',
            'provincia' => 'required|min:1|max:190',
            'observaciones' => 'nullable'
        ]);

        $negocio = Negocio::create($data);

        return $negocio;
    }

    public function show($id)
    {
        $negocio = Negocio::find($id);
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
            'nombre' => 'required|min:1|max:190|unique:negocios,nombre,' . $negocio->id,
            'direccion' => 'required|min:1|max:190',
            'codigopostal' => 'required|min:4|max:4',
            'localidad' => 'required|min:1|max:190',
            'provincia' => 'required|min:1|max:190',
            'observaciones' => 'nullable'
        ]);

        $negocio->update($data);

        return response()->json('Negocio actualizado', 200);
    }

    public function destroy($id)
    {
        $negocio = Negocio::findOrFail($id);
        $negocio->delete();

        return ['message' => 'eliminado'];
    }

    public function restaurar($id)
    {
        Negocio::withTrashed()->find($id)->restore();
        return Negocio::find($id);
    }
}
