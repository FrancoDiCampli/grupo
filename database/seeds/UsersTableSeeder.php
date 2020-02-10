<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Controller',
            'email' => 'controller.programacion@gmail.com',
            'password' => bcrypt('c0ntr0ll3r'),
            'role_id' => 1,
            'email_verified_at' => now()
        ]);

        User::create([
            'name' => 'Nea APC',
            'email' => 'neaapc@grupoapc.com.ar',
            'password' => bcrypt('123456'),
            'role_id' => 2,
            'email_verified_at' => now()
        ]);
    }
}
