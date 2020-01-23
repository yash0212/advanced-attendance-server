<?php

use Illuminate\Database\Seeder;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => "Sardar Khan",
            'email' => 'a@a.com',
            'user_type' => 0,
            'password' => Hash::make('a'),
        ]);
        DB::table('users')->insert([
            'name' => "Jesse Pinkman",
            'email' => 's@s.com',
            'user_type' => 1,
            'password' => Hash::make('s'),
        ]);
        DB::table('users')->insert([
            'name' => "Harrison Wells",
            'email' => 't@t.com',
            'user_type' => 2,
            'password' => Hash::make('t'),
        ]);
        DB::table('users')->insert([
            'name' => "Arnold Schwarzenegger",
            'email' => 'g@g.com',
            'user_type' => 3,
            'password' => Hash::make('g'),
        ]);
    }
}
