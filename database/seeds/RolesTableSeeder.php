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
        $permissions = Permission::toString();

        Role::create([
            'role' => 'superAdmin',
            'permission' => $permissions['permissions'],
            'description' => $permissions['descriptions']
        ]);

        Role::create([
            'role' => 'administrador',
            'permission' => 'ventas-index ventas-show ventas-store ventas-destroy facturas-index facturas-show facturas-store presupuestos-index presupuestos-show presupuestos-store presupuestos-destroy clientes-index clientes-show clientes-store clientes-update clientes-destroy cuentascorrientes-index articulos-index articulos-show articulos-store articulos-update articulos-destroy inventarios-index inventarios-store movimientos-index suppliers-index suppliers-show suppliers-store suppliers-update suppliers-destroy compras-index compras-show compras-store consignaciones-index consignaciones-show consignaciones-store users-index users-store users-update users-destroy roles-index',
            'description' => 'Listar ventas, Ver ventas, Guardar ventas, Anular ventas, Listar facturas, Ver facturas, Guardar facturas, Listar presupuestos, Ver presupuestos, Guardar presupuestos, Eliminar presupuestos, Listar clientes, Ver clientes, Guardar clientes, Editar clientes, Eliminar clientes, Establecer el pago de cuentas, Listar articulos, Ver articulos, Guardar articulos, Editar articulos, Eliminar articulos, Listar inventarios, Guardar inventarios, Listar movimientos, Listar proveedores, Ver proveedores, Guardar proveedores, Editar proveedores, Eliminar proveedores, Listar compras, Ver compras, Guardar compras, Listar consignaciones, Ver consignaciones, Guardar consignaciones, Listar usuarios, Guardar usuarios, Editar usuarios, Eliminar usuarios, Listar roles'
        ]);

        Role::create([
            'role' => 'vendedor',
            'permission' => 'ventas-index ventas-show ventas-store facturas-index facturas-show facturas-store presupuestos-index presupuestos-show presupuestos-store clientes-index clientes-show clientes-store clientes-update cuentascorrientes-index articulos-index articulos-show',
            'description' => 'Listar ventas, Ver ventas, Guardar ventas, Listar facturas, Ver facturas, Guardar facturas, Listar presupuestos, Ver presupuestos, Guardar presupuestos, Listar clientes, Ver clientes, Guardar clientes, Editar clientes, Establecer el pago de cuentas, Listar articulos, Ver articulos'
        ]);

        Role::create([
            'role' => 'distribuidor',
            'permission' => 'ventas-index ventas-show ventas-store presupuestos-index presupuestos-show presupuestos-store clientes-index clientes-show clientes-store clientes-update cuentascorrientes-index articulos-index articulos-show ',
            'description' => 'Listar ventas, Ver ventas, Guardar ventas, Listar presupuestos, Ver presupuestos, Guardar presupuestos, Listar clientes, Ver clientes, Guardar clientes, Editar clientes, Establecer el pago de cuentas, Listar articulos, Ver articulos'
        ]);

        Role::create([
            'role' => 'cliente',
            'permission' => 'clientes-miCuenta ventas-show',
            'description' => 'Ver su cuenta, Ver ventas'
        ]);
    }
}
