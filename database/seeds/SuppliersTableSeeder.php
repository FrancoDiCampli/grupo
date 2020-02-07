<?php

use App\Supplier;
use Illuminate\Database\Seeder;

class SuppliersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Supplier::create([
            'razonsocial' => 'GRUPO APC',
            'cuit' => 30715269801,
            'direccion' => 'N/D',
            'telefono' => 'N/D',
            'email' => 'neaapc@grupoapc.com.ar',
            'codigopostal' => 3540,
            'localidad' => 'Villa-Ángela',
            'provincia' => 'Chaco',
            'observaciones' => 'N/D'
        ]);
    }
}
