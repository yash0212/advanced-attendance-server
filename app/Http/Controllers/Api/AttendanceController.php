<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;

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

    public function mark_attendance(Request $request)
    {
        $attendance_data = $request->input('attendance_data');
    }
}
