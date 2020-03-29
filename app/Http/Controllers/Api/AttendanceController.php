<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Mail;
use App\User;
use App\Attendance;
use Encrypto;
use Decrypto;
use App\Subject;
use App\Degree;
use App\Department;
use App\Mail\StudentLowAttendance;
use App\Mail\StudentSelfLowAttendance;

class AttendanceController extends Controller
{
    // Function for teacher to fetch attendance details
    public function fetch_students_attendance_detail(Request $request) {
        //Parse input data
        $degree = Degree::where('id', $request->input('degree'))->first();
        $department = Department::where('id', $request->input('department'))->first();
        $subject = Subject::where('id', $request->input('subject_code'))->first();
        $section = $request->input('section');
        $year = $request->input('year');
        $lecture_number = $request->input('lecture_no');
        $user = Auth::user();
        $attendance_data = [];

        //Check for empty inputs
        if(!is_null($degree) && !is_null($department) && !is_null($subject) && isset($section, $year, $lecture_number)) {
            $date = (new \DateTime())->format('Y-m-d');
            //Find all students of the class
            $students = User::select('id', 'name', 'regno')
                                ->where([
                                    ['degree_id', $degree['id']],
                                    ['department_id', $department['id']],
                                    ['section', $section],
                                    ['year', $year]
                                ])
                                ->orderBy('name', 'asc')
                                ->get();

            //Fetch attendance of student or create if it doesn't exist
            foreach ($students as $student) {
                $att = Attendance::select('student_id', 'attendance_status')->firstOrCreate([
                        'student_id' => $student['id'],
                        'lecture_number' => $lecture_number,
                        'date' => $date
                    ],[
                        'subject_id' => $subject['id'],
                        'marked_by' => $user['id'],
                        'attendance_status' => 0
                    ]);
                $att['name'] = $student['name'];
                $att['regno'] = $student['regno'];
                array_push($attendance_data, $att);

                $attendances = $student->attendances()->select('attendance_status')->where('subject_id', $subject['id'])->get();
                if(count($attendances) > 0){
                    $total_hours = 0;
                    $present_count = 0;
                    foreach ($attendances as $att) {
                        $present_count += $att['attendance_status'];
                        $total_hours++;
                    }
                    // Calculate attendance percentage
                    $att_percentage = $present_count * 100 / $total_hours;
                    // Check for attendance less than 50%
                    if($att_percentage < 50) {
                        // Mail to parent
                        $mail = new StudentLowAttendance($subject['name'], $total_hours, $present_count);
                        Mail::to($student->extra_details()->first()->parent_email)->send($mail);
                        // Mail to student
                        $selfmail = new StudentSelfLowAttendance($subject['name'], $total_hours, $present_count);
                        Mail::to($student->email)->send($selfmail);
                    }
                }
            }

            // Return the response
            return response()->json(["status"=>"success", "attendance_data"=>$attendance_data]);
        }else{
            return response()->json(["status"=>"error", "msg"=>"Degree/Deparment incorrect"]);
        }
    }

