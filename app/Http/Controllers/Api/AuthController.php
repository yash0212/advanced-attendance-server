<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Encrypto;
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

        if(!auth()->attempt($login_data)){
            return response(['message'=>'Invalid Credentials']);
        }
        $user = auth()->user();
        $access_token = $user->createToken('authToken')->accessToken;
        return response(['user' => $user, 'access_token' => $access_token]);
    }

    public function test(Request $request) {
        $obj = new Encrypto();
        $result = $obj->getCode(2,14,203,1,1,2,4);
        var_dump($result);die;
    }
}
