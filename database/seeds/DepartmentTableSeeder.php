<?php

use Illuminate\Database\Seeder;
use App\Department;

class DepartmentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		Department::create(['name' => 'CSE']);
		Department::create(['name' => 'IT']);
		Department::create(['name' => 'EEE']);
		Department::create(['name' => 'ECE']);
    }
}
