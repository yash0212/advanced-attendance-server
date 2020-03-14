<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Subject;
use App\Degree;
use App\Department;

class MiscController extends Controller
{
    // Function to fetch all degrees
    public function fetch_degrees(Request $request) {
        $degrees = Degree::select('id', 'name')->get();
        return response()->json(["status" => "success", "data" => $degrees]);
    }

    // Function to fetch all departments
    public function fetch_departments(Request $request) {
        $departments = Department::select('id', 'name')->get();
        return response()->json(["status" => "success", "data" => $departments]);
    }

    // Function to fetch all departments
    public function fetch_subjects(Request $request) {
        $subjects = Subject::select('id', 'name')->get();
        return response()->json(["status" => "success", "data" => $subjects]);
    }
}
