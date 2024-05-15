<?php

namespace Database\Seeders;

use Faker\Guesser\Name;
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
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@argon.com',
            'password' => bcrypt('secret'),
            'level' => 'admin'
        ]);
        DB::table('users')->insert([
            'name' => 'Mario',
            'username' => 'mario',
            'email' => 'mario@argon.com',
            'password' => bcrypt('secret'),
            'level' => 'user'
        ]);
        DB::table('users')->insert([
            'name' => 'dev',
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
        DB::table('energy_panels')->insert([
            'nama' => 'AC',
            'status' => '0',
        ]);
        DB::table('lights')->insert([
            'nama' => 'Lampu Utama',
            'status' => '0',
        ]);
        DB::table('lights')->insert([
            'nama' => 'Lampu Luar',
            'status' => '0',
        ]);
    }
}
