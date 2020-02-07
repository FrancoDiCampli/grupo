<?php

namespace App\Http\Controllers\API;

use App\Role;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UsersController extends Controller
{
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
                $negocio = $usuario->negocio;
                $usuario = collect($usuario);
                $usuario->put('negocio', $negocio);
                $usuario->put('rol', $aux);
                $data->push($usuario);
            }
            return $data;
        } else {
            $users = User::where('id', '!=', 1)->get();
            $data = collect();
            foreach ($users as $usuario) {
                if ($usuario->role_id != null) {
                    $aux = $usuario->role->role;
                } else {
                    $aux = null;
                }
                $negocio = $usuario->negocio;
                $usuario = collect($usuario);
                $usuario->put('negocio', $negocio);
                $usuario->put('rol', $aux);
                $data->push($usuario);
            }
            return $data;
        }
    }

    public function store(Request $request)
    {
        if ($request->password == $request->password_confirm) {
            $attributes = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
                'role_id' => 'nullable',
                'negocio_id' => 'required'
            ]);
            $attributes['password'] = bcrypt($attributes['password']);
            $attributes['role_id'] = $request->get('role_id', 3);
            User::create($attributes);
        }
    }

    public function show($id)
    {
        return $user = User::find($id);
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

    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();
    }
}
