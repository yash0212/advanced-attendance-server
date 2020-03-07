<?php

use Illuminate\Database\Seeder;
use App\Subject;

class SubjectTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		Subject::create(['name' => '15CS101']);
		Subject::create(['name' => '15CS102']);
		Subject::create(['name' => '15CS103']);
		Subject::create(['name' => '15CS104']);
		Subject::create(['name' => '15CS201']);
		Subject::create(['name' => '15CS202']);
		Subject::create(['name' => '15CS203']);
		Subject::create(['name' => '15CS204']);
		Subject::create(['name' => '15CS301']);
		Subject::create(['name' => '15CS302']);
		Subject::create(['name' => '15CS303']);
		Subject::create(['name' => '15CS304']);
		Subject::create(['name' => '15CS401']);
		Subject::create(['name' => '15CS402']);
		Subject::create(['name' => '15CS403']);
		Subject::create(['name' => '15CS404']);
    }
}
