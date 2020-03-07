<?php

use Illuminate\Database\Seeder;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Subject;
use App\Attendance;

class AttendanceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$subjects = Subject::select('id')->get();
		$s = [];
		foreach ($subjects as $subject) {
			if(!is_null($subject['id'])){
				array_push($s, $subject['id']);
			}
		}
		$subjects = $s;
		$subject_codes = [
			[1, 2, 3, 4],
			[5, 6, 7, 8],
			[9, 10, 11, 12],
			[13, 14, 15, 16]
		];
		$teachers = User::select('id')->where('user_type', 2)->get();
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
					$students = User::select('id', 'name', 'regno')->where('degree_id', 1)->where('department_id', 1)->where('section', $section)->where('year', $year)->orderBy('name', 'asc')->get();
					for ($lno = 1; $lno <= 4; $lno++) { 
						$t_id = $teachers[array_rand($teachers)];
						foreach($students as $student){
							Attendance::create([
								'lecture_number' => $lno,
								'subject_id' => $subject_codes[$year-1][$lno-1],
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
