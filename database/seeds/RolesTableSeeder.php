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
    }
}
