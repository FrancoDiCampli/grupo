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
            'nombre' => 'CASA CENTRAL',
            'direccion' => 'N/D',
            'codigopostal' => 0000,
            'localidad' => 'N/D',
            'provincia' => 'N/D',
            'observaciones' => 'N/D'
        ]);
    }
}
