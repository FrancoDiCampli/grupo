<?php

namespace App\Http\Controllers\API;

use App\User;
use App\Marca;
use App\Articulo;
use App\Categoria;
use App\Distributor;
use Carbon\Carbon;
use App\Inventario;
use App\Movimiento;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArticulo;
use App\Http\Requests\UpdateArticulo;
use App\Negocio;
use Intervention\Image\Facades\Image;
use App\Notifications\ArticuloNotification;

class ArticulosController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:airlock');

        // $this->middleware('scope:articulos-index')->only('index');
        // $this->middleware('scope:articulos-show')->only('show');
        // $this->middleware('scope:articulos-store')->only('store');
        // $this->middleware('scope:articulos-update')->only('update');
        // $this->middleware('scope:articulos-destroy')->only('destroy');
    }

    public function index(Request $request)
    {
        $articles = Articulo::orderBy('id', 'desc')->buscar($request)->get();
        $articulos = collect();
        $inventarios = collect();

        foreach ($articles as $art) {

            if (auth()->user()->role_id == 1) {
                $inventarios = $art->inventarios;
            } else {
                foreach ($art->inventarios as $inv) {
                    if ($inv->distributor_id == auth()->user()->distributor_id) {
                        $inventarios->push($inv);
                    }
                }
            }

            $stock = $inventarios->sum('cantidad');

            $art = collect($art);
            $art->put('stock', $stock);
            if (count($inventarios) == 0) {
                $art->put('vencido', false);
            } else {
                foreach ($inventarios as $inventario) {
                    $hoy = now();
                    $fechavenc = new Carbon($inventario->vencimiento);
                    if ($hoy > $fechavenc) {
                        $art->put('vencido', true);
                    } else {
                        $art->put('vencido', false);
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

    public function store(StoreArticulo $request)
    {
        $stockInicial = $request['stockInicial'];
        // FOTO
        $name = 'noimage.png';
        if ($request->get('foto')) {
            $carpeta = public_path() . '/img/articulos/';
            if (!file_exists($carpeta)) {
                mkdir($carpeta, 0777, true);
            }
            $image = $request->get('foto');
            $name = time() . '.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
            Image::make($request->get('foto'))->save(public_path('img/articulos/') . $name);
        }
        $foto = '/img/articulos/' . $name;

        $data = $request;

        $marca = Marca::where('marca', $data['marca'])->get();
        $marca_id = null;
        if (count($marca) > 0) {
            $marca_id = $marca[0]->id;
        } else {
            $nuevaMarca = Marca::create([
                'marca' => $data['marca']
            ]);
            $marca_id = $nuevaMarca->id;
        }

        $categoria = Categoria::where('categoria', $data['categoria'])->get();
        $categoria_id = null;
        if (count($categoria) > 0) {
            $categoria_id = $categoria[0]->id;
        } else {
            $nuevaCategoria = Categoria::create([
                'categoria' => $data['categoria']
            ]);
            $categoria_id = $nuevaCategoria->id;
        }

        $distributor = Distributor::where('nombre', $data['distributor'])->get();
        $distributor_id = null;
        if (count($distributor) > 0) {
            $distributor_id = $distributor[0]->id;
        }

        $data = $request->validated();
        $data['marca_id'] = $marca_id;
        $data['categoria_id'] = $categoria_id;
        $data['foto'] = $foto;

        $articulo = Articulo::create($data);

        if ($stockInicial || $distributor_id) {
            $inventario = Inventario::create([
                'cantidad' => $stockInicial,
                'cantidadlitros' => $stockInicial * $articulo->litros,
                'lote' => 1,
                'articulo_id' => $articulo->id,
                'supplier_id' => 1,
                'distributor_id' => $distributor_id
            ]);
            Movimiento::create([
                'tipo' => 'ALTA',
                'cantidad' => $inventario->cantidad,
                'cantidadlitros' => $inventario->cantidadlitros,
                'fecha' => now(),
                'inventario_id' => $inventario->id,
                'user_id' => auth()->user()->id
            ]);
        } else {
            $user = User::findOrFail(auth()->user()->id);
            $user->notify(new ArticuloNotification($articulo));
        }

        return 'creado';
    }

    public function update(UpdateArticulo $request, $id)
    {
        $articulo = Articulo::find($id);

        // FOTO
        if ($request->get('foto') != $articulo->foto) {
            $carpeta = public_path() . '/img/articulos/';
            if (!file_exists($carpeta)) {
                mkdir($carpeta, 0777, true);
            }
            $eliminar = $articulo->foto;
            if (file_exists($eliminar)) {
                @unlink($eliminar);
            }
            $image = $request->get('foto');
            $name = time() . '.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
            Image::make($request->get('foto'))->save(public_path('img/articulos/') . $name,);
            $foto = '/img/articulos/' . $name;
        } else {
            $foto = $articulo->foto;
        }

        $data = $request;

        if (gettype($data['marca']) == 'array') {
            $data['marca'] = $data['marca']['marca'];
        }

        $marca = Marca::where('marca', $data['marca'])->get();
        $marca_id = null;
        if (count($marca) > 0) {
            $marca_id = $marca[0]->id;
        } else {
            $nuevaMarca = Marca::create([
                'marca' => $data['marca']
            ]);
            $marca_id = $nuevaMarca->id;
        }

        if (gettype($data['categoria']) == 'array') {
            $data['categoria'] = $data['categoria']['categoria'];
        }

        $categoria = Categoria::where('categoria', $data['categoria'])->get();
        $categoria_id = null;
        if (count($categoria) > 0) {
            $categoria_id = $categoria[0]->id;
        } else {
            $nuevaCategoria = Categoria::create([
                'categoria' => $data['categoria']
            ]);
            $categoria_id = $nuevaCategoria->id;
        }

        $data = $request->validated();
        $data['marca_id'] = $marca_id;
        $data['categoria_id'] = $categoria_id;
        $data['foto'] = $foto;

        $articulo->update($data);

        // return $articulo;
        return ['message' => 'actualizado'];
    }

    public function destroy($id)
    {
        $articulo = Articulo::find($id);
        $articulo->delete();

        return ['message' => 'eliminado'];
    }

    public function show($id)
    {
        $articulo = Articulo::find($id);
        $marca = $articulo->marca->marca;
        $categoria = $articulo->categoria->categoria;
        $aux = $articulo->inventarios;
        $lotes = $this->lotes($id);
        $inventarios = collect();
        foreach ($aux as $inventario) {
            if (auth()->user()->role_id == 1 || auth()->user()->role_id == 2) {
                $distributor = $inventario->distributor;
                $inv = collect($inventario);
                $inv->put('distributor', $distributor);
                $inv->put('proveedor', $inventario->proveedor);
                $inventarios->push($inv);
            } elseif ($inventario->distributor_id == auth()->user()->distributor_id) {
                $distributor = $inventario->distributor;
                $inv = collect($inventario);
                $inv->put('distributor', $distributor);
                $inv->put('proveedor', $inventario->proveedor);
                $inventarios->push($inv);
            }
        }
        $stock = $inventarios->sum('cantidad');
        return ['articulo' => $articulo, 'stock' => $stock, 'inventarios' => $inventarios, 'lotes' => $lotes, 'marca' => $marca, 'categoria' => $categoria];
    }

    public function lotes($id)
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

    public function restaurar($id)
    {
        Articulo::withTrashed()->find($id)->restore();
        return Articulo::find($id);
    }
}
