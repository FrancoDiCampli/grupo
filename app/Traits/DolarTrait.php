<?php

namespace App\Traits;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

trait DolarTrait
{
    public static function consultar()
    {
        $jsonString = file_get_contents(base_path('dolar.json'));
        return json_decode($jsonString, true);
    }

    public static function alternativa()
    {
        $client = new Client;
        $start = Carbon::now()->subMonth()->format('Y-m-d');

        $newURL = 'https://apis.datos.gob.ar/series/api/series/?ids=168.1_T_CAMBIOR_D_0_0_26&limit=5000&start_date=' . $start . '&format=json';

        $response = $client->get($newURL, [
            'headers' => [
                // 'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
        ]);

        $dolar = collect(json_decode((string) $response->getBody(), true));

        $dolar = collect($dolar['data']);

        $ultima = $dolar->last();

        $fecha = new Carbon($ultima[0]);

        return [
            'fecha' => $fecha->format('Y-m-d'),
            'valor' => $ultima['1'],
        ];
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

        return 'Cotización Almacenada';
    }
}
