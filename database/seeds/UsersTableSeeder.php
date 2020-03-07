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
        $students = factory(User::class, 500)->states('student')->create();
        $teachers = factory(User::class, 5)->states('teacher')->create();
        $guards = factory(User::class, 3)->states('guard')->create();
    }
}
