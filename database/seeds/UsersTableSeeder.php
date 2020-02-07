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
    }
}
