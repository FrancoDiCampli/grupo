<?php

namespace App\Http\Controllers\API;

use App\Role;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;

class UsersController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:airlock');

        // $this->middleware('scope:users-index')->only('index');
        // $this->middleware('scope:users-store')->only('store');
        // $this->middleware('scope:users-update')->only('update');
        // $this->middleware('scope:users-destroy')->only('destroy');
    }

    public function user()
    {
        $user = auth()->user();

        if ($user->role_id) {
            $rol = Role::where('id', $user->role_id)->get()[0];
            $permission = explode(" ", $rol->permission);
            return [
                'user' => $user,
                'rol' => $rol->role,
                'permissions' => $permission
            ];
        } else {
            return [
                'user' => $user,
                'rol' => '',
                'permissions' => []
            ];
        }
    }

    public function index(Request $request)
    {
        $user = User::find(auth()->user()->id);
        $rol = $user->role_id;
        if ($rol == 1) {
            $users = User::all();
            $data = collect();
            foreach ($users as $usuario) {
                if ($usuario->role_id != null) {
                    $aux = $usuario->role->role;
                } else {
                    $aux = null;
                }
                $distributor = $usuario->distributor;
                $usuario = collect($usuario);
                $usuario->put('distributor', $distributor);
                $usuario->put('rol', $aux);
                $data->push($usuario);
            }
        } else {
            $users = User::where('id', '!=', 1)->get();
            $data = collect();
            foreach ($users as $usuario) {
                if ($usuario->role_id != null) {
                    $aux = $usuario->role->role;
                } else {
                    $aux = null;
                }
                $distributor = $usuario->distributor;
                $usuario = collect($usuario);
                $usuario->put('distributor', $distributor);
                $usuario->put('rol', $aux);
                $data->push($usuario);
            }
        }

        return [
            'users' => $data->take($request->get('limit', null)),
            'total' => $data->count(),
        ];
    }

    public function store(Request $request)
    {
        if ($request->password == $request->password_confirm) {
            $attributes = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
                'role_id' => 'nullable',
                // 'distributor_id' => 'required'
            ]);
            $attributes['password'] = bcrypt($attributes['password']);
            $attributes['role_id'] = $request->get('role_id', 3);
            User::create($attributes);
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users,email,' . $user->id,
        ]);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role_id = $request->role_id;
        if ($request->password) {
            if ($request->password == $request->password_confirm) {
                $user->password =  bcrypt($request->password);
            }
        }
        $user->save();
    }

    public function updateAccount(Request $request)
    {
        $user = User::find(auth()->user()->id);

        if ($request->get('newFoto')) {
            $eliminar = $user->foto;
            if ($eliminar) {
                @unlink(public_path($eliminar));
            }
            $image = $request->get('newFoto');
            $name = time() . '.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
            Image::make($request->get('newFoto'))->save(public_path('img/usuarios/') . $name);
            $foto = '/img/usuarios/' . $name;
            $user->foto = $foto;
        }

        if ($request->current_password) {
            if (Hash::check($request->current_password, auth()->user()->password)) {

                $request->validate([
                    'name' => 'required|string|max:255',
                    'password' => 'required|string|min:6',
                    'confirm_password' => 'required|same:password',
                    'email' => 'required|string|max:255|unique:users,email,' . $user->id,
                ]);

                $user->name = $request->name;
                $user->email = $request->email;
                $user->password =  bcrypt($request->password);
                $user->save();

                return response()->json('ok', 200);
            } else {
                return response()->json('Contraseña Incorrecta', 401);
            }
        } else {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|max:255|unique:users,email,' . $user->id,
            ]);

            $user->name = $request->name;
            $user->email = $request->email;
            $user->save();

            return response()->json('ok', 200);
        }
    }

    public function destroy($id)
    {
        if ($id != 1) {
            $user = User::find($id);
            $user->delete();
        }
    }
}
