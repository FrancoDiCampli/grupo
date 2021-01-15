<?php

namespace App\Traits;

use App\Pago;
use App\Role;
use App\User;
use App\Venta;
use App\Cliente;
use Carbon\Carbon;
use App\Inventario;
use App\Cuentacorriente;
use App\Traits\FotosTrait;
use App\Traits\RecibosTrait;
use App\Traits\ContactosTrait;
use App\Notifications\Verificar;
use App\Traits\FormasDePagoTrait;
use Illuminate\Support\Facades\DB;

trait ClientesTrait
{
    public static function index($request)
    {
        $distribuidores = collect();
        if (auth()->user()->role->role != 'vendedor') {
            $clientes = Cliente::orderBy('razonsocial', 'asc')
                ->where('documentounico', '<>', 0)
                ->where('distribuidor', false);

            $distribuidores = Cliente::orderBy('razonsocial', 'asc')
                ->where('documentounico', '<>', 0)
                ->where('distribuidor', true)
                ->get();
        } else {
            $clientes = Cliente::orderBy('razonsocial', 'asc')
                ->where('documentounico', '<>', 0)
                ->where('user_id', auth()->user()->id);
        }

        if ($clientes->count() <= $request->get('limit')) {
            return [
                'clientes' => [
                    'clientes' => $clientes->get(),
                    'total' => $clientes->count()
                ],
                'distribuidores' => [
                    'distribuidores' => $distribuidores,
                    'total' => $distribuidores->count()
                ]
            ];
        } else {
            return [
                'clientes' => [
                    'clientes' => $clientes->take($request->get('limit', null))->get(),
                    'total' => $clientes->count()
                ],
                'distribuidores' => [
                    'distribuidores' => $distribuidores->take($request->get('limit', null)),
                    'total' => $distribuidores->count()
                ]
            ];
        }
    }

