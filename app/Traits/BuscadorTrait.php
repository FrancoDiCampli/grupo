<?php

namespace App\Traits;

use App\User;
use App\Cliente;
use App\Articulo;
use App\Supplier;

trait BuscadorTrait
{
    public static function buscando($request)
    {
        $buscar = $request->get('buscar');

        $vendedores = collect();
        $distribuidores = collect();

        $users = User::where('name', 'LIKE', "$buscar%")->get();
        $usuarios = collect();
        foreach ($users as $user) {
            if ($user->role->role == 'vendedor') {
                $usuarios->push($user);
            }
        }
        $vendedores->push($usuarios);

        $distributors = Cliente::orWhere('razonsocial', 'LIKE', "$buscar%")
            ->orWhere('documentounico', 'LIKE', "$buscar%")
            ->get()
            ->where('distribuidor', true);
        $distribuidores->push($distributors);

        switch (auth()->user()->role->role) {
            case 'superAdmin':
                $aux = Cliente::orWhere('razonsocial', 'LIKE', "$buscar%")
                    ->orWhere('documentounico', 'LIKE', "$buscar%")
                    ->get();

                return [
                    'clientes' => $aux->where('distribuidor', false),
                    'proveedores' => Supplier::orWhere('razonsocial', 'LIKE', "$buscar%")
                        ->orWhere('cuit', 'LIKE', "$buscar%")
                        ->get(),
                    'articulos' => Articulo::orWhere('articulo', 'LIKE', "$buscar%")
                        ->orWhere('codarticulo', "$buscar%")
                        ->get(),
                    'distribuidores' => $distribuidores->flatten(),
                    'vendedores' => $vendedores->flatten()
                ];
                break;

            case 'administrador':
                if ($request->exists('nuevoComp')) {
                    $user = User::find(auth()->user()->id);
                    $aux = Cliente::orWhere('razonsocial', 'LIKE', "$buscar%")
                        ->orWhere('documentounico', 'LIKE', "$buscar%")
                        ->get()
                        ->where('distribuidor', false);
                } else {
                    $aux = Cliente::orWhere('razonsocial', 'LIKE', "$buscar%")
                        ->orWhere('documentounico', 'LIKE', "$buscar%")
                        ->get()
                        ->where('distribuidor', false);
                }
                return [
                    'clientes' => $aux->where('distribuidor', false),
                    'proveedores' => Supplier::orWhere('razonsocial', 'LIKE', "$buscar%")
                        ->orWhere('cuit', 'LIKE', "$buscar%")
                        ->get(),
                    'articulos' => Articulo::orWhere('articulo', 'LIKE', "$buscar%")
                        ->orWhere('codarticulo', "$buscar%")
                        ->get(),
                    'distribuidores' => $distribuidores->flatten(),
                    'vendedores' => $vendedores->flatten()
                ];
                break;

            case 'vendedor':
                $user = User::find(auth()->user()->id);
                $clientes = $user->clientes()->where('razonsocial', 'LIKE', "$buscar%")
                    ->get()
                    ->where('distribuidor', false);
                return [
                    'clientes' => $clientes
                ];
                break;
        }
    }
}
