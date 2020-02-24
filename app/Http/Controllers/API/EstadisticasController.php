<?php

namespace App\Http\Controllers\API;

use App\User;
use App\Venta;
use App\Cheque;
use App\Compra;
use App\Cliente;
use App\Articulo;
use App\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Traits\ChequesTrait;

class EstadisticasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:airlock');
    }

    public function chequeCobrado($id)
    {
        return ChequesTrait::chequeCobrado($id);
    }

    public function cartera(Request $request)
    {
        return ChequesTrait::cartera($request);
    }

    public function ventas(Request $request)
    {
        // Traer todas la facturas y los detalles
        $facturas = Venta::orderBy('fecha', 'ASC')->get();
        // Establecer la fecha desde y hasta
        if (count($facturas) > 0) {
            $inicio = $facturas->first();
            $ultima = $facturas->last();
            $from = $request->get('desde', $inicio->fecha);
            $to = $request->get('hasta', $ultima->fecha);
        } else {
            $from = $request->get('desde', now());
            $to = $request->get('hasta', now());
        }

        $desde = new Carbon($from);
        $hasta = new Carbon($to);
        $ventasFecha = [];
        $fecs = collect();
        $vendedores = [];
        $ventasVendedores = [];
        $sellers = collect();
        $ventasClientes = [];
        $clientes = [];
        $clients = collect();
        $ventasCondiciones = [];
        $condiciones = collect(['CONTADO', 'CUENTA CORRIENTE']);

        // Buscar las facturas entre las fechas
        $facturas = Venta::whereDate('fecha', '>=', $desde->format('Ymd'))->whereDate('fecha', '<=', $hasta->format('Ymd'))->orderBy('fecha', 'ASC')->take($request->get('limit', null))->get();

        foreach ($facturas as $factura) {
            $seller = User::find($factura->user_id);
            $sellers->push($seller);
            $client = Cliente::withTrashed()->find($factura->cliente_id);
            $clients->push($client);
        }

        $facturas2 = Venta::whereDate('fecha', '>=', $desde->format('Ymd'))->whereDate('fecha', '<=', $hasta->format('Ymd'))->orderBy('fecha', 'ASC')->get();
        foreach ($facturas2 as $factura) {
            $fecs->push($factura->fecha);
        }

        // Fechas
        $auxFechas = $fecs->unique();
        foreach ($auxFechas as $value) {
            $factus = Venta::whereDate('fecha', $value)->get();
            array_push($ventasFecha, $factus);
        };
        $columnsFechas = ['fecha', 'total'];
        $rowsFechas = collect();
        $total = 0;
        for ($i = 0; $i < count($ventasFecha); $i++) {
            $otro = $ventasFecha[$i];
            foreach ($otro as $a) {
                $total += $a->total;
            }
            $fecha = $ventasFecha[$i][0]->fecha;
            $fechaNew = new Carbon($fecha);
            $rowsFechas->push([
                'fecha' =>
                $fechaNew->format('d-m-Y'),
                'total' => $total
            ]);
            $total = 0;
        };
        $ventasFechasChart = collect();
        $ventasFechasChart->put('columns', $columnsFechas);
        $ventasFechasChart->put('rows', $rowsFechas);
        // Fin Fechas

        // Vendedores
        $auxVendedores = $sellers->unique();
        foreach ($auxVendedores as $key) {
            $facs = Venta::where('user_id', $key->id)
                ->where('fecha', '>=', $desde->format('Ymd'))
                ->where('fecha', '<=', $hasta->format('Ymd'))
                ->orderBy('fecha', 'ASC')
                ->get();
            array_push($vendedores, $key);
            array_push($ventasVendedores, $facs);
        }
        $columnsVendedores = ['vendedor', 'totalVendido'];
        $rowsVendedores = collect();
        $total = 0;
        for ($i = 0; $i < count($ventasVendedores); $i++) {
            $otro = $ventasVendedores[$i];
            foreach ($otro as $a) {
                $total += $a->total;
            }
            $rowsVendedores->push([
                'vendedor' => $vendedores[$i]->name,
                'totalVendido' =>  $total
            ]);
            $total = 0;
        };
        $ventasVendedores = collect();
        $ventasVendedores->put('columns', $columnsVendedores);
        $ventasVendedores->put('rows', $rowsVendedores);
        // Fin Vendedores

        // Clientes
        $auxClientes = $clients->unique();
        foreach ($auxClientes as $aux) {
            $facturs = Venta::where('cliente_id', $aux->id)
                ->whereDate('fecha', '>=', $desde->format('Ymd'))
                ->whereDate('fecha', '<=', $hasta->format('Ymd'))
                ->orderBy('fecha', 'ASC')->get();
            array_push($clientes, $aux);
            array_push($ventasClientes, $facturs);
        }
        $columnsClientes = ['cliente', 'totalComprado'];
        $rowsClientes = collect();
        $total = 0;
        for ($i = 0; $i < count($ventasClientes); $i++) {
            $otro = $ventasClientes[$i];
            foreach ($otro as $a) {
                $total += $a->total;
            }
            $rowsClientes->push([
                'cliente' => $clientes[$i]->razonsocial,
                'totalComprado' =>  $total
            ]);
            $total = 0;
        };
        $ventasClientes = collect();
        $ventasClientes->put('columns', $columnsClientes);
        $ventasClientes->put('rows', $rowsClientes);
        // Fin Clientes

        // Condiciones
        foreach ($condiciones as $cond) {
            $fa = Venta::where('condicionventa', $cond)
                ->whereDate('fecha', '>=', $desde->format('Ymd'))
                ->whereDate('fecha', '<=', $hasta->format('Ymd'))
                ->orderBy('fecha', 'ASC')->get();;
            array_push($ventasCondiciones, $fa);
        }
        $columnsCondiciones = ['condicion', 'totalVendido'];
        $rowsCondiciones = collect();
        $total = 0;
        for ($i = 0; $i < count($ventasCondiciones); $i++) {
            $otro = $ventasCondiciones[$i];
            foreach ($otro as $a) {
                $total += $a->total;
            }
            $rowsCondiciones->push([
                'condicion' => $condiciones[$i],
                'totalVendido' =>  $total
            ]);
            $total = 0;
        };
        $ventasCondiciones = collect();
        $ventasCondiciones->put('columns', $columnsCondiciones);
        $ventasCondiciones->put('rows', $rowsCondiciones);
        // Fin Condiciones

        $ventas = [
            'fechas' => ['desde' => $desde->format('Y-m-d'), 'hasta' => $hasta->format('Y-m-d')],
            'ventasFecha' => $facturas,
            'ventasFechaChart' => $ventasFechasChart,
            'total' => count(Venta::all()),
            'vendedores' => $vendedores,
            'ventasVendedores' => $ventasVendedores,
            'clientes' => $clientes,
            'ventasClientes' => $ventasClientes,
            'condiciones' => $condiciones,
            'ventasCondiciones' => $ventasCondiciones
        ];

        return ['ventas' => $ventas];
    }

    public function compras(Request $request)
    {
        $remitos = Compra::orderBy('fechaCompra', 'ASC')->get();

        if (count($remitos) > 0) {
            $inicio = $remitos->first();
            $ultima = $remitos->last();
            $from = $request->get('desde', $inicio->fechaCompra);
            $to = $request->get('hasta', $ultima->fechaCompra);
        } else {
            $from = $request->get('desde', now()->format('Y-m-d'));
            $to = $request->get('hasta', now()->format('Y-m-d'));
        }

        $desde = new Carbon($from);
        $hasta = new Carbon($to);
        $comprasFechas = [];
        $fecs = collect();
        $suppliers = collect();
        $proveedores = [];
        $comprasProveedores = [];

        $remitos = Compra::whereDate('fechaCompra', '>=', $desde->format('Y-m-d'))
            ->whereDate('fechaCompra', '<=', $hasta->format('Y-m-d'))
            ->orderBy('fechaCompra', 'ASC')
            ->take($request->get('limit', null))
            ->get();

        foreach ($remitos as $remito) {
            $supplier = Supplier::withTrashed()->find($remito->supplier_id);
            $suppliers->push($supplier);
        }

        $remitos2 = Compra::whereDate('fechaCompra', '>=', $desde->format('Y-m-d'))->whereDate('fechaCompra', '<=', $hasta->format('Y-m-d'))->orderBy('fechaCompra', 'ASC')->get();
        foreach ($remitos2 as $remito) {
            $fecs->push($remito->fechaCompra);
        }

        // Fechas
        $auxFechas = $fecs->unique();
        foreach ($auxFechas as $value) {
            $remis = Compra::whereDate('fechaCompra', $value)->get();
            array_push($comprasFechas, $remis);
        };
        $columnsFechas = ['fecha', 'total'];
        $rowsFechas = collect();
        $total = 0;
        for ($i = 0; $i < count($comprasFechas); $i++) {
            $otro = $comprasFechas[$i];
            foreach ($otro as $a) {
                $total += $a->total;
            }
            $fecha = $comprasFechas[$i][0]->fechaCompra;
            $fechaNew = new Carbon($fecha);
            $rowsFechas->push([
                'fecha' =>
                $fechaNew->format('d-m-Y'),
                'total' => $total
            ]);
            $total = 0;
        };
        $comprasFechasChart = collect();
        $comprasFechasChart->put('columns', $columnsFechas);
        $comprasFechasChart->put('rows', $rowsFechas);
        // Fin Fechas

        // Proveedores
        $auxProveedores = $suppliers->unique();
        foreach ($auxProveedores as $key) {
            $rems = Compra::where('user_id', $key->id)
                ->whereDate('fechaCompra', '>=', $desde->format('Y-m-d'))
                ->whereDate('fechaCompra', '<=', $hasta->format('Y-m-d'))
                ->orderBy('fechaCompra', 'ASC')
                ->get();
            array_push($proveedores, $key);
            array_push($comprasProveedores, $rems);
        }
        $columnsProveedores = ['vendedor', 'totalVendido'];
        $rowsProveedores = collect();
        $total = 0;
        for ($i = 0; $i < count($comprasProveedores); $i++) {
            $otro = $comprasProveedores[$i];
            foreach ($otro as $a) {
                $total += $a->total;
            }
            $rowsProveedores->push([
                'vendedor' => $proveedores[$i]->razonsocial,
                'totalVendido' =>  $total
            ]);
            $total = 0;
        };
        $comprasProveedores = collect();
        $comprasProveedores->put('columns', $columnsProveedores);
        $comprasProveedores->put('rows', $rowsProveedores);
        // Fin Proveedores

        $compras = [
            'fechas' => ['desde' => $desde->format('Y-m-d'), 'hasta' => $hasta->format('Y-m-d')],
            'comprasFecha' => $remitos,
            'comprasFechasChart' => $comprasFechasChart,
            'proveedores' => $proveedores,
            'comprasProveedores' => $comprasProveedores,
            'total' => count(Compra::all()),
        ];

        return ['compras' => $compras];
    }

    public function detallesVentas(Request $request)
    {
        $products = collect();
        $productos = [];
        $ventasProductos = [];

        $detallesVentas = DB::table('articulo_venta')->orderBy('created_at', 'ASC')->get();
        if (count($detallesVentas) > 0) {
            $inicio = $detallesVentas->first();
            $ultima = $detallesVentas->last();
            $from = $request->get('desde', $inicio->created_at);
            $to = $request->get('hasta', $ultima->created_at);
        } else {
            $from = $request->get('desde', now());
            $to = $request->get('hasta', now());
        }
        $desde = new Carbon($from);
        $hasta = new Carbon($to);

        // $detallesVentas = DB::table('articulo_factura')->orderBy('created_at', 'ASC')->get();

        $detallesVentas = DB::table('articulo_venta')
            // ->whereDate('created_at', '>=', $desde->format('Y-m-d'))
            // ->whereDate('created_at', '<=', $hasta->format('Y-m-d'))
            ->take($request->get('limit', null))
            ->orderBy('created_at', 'ASC')->get();


        // Productos
        foreach ($detallesVentas as $detalle) {
            $detalle->articulo_id;
            $product = Articulo::withTrashed()->find($detalle->articulo_id);
            $products->push($product);
        }
        $auxProductos = $products->unique();
        foreach ($auxProductos as $article) {
            $det = DB::table('articulo_venta')->where('articulo_id', $article->id)->get();
            array_push($productos, $article);
            array_push($ventasProductos, $det);
        }

        $coleccion  = collect();
        $nuevo = null;
        foreach ($ventasProductos as $venta) {
            if (count($venta) > 1) {
                $suma = $venta->sum('cantidad');
                foreach ($venta as $ven) {
                    $nuevo = $ven;
                }
                $nuevo->cantidad = $suma;
                $coleccion->push($nuevo);
            } else {
                foreach ($venta as $ven) {
                    $nuevo = $ven;
                }
                $coleccion->push($nuevo);
            }
        }

        $columnsProductos = ['producto', 'totalVendido'];
        $rowsProductos = collect();
        $total = 0;
        $prod = null;
        for ($i = 0; $i < count($ventasProductos); $i++) {
            $otro = $ventasProductos[$i];
            foreach ($otro as $a) {
                $prod = Articulo::withTrashed()->find($a->articulo_id);
                $total = $a->cantidad;
            }
            $rowsProductos->push([
                'producto' => $prod->articulo,
                'totalVendido' =>  $total,
            ]);
            $total = 0;
        };
        $ventasProductosChart = collect();
        $ventasProductosChart->put('columns', $columnsProductos);
        $ventasProductosChart->put('rows', $rowsProductos);

        $detalles = [
            'productos' =>  $productos,
            'ventasProductos' =>  $coleccion,
            'ventasProductosChart' => $ventasProductosChart
        ];

        return ['detalles' => $detalles];
    }

    public function detallesCompras(Request $request)
    {
        $products = collect();
        $productos = [];
        $comprasProductos = [];

        $detallesCompras = DB::table('articulo_compra')->orderBy('created_at', 'ASC')->get();
        if (count($detallesCompras) > 0) {
            $inicio = $detallesCompras->first();
            $ultima = $detallesCompras->last();
            $from = $request->get('desde', $inicio->created_at);
            $to = $request->get('hasta', $ultima->created_at);
        } else {
            $from = $request->get('desde', now());
            $to = $request->get('hasta', now());
        }
        $desde = new Carbon($from);
        $hasta = new Carbon($to);

        // $detallesCompras = DB::table('articulo_factura')->orderBy('created_at', 'ASC')->get();

        $detallesCompras = DB::table('articulo_compra')
            // ->whereDate('created_at', '>=', $desde->format('Y-m-d'))
            // ->whereDate('created_at', '<=', $hasta->format('Y-m-d'))
            ->take($request->get('limit', null))
            ->orderBy('created_at', 'ASC')->get();


        // Productos
        foreach ($detallesCompras as $detalle) {
            $detalle->articulo_id;
            $product = Articulo::withTrashed()->find($detalle->articulo_id);
            $products->push($product);
        }
        $auxProductos = $products->unique();
        foreach ($auxProductos as $article) {
            $det = DB::table('articulo_compra')->where('articulo_id', $article->id)->get();
            array_push($productos, $article);
            array_push($comprasProductos, $det);
        }

        $coleccion  = collect();
        $nuevo = null;
        foreach ($comprasProductos as $compra) {
            if (count($compra) > 1) {
                $suma = $compra->sum('cantidad');
                foreach ($compra as $com) {
                    $nuevo = $com;
                }
                $nuevo->cantidad = $suma;
                $coleccion->push($nuevo);
            } else {
                foreach ($compra as $com) {
                    $nuevo = $com;
                }
                $coleccion->push($nuevo);
            }
        }

        $columnsProductos = ['producto', 'totalComprado'];
        $rowsProductos = collect();
        $total = 0;
        $prod = null;
        for ($i = 0; $i < count($comprasProductos); $i++) {
            $otro = $comprasProductos[$i];
            foreach ($otro as $a) {
                $prod = Articulo::withTrashed()->find($a->articulo_id);
                $total = $a->cantidad;
            }
            $rowsProductos->push([
                'producto' => $prod->articulo,
                'totalComprado' =>  $total,
            ]);
            $total = 0;
        };
        $comprasProductosChart = collect();
        $comprasProductosChart->put('columns', $columnsProductos);
        $comprasProductosChart->put('rows', $rowsProductos);

        $detalles = [
            'productos' =>  $productos,
            'comprasProductos' =>  $coleccion,
            'comprasProductosChart' => $comprasProductosChart
        ];

        return ['detalles' => $detalles];
    }
}
