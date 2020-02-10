<?php

namespace App\Http\Controllers\API;

use App\User;
use App\Cliente;
use App\Articulo;
use App\Supplier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BuscadorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:airlock');
    }

    public function buscando(Request $request)
    {
        $buscar = $request->get('buscar');

        switch (auth()->user()->role_id) {
            case 1:
                $aux = Cliente::orWhere('razonsocial', 'LIKE', "$buscar%")->orWhere('documentounico', 'LIKE', "$buscar%")->get();
                return [
                    'clientes' => $aux->where('activo', true),
                    'proveedores' => Supplier::orWhere('razonsocial', 'LIKE', "$buscar%")->orWhere('cuit', 'LIKE', "$buscar%")->get(),
                    'articulos' => Articulo::orWhere('articulo', 'LIKE', "$buscar%")->orWhere('codarticulo', "$buscar%")->get(),
                    'usuarios' => User::orWhere('name', 'LIKE', "$buscar%")->orWhere('email', "$buscar%")->get()
                ];
                break;

            case 2:
                if ($request->exists('nuevoComp')) {
                    $user = User::find(auth()->user()->id);
                    $aux = Cliente::orWhere('razonsocial', 'LIKE', "$buscar%")->orWhere('documentounico', 'LIKE', "$buscar%")->get();
                } else {
                    $aux = Cliente::orWhere('razonsocial', 'LIKE', "$buscar%")->orWhere('documentounico', 'LIKE', "$buscar%")->get();
                }
                return [
                    'clientes' => $aux->where('activo', true),
                    'proveedores' => Supplier::orWhere('razonsocial', 'LIKE', "$buscar%")->orWhere('cuit', 'LIKE', "$buscar%")->get(),
                    'articulos' => Articulo::orWhere('articulo', 'LIKE', "$buscar%")->orWhere('codarticulo', "$buscar%")->get(),
                    'usuarios' => User::orWhere('name', 'LIKE', "$buscar%")->orWhere('email', "$buscar%")->get()
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
