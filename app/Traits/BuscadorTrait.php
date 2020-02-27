<?php

namespace App\Traits;

use App\Role;
use App\User;
use App\Cliente;
use App\Articulo;
use App\Supplier;

trait BuscadorTrait
{
    public static function buscando($request)
    {
        $buscar = $request->get('buscar');

        $dependencias = collect();

        $users = User::where('name', 'LIKE', "$buscar%")->get()->where('role_id', '>', 2)->where('role_id', '<>', 5);
        foreach ($users as $user) {
            $user->rol = Role::find($user->role_id);
        }
        $dependencias->push($users);

        $distribuidores = Cliente::orWhere('razonsocial', 'LIKE', "$buscar%")->orWhere('documentounico', 'LIKE', "$buscar%")->get()->where('distribuidor', true);
        $dependencias->push($distribuidores);

        switch (auth()->user()->role_id) {
            case 1:
                $aux = Cliente::orWhere('razonsocial', 'LIKE', "$buscar%")->orWhere('documentounico', 'LIKE', "$buscar%")->get();

                return [
                    'clientes' => $aux->where('distribuidor', false),
                    'proveedores' => Supplier::orWhere('razonsocial', 'LIKE', "$buscar%")->orWhere('cuit', 'LIKE', "$buscar%")->get(),
                    'articulos' => Articulo::orWhere('articulo', 'LIKE', "$buscar%")->orWhere('codarticulo', "$buscar%")->get(),
                    'dependencias' => $dependencias->flatten()
                ];
                break;

            case 2:
                if ($request->exists('nuevoComp')) {
                    $user = User::find(auth()->user()->id);
                    $aux = Cliente::orWhere('razonsocial', 'LIKE', "$buscar%")->orWhere('documentounico', 'LIKE', "$buscar%")->get()->where('distribuidor', false);
                } else {
                    $aux = Cliente::orWhere('razonsocial', 'LIKE', "$buscar%")->orWhere('documentounico', 'LIKE', "$buscar%")->get()->where('distribuidor', false);
                }
                return [
                    'clientes' => $aux->where('distribuidor', false),
                    'proveedores' => Supplier::orWhere('razonsocial', 'LIKE', "$buscar%")->orWhere('cuit', 'LIKE', "$buscar%")->get(),
                    'articulos' => Articulo::orWhere('articulo', 'LIKE', "$buscar%")->orWhere('codarticulo', "$buscar%")->get(),
                    'dependencias' => $dependencias->flatten()
                ];
                break;

            case 3:
                $user = User::find(auth()->user()->id);
                $clientes = $user->clientes()->where('razonsocial', 'LIKE', "$buscar%")->get();
                return [
                    'clientes' => $clientes
                ];
                break;
        }
    }
}
