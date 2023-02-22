<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Admin Dulub',
            'email' => 'admin@dulub.com',
            'type' => 'ADM',
            'password' => Hash::make('*dUNAXADM()1452'),
        ]);

        DB::table('users')->insert([
            'name' => 'Usuário',
            'email' => 'user_prov@dulub.com.br',
            'type' => 'ADM',
            'password' => Hash::make('*dUNAX()123'),
        ]);
    }
}
