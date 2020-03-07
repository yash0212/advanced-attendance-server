<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UsersTableSeeder::class,
            SubjectTableSeeder::class,
            DegreesTableSeeder::class,
            DepartmentTableSeeder::class,
            AttendanceTableSeeder::class,
        ]);
    }
}
