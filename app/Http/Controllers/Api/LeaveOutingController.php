<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Outing;
use App\Leave;
use Auth;
use Illuminate\Validation\ValidationException;
use Encrypto;
use Decrypto;
use Mail;
use App\Mail\StudentLeftCampusOuting;
use App\Mail\StudentLeftCampusLeave;

class LeaveOutingController extends Controller
{
    public function fetch_outing(Request $request)
    {
        $user = Auth::user();
        $limit = $request->input('length') ?? 15;
        $offset = $request->input('start') ?? 0;
        if($user["user_type"] == 0){
            $outing_requests = Outing::with(['applied_by', 'approved_by'])->orderBy('updated_at','desc')->take($limit)->offset($offset)->get();
        }else if($user["user_type"] == 1){
            $outing_requests = $user->outings()->select('id', 'date', 'out_time', 'in_time', 'visit_to', 'reason', 'status')->latest('updated_at')->limit($limit)->offset($offset)->get();
        }else{
            return response('Unauthorized', 403);
        }
        return response()->json(["status" => "success", "data" => $outing_requests]);
    }

    public function fetch_leave(Request $request)
    {
        $user = Auth::user();
        $limit = $request->input('length') ?? 15;
        $offset = $request->input('start') ?? 0;
        if($user["user_type"] == 0){
            $leave_requests = Leave::with(['applied_by', 'approved_by'])->orderBy('updated_at')->take($limit)->offset($offset)->get();
        }else if($user["user_type"] == 1){
            $leave_requests = $user->leaves()->select('id', 'out_date', 'in_date', 'visit_to', 'reason', 'status')->latest('updated_at')->limit($limit)->offset($offset)->get();
        }else{
            return response('Unauthorized', 403);
        }
        return response()->json(["status" => "success", "data" => $leave_requests]);
    }

    public function apply_outing(Request $request)
    {
        $user = Auth::user();
        $validatedData = $request->validate([
            'visit_to' => 'required',
            'reason' => 'required',
        ]);
        $out_time = (new \DateTime())->setTimestamp($request->input('out_time'));
        $in_time = (new \DateTime())->setTimestamp($request->input('in_time'));
        if($out_time == $in_time){
            throw ValidationException::withMessages([
                "out_time" => 'Out time and in time cannot be same.'
            ]);
        }else if(date($request->input("out_time")) > date($request->input("in_time"))){
            throw ValidationException::withMessages([
                "out_time" => 'Out time cannot be after in time.'
            ]);
        }
        $create_result = $user->outings()->create([
            'date' => date("Y-m-d", (new \DateTime())->getTimestamp()),
            'out_time' => $out_time,
            'in_time' => $in_time,
            'visit_to' => $request->input('visit_to'),
            'reason' => $request->input('reason'),
        ]);
        return response()->json(["status" => "success","msg" => "Outing request created successfully", "data" => $create_result]);
    }

    public function apply_leave(Request $request)
    {
        $user = Auth::user();
        $validatedData = $request->validate([
            'visit_to' => 'required',
            'reason' => 'required',
        ]);
        $out_date = date("Y-m-d", $request->input('out_date'));
        $in_date = date("Y-m-d", $request->input('in_date'));
        if($out_date == $in_date){
            throw ValidationException::withMessages([
                "out_date" => 'Out date and in date cannot be same.'
            ]);
        }else if(date($request->input("out_date")) > date($request->input("in_date"))){
            throw ValidationException::withMessages([
                "out_date" => 'Out date cannot be after in date.'
            ]);
        }
        $create_result = $user->leaves()->create([
            'out_date' => $out_date,
            'in_date' => $in_date,
            'visit_to' => $request->input('visit_to'),
            'reason' => $request->input('reason'),
        ]);
        return response()->json(["status" => "success", "msg" => "Leave request created successfully", "data" => $create_result]);
    }

    public function update_outing(Request $request)
    {
        $user = Auth::user();
        $outing = Outing::where("id", $request->input("outing_id"))->first();
        $limit = $request->input('length') ?? 15;
        $offset = $request->input('start') ?? 0;
        if($outing["status"] == -1 || $outing["status"] == 0){
            switch(strtolower($request->input('status'))){
                case "approved":{
                    $outing->status = 1;
                    break;
                }
                case "rejected":{
                    $outing->status = 2;
                    break;
                }
                default:{
                    $outing->status = -1;
                    break;
                }
            }
            $outing->approved_by = $user["id"];
            $outing->save();
            $outing_requests = Outing::with(['applied_by', 'approved_by'])->orderBy('updated_at','desc')->take($limit)->offset($offset)->get();
            return response()->json(["status"=> "success", 'msg'=> 'Outing request\'s status updated successfully', 'data'=>$outing_requests]);
        }else{
            return response()->json(['status'=>'error', 'msg'=>'Request is already approved/rejected']);
        }
    }

    public function update_leave(Request $request)
    {
        $user = Auth::user();
        $leave = Leave::where("id", $request->input("leave_id"))->first();
        $limit = $request->input('length') ?? 15;
        $offset = $request->input('start') ?? 0;
        if($leave["status"] == -1 || $leave["status"] == 0){
            switch(strtolower($request->input('status'))){
                case "approved":{
                    $leave->status = 1;
                    break;
                }
                case "rejected":{
                    $leave->status = 2;
                    break;
                }
                default:{
                    $leave->status = -1;
                    break;
                }
            }
            $leave->approved_by = $user["id"];
            $leave->save();
            $leave_requests = Leave::with(['applied_by', 'approved_by'])->orderBy('updated_at','desc')->take($limit)->offset($offset)->get();
            return response()->json(["status"=> "success", "msg"=> "Leave request's status updated successfully", "data"=>$leave_requests]);
        }else{
            return response()->json(['status'=>'error', 'msg'=>'Request is already approved/rejected']);
        }
    }

    public function verify_leave_outing(Request $request)
    {
        if($request->input('hash') !== null &&strlen($request->input('hash')) === 100){
            $hash = $request->input('hash');
            $obj = new Decrypto();
            $result = $obj->decpCode($hash,2);
            if($result["status"] == 1){
                $requestType = $result["data"][0];
                $id = $result["data"][1];
                if($requestType == 1) {
                    $data = Leave::where('id', $id)->with('applied_by')->first();
                } else {
                    $data = Outing::where('id', $id)->with('applied_by')->first();
                }
                if($data !== null) {
                    // Send email notification to parents
                    $student_ed = \App\StudentExtraDetail::where('user_id', $data['applied_by'])->first();
                    if($requestType == 'leave') {
                        $mail = new StudentLeftCampusLeave(Leave::where('id', $id)->first());
                    } else {
                        $mail = new StudentLeftCampusOuting(Outing::where('id', $id)->first());
                    }
                    if(isset($student_ed)){
                        Mail::to($student_ed->parent_email)->subject('Student campus left notification')->send($mail);
                    }
                    return response()->json(["status"=>"success", "data"=>$data]);
                } else {
                    return response()->json(["status"=>"error", "msg"=>"Leave/Outing request doesn't exist"]);
                }
            } else {
                return response()->json(["status"=>"error", "msg"=>"Invalid Code. Please Try Again."]);
            }
        } else {
            return response()->json(["status"=>"error", "msg"=>"Invalid Code"]);
        }
        var_dump($request->all());die;
    }
}
