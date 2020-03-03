<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\User;
use App\Attendance;

class AttendanceController extends Controller
{
    public function fetch_students_detail(Request $request)
    {
        $degree = $request->input('degree');
        $department = $request->input('department');
        $section = $request->input('section');
        $year = $request->input('year');
        $students = User::select('id', 'name', 'regno')->where('degree', $degree)->where('department', $department)->where('section', $section)->where('year', $year)->orderBy('name', 'asc')->get();
        if(!empty($students)){
            return response()->json(["status"=>"success", "students_data"=>$students]);
        }else{
            return response()->json(["status"=>"error", "msg"=>"Student not found"]);
        }
    }

    public function submit_attendance(Request $request)
    {
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
                            $status = $s['attStatus'];
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

    public function student_view_attendance(Request $request)
    {
        $student = Auth::user();
        $attendances = $student->attendances()->select(['attendance_status','subject_code'])->where('year', $student->year)->get()->groupBy('subject_code');
        $attendance_data = [];
        foreach ($attendances as $sub_code => $attendance) {
            $present_count = 0;
            $total_hours = 0;
            foreach ($attendance as $att) {
                $total_hours++;
                if($att['attendance_status'] == 1){
                    $present_count++;
                }
            }
            array_push($attendance_data, [
                'subject_code' => $sub_code,
                'attended_hours' => $present_count,
                'total_hours' => $total_hours,
                'absent_hours' => $total_hours - $present_count,
            ]);
        }
        return response()->json(["status"=>"success", "attendance_data"=>$attendance_data]);
    }
    
    public function student_view_detailed_attendance(Request $request)
    {
        if(!is_null($request->input('subject_code'))){
            $student = Auth::user();
            $attendances = $student->attendances()->select(['attendance_status', 'date', 'lecture_number'])->where('year', $student->year)->where('subject_code', $request->input('subject_code'))->get();
            return response()->json(["status"=>"success", "attendance_data"=>$attendances]);
        }else{
            return response()->json(['status'=>'error', 'msg' => 'Please provide subject code']);
        }
    }
}
