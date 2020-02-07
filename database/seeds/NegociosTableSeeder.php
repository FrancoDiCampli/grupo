<?php

use App\Negocio;
use Illuminate\Database\Seeder;

class NegociosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Negocio::create([
            'razonsocial' => 'CASA CENTRAL',
            'cuit' => 00000000000,
            'direccion' => 'N/D',
            'telefono' => 'N/D',
            'email' => 'N/D',
            'codigopostal' => 0000,
            'localidad' => 'N/D',
            'provincia' => 'N/D',
            'observaciones' => 'N/D'
        ]);
    }
}
