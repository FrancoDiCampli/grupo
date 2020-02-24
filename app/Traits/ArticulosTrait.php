<?php

namespace App\Traits;

use App\Articulo;
use App\Traits\FotosTrait;
use App\Traits\MarcasTrait;
use App\Traits\CategoriasTrait;
use App\Traits\InventariosAdmin;
use App\Traits\ArticulosNotificacionesTrait;

trait ArticulosTrait
{
    public static function index($request)
    {
        $articles = Articulo::orderBy('id', 'desc')->buscar($request)->get();
        $articulos = collect();
        $inventarios = collect();

        foreach ($articles as $art) {

            if (auth()->user()->role_id == 1 || auth()->user()->role_id == 2) {

                if ($art->inventarios->count() > 0) {
                    $stock = $art->inventarios[0]['cantidad'];
                    $inventarios = $art->inventarios[0];
                } else {
                    $inventarios = [];
                    $stock = 0;
                }

                // $inventarios = $art->inventarios;
                // $stock = $inventarios->sum('cantidad');
                $art = collect($art);
                $art->put('stock', $stock);
            } else {
                foreach ($art->inventarios as $inv) {
                    if ($inv->dependencia == auth()->user()->id) {
                        $inventarios->push($inv);
                        $stock = $inv['cantidad'];
                        $art = collect($art);
                        $art->put('stock', $stock);
                    }
                }
            }

            $art['inventarios'] = $inventarios;
            $articulos->push($art);
        }

        if ($articulos->count() <= $request->get('limit')) {
            return [
                'articulos' => $articulos,
                'total' => $articulos->count(),
                'ultimo' => $articulos->first(),
            ];
        } else {
            return [
                'articulos' => $articulos->take($request->get('limit', null)),
                'total' => $articulos->count(),
                'ultimo' => $articulos->first(),
            ];
        }
    }

    public static function store($request)
    {
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
                'lote' => 1,
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
            ArticulosNotificacionesTrait::crearNotificacion($articulo);
        }

        return 'creado';
    }

    public static function update($request, $id)
    {
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

        return ['message' => 'actualizado'];
    }

    public static function show($id)
    {
        $articulo = Articulo::find($id);
        $marca = $articulo->marca->marca;
        $categoria = $articulo->categoria->categoria;
        $stock = $articulo->inventarios->sum('cantidad');
        $inventarios = $articulo->inventarios;
        $lotes = ArticulosTrait::lotes($id);
        foreach ($inventarios as $inventario) {
            $inv = collect($inventario);
            $inv->put('proveedor', $inventario->proveedor);
        }
        return ['articulo' => $articulo, 'stock' => $stock, 'inventarios' => $inventarios, 'lotes' => $lotes, 'marca' => $marca, 'categoria' => $categoria];
    }

    public static function lotes($id)
    {
        $articulo = Articulo::find($id);
        $inventarios = $articulo->inventarios;
        $lotes = collect();
        foreach ($inventarios as $inventario) {
            $lotes->push($inventario->lote);
        }
        $lotes = $lotes->sort();
        $proximo = $lotes->max() + 1;
        return ['lotes' => $lotes, 'proximo' => $proximo];
    }
}
