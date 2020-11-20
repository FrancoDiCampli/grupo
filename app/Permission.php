<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    public static $scopes = [];

    public static function permissionCan(array $scopes)
    {
        static::$scopes = $scopes;
    }

    public static function permissions()
    {
        return collect(static::$scopes)->map(function ($description, $permission) {
            return [
                'permission' => $permission,
                'description' => $description
            ];
        });
    }

    public static function permissionExcept()
    {
        $string = explode(' ', 'ventas-index ventas-show ventas-store ventas-destroy');

        $permissionsString = [
            'permissions' => '',
            'descriptions' => ''
        ];

        $permissions = static::permissions();

        return $aux = $permissions->whereNotIn('permission', $string);

        foreach ($permissions as $per) {
            $permissionsString['permissions'] = $permissionsString['permissions'] . $per['permission'] . ' ';
            $permissionsString['descriptions'] = $permissionsString['descriptions'] . $per['description'] . ', ';
        }

        return $permissionsString;
    }

    // public static function permissionAll()
    // {
    //     $permissionsString = [
    //         'permissions' => '',
    //         'descriptions' => ''
    //     ];

    //     $permissions = self::permissions();

    //     foreach ($permissions as $per) {
    //         $permissionsString['permissions'] = $permissionsString['permissions'] . $per['permission'] . ' ';
    //         $permissionsString['descriptions'] = $permissionsString['descriptions'] . $per['description'] . ', ';
    //     }

    //     return $permissionsString;
    // }
}
