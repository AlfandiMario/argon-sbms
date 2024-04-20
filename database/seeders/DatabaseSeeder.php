<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'username' => 'admin',
            'email' => 'admin@argon.com',
            'password' => bcrypt('secret'),
            'level' => 'admin'
        ]);
        DB::table('users')->insert([
            'username' => 'mario',
            'email' => 'mario@argon.com',
            'password' => bcrypt('secret'),
            'level' => 'user'
        ]);
        DB::table('users')->insert([
            'username' => 'dev',
            'email' => 'dev@gmail.com',
            'password' => bcrypt('123456'),
            'level' => 'admin'
        ]);
        DB::table('energy_costs')->insert([
            'harga' => '1440',
            'pokok' => '1440',
            'delay' => '300',
        ]);
    }
}
