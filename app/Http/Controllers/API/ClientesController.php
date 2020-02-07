<?php

namespace App\Http\Controllers\API;

use Afip;
use App\User;
use App\Recibo;
use App\Cliente;
use App\Negocio;
use App\Contacto;
use Carbon\Carbon;
use App\Preference;
use Illuminate\Http\Request;
use App\Notifications\Verificar;
use App\Http\Requests\StoreCliente;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCliente;
use Intervention\Image\Facades\Image;

class ClientesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if (auth()->user()->role_id == 1 || auth()->user()->role_id == 2) {
            if ($request->negocio_id) {
                $sucursal = Negocio::find($request->negocio_id);
                $usuarios = $sucursal->usuarios;
                $clientes = collect();
                foreach ($usuarios as $user) {
                    $clientes->push(Cliente::where('user_id', $user->id)->get());
                }
                $clientes = $clientes->flatten();
            } else {
                $clientes = Cliente::orderBy('razonsocial', 'asc')
                    ->get();
            }
        } else {
            $clientes = Cliente::orderBy('razonsocial', 'asc')
                ->where('user_id', auth()->user()->id)
                ->get();
        }

        if ($clientes->count() <= $request->get('limit')) {
            return [
                'clientes' => $clientes,
                'total' => $clientes->count()
            ];
        } else {
            return [
                'clientes' => $clientes->take($request->get('limit', null)),
                'total' => $clientes->count()
            ];
        }
    }

    public function store(StoreCliente $request)
    {
        $name = 'noimage.png';
        if ($request->get('foto')) {
            $carpeta = public_path() . '/img/clientes/';
            if (!file_exists($carpeta)) {
                mkdir($carpeta, 0777, true);
            }
            $image = $request->get('foto');
            $name = time() . $image;
            Image::make($request->get('foto'))->save(public_path('img/clientes/') . $name);
        }
        $foto = '/img/clientes/' . $name;

        $atributos = $request->validated();

        $atributos['foto'] = $foto;
        $atributos['user_id'] = auth()->user()->id;
        $atributos['observaciones'] = $request['observaciones'];

        $cliente = Cliente::create($atributos);

        if ($request['contactos']) {
            foreach ($request['contactos'] as $tel) {
                Contacto::create([
                    'nombre' => $tel['nombre'],
                    'numero' => $tel['numero'],
                    'email' => $tel['email'],
                    'cargo' => $tel['cargo'],
                    'referencia' => 'CL' . $cliente->id,
                ]);
            }
        }

        if ($request->password == $request->confirm_password) {
            $user = User::create([
                'name' => $atributos['razonsocial'],
                'email' => $atributos['email'],
                'password' => bcrypt($request->password),
                'role_id' => 4,
                'cliente_id'  => $cliente->id
            ]);

            // $user->notify(new Verificar($user));
        }

        return ['message' => 'guardado'];
    }

    public function contactos($cliente)
    {
        $referencia = 'CL' . $cliente->id;
        return Contacto::where('referencia', $referencia)->get();
    }

    public function show($id)
    {
        $cliente = Cliente::find($id);
        if ($cliente->haber) {
            $haber = $cliente->haber->haber;
        } else $haber = 0;
        $contactos = $this->contactos($cliente);
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

    public function update(UpdateCliente $request, $id)
    {
        $cliente = Cliente::find($id);

        if ($request->get('foto') != $cliente->foto) {
            $carpeta = '/img/clientes/';
            if (!file_exists($carpeta)) {
                mkdir($carpeta, 0777, true);
            }
            $eliminar = $cliente->foto;
            if (file_exists($eliminar)) {
                @unlink($eliminar);
            }
            $image = $request->get('foto');
            $name = time() . '.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
            Image::make($request->get('foto'))->save(public_path('img/clientes/') . $name);
            $foto = 'img/clientes/' . $name;
        } else {
            $foto = $cliente->foto;
        }

        $atributos = $request->validated();

        $atributos['foto'] = $foto;
        $atributos['observaciones'] = $request['observaciones'];
        $atributos['user_id'] = auth()->user()->id;

        $cliente->update($atributos);

        $nuevosContactos = $request['contactos'];
        $contactosEliminados = [];
        if ($request['eliminados']) {
            $contactosEliminados = $request['eliminados'];
        }

        // Agregar nuevos
        foreach ($nuevosContactos as $tel) {
            if (!array_key_exists('id', $tel)) {
                Contacto::create([
                    'nombre' => $tel['nombre'],
                    'numero' => $tel['numero'],
                    'email' => $tel['email'],
                    'cargo' => $tel['cargo'],
                    'referencia' => 'CL' . $cliente->id,
                ]);
            }
        }

        // Eliminar
        if (count($contactosEliminados) > 0) {
            foreach ($contactosEliminados as $tel) {
                Contacto::destroy($tel['id']);
            }
        }


        if ($request->password) {
            if ($request->password == $request->confirm_password) {
                $user = $cliente->user;
                $user->password =  bcrypt($request->password);
                $user->save();
            }
        }

        return ['message' => 'actualizado'];
    }

    public function destroy($id)
    {
        $cliente = Cliente::findOrFail($id);
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

    // BUSCA EN AFIP LOS DATOS CORRESPONDIENTES DE UNA CUIT
    public function buscarAfip($num)
    {
        $num = $num * 1;
        $cuituser = Preference::all()->first()->cuit;
        $afip = new Afip(array('CUIT' => $cuituser, 'production' => true));
        $contribuyente = $afip->RegisterScopeThirteen->GetTaxpayerDetails($num);
        return json_encode($contribuyente);
    }

    public function miCuenta()
    {
        return $this->show(auth()->user()->cliente_id);
    }

    public function restaurar($id)
    {
        $cliente = Cliente::withTrashed()->find($id);
        $cliente->user()->restore();
        $cliente->restore();
        return $cliente;
    }

    public function showRecibo($id)
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
}
