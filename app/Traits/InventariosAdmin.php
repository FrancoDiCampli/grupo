<?php

namespace App\Traits;

use App\Inventario;
use App\Movimiento;

trait InventariosAdmin
{
    public static function actualizarCasaCentral($actualizar, $data)
    {
        switch ($data['movimiento']) {
            case 'INCREMENTO':
                $actualizar->cantidad += $data['cantidad'];
                $actualizar->cantidadlitros += $data['cantidadlitros'];
                $actualizar->save();

                $move = Movimiento::create([
                    'tipo' => 'INCREMENTO',
                    'inventario_id' => $actualizar->id,
                    'cantidad' => $data['cantidad'],
                    'cantidadlitros' => $data['cantidadlitros'],
                    'fecha' => now(),
                    'user_id' => auth()->user()->id
                ]);
                break;

            case 'DECREMENTO':
                $actualizar->cantidad -= $data['cantidad'];
                $actualizar->cantidadlitros -= $data['cantidadlitros'];
                $actualizar->save();

                $move = Movimiento::create([
                    'tipo' => 'DECREMENTO',
                    'inventario_id' => $actualizar->id,
                    'cantidad' => $data['cantidad'],
                    'cantidadlitros' => $data['cantidadlitros'],
                    'fecha' => now(),
                    'user_id' => auth()->user()->id
                ]);
                break;

            case 'DEVOLUCION':
                $actualizar->cantidad -= $data['cantidad'];
                $actualizar->cantidadlitros -= $data['cantidadlitros'];
                $actualizar->save();

                $move = Movimiento::create([
                    'tipo' => 'DEVOLUCION',
                    'inventario_id' => $actualizar->id,
                    'cantidad' => $data['cantidad'],
                    'cantidadlitros' => $data['cantidadlitros'],
                    'fecha' => now(),
                    'user_id' => auth()->user()->id
                ]);
                break;

            case 'MODIFICACION':
                $actualizar->cantidad = $data['cantidad'];
                $actualizar->cantidadlitros = $data['cantidadlitros'];
                $actualizar->save();

                $move = Movimiento::create([
                    'tipo' => 'MODIFICACION',
                    'inventario_id' => $actualizar->id,
                    'cantidad' => $data['cantidad'],
                    'cantidadlitros' => $data['cantidadlitros'],
                    'fecha' => now(),
                    'user_id' => auth()->user()->id
                ]);
                break;
        }
    }

    // Inventarios
    public static function altaInventario($data)
    {
        $inventario = Inventario::create([
            'cantidad' => $data['cantidad'],
            'cantidadlitros' => $data['cantidadlitros'],
            'articulo_id' => $data['articulo_id'],
            'supplier_id' => $data['supplier_id'],
            'lote' => $data['lote']
        ]);
        return $inventario;
    }

    public static function altaConsignacion($data)
    {
        $inventario = Inventario::create([
            'cantidad' => $data['cantidad'],
            'cantidadlitros' => $data['cantidadlitros'],
            'articulo_id' => $data['articulo_id'],
            // 'supplier_id' => $data['supplier_id'],
            // 'lote' => $data['lote'],
            'dependencia' => $data['dependencia']
        ]);
        return $inventario;
    }

    public static function decrementarInventario($data)
    {
        $inventa = Inventario::findOrFail($data['inventario_id']);
        $inventa->cantidad -= $data['cantidad'];
        $inventa->cantidadlitros -= $data['cantidad'] * $inventa->articulo->litros;
        $inventa->save();

        return $inventa;
    }

    public static function incrementarInventario($data)
    {
        $inventa = Inventario::findOrFail($data['inventario_id']);
        $inventa->cantidad += $data['cantidad'];
        $inventa->cantidadlitros += $data['cantidad'] * $inventa->articulo->litros;
        $inventa->save();

        return $inventa;
    }

    public static function actualizarInventario($data, $actualizar)
    {
        $actualizar->cantidad += $data['cantidad'];
        $actualizar->cantidadlitros += $data['cantidadlitros'];
        $actualizar->save();
    }

    // Movimientos
    public static function movimientoAlta($inventario, $request)
    {
        $data = $request;
        $move = Movimiento::create([
            'tipo' => 'ALTA',
            'inventario_id' => $inventario->id,
            'cantidad' => $data['cantidad'],
            'cantidadlitros' => $data['cantidadlitros'],
            'fecha' => now(),
            'user_id' => auth()->user()->id
        ]);
    }

    public static function movimientoAltaConsignacion($inventario, $data)
    {
        $move = Movimiento::create([
            'tipo' => 'ALTA X CONSIGNACION',
            'inventario_id' => $inventario->id,
            'origen' => $data['inventario_id'],
            'cantidad' => $data['cantidad'],
            'cantidadlitros' => $data['cantidadlitros'],
            'fecha' => now(),
            'user_id' => auth()->user()->id
        ]);
    }

    public static function movimientoBajaConsignacion($data)
    {
        $move = Movimiento::create([
            'tipo' => 'BAJA X CONSIGNACION',
            'inventario_id' => $data['inventario_id'],
            'cantidad' => $data['cantidad'],
            'cantidadlitros' => $data['cantidadlitros'],
            'fecha' => now(),
            'user_id' => auth()->user()->id
        ]);
    }

    public static function movimientoBajaDevolucion($data)
    {
        $move = Movimiento::create([
            'tipo' => 'BAJA X DEVOLUCION',
            'inventario_id' => $data['inventario_id'],
            'cantidad' => $data['cantidad'],
            'cantidadlitros' => $data['cantidadlitros'],
            'fecha' => now(),
            'user_id' => auth()->user()->id
        ]);
    }

    public static function movimientoAltaDevolucion($data)
    {
        $move = Movimiento::create([
            'tipo' => 'ALTA X DEVOLUCION',
            'inventario_id' => $data['inventario_id'],
            'cantidad' => $data['cantidad'],
            'cantidadlitros' => $data['cantidadlitros'],
            'origen' => $data['origen'],
            'fecha' => now(),
            'user_id' => auth()->user()->id
        ]);
    }

    public static function movimientoIncrementoConsignacion($data)
    {
        $move = Movimiento::create([
            'tipo' => 'INCREMENTO X CONSIGNACION',
            'inventario_id' => $data['inventario_id'],
            'cantidad' => $data['cantidad'],
            'cantidadlitros' => $data['cantidadlitros'],
            'fecha' => now(),
            'user_id' => auth()->user()->id
        ]);
    }
}
