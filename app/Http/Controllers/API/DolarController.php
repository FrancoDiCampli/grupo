<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DolarController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:airlock');
    }

    public function consultar()
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

        return $nuevo = [
            'fecha' => $ultima['0'],
            'valor' => $ultima['1'],
        ];

        // return [
        //     'valor' => 62,
        //     'fecha' => now()->format('d-m-Y')
        // ];

        // REVISAR CORS ON LARAVEL AIRLOCK

        $jsonString = file_get_contents(base_path('dolar.json'));
        $dolarTable = json_decode($jsonString, true);

        $client = new Client;
        $hoy = now()->format('Ymd');

        if ($dolarTable) {
            $dolarFecha = new Carbon($dolarTable['created_at']);
        }

        $token = 'eyJhbGciOiJIUzUxMiIsInR5cCI6IkpXVCJ9.eyJleHAiOjE1OTU2NDQ0MzAsInR5cGUiOiJleHRlcm5hbCIsInVzZXIiOiJnZXJtYW4uZS5lc2NvYmFyQGdtYWlsLmNvbSJ9._dtUKDf9Rm4w0lAYtBoOYJ9Jxk38cpQRz_-DcZjTrDruCWWvRet_WOraLOMqQPrVV6gYpp1MNlJXw5lngMWrbQ';

        $url = 'https://api.estadisticasbcra.com/usd_of_minorista';

        if ($hoy != $dolarFecha->format('Ymd')) {
            $response = $client->get($url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
            ]);

            $dolar = collect(json_decode((string) $response->getBody(), true));

            $ultima = $dolar->last();
            $fec = new Carbon($ultima['d']);
            $ultima['d'] = $fec->format('d-m-Y');

            $nuevo = [
                'fecha' => $ultima['d'],
                'valor' => $ultima['v'],
                'created_at' => now()->format('Y-m-d')
            ];

            $updateJson = json_encode($nuevo);
            file_put_contents(base_path('dolar.json'), $updateJson);
            return $nuevo;
        } else {
            return $dolarTable;
        }
    }
}