    // Deprecated
    public function submit_attendance(Request $request) {
        $lecture_number = $request->input('lecture_number');
        $subject_code = $request->input('subject_code');
        $degree = $request->input('degree');
        $department = $request->input('department');
        $section = $request->input('section');
        $year = $request->input('year');
        $attendance_data = $request->input('attendance_data');
        $teacher = Auth::user();

        if(isset($lecture_number, $subject_code, $degree, $department, $section, $year, $attendance_data) && is_array($attendance_data)){
            if(!empty($attendance_data)){
                $lecture_number = intval($lecture_number);
                $year = intval($year);
                $students = User::select('id', 'name', 'regno')->where('degree', $degree)->where('department', $department)->where('section', $section)->where('year', $year)->orderBy('name', 'asc')->get();
                $date = (new \DateTime())->format('Y-m-d');
                $attendance_statuses = [];
                foreach ($students as $student) {
                    $status = 0;
                    foreach ($attendance_data as $s) {
                        if($student->id === $s['id']){
                            $status = $s['$attendance_statuses'];
                            break;
                        }
                    }
                    $attendance_statuses[$student->id] = $status;
                }

                foreach ($students as $student) {
                    $attendance = Attendance::firstorNew([
                        'lecture_number' => $lecture_number,
                        'subject_code' => $subject_code,
                        'degree' => $degree,
                        'department' => $department,
                        'section' => $section,
                        'year' => $year,
                        'student_id' => $student->id,
                        'date' => $date,
                    ]);
                    $attendance->marked_by = $teacher->id;
                    $attendance->attendance_status = $attendance_statuses[$student->id];
                    $attendance->date = (new \DateTime())->format('Y-m-d');
                    $attendance->save();
                }
                return response()->json(['status'=>'success', 'msg'=>'Attendance Marked Successfully']);
            }else{
                return response()->json(['status'=>'error', 'msg'=>'Attendance data cannot be empty']);
            }
        }else{
            return response()->json(['status'=>'error', 'msg'=>'Missing Attributes']);
        }
    }

    // Function to mark attendance of by student
    public function student_mark_attendance(Request $request) {
        $obj = new Decrypto();
        //lect no., subject id, teacher id, degree, dept, sec, year, type(0=>smart, 1=>QR)
        $result = $obj->decpCode($request->input('code'),8);
        $student = Auth::user();
        if($result["status"] == 1){
            //Parse decrypted data
            $lecture_no = $result['data'][0];
            $subject_id = $result['data'][1];
            $teacher_id = $result['data'][2];
            $degree = $result['data'][3];
            $department = $result['data'][4];
            $section = $result['data'][5];
            $year = $result['data'][6];
            $attendance_type = $result['data'][7];

            // Check if student is of same class as teacher marking attendance
            if($student['degree_id'] == $degree && $student['department_id'] == $department && $student['section'] == chr(ord('A')+$section-1) && $student['year'] == $year) {
                $date = (new \DateTime())->format('Y-m-d');
                $teacher = User::where('id', $teacher_id)->where('user_type', 2)->first();
                if($teacher != null) {
                    $t_id = $teacher['id'];
                    $att = Attendance::UpdateOrCreate([
                        'student_id' => $student['id'],
                        'lecture_number' => intval($lecture_no),
                        'date' => $date
                    ],[
                        'subject_id' => $subject_id,
                        'marked_by' => $t_id,
                        'attendance_status' => 1
                    ]);

                    // Check if attendance is marked via QR
                    if($attendance_type == 1) {
                        // Check for low attendance and send email notification
                        $subject_name = Subject::where('id', $subject_id)->first()->name;
                        $attendances = $student->attendances()->select('attendance_status')->where('subject_id', $subject_id)->get();
                        if(count($attendances) > 0){
                            $total_hours = 0;
                            $present_count = 0;
                            foreach ($attendances as $att) {
                                $present_count += $att['attendance_status'];
                                $total_hours++;
                            }
                            // Calculate attendance percentage
                            $att_percentage = $present_count*100/$total_hours;
                            // Check for attendance less than 50%
                            if($att_percentage < 50) {
                                // Mail to parent
                                $mail = new StudentLowAttendance($subject_name, $total_hours, $present_count);
                                Mail::to($student->extra_details()->first()->parent_email)->send($mail);
                                // Mail to student
                                $selfmail = new StudentSelfLowAttendance($subject_name, $total_hours, $present_count);
                                Mail::to($student->email)->send($selfmail);
                            }
                        }
                    }
                    return response()->json(['status'=>'success', 'msg'=>'Attendance Marked Successfully']);
                } else {
                    // Teacher cannot be found
                    return response()->json(['status'=>'error', 'msg'=>'Invalid Request']);
                }
            }else{
                // Student does not belong to this class
                return response()->json(['status'=>'error', 'msg'=>'Invalid Request']);
            }
        } else {
            // Code cannot be decrypted
            return response()->json(['status'=>'error', 'msg'=>'Invalid Code']);
        }
    }

