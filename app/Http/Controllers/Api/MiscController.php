<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Subject;
use App\Degree;
use App\Department;
use Encrypto;
use Decrypto;

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

    //Function to encrypt using encrypto
    public function get_encrypted_code(Request $request) {
        $params = $request->input('data');
        $obj = new Encrypto();
        $result = $obj->getCode(...$params);
        return response()->json($result);
    }

    //Function to encrypt using encrypto
    public function get_decrypted_data(Request $request) {
        $params = $request->input('hash');
        $obj = new Decrypto();
        $result = $obj->decpCode($params,2);
        return response()->json($result);
    }
}
