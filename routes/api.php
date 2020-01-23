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
    // Route::post('/fetch-outing-code', 'Api\LeaveOutingController@fetch_outing_code');
    // Route::post('/fetch-leave-code', 'Api\LeaveOutingController@fetch_leave_code');
});
Route::group(['middleware' => ['auth:api', 'admin']], function () {
    Route::post('/update-outing', 'Api\LeaveOutingController@update_outing');
    Route::post('/update-leave', 'Api\LeaveOutingController@update_leave');
});
Route::group(['middleware' => ['auth:api', 'guard']], function () {
    Route::post('/verify-leave-outing', 'Api\LeaveOutingController@verify_leave_outing');
});
Route::post('/register', 'Api\AuthController@register');
Route::post('/login', 'Api\AuthController@login');
