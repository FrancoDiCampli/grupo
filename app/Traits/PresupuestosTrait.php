<?php

namespace App\Traits;

use App\Venta;
use App\Cliente;
use Carbon\Carbon;
use App\Presupuesto;
use App\Traits\ConfiguracionTrait;
use Illuminate\Support\Facades\DB;

trait PresupuestosTrait
{
    public static function index($request)
    {
        if (auth()->user()->role->role != 'vendedor') {
            if ($request->fec) {
                $fec = $request->fec;
                $pres = Presupuesto::whereDate('created_at', $fec)->orderBy('id', 'DESC')->buscar($request)->get();
            } else {
                $pres = Presupuesto::orderBy('id', 'DESC')->buscar($request)->get();
            }
        } else {
            if ($request->fec) {
                $fec = $request->fec;
                $pres = Presupuesto::whereDate('created_at', $fec)
                    ->where('user_id', auth()->user()->id)
                    ->orderBy('id', 'DESC')->buscar($request)->get();
            } else {
                $pres = Presupuesto::orderBy('id', 'DESC')
                    ->where('user_id', auth()->user()->id)
                    ->buscar($request)->get();
            }
        }

        $presupuestos = collect();

        foreach ($pres as $pre) {
            $fecha = new Carbon($pre->fecha);
            $pre->fecha = $fecha->format('d-m-Y');
            $pre->cliente = Cliente::withTrashed()->find($pre->cliente_id);;
            $pre = collect($pre);
            $presupuestos->push($pre);
        }

        if ($presupuestos->count() <= $request->get('limit')) {
            return [
                'presupuestos' => $presupuestos,
                'ultimo' => $presupuestos->first(),
                'total' => $presupuestos->count(),
            ];
        } else {
            return [
                'presupuestos' => $presupuestos->take($request->get('limit', null)),
                'ultimo' => $presupuestos->first(),
                'total' => $presupuestos->count(),
            ];
        }
    }

    public static function store($request)
    {
        $atributos = $request;
        $cliente = Cliente::find($atributos['cliente_id']);
        $atributos['cuit'] = $cliente->documentounico;

        // ALMACENAMIENTO DE PRESUPUESTO
        $presupuesto = static::crearPresupuesto($atributos);

        if ($atributos->confirmacion) {
            $atributos['tipoComprobante'] = 'REMITO X';
            $atributos['condicionventa'] = 'CUENTA CORRIENTE';
            $atributos['numventa'] = 1;
            $venta_id = VentasTrait::store($atributos);
            $presupuesto->numventa = $venta_id;
            $presupuesto->update();
        }

        // ALMACENAMIENTO DE DETALLES
        $det = static::detallesPresupuesto($request->get('detalles'), $presupuesto);

        $presupuesto->articulos()->attach($det);

        return $presupuesto->id;
    }

    public static function detallesPresupuesto($details, $presupuesto)
    {
        foreach ($details as $detail) {
            $detalles = array(
                'codarticulo' => $detail['codarticulo'],
                'articulo' => $detail['articulo'],
                'cantidad' => $detail['cantidad'],
                'cantidadLitros' => $detail['cantidadLitros'],
                'medida' => $detail['medida'],
                'preciounitario' => $detail['precio'],
                'subtotalPesos' => $detail['subtotalDolares'] * $detail['cotizacion'],
                'subtotal' => $detail['subtotalDolares'],
                'cotizacion' => $detail['cotizacion'],
                'fechaCotizacion' => $detail['fechaCotizacion'],
                'articulo_id' => $detail['id'],
                'presupuesto_id' => $presupuesto->id,
            );
            $det[] = $detalles;
        }
        return $det;
    }

    public static function crearPresupuesto($atributos)
    {
        $configuracion = ConfiguracionTrait::configuracion();

        return Presupuesto::create([
            "ptoventa" => $configuracion['puntoventa'],
            "numpresupuesto" => $atributos['numpedido'],
            "comprobanteadherido" => $atributos['comprobanteadherido'],
            "cuit" => $atributos['cuit'],
            "fecha" => $atributos['fecha'],
            "bonificacion" => $atributos['bonificacion'] * 1,
            "recargo" => $atributos['recargo'] * 1,
            "subtotal" => $atributos['subtotal'],
            "total" => $atributos['total'],
            "subtotalPesos" => $atributos['subtotal'] * $atributos['cotizacion'],
            "totalPesos" => $atributos['total'] * $atributos['cotizacion'],
            'cotizacion' => $atributos['cotizacion'],
            'fechaCotizacion' => $atributos['fechaCotizacion'],
            "observaciones" => $atributos['observaciones'],
            "cliente_id" => $atributos['cliente_id'],
            "user_id" => auth()->user()->id
        ]);
    }

    public static function show($id)
    {
        $res = static::verPresupuesto($id);
        $articulos = collect($res['presupuesto']->articulos);
        $detallesVenta = collect();
        foreach ($articulos as $art) {
            $stock = $art->inventarios->sum('cantidad');
            if ($stock > 0) {
                $detallesVenta->push($art->pivot);
            }
        }
        $res['detallesVenta'] = $detallesVenta;
        return $res;
    }

    public static function verPresupuesto($id)
    {
        $configuracion = ConfiguracionTrait::configuracion();
        $presupuesto = Presupuesto::find($id);
        $fecha = new Carbon($presupuesto->fecha);
        $presupuesto->fecha = $fecha->format('d-m-Y');
        $vencimiento = new Carbon($presupuesto->vencimiento);
        $presupuesto->vencimiento = $vencimiento->format('d-m-Y');
        $cliente = Cliente::withTrashed()->find($presupuesto->cliente_id);
        $detalles = DB::table('articulo_presupuesto')->where('presupuesto_id', $presupuesto->id)->get();

        return [
            'configuracion' => $configuracion,
            'presupuesto' => $presupuesto,
            'detalles' => $detalles,
            'cliente' => $cliente
        ];
    }

    public static function vender($request)
    {
        $presupuesto = Presupuesto::find($request->id);
        $pre = Presupuesto::find($request->id);

        $presupuesto['tipoComprobante'] = 'REMITO X';
        $numventa = Venta::all()->last() ? Venta::all()->last()->id : 0;
        $presupuesto['numventa'] = $numventa + 1;
        $presupuesto['pagada'] = false;
        $presupuesto['condicionventa'] = 'CUENTA CORRIENTE';

        $venta = VentasTrait::crearVenta($presupuesto);

        foreach ($presupuesto->articulos as $item) {
            $detail = $item['pivot'];

            $detalles = array(
                'codarticulo' => $detail['codarticulo'],
                'articulo' => $detail['articulo'],
                'cantidad' => $detail['cantidad'],
                'cantidadLitros' => $detail['cantidadLitros'],
                'medida' => $detail['medida'],
                'preciounitario' => $detail['preciounitario'],
                'subtotalPesos' => ($detail['preciounitario'] * $detail['cantidadLitros']) * $detail['cotizacion'],
                'subtotal' => $detail['preciounitario'] * $detail['cantidadLitros'],
                'cotizacion' =>  $detail['cotizacion'],
                'fechaCotizacion' => $detail['fechaCotizacion'],
                'articulo_id' => $detail['articulo_id'],
                'venta_id' => $venta->id,
                'created_at' => now()->format('Ymd'),
            );
            $det[] = $detalles;
        }

        $venta->articulos()->attach($det);

        $pre->numventa = $venta->id;
        $pre->save();

        return 'listo';
    }
}
