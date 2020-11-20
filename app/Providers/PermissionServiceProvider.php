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
            // PEDIDOS
            'pedidos-index' => 'Listar pedidos',
            'pedidos-show' => 'Ver pedidos',
            'pedidos-store' => 'Guardar pedidos',
            'pedidos-update' => 'Editar pedidos',
            'pedidos-destroy' => 'Eliminar pedidos',
            'pedidos-vender' => 'Convertir pedidos a remitos',

            // REMITOS
            'remitos-index' => 'Listar remitos',
            'remitos-show' => 'Ver remitos',
            'remitos-store' => 'Guardar remitos',
            'remitos-destroy' => 'Eliminar remitos',
            'remitos-facturar' => 'Generar una factura de los remitos',

            // FACTURAS
            'facturas-index' => 'Listar facturas',
            'facturas-show' => 'Ver facturas',
            'facturas-store' => 'Guardar facturas',
            'facturas-destroy' => 'Eliminar facturas',

            // ENTREGAS
            'entregas-index' => 'Listar entregas',
            'entregas-show' => 'Ver entregas',
            'entregas-store' => 'Guardar entregas',
            'entregas-destroy' => 'Eliminar entregas',

            // CLIENTES
            'clientes-index' => 'Listar clientes',
            'clientes-show' => 'Ver clientes',
            'clientes-store' => 'Guardar clientes',
            'clientes-update' => 'Editar clientes',
            'clientes-destroy' => 'Eliminar clientes',
            'clientes-mi-cuenta' => 'Ver la cliente registrado',

            // CUENTAS CORRIENTES
            'cuentascorrientes-pagar' => 'Establecer el pago de cuentas',
            'cuentascorrientes-generar-resumen' => 'Generar un resumen de la cuenta corriente',
            'cuentascorrientes-generar-recibo' => 'Generar un recibo de la cuenta corriente',

            // ARTICULOS
            'articulos-index' => 'Listar articulos',
            'articulos-show' => 'Ver articulos',
            'articulos-store' => 'Guardar articulos',
            'articulos-update' => 'Editar articulos',
            'articulos-destroy' => 'Eliminar articulos',

            // INVENTARIOS
            'inventarios-index' => 'Listar inventarios',
            'inventarios-store' => 'Guardar inventarios',

            // MOVIMIENTOS
            'movimientos-index' => 'Listar movimientos',

            // PROVEEDORES
            'suppliers-index' => 'Listar proveedores',
            'suppliers-show' => 'Ver proveedores',
            'suppliers-store' => 'Guardar proveedores',
            'suppliers-update' => 'Editar proveedores',
            'suppliers-destroy' => 'Eliminar proveedores',

            // COMPRAS
            'compras-index' => 'Listar compras',
            'compras-show' => 'Ver compras',
            'compras-store' => 'Guardar compras',
            'compras-destroy' => 'Eliminar compras',

            // CONSIGNACIONES
            'consignaciones-index' => 'Listar consignaciones',
            'consignaciones-show' => 'Ver consignaciones',
            'consignaciones-store' => 'Guardar consignaciones',

            // DEVOLUCIONES
            'devoluciones-index' => 'Listar devoluciones',
            'devoluciones-show' => 'Ver devoluciones',
            'devoluciones-store' => 'Guardar devoluciones',

            // REPORTES
            'reportes' => 'Reportes, movimientos y cartera',

            // USUARIOS
            'users-index' => 'Listar usuarios',
            'users-store' => 'Guardar usuarios',
            'users-update' => 'Editar usuarios',
            'users-destroy' => 'Eliminar usuarios',

            // ROLES
            'roles-index' => 'Listar roles',
            'roles-show' => 'Ver roles',
            'roles-store' => 'Guardar roles',
            'roles-update' => 'Editar roles',
            'roles-destroy' => 'Eliminar roles',
        ]);
    }
}
