<?php

namespace App\Http\Controllers\API;

use App\User;
use App\Contacto;
use Carbon\Carbon;
use App\Distributor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DistributorsController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:airlock');

        // $this->middleware('scope:distributors-index')->only('index');
        // $this->middleware('scope:distributors-show')->only('show');
        // $this->middleware('scope:distributors-store')->only('store');
        // $this->middleware('scope:distributors-update')->only('update');
        // $this->middleware('scope:distributors-destroy')->only('destroy');
    }

    public function index(Request $request)
    {
        $distributors = Distributor::orderBy('razonsocial', 'asc')
            ->buscar($request);

        return [
            'distribuidores' => $distributors->take($request->get('limit', null))->get(),
            'total' => $distributors->count(),
        ];
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'razonsocial' => 'required|min:1|max:190',
            'documentounico' => 'required|min:8|max:11|unique:distributors',
            'direccion' => 'required|min:1|max:190',
            'email' => 'required|email',
            'telefono' => 'required|min:6|max:13',
            'codigopostal' => 'required|min:4|max:4',
            'localidad' => 'required|min:1|max:190',
            'provincia' => 'required|min:1|max:190',
            'condicioniva' => 'required|min:1|max:190',
            'observaciones' => 'nullable',
            'foto' => 'nullable'
        ]);

        $distributor = Distributor::create($data);

        if ($request['contactos']) {
            foreach ($request['contactos'] as $tel) {
                Contacto::create([
                    'nombre' => $tel['nombre'],
                    'numero' => $tel['numero'],
                    'email' => $tel['email'],
                    'cargo' => $tel['cargo'],
                    'referencia' => 'DI' . $distributor->id,
                ]);
            }
        }

        if ($request->password == $request->confirm_password) {
            $user = User::create([
                'name' => $data['razonsocial'],
                'email' => $data['email'],
                'password' => bcrypt($request->password),
                'role_id' => 4,
                'distributor_id'  => $distributor->id
            ]);

            // $user->notify(new Verificar($user));
        } else {
            return response()->json('Las contraseñas no coinciden', 422);
        }

        return ['message' => 'guardado'];
    }

    public function update(Request $request, $id)
    {
        $distributor = Distributor::findOrFail($id);

        $data = $request->validate([
            'razonsocial' => 'required|min:1|max:190',
            'documentounico' => 'required|min:8|max:11|unique:distributors,documentounico,' . $this->distributor,
            'direccion' => 'required|min:1|max:190',
            'email' => 'required|email',
            'telefono' => 'required|min:6|max:13',
            'codigopostal' => 'required|min:4|max:4',
            'localidad' => 'required|min:1|max:190',
            'provincia' => 'required|min:1|max:190',
            'condicioniva' => 'required|min:1|max:190',
            'observaciones' => 'nullable',
            'foto' => 'nullable'
        ]);

        $distributor->update($data);

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
                    'referencia' => 'DI' . $distributor->id,
                ]);
            }
        }

        // Eliminar
        if (count($contactosEliminados) > 0) {
            foreach ($contactosEliminados as $tel) {
                Contacto::destroy($tel['id']);
            }
        }

        if ($request->password) {
            if ($request->password == $request->confirm_password) {
                $user = $distributor->user;
                $user->password =  bcrypt($request->password);
                $user->save();
            }
        }
    }

    public function destroy($id)
    {
        $distributor = Distributor::findOrFail($id);
        $distributor->user->delete();
        $distributor->delete();

        return ['message' => 'eliminado'];
    }

    public function contactos($distributor)
    {
        $referencia = 'DI' . $distributor->id;
        return Contacto::where('referencia', $referencia)->get();
    }

    public function show($id)
    {
        $distribuidor = Distributor::find($id);
        $inventarios = $distribuidor->inventarios;
        $distribuidor->inventarios->each->proveedor;
        $distribuidor->inventarios->each->articulo;
        $usuario = $distribuidor->user;
        $usuario->role;
        $clientes = $usuario->clientes;
        $ventas = $usuario->ventas;

        return [
            'distribuidor' => $distribuidor,
            'usuario' => $usuario,
            'clientes' => $clientes,
            'inventarios' => $inventarios,
            'ventas' => $ventas
        ];
    }

    public function restaurar($id)
    {
        $distributor = Distributor::withTrashed()->find($id);
        $distributor->user()->restore();
        $distributor->restore();
        return $distributor;
    }
}
