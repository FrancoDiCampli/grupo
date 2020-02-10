<?php

namespace App\Providers;

use App\Permission;
use Illuminate\Support\ServiceProvider;

class PermissionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Permission::permissionCan([
            // Ventas
            'ventas-index' => 'Listar ventas',
            'ventas-show' => 'Ver ventas',
            'ventas-store' => 'Guardar ventas',
            'ventas-destroy' => 'Anular ventas',

            // Facturas
            'facturas-index' => 'Listar facturas',
            'facturas-show' => 'Ver facturas',
            'facturas-store' => 'Guardar facturas',

            // Presupuestos
            'presupuestos-index' => 'Listar presupuestos',
            'presupuestos-show' => 'Ver presupuestos',
            'presupuestos-store' => 'Guardar presupuestos',
            'presupuestos-destroy' => 'Eliminar presupuestos',

            // Clientes
            'clientes-index' => 'Listar clientes',
            'clientes-show' => 'Ver clientes',
            'clientes-store' => 'Guardar clientes',
            'clientes-update' => 'Editar clientes',
            'clientes-destroy' => 'Eliminar clientes',
            'clientes-miCuenta' => 'Ver su cuenta',

            // Cuentas Corrientes
            'cuentascorrientes-index' => 'Establecer el pago de cuentas',

            // Articulos
            'articulos-index' => 'Listar articulos',
            'articulos-show' => 'Ver articulos',
            'articulos-store' => 'Guardar articulos',
            'articulos-update' => 'Editar articulos',
            'articulos-destroy' => 'Eliminar articulos',

            // Inventarios
            'inventarios-index' => 'Listar inventarios',
            'inventarios-store' => 'Guardar inventarios',

            // Movimientos
            'movimientos-index' => 'Listar movimientos',

            // Proveedores
            'suppliers-index' => 'Listar suppliers',
            'suppliers-show' => 'Ver suppliers',
            'suppliers-store' => 'Guardar suppliers',
            'suppliers-update' => 'Editar suppliers',
            'suppliers-destroy' => 'Eliminar suppliers',

            // Compras
            'compras-index' => 'Listar compras',
            'compras-show' => 'Ver compras',
            'compras-store' => 'Guardar compras',

            // Negocios
            'negocios-index' => 'Listar sucursales',
            'negocios-show' => 'Ver sucursales',
            'negocios-store' => 'Guardar sucursales',
            'negocios-update' => 'Editar sucursales',
            'negocios-destroy' => 'Eliminar sucursales',

            // Usuario
            'users-index' => 'Listar usuarios',
            'users-store' => 'Guardar usuarios',
            'users-update' => 'Editar usuarios',
            'users-destroy' => 'Eliminar usuarios',

            // Roles
            'roles-index' => 'Listar roles',
            'roles-show' => 'Ver roles',
            'roles-store' => 'Guardar roles',
            'roles-update' => 'Editar roles',
            'roles-destroy' => 'Eliminar roles',
        ]);
    }
}
