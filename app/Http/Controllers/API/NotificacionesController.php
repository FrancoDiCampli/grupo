<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificacionesController extends Controller
{
    public function index(Request $request)
    {
        $noLeidas = auth()->user()->unreadNotifications()->get();
        $noLeidas->each(function ($notif) {
            $notif['creada'] = $notif->created_at->diffForHumans();
        });
        $leidas = auth()->user()->readNotifications()->get();
        $leidas->each(function ($notif) {
            $notif['creada'] = $notif->created_at->diffForHumans();
        });
        $notificaciones = collect();
        $notificaciones->put('noLeidas', $noLeidas);
        $notificaciones->put('leidas', $leidas->take($request->get('limit', null)));
        $notificaciones->put('totalLeidas', $leidas->count());


        return ['notificaciones' => $notificaciones];
    }

    public function show($id)
    {
        auth()->user()->notifications()->find($id)->markAsRead();

        return 'leida';
    }
}
