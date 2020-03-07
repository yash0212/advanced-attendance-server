<?php

use Illuminate\Database\Seeder;
use App\Degree;

class DegreesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		Degree::create(['name' => 'B.Tech.']);
		Degree::create(['name' => 'BCA']);
		Degree::create(['name' => 'Hotel Management']);
		Degree::create(['name' => 'MBA']);
    }
}
