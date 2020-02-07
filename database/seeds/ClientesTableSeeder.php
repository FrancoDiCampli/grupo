<?php

use App\Cliente;
use Illuminate\Database\Seeder;

class ClientesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Cliente::create([
            'razonsocial' => 'CONSUMIDOR FINAL',
            'documentounico' => 000000000000,
            'direccion' => 'N/D',
            'telefono' => 'N/D',
            'email' => 'N/D',
            'codigopostal' => 0000,
            'localidad' => 'N/D',
            'provincia' => 'N/D',
            'condicioniva' => 'CONSUMIDOR FINAL',
            'observaciones' => 'N/D',
            'user_id' => 1
        ]);
    }
}
