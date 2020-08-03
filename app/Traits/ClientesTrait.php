<?php

namespace App\Traits;

use App\Pago;
use App\Role;
use App\User;
use App\Venta;
use App\Cliente;
use App\Cuentacorriente;
use Carbon\Carbon;
use App\Traits\FotosTrait;
use App\Traits\RecibosTrait;
use App\Traits\ContactosTrait;
use App\Notifications\Verificar;
use App\Traits\FormasDePagoTrait;

trait ClientesTrait
{
    public static function index($request)
    {
        $distribuidores = collect();
        if (auth()->user()->role->role != 'vendedor') {
            $clientes = Cliente::orderBy('razonsocial', 'asc')
                ->where('documentounico', '<>', 0)
                ->where('distribuidor', false)
                ;

            $distribuidores = Cliente::orderBy('razonsocial', 'asc')
                ->where('documentounico', '<>', 0)
                ->where('distribuidor', true)
                ->get();
        } else {
            $clientes = Cliente::orderBy('razonsocial', 'asc')
                ->where('documentounico', '<>', 0)
                ->where('user_id', auth()->user()->id)
                ;
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
        $foto = FotosTrait::store($request, $ubicacion = 'clientes');
        $isDistributor = false;

        if ($request['tipo'] == 'distribuidor') {
            $isDistributor = true;
        }

        $atributos = $request->validated();

        $atributos['foto'] = $foto;
        $atributos['user_id'] = auth()->user()->id;
        $atributos['observaciones'] = $request['observaciones'];
        $atributos['distribuidor'] = $isDistributor;

        $usuario = static::crearUsuario($request);

        $cliente = Cliente::create($atributos);

        $usuario->cliente_id = $cliente->id;
        $usuario->save();

        ContactosTrait::crearContactos($cliente, $request);

        return ['message' => 'guardado'];
    }

    public static function crearUsuario($request)
    {
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
        if ($request->password) {
            if ($request->password == $request->confirm_password) {
                $user = $cliente->user;
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
            $cliente->user->delete();
            $cliente->delete();
            return ['message' => 'eliminado'];
        } else {
            return ['message' => 'El cliente posee cuentas activas'];
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

        return compact('cliente', 'contactos', 'user', 'facturas', 'cuentas', 'recibos', 'billing', 'haber');
    }

    public static function resumenCuenta($request)
    {
        $cliente = Cliente::find($request->get('id'));
        $desde = new Carbon($request->get('desde'));
        $hasta = new Carbon($request->get('hasta'));

        $cuentas = collect();
        $cuentasAnterior = collect();
        $pagos = collect();
        $pagosAnterior = collect();

        foreach ($cliente->ctacte as $cuenta) {
            if ($cuenta->created_at >= $desde->format('Y-m-d') && $cuenta->created_at <= $hasta->format('Y-m-d')) {
                $cuenta->factura;
                $fecha = new Carbon($cuenta->alta);
                $cuenta->alta = $fecha->format('d-m-Y');
                $cuentas->push($cuenta);
            } else if ($cuenta->created_at < $desde->format('Y-m-d')) {
                $cuentasAnterior->push($cuenta);
            }

            foreach ($cuenta->pagos as $pago) {
                if ($pago->fecha >= $desde->format('Ymd') && $pago->fecha <= $hasta->format('Ymd')) {
                    $pago->recibo;
                    $fec = new Carbon($pago->fecha);
                    $pago->fecha = $fec->format('d-m-Y');
                    $pagos->push($pago);
                } else if ($pago->fecha < $desde->format('Ymd')) {
                    $pagosAnterior->push($pago);
                }
            }
        }

        $debe = $cuentas->sum('importe');
        $haber = $pagos->sum('importe');
        $saldo = $haber - $debe;

        $debeAnterior = $cuentasAnterior->sum('importe');
        $haberAnterior = $pagosAnterior->sum('importe');
        $saldoAnterior = $haberAnterior - $debeAnterior;

        return [
            'cliente' => $cliente,
            'desde' => $desde->format('d-m-Y'),
            'hasta' => $hasta->format('d-m-Y'),
            'cuentas' => $cuentas,
            'pagos' => $pagos,
            'debe' => number_format($debe, 2, ',', '.'),
            'haber' => number_format($haber, 2, ',', '.'),
            'saldoAnterior' => number_format($saldoAnterior, 2, ',', '.'),
            'saldo' => number_format($saldo, 2, ',', '.')
        ];
    }
}