    // Function for teacher to mark students attendance
    public function teacher_update_attendance(Request $request) {
        //Parse input data
        $degree = Degree::where('id', $request->input('degree'))->first();
        $department = Department::where('id', $request->input('department'))->first();
        $subject = Subject::where('id', $request->input('subject_code'))->first();
        $section = $request->input('section');
        $year = $request->input('year');
        $lecture_number = $request->input('lecture_number');
        $user = Auth::user();
        $attendance_data = $request->input('attendance_data');

        //Check for empty inputs
        if(!is_null($degree) && !is_null($department) && !is_null($subject) && isset($section, $year, $lecture_number)) {
            $student_ids = [];
            $students = User::select('id')
                                ->where([
                                    ['degree_id', $degree['id']],
                                    ['department_id', $department['id']],
                                    ['section', $section],
                                    ['year', $year]
                                ])
                                ->orderBy('name', 'asc')
                                ->get();

            // Parse student ids
            if(count($students) > 0){
                foreach ($students as $student) {
                    array_push($student_ids, $student['id']);
                }
            }

            $date = (new \DateTime())->format('Y-m-d');
            $attendances = [];
            // Check if student is of given class and then mark attendance
            foreach ($attendance_data as $att) {
                if(in_array($att['student_id'], $student_ids)){
                    $attendance = Attendance::UpdateOrCreate([
                        'student_id' => $att['student_id'],
                        'lecture_number' => intval($lecture_number),
                        'date' => $date
                    ],[
                        'subject_id' => $subject['id'],
                        'marked_by' => $user['id'],
                        'attendance_status' => $att['attendance_status']
                    ]);
                    array_push($attendances, $attendance);
                }
            }
            return response()->json(["status" => "success", 'msg' => 'Attendance Marked Successfully for '.count($attendances).(count($attendances) == 1?' student':' students')]);
        } else {
            return response()->json(["status" => "error", 'msg' => 'Missing Parameters']);
        }
    }

    // Function for students to view attendance
    public function student_view_attendance(Request $request) {
        $student = Auth::user();
        $attendances = $student->attendances()->select(['attendance_status','subject_id'])->get()->groupBy('subject_id');
        $attendance_data = [];
        $subject_ids = [];
        foreach ($attendances as $sub_id => $attendance) {
            $present_count = 0;
            $total_hours = 0;
            foreach ($attendance as $att) {
                $total_hours++;
                if($att['attendance_status'] == 1){
                    $present_count++;
                }
            }
            array_push($subject_ids, $sub_id);
            array_push($attendance_data, [
                'subject_code' => $sub_id,
                'attended_hours' => $present_count,
                'total_hours' => $total_hours,
                'absent_hours' => $total_hours - $present_count,
            ]);
        }

        $subjects = Subject::whereIn('id', $subject_ids)->get();
        foreach ($attendance_data as $index => $ad) {
            foreach ($subjects as $subject) {
                if($ad['subject_code'] == $subject['id']) {
                    $attendance_data[$index]['subject_code'] = $subject['name'];
                }
            }
        }
        return response()->json(["status"=>"success", "attendance_data"=>$attendance_data]);
    }

    //Function for students to view detailed attendance for a subject
    public function student_view_detailed_attendance(Request $request) {
        if(!is_null($request->input('subject_code'))){
            $student = Auth::user();
            $subject = Subject::where('name', $request->input('subject_code'))->first();

            if($subject != NULL) {
                $attendances = $student->attendances()->select(['attendance_status', 'date', 'lecture_number'])->where('subject_id', $subject['id'])->get();
                return response()->json(["status"=>"success", "attendance_data"=>$attendances]);
            }else{
                return response()->json(['status'=>'error', 'msg' => 'Please provide valid subject code']);
            }
        }else{
            return response()->json(['status'=>'error', 'msg' => 'Please provide subject code']);
        }
    }
}
