<?php

namespace App\Traits;

use App\User;
use App\Articulo;
use App\Inventario;
use App\Traits\FotosTrait;
use App\Traits\MarcasTrait;
use App\Traits\CategoriasTrait;
use App\Traits\InventariosAdmin;
use Illuminate\Support\Facades\DB;
use App\Notifications\ArticuloNotification;

trait ArticulosTrait
{
    public static function index($request)
    {
        $articles = Articulo::with(['inventarios'])->take($request->get('limit'), null)->get();
        $articulos = collect();
        $inventarios = collect();

        foreach ($articles as $art) {

            if (auth()->user()->role->role == 'superAdmin' || auth()->user()->role->role == 'administrador') {

                if ($art->inventarios->count() > 0) {
                    $stock = $art->inventarios[0]['cantidadlitros'];
                    $inventarios = $art->inventarios[0];
                } else {
                    $inventarios = [];
                    $stock = 0;
                }
                $art = collect($art);
                $art->put('stock', $stock);
                $articulos->push($art);
            } else {
                foreach ($art->inventarios as $inv) {
                    if ($inv->dependencia == auth()->user()->id) {
                        $inventarios->push($inv);
                        $stock = $inv['cantidadlitros'];
                        $art = collect($art);
                        $art->put('stock', $stock);
                        $articulos->push($art);
                    }
                }
            }

            $art['inventarios'] = $inventarios;
        }

        return [
            'articulos' => $articulos,
            'total' => Articulo::withTrashed()->count(),
            'ultimo' => $articulos->first(),
        ];
    }

    public static function store($request)
    {
        try {
            DB::transaction(function () use ($request) {
                $stockInicial = $request['stockInicial'];

                $foto = FotosTrait::store($request, $ubicacion = 'articulos');

                $data = $request;

                $marca_id = MarcasTrait::marcas($data['marca']);

                $categoria_id = CategoriasTrait::categorias($data['categoria']);

                $data = $request->validated();
                $data['marca_id'] = $marca_id;
                $data['categoria_id'] = $categoria_id;
                $data['foto'] = $foto;

                $articulo = Articulo::create($data);

                if ($stockInicial) {
                    $data = [
                        'cantidad' => $stockInicial,
                        'cantidadlitros' => $stockInicial * $articulo->litros,
                        // 'lote' => 1,
                        'articulo_id' => $articulo->id,
                        'supplier_id' => 1
                    ];
                    $inventario = InventariosAdmin::altaInventario($data);

                    $datos = [
                        'inventario_id' => $inventario->id,
                        'cantidad' => $inventario->cantidad,
                        'cantidadlitros' => $inventario->cantidadlitros,
                    ];
                    InventariosAdmin::movimientoAlta($inventario, $datos);
                } else {
                    static::crearNotificacion($articulo);
                }
            });
            return response()->json('creado');
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function update($request, $id)
    {
        try {
            DB::transaction(function () use ($request, $id) {
                $articulo = Articulo::find($id);

                $foto = FotosTrait::update($request, $ubicacion = 'articulos', $articulo);

                $data = $request;

                if (gettype($data['marca']) == 'array') {
                    $data['marca'] = $data['marca']['marca'];
                }

                $marca_id = MarcasTrait::marcas($data['marca']);

                if (gettype($data['categoria']) == 'array') {
                    $data['categoria'] = $data['categoria']['categoria'];
                }

                $categoria_id = CategoriasTrait::categorias($data['categoria']);

                $data = $request->validated();
                $data['marca_id'] = $marca_id;
                $data['categoria_id'] = $categoria_id;
                $data['foto'] = $foto;

                $articulo->update($data);
            });
            return response()->json('actualizado');
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function show($id)
    {
        $articulo = Articulo::find($id);
        $marca = $articulo->marca->marca;
        $categoria = $articulo->categoria->categoria;
        $stock = $articulo->inventarios->sum('cantidad');
        $inventarios = $articulo->inventarios;
        $aux = collect();
        foreach ($inventarios as $inventario) {
            $inv = collect($inventario);
            if ($inventario->supplier_id) {
                $inv->put('dependencia', $inventario->proveedor);
            } else {
                $user = User::find($inventario->dependencia);
                $user->role;
                $inv->put('dependencia', $user);
            }
            $aux->push($inv);
        }
        return ['articulo' => $articulo, 'stock' => $stock, 'inventarios' => $aux, 'marca' => $marca, 'categoria' => $categoria];
    }

    public static function crearNotificacion($articulo)
    {
        $user = User::findOrFail(auth()->user()->id);

        if ($user->role->role == 'administrador') {
            $user->notify(new ArticuloNotification($articulo));
        } else {
            $tiene = Inventario::where('dependencia', $user->id)->where('articulo_id', $articulo->id)->get();
            if ($tiene) {
                $user->notify(new ArticuloNotification($articulo));
            }
        }
    }
}
