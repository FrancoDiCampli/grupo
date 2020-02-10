<?php

namespace App\Http\Controllers\API;

use App\Role;
use App\User;
use App\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RolesController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:airlock');

        $this->middleware('scope:roles-index')->only('index');
        $this->middleware('scope:roles-store')->only('store');
        $this->middleware('scope:roles-update')->only('update');
        $this->middleware('scope:roles-destroy')->only('destroy');
    }

    public function index(Request $request)
    {
        $user = User::find(auth()->user()->id);
        if ($user->role_id == '1') {
            $data = Role::get();
        } else {
            $data = Role::where('role', '!=', 'superAdmin')->get();
        }

        return [
            'roles' => $data->take($request->get('limit', null)),
            'total' => $data->count(),
        ];
    }

    public function store(Request $request)
    {
        $attributes = $request->validate([
            'role' => 'required',
            'permission' => 'required',
            'description' => 'required'
        ]);
        Role::create($attributes);
    }

    public function permissions()
    {
        return Permission::permissions();
    }

    public function update(Request $request, $id)
    {
        $rol = Role::find($id);
        $attributes = $request->validate([
            'role' => 'required',
            'permission' => 'required',
            'description' => 'required'
        ]);
        $rol->update($attributes);
    }

    public function destroy($id)
    {
        if ($id != 1) {
            $users = User::where('role_id', $id)->get();
            foreach ($users as $user) {
                $user->update([
                    'role_id' => null,
                ]);
            }
            $rol = Role::find($id);
            $rol->delete();
        }
    }
}
