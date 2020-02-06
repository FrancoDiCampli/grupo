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
