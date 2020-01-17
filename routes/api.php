<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();

});
Route::group(['middleware' => ['auth:api']], function () {
    Route::get('/fetch-outing', 'Api\LeaveOutingController@fetch_outing');
    Route::get('/fetch-leave', 'Api\LeaveOutingController@fetch_leave');
});
Route::group(['middleware' => ['auth:api', 'student']], function () {
    Route::post('/apply-outing', 'Api\LeaveOutingController@apply_outing');
    Route::post('/apply-leave', 'Api\LeaveOutingController@apply_leave');
});
Route::post('/register', 'Api\AuthController@register');
Route::post('/login', 'Api\AuthController@login');
