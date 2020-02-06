<?php

namespace App\Http\Controllers\API;

use App\Role;
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
}
