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
        })->values();
    }

    public static function toString()
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
}
