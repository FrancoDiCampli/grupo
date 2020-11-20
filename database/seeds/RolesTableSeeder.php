<?php

use App\Role;
use App\Permission;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissionsSuperAdmin = Permission::permissionAll();
        $permissionsAdmin = Permission::permissionExcept('clientes-mi-cuenta roles-index roles-show roles-store roles-update roles-destroy');
        $permissionsVendedor = Permission::permissionExcept('clientes-mi-cuenta articulos-store articulos-update articulos-destroy inventarios-index inventarios-store movimientos-index suppliers-index suppliers-show suppliers-store suppliers-update suppliers-destroy compras-index compras-show compras-store compras-destroy consignaciones-index consignaciones-show consignaciones-store devoluciones-index devoluciones-show devoluciones-store reportes users-index users-store users-update users-destroy roles-index roles-show roles-store roles-update roles-destroy');
        $permissionsCliente = Permission::permissionOnly('clientes-mi-cuenta remitos-show facturas-show entregas-show cuentascorrientes-generar-resumen cuentascorrientes-generar-recibo');

        Role::create([
            'role' => 'superAdmin',
            'permission' => $permissionsSuperAdmin['permissions'],
            'description' => $permissionsSuperAdmin['descriptions']
        ]);

        Role::create([
            'role' => 'administrador',
            'permission' => $permissionsAdmin['permissions'],
            'description' => $permissionsAdmin['descriptions']
        ]);

        Role::create([
            'role' => 'vendedor',
            'permission' => $permissionsVendedor['permissions'],
            'description' => $permissionsVendedor['descriptions']
        ]);

        Role::create([
            'role' => 'cliente',
            'permission' => $permissionsCliente['permissions'],
            'description' => $permissionsCliente['descriptions']
        ]);
    }
}
