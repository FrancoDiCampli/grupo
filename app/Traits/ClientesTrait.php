<?php

namespace App\Traits;

use App\User;
use App\Recibo;
use App\Cliente;
use App\Role;
use Carbon\Carbon;

trait ClientesTrait
{
    public static function index($request)
    {
        if (auth()->user()->role_id <> 3) {
            $clientes = Cliente::orderBy('razonsocial', 'asc')
                ->where('documentounico', '<>', 0)
                ->where('distribuidor', false)
                ->buscar($request);
        } else {
            $clientes = Cliente::orderBy('razonsocial', 'asc')
                ->where('documentounico', '<>', 0)
                ->where('distribuidor', false)
                ->where('user_id', auth()->user()->id)
                ->buscar($request);
        }

        if ($clientes->count() <= $request->get('limit')) {
            return [
                'clientes' => $clientes->get(),
                'total' => $clientes->count()
            ];
        } else {
            return [
                'clientes' => $clientes->take($request->get('limit', null))->get(),
                'total' => $clientes->count()
            ];
        }
    }

    public static function store($request)
    {
        $foto = FotosTrait::store($request, $ubicacion = 'clientes');

        if ($request['tipo'] == 'distribuidor') {
            $isDistributor = true;
        }

        $atributos = $request->validated();

        $atributos['foto'] = $foto;
        $atributos['user_id'] = auth()->user()->id;
        $atributos['observaciones'] = $request['observaciones'];
        $atributos['distribuidor'] = $isDistributor;

        $usuario = ClientesTrait::crearUsuario($request);

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
        $jsonString = file_get_contents(base_path('config.json'));
        $configuracion = json_decode($jsonString, true);
        $recibo = Recibo::find($id);
        $fecha = new Carbon($recibo->fecha);
        $recibo->fecha = $fecha->format('d-m-Y');
        $pagos = $recibo->pagos;
        $cliente = null;
        $cotizacion = null;
        $fechaCotizacion = null;
        foreach ($pagos as $pay) {
            $fecha = new Carbon($pay->fecha);
            $pay->fecha = $fecha->format('d-m-Y');
            $pay['cuenta'] = $pay->ctacte;
            $pay['factura'] = $pay->ctacte->factura;
            $pay['forma'] = $pay->forma();
            $cliente = $pay->ctacte->factura->cliente;
            $cotizacion = $pay['forma'][1]['cotizacion'];
            $fechaCotizacion = $pay['forma'][1]['fecha_cotizacion'];
        }
        $recibo['cotizacion'] = $cotizacion;
        $recibo['fecha_cotizacion'] = $fechaCotizacion;

        return [
            'configuracion' => $configuracion,
            'recibo' => $recibo,
            'cliente' => $cliente,
            'pagos' => $pagos
        ];
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
                    $key['forma'] = $key->forma();
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
}
