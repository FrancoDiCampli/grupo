<?php

namespace App\Http\Controllers\API;

use Afip;
use App\Cliente;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCliente;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCliente;
use App\Traits\ClientesTrait;
use App\Traits\ContactosTrait;

class ClientesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:airlock');

        $this->middleware('scope:clientes-index')->only('index');
        $this->middleware('scope:clientes-show')->only('show');
        $this->middleware('scope:clientes-store')->only('store');
        $this->middleware('scope:clientes-update')->only('update');
        $this->middleware('scope:clientes-destroy')->only('destroy');
        $this->middleware('scope:clientes-miCuenta')->only('miCuenta');
    }

    public function index(Request $request)
    {
        return ClientesTrait::index($request);
    }

    public function store(StoreCliente $request)
    {
        return ClientesTrait::store($request);
    }

    public function show($id)
    {
        return ClientesTrait::showCliente($id);
    }

    public function update(UpdateCliente $request, $id)
    {
        $cliente = Cliente::find($id);
        $foto = $cliente->foto;

        $atributos = $request->validated();

        $atributos['foto'] = $foto;
        $atributos['observaciones'] = $request['observaciones'];
        $atributos['user_id'] = auth()->user()->id;

        $cliente->update($atributos);

        ContactosTrait::editarContactos($cliente, $request);
        ClientesTrait::editarUsuario($cliente, $request);

        return ['message' => 'actualizado'];
    }

    public function destroy($id)
    {
        $cliente = Cliente::findOrFail($id);

        ClientesTrait::eliminarCliente($cliente);
    }

    public function miCuenta()
    {
        return $this->show(auth()->user()->cliente_id);
    }

    public function restaurar($id)
    {
        // $cliente = Cliente::withTrashed()->find($id);
        // $cliente->user()->restore();
        // $cliente->restore();
        $cliente = Cliente::find($id);
        $cliente->activo = true;
        $cliente->save();
        return $cliente;
    }

    public function showRecibo($id)
    {
        return ClientesTrait::showRecibos($id);
    }
}
