<?php

namespace App\Traits;

use App\Inventario;
use App\User;
use App\Notifications\ArticuloNotification;

trait ArticulosNotificacionesTrait
{
    public static function crearNotificacion($articulo)
    {
        $user = User::findOrFail(auth()->user()->id);

        if ($user->role_id == 2) {
            $user->notify(new ArticuloNotification($articulo));
        } else {
            $tiene = Inventario::where('dependencia', $user->id)->where('articulo_id', $articulo->id)->get();
            if ($tiene) {
                $user->notify(new ArticuloNotification($articulo));
            }
        }
    }
}