    public static function store($request)
    {
        // $foto = FotosTrait::store($request, $ubicacion = 'clientes');
        $isDistributor = false;

        if ($request['tipo'] == 'distribuidor') {
            $isDistributor = true;
        }

        $atributos = $request->validated();

        // $atributos['foto'] = $foto;
        $atributos['user_id'] = auth()->user()->id;
        $atributos['observaciones'] = $request['observaciones'];
        $atributos['distribuidor'] = $isDistributor;

        try {
            DB::transaction(function () use ($request, $atributos) {
                $usuario = static::crearUsuario($request);
                $cliente = Cliente::create($atributos);
                $usuario->cliente_id = $cliente->id;
                $usuario->save();
                ContactosTrait::crearContactos($cliente, $request);
            });
            return response()->json('guardado');
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static function crearUsuario($request)
    {
        $request->validate(
            [
                'email' => 'required|email|unique:users,email,NULL,id,deleted_at,NULL',
                'password' => 'required|min:6',
                'confirm_password' => 'required|min:6|same:password'
            ],
            [
                'email.unique' => 'El valor del campo email ya está en uso.',
                'password.required' => 'La contraseña es requerida',
                'confirm_password.same' => 'Las contraseñas deben coincidir',
            ]
        );

        if ($request->password == $request->confirm_password) {
            $rol = Role::where('role', 'cliente')->get()->first();

            return $user = User::create([
                'name' => $request['razonsocial'],
                'email' => $request['email'],
                'password' => bcrypt($request->password),
                'role_id' => $rol->id
            ]);

            // $user->notify(new Verificar($user));
        } else {
            return response()->json('Las contraseñas no coinciden', 422);
        }
    }

    public static function editarUsuario($cliente, $request)
    {
        $user = $cliente->user;

        $request->validate(
            [
                'email' => 'required|email|unique:users,email,' . $user->id,
                'password' => 'nullable|min:6',
                'confirm_password' => 'nullable|min:6|same:password'
            ],
            [
                'email.unique' => 'El valor del campo email ya está en uso.',
                'password.required' => 'La contraseña es requerida',
                'confirm_password.same' => 'Las contraseñas deben coincidir',
            ]
        );

        if ($request->password) {
            if ($request->password == $request->confirm_password) {
                $user->password =  bcrypt($request->password);

                if ($cliente->email != $user->email) {
                    $user->email = $cliente->email;
                }

                $user->save();
            }
        }
    }

    public static function eliminarCliente($cliente)
    {
        $facturas = $cliente->facturas;

        if (count($facturas) > 0) {
            foreach ($facturas as $factura) {
                $fecha = new Carbon($factura->fecha);
                $factura->fecha = $fecha->format('d-m-Y');
            }
        }

        $cuentas = collect();

        if (count($facturas) > 0) {
            for ($i = 0; $i < count($facturas); $i++) {
                if ($facturas[$i]->cuenta <> null) {
                    $cuentas->push($facturas[$i]->cuenta);
                }
            }
        }

        $cond = true;
        if (count($cuentas) > 0) {
            for ($i = 0; $i < count($cuentas); $i++) {
                if ($cuentas[$i]->estado == 'ACTIVA') {
                    $cond = false;
                }
            }
        }

        if ($cond) {
            try {
                DB::transaction(function () use ($cliente) {
                    $cliente->user->delete();
                    $cliente->delete();
                });
                return response()->json('eliminado');
            } catch (\Throwable $th) {
                throw $th;
            }
        } else {
            return response()->json('El cliente posee cuentas activas');
        }
    }

    public  static function showRecibos($id)
    {
        return RecibosTrait::verRecibo($id);
    }

    public static function showCliente($id)
    {
        $cliente = Cliente::find($id);
        if ($cliente->haber) {
            $haber = $cliente->haber->haber;
        } else $haber = 0;
        $contactos = ContactosTrait::contactos($cliente);
        $user = $cliente->user;
        $ordenados = collect($cliente->facturas);
        $order = $ordenados->sortByDesc('id');
        $facturas = $order->values()->all();
        foreach ($facturas as $factura) {
            $fecha = new Carbon($factura->fecha);
            $factura->fecha = $fecha->format('d-m-Y');
        }
        $cuentas = collect();

        if (count($facturas) > 0) {
            for ($i = 0; $i < count($facturas); $i++) {
                if ($facturas[$i]->cuenta <> null) {
                    $cuentas->push($facturas[$i]->cuenta);
                }
            }
        }

        // CUENTAS CORRIENTES DEL CLIENTE Y MOVIMIENTOS DE LAS MISMAS
        $auxRecibos = collect();
        if (count($cuentas) > 0) {
            for ($i = 0; $i < count($cuentas); $i++) {
                $cuentas[$i]['numfactura'] = $cuentas[$i]->factura['numfactura'];
                $alta = new Carbon($cuentas[$i]['alta']);
                $cuentas[$i]['alta'] = $alta->format('d-m-Y');

                if ($cuentas[$i]['ultimopago'] != null) {
                    $ultimo = new Carbon($cuentas[$i]['ultimopago']);
                    $cuentas[$i]['ultimopago'] = $ultimo->format('d-m-Y');
                }

                $cuentas[$i]['movimientos'] = $cuentas[$i]->movimientos;
                $aux = $cuentas[$i]->movimientos;

                $cuentas[$i]['movimientos'];
                foreach ($cuentas[$i]['movimientos'] as $aux) {
                    $fechamov = new Carbon($aux->fecha);
                    $aux->fecha = $fechamov->format('d-m-Y');
                }

                $orderMoves = collect($cuentas[$i]['movimientos']);
                $ord = $orderMoves->sortByDesc('id');
                $cuentas[$i]['movimientos'] = $ord->values()->all();

                $orden = collect($cuentas[$i]->pagos);
                $order = $orden->sortByDesc('id');
                $pagos = $order->values()->all();

                foreach ($pagos as $pago) {
                    $auxRecibos->push($pago->recibo);
                }

                $cuentas[$i]['pagos'] = $pagos;
                foreach ($cuentas[$i]['pagos'] as $key) {
                    $fec = new Carbon($key->fecha);
                    $key->fecha = $fec->format('d-m-Y');
                    $pays = FormasDePagoTrait::verPagos([$key]); // el parametro debe ser array
                    $key['forma'] = $pays;
                }

                if ($cuentas[$i]->estado == 'ACTIVA') {
                    if ($cuentas[$i]->ultimopago == null) {
                        $ultimopago = new Carbon($cuentas[$i]->updated_at);
                    } else {
                        $ultimopago = new Carbon($cuentas[$i]->ultimopago);
                    }
                    $hoy = now();
                    $diff = $hoy->diffInDays($ultimopago);
                    $cuentas[$i]['diferencia'] = $diff;
                    $cuentas[$i]['menuDiff'] = false;
                }

                unset($aux);
            }
        }
        $recibosCol = collect();
        foreach ($auxRecibos as $rec) {
            $fecha = new Carbon($rec[0]->fecha);
            $rec[0]->fecha = $fecha->format('d-m-Y');
            $recibosCol->push($rec[0]);
        }
        $recibos = $recibosCol->keyBy('id')->values();

        $ordenando = $cliente->facturas->sortByDesc('id');

        $billing = $ordenando->values()->all();

        $ventas = collect();
        foreach ($facturas as $item) {
            if ($item->tipocomprobante != 'IVA') {
                $ventas->push($item);
            }
        }

        $ventas = static::detallesVentas($ventas);

        $facturas = $cliente->invoices; // FACTURAS

        $entregas = $cliente->entregas; // ENTREGAS

        $pedidos = $cliente->pedidos;

        return compact('cliente', 'contactos', 'user', 'facturas', 'ventas', 'entregas', 'pedidos', 'cuentas', 'recibos', 'billing', 'haber');
    }

    public static function resumenCuenta($request)
    {
        $cliente = Cliente::find($request->get('id'));
        $desde = new Carbon($request->get('desde'));
        $hasta = new Carbon($request->get('hasta'));

        $auxDebe = collect();
        $auxHaber = collect();
        $auxAntDebe = collect();
        $auxAntHaber = collect();

        foreach ($cliente->ctacte as $cuenta) {

            $auxD = $cuenta->movimientos->where('created_at', '>=', $desde->format('Y-m-d'))->where('created_at', '<=', $hasta->format('Y-m-d'))->whereIn('tipo', ['IVA', 'ALTA']);
            $auxDebe->push($auxD->flatten());

            $auxH = $cuenta->movimientos->where('created_at', '>=', $desde->format('Y-m-d'))->where('created_at', '<=', $hasta->format('Y-m-d'))->whereIn('tipo', ['PAGO PARCIAL', 'PAGO TOTAL', 'DESCUENTO IVA']);
            $auxHaber->push($auxH->flatten());

            $auxAD = $cuenta->movimientos->where('created_at', '<', $desde->format('Y-m-d'))->whereIn('tipo', ['IVA', 'ALTA']);
            $auxAntDebe->push($auxAD->flatten());

            $auxAH = $cuenta->movimientos->where('created_at', '<', $desde->format('Y-m-d'))->whereIn('tipo', ['PAGO PARCIAL', 'PAGO TOTAL', 'DESCUENTO IVA']);
            $auxAntHaber->push($auxAH->flatten());
        }

        $flattenD = $auxDebe->flatten();
        $flattenH = $auxHaber->flatten();
        $flattenAD = $auxAntDebe->flatten();
        $flattenAH = $auxAntHaber->flatten();

        $debe = $flattenD->sum('importe');
        $haber = $flattenH->sum('importe');
        $saldo = $haber - $debe;

        $debeAnterior = $flattenAD->sum('importe');
        $haberAnterior = $flattenAH->sum('importe');
        $saldoAnterior = $haberAnterior - $debeAnterior;

        $flattenD->map(function ($item) {
            $fec = new Carbon($item->fecha);
            $item->fecha = $fec->format('d-m-Y');
        });
        $sortedD = $flattenD->sort();

        $flattenH->map(function ($item) {
            $fec = new Carbon($item->fecha);
            $item->fecha = $fec->format('d-m-Y');
        });
        $sortedH = $flattenH->sort();

        return [
            'cliente' => $cliente,
            'desde' => $desde->format('d-m-Y'),
            'hasta' => $hasta->format('d-m-Y'),
            'cuentas' => $sortedD->values()->all(),
            'pagos' => $sortedH->values()->all(),
            'debe' => number_format($debe, 2, ',', '.'),
            'haber' => number_format($haber, 2, ',', '.'),
            'saldoAnterior' => number_format($saldoAnterior, 2, ',', '.'),
            'saldo' => number_format($saldo, 2, ',', '.')
        ];
    }

    public static function detallesVentas($ventas)
    {
        $auxVentas = collect();
        foreach ($ventas as $venta) {
            $fecha = new Carbon($venta->fecha);
            $venta->fecha = $fecha->format('d-m-Y');
            $venta->cliente = Cliente::withTrashed()->find($venta->cliente_id);
            // $fac->forma;
            // $pagos = FormasDePagoTrait::verPagos($fac);
            // $fac->cuenta ? $fac->cuenta->pagos : null;
            // $fac = collect($fac);
            // $fac->put('pagos', $pagos);

            foreach ($venta->articulos as $det) {
                if (auth()->user()->role->role == 'superAdmin' || auth()->user()->role->role == 'administrador') {
                    $article = Inventario::where('dependencia', null)
                        ->where('cantidad', '>', 0)
                        ->where('articulo_id', $det['id'])
                        ->first();
                } else {
                    $article = Inventario::where('dependencia', auth()->user()->id)
                        ->where('cantidad', '>', 0)
                        ->where('articulo_id', $det['id'])
                        ->first();
                }

                $article ? $det['pivot']['disponible'] = $article->cantidad : $det['pivot']['disponible'] = 0;

                $detsVenta = DB::table('articulo_factura')->where('articulo_venta_id', $det['pivot']->id)->get();
                $detsEntrega = DB::table('articulo_entrega')->where('articulo_venta_id', $det['pivot']->id)->get();

                $detsVenta->sum('cantidad') < $det['pivot']->cantidad ? $fac['todofacturado'] = false : $fac['todofacturado'] = true;
                $det['pivot']['cantidadfacturado'] = $detsVenta->sum('cantidad');

                $detsEntrega->sum('cantidad') < $det['pivot']->cantidad ? $fac['todoentregado'] = false : $fac['todoentregado'] = true;
                $det['pivot']['cantidadentregado'] = $detsEntrega->sum('cantidad');

                $det['pivot']['litros'] = $det->litros;

                if ($venta->cliente_id <> 1) {
                    count($venta->cuenta->pagos) > 0 ? $venta['hasPagos'] = true : $venta['hasPagos'] = false;
                    count($venta->entregas) > 0 ? $venta['hasEntregas'] = true : $venta['hasEntregas'] = false;
                    count($venta->facturas) > 0 ? $venta['hasFacturas'] = true : $venta['hasFacturas'] = false;
                }
            }

            $auxVentas->push($venta);
        }

        return $auxVentas;
    }
}
