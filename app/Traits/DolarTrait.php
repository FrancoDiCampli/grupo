<?php

namespace App\Traits;

trait DolarTrait
{
    public static function getCotizacion()
    {
        $jsonString = file_get_contents(base_path('dolar.json'));
        return response()->json(json_decode($jsonString, true));
    }

    public static function setCotizacion($request)
    {
        $cotizacion =  $request->get('cotizacion');
        $fechaCotizacion =  $request->get('fechaCotizacion');

        $nuevo = [
            'fecha' => $fechaCotizacion,
            'valor' => $cotizacion,
            'created_at' => now()->format('Y-m-d')
        ];

        $updateJson = json_encode($nuevo);
        file_put_contents(base_path('dolar.json'), $updateJson);

        return response()->json(['msg' => 'Cotización Almacenada']);
    }
}
