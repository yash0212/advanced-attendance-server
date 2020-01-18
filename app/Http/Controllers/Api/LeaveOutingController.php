<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Outing;
use App\Leave;
use Auth;

class LeaveOutingController extends Controller
{
    public function fetch_outing(Request $request)
    {
        $user = Auth::user();
        $limit = $request->input('length') ?? 15;
        $offset = $request->input('start') ?? 0;
        if($user["user_type"] == 0){
            $outing_requests = Outing::orderBy('updated_at')->take($limit)->offset($offset)->get();
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
            $leave_requests = Leave::orderBy('updated_at')->take($limit)->offset($offset)->get();
        }else if($user["user_type"] == 1){
            $leave_requests = $user->leaves()->latest('updated_at')->limit($limit)->offset($offset);
        }else{
            return response('Unauthorized', 403);
        }
        return response()->json(["status" => "success", "data" => $leave_requests]);
    }

    public function apply_outing(Request $request)
    {
        $user = Auth::user();
        $out_time = (new \DateTime())->setTimestamp($request->input('out_time'));
        $in_time = (new \DateTime())->setTimestamp($request->input('in_time'));
        $create_result = $user->outings()->create([
            'date' => date("Y-m-d", $request->input('date')), 
            'out_time' => $out_time, 
            'in_time' => $in_time, 
            'visit_to' => $request->input('visit_to'), 
            'reason' => $request->input('reason'),
        ]);
        return response()->json(["status" => "success", "data" => $create_result]);
    }

    public function apply_leave(Request $request)
    {
        $user = Auth::user();
        $create_result = $user->leaves()->create([
            'out_date' => date("Y-m-d", $request->input('out_date')), 
            'in_date' => date("Y-m-d", $request->input('in_date')), 
            'visit_to' => $request->input('visit_to'), 
            'reason' => $request->input('reason'),
        ]);
        return response()->json(["status" => "success", "data" => $create_result]);
    }

    public function update_outing(Request $request)
    {
        $user = Auth::user();
        $outing = Outing::where("id", $request->input("outing_id"))->first();
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
            $outing->save();
            return response()->json(["status"=> "success", 'msg'=> 'Outing request\'s status updated successfully']);
        }else{
            return response()->json(['status'=>'error', 'msg'=>'Request is already approved/rejected']);
        }
    }

    public function update_leave(Request $request)
    {
        $user = Auth::user();
        $leave = Leave::where("id", $request->input("leave_id"))->first();
        if($leave["status"] == -1 || $leave["status"] == 0){
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
            $leave->save();
            return response()->json(["status"=> "success", "msg"=> "Leave request's status updated successfully"]);
        }else{
            return response()->json(['status'=>'error', 'msg'=>'Request is already approved/rejected']);
        }
    }

}
