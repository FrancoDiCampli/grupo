<?php

namespace App\Http\Middleware;

use Closure;
use App\Role;
use App\User;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $scope)
    {

        $user = User::find(auth()->user()->id);
        $rol = Role::find($user->role_id);
        $permissions = explode(" ", $rol->permission);

        if (in_array($scope, $permissions)) {
            return $next($request);
        } else {
            return response()->json('forbidden', 403);
        }
    }
}
