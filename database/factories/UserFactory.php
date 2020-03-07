<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email_verified_at' => now(),
        'remember_token' => Str::random(10),
    ];
});
$factory->state(User::class, 'student', function (Faker $faker) {
    $sections = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
    $years = [1, 2, 3, 4];
    static $ruid = 1;
    
    return [
        'email' => 's'.$ruid++.'@s.com',
        'user_type' => 1,
        'regno' => "$faker->randomNumber",
        'degree' => 'B.Tech.',
        'department' => 'CSE',
        'section' => $sections[array_rand($sections)],
        'year' => $years[array_rand($years)],
        'password' => Hash::make('s'),
    ];
});

$factory->state(User::class, 'teacher', function (Faker $faker) {
    static $ruid = 1;
    
    return [
        'email' => 't'.$ruid++.'@t.com',
        'user_type' => 2,
        'password' => Hash::make('t'),
    ];
});

$factory->state(User::class, 'guard', function (Faker $faker) {
    static $ruid = 1;
    
    return [
        'email' => 'g'.$ruid++.'@g.com',
        'user_type' => 3,
        'password' => Hash::make('g'),
    ];
});
