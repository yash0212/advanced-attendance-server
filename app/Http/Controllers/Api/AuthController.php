<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\LoginValidator;
use Encrypto;
use Decrypto;
use Carbon\Carbon;
use Auth;
use Mail;

class AuthController extends Controller
{
    public function register(Request $request) {
        $validatedData = $request->validate([
            'email' => 'required|email|unique:users',
            'name' => 'required|max:255',
            'password' => 'required|confirmed',
            'regno' => 'max:255',
        ]);
        $validatedData["password"] = bcrypt($validatedData["password"]);
        $validatedData["user_type"] = $request->input("user_type");
        $user = User::create($validatedData);

        $access_token = $user->createToken('authToken')->accessToken;

        return response(['user' => $user, 'access_token' => $access_token]);
    }

    public function login(Request $request) {
        $login_data = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if (!auth()->attempt($login_data)) {
            return response(['message'=>'Invalid Credentials']);
        }
        $user = auth()->user();

        // Decrypt device id from device hash
        $obj = new Decrypto();
        $result = $obj->decpCode($request->input('device_hash'), 1);
        // Check if device hash is valid
        if ($result["status"] == 1) {
            $device_id = $result['data'][0];
            $lv = LoginValidator::where('device_id', $device_id)->orderBy('updated_at', 'desc')->first();
            //Check if device is already used for login
            if ($lv != null) {
                // Check if same user is logging in which was previously logged in
                if ($lv->user_id == $user->id) {
                    $temp_lv = LoginValidator::updateOrCreate([
                        'device_id' => $device_id,
                        'user_id' => $user->id
                    ]);
                    $temp_lv->updated_at = Carbon::now();
                    $temp_lv->save();
                    $access_token = $user->createToken('authToken')->accessToken;
                    return response(['user' => $user, 'access_token' => $access_token]);
                } else {
                    // New user is trying to login into device used by some other user

                    // Send sms notification for new login

                    // Check if user is student
                    if ($user->user_type == 1) {
                        $date = $lv->updated_at->timestamp;
                        $now = Carbon::now()->timestamp;
                        //Check if difference in time between loggin si more than 10 mins
                        if (($now - $date) > 600) {
                            $temp_lv = LoginValidator::updateOrCreate([
                                'device_id' => $device_id,
                                'user_id' => $user->id
                            ]);
                            $temp_lv->updated_at = Carbon::now();
                            $temp_lv->save();
                            $access_token = $user->createToken('authToken')->accessToken;
                            return response(['user' => $user, 'access_token' => $access_token]);
                        } else {
                            //Restrict user from login
                            return response(['message'=>'Login allowed 10 min after previous logout']);
                        }
                    }
                    //Login user
                    $temp_lv = LoginValidator::updateOrCreate([
                        'device_id' => $device_id,
                        'user_id' => $user->id
                    ]);
                    $temp_lv->updated_at = Carbon::now();
                    $temp_lv->save();
                    $access_token = $user->createToken('authToken')->accessToken;
                    return response(['user' => $user, 'access_token' => $access_token]);
                }
            } else {
                // New device login
                $temp_lv = LoginValidator::updateOrCreate([
                    'device_id' => $device_id,
                    'user_id' => $user->id
                ]);
                $temp_lv->updated_at = Carbon::now();
                $temp_lv->save();
                $access_token = $user->createToken('authToken')->accessToken;
                return response(['user' => $user, 'access_token' => $access_token]);
            }
        } else {
            //Device hash is invalid
            return response(['message'=>'This device is not supported']);
        }
    }

    public function test(Request $request) {
        $leave = \App\Leave::where('id', 1)->first();
        $mail = new \App\Mail\StudentLeftCampusLeave($leave);
        Mail::to('yashlotan7@gmail.com')->send($mail);
        var_dump($mail);
        die;
    }

    public function update_student_phone_number(Request $request) {
        $regno = $request->input('regno');
        $s_phno = $request->input('student_phone_number');
        $p_phno = $request->input('parent_phone_number');
        $p_email = $request->input('parent_email');

        if (isset($regno, $s_phno, $p_phno, $p_email) && is_numeric($s_phno) && is_numeric($p_phno)) {
            $user = Auth::user();
            $student = User::where('regno', $regno)->first();
            if ($student != null) {
                $student->extra_details()->UpdateOrCreate([],[
                    'student_phone_number' => $s_phno,
                    'parent_phone_number' => $p_phno,
                    'parent_email' => $p_email
                ]);
                return response(['status' => 'success', 'message'=>'Student details updated successfully']);
            } else {
                return response(['status' => 'error', 'message'=>'Student not found']);
            }
        } else {
            if(isset($s_phno, $p_phno) && (!is_numeric($s_phno) || !is_numeric($p_phno))) {
                return response(['status' => 'error', 'message'=>'Phone number should only contain numbers']);
            }
            return response(['status' => 'error', 'message'=>'Missing Parameters']);
        }
    }
}
