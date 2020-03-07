<?php

use Illuminate\Database\Seeder;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AttendanceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$subject_codes = [
			['15CS101', '15CS102', '15CS103' , '15CS104', '15CS105', '15CS106', '15CS107' , '15CS108'],
			['15CS201', '15CS202', '15CS203' , '15CS204', '15CS205', '15CS206', '15CS207' , '15CS208'],
			['15CS301', '15CS302', '15CS303' , '15CS304', '15CS305', '15CS306', '15CS307' , '15CS308'],
			['15CS401', '15CS402', '15CS403' , '15CS404', '15CS405', '15CS406', '15CS407' , '15CS408']
		];
		$teachers = User::select('id')->where('user_type', 1)->get();
		$t = [];
		foreach ($teachers as $teacher) {
				array_push($t, $teacher['id']);
		}
		$teachers = $t;

		$dates = [
			date_create(date_create()->format('Y-m-d'))->format('Y-m-d'),
			date_sub(date_create(date_create()->format('Y-m-d')), date_interval_create_from_date_string('1 day'))->format('Y-m-d'),
			date_sub(date_create(date_create()->format('Y-m-d')), date_interval_create_from_date_string('2 days'))->format('Y-m-d'),
			date_sub(date_create(date_create()->format('Y-m-d')), date_interval_create_from_date_string('3 days'))->format('Y-m-d'),
			date_sub(date_create(date_create()->format('Y-m-d')), date_interval_create_from_date_string('4 days'))->format('Y-m-d'),
		];
		$sections = User::select('section')->distinct('section')->get()->toArray();
		$s = [];
		foreach ($sections as $section) {
			if(!is_null($section['section'])){
				array_push($s, $section['section']);
			}
		}
		$sections = $s;
		$attendances =  [];
		foreach ($dates as $date) {
			for ($year = 1; $year <= 4; $year++) { 
				foreach ($sections as $section) {
					$students = User::select('id', 'name', 'regno')->where('degree', 'B.Tech.')->where('department', 'CSE')->where('section', $section)->where('year', $year)->orderBy('name', 'asc')->get();
					for ($lno = 1; $lno <= 8; $lno++) { 
						$t_id = $teachers[array_rand($teachers)];
						foreach($students as $student){
							\App\Attendance::create([
								'lecture_number' => $lno,
								'subject_code' => $subject_codes[$year-1][$lno-1],
								'degree' => 'B.Tech.',
								'department' => 'CSE',
								'section' => $section,
								'year' => $year,
								'marked_by' => $t_id,
								'student_id' => $student['id'],
								'attendance_status' => rand(0,1),
								'date' => $date,
							]);
						}
					}
				}
			}
		}
    }
}
