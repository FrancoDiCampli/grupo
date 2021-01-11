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
use Illuminate\Support\Facades\DB;

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
        $this->middleware('scope:clientes-mi-cuenta')->only('miCuenta');

        $this->middleware('scope:cuentascorrientes-generar-recibo')->only('showRecibo');
        $this->middleware('scope:cuentascorrientes-generar-resumen')->only('resumenCuenta');
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
        // $atributos['user_id'] = auth()->user()->id;

        try {
            DB::transaction(function () use ($cliente, $atributos, $request) {
                $cliente->update($atributos);
                ContactosTrait::editarContactos($cliente, $request);
                ClientesTrait::editarUsuario($cliente, $request);
            });
            return response()->json('actualizado');
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function destroy($id)
    {
        $cliente = Cliente::findOrFail($id);

        return ClientesTrait::eliminarCliente($cliente);
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

    public function resumenCuenta(Request $request)
    {
        return ClientesTrait::resumenCuenta($request);
    }
}
