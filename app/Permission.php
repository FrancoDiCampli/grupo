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

    public static function permissionAll()
    {
        $permissionsString = [
            'permissions' => '',
            'descriptions' => ''
        ];

        $permissions = self::permissions();

        foreach ($permissions as $per) {
            $permissionsString['permissions'] = $permissionsString['permissions'] . $per['permission'] . ' ';
            $permissionsString['descriptions'] = $permissionsString['descriptions'] . $per['description'] . ', ';
        }

        return $permissionsString;
    }

    public static function permissionExcept(String $params)
    {
        $permissionsString = [
            'permissions' => '',
            'descriptions' => ''
        ];

        $permissions = self::permissions();

        $excepts = explode(' ', $params);



        return array_diff_assoc($excepts, $permissions->toArray());

        return gettype($permissions->toArray());



        // foreach ($permissions as $per) {
        //     $permissionsString['permissions'] = $permissionsString['permissions'] . $per['permission'] . ' ';
        //     $permissionsString['descriptions'] = $permissionsString['descriptions'] . $per['description'] . ', ';
        // }

        // return $permissionsString;
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
