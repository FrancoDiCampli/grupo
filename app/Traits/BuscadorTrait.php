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

        $auxClientes = Cliente::select('id', 'razonsocial')->orWhere('razonsocial', 'LIKE', "%$buscar%")
            ->orWhere('documentounico', 'LIKE', "%$buscar%")
            ->get();

        $auxProveedores = Supplier::select('id', 'razonsocial')->orWhere('razonsocial', 'LIKE', "%$buscar%")
            ->orWhere('cuit', 'LIKE', "%$buscar%")
            ->get();

        $auxArticulos = Articulo::select('id', 'articulo')->orWhere('articulo', 'LIKE', "%$buscar%")
            ->orWhere('codarticulo', "%$buscar%")
            ->get();

        $vendedores = collect();
        $distribuidores = collect();

        $users = User::where('name', 'LIKE', "%$buscar%")->get();
        $usuarios = collect();
        foreach ($users as $user) {
            if ($user->role->role == 'vendedor') {
                $usuarios->push($user);
            }
        }
        $vendedores->push($usuarios);

        $distributors = $auxClientes->where('distribuidor', true);
        $distribuidores->push($distributors);

        switch (auth()->user()->role->role) {
            case 'superAdmin':
                response()->json(
                    [
                        'clientes' => $auxClientes->where('distribuidor', false),
                        'proveedores' => $auxProveedores,
                        'articulos' => $auxArticulos,
                        'distribuidores' => $distribuidores->flatten(),
                        'vendedores' => $vendedores->flatten()
                    ]
                );

                break;

            case 'administrador':
                return response()->json([
                    'clientes' => $auxClientes->where('distribuidor', false),
                    'proveedores' => $auxProveedores,
                    'articulos' => $auxArticulos,
                    'distribuidores' => $distribuidores->flatten(),
                    'vendedores' => $vendedores->flatten()
                ]);
                break;

            case 'vendedor':
                $user = User::find(auth()->user()->id);
                $clientes = $user->clientes()->where('razonsocial', 'LIKE', "%$buscar%")
                    ->get()
                    ->where('distribuidor', false);
                return response()->json([
                    'clientes' => $clientes->where('distribuidor', false),
                    'articulos' => $auxArticulos
                ]);
                break;
        }
    }
}
