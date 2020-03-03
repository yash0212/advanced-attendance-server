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
    Route::get('/student-view-attendance', 'Api\AttendanceController@student_view_attendance');
    Route::get('/student-view-detailed-attendance', 'Api\AttendanceController@student_view_detailed_attendance');
});
Route::group(['middleware' => ['auth:api', 'admin']], function () {
    Route::post('/update-outing', 'Api\LeaveOutingController@update_outing');
    Route::post('/update-leave', 'Api\LeaveOutingController@update_leave');
});
Route::group(['middleware' => ['auth:api', 'guard']], function () {
    Route::post('/verify-leave-outing', 'Api\LeaveOutingController@verify_leave_outing');
});
Route::group(['middleware' => ['auth:api', 'teacher']], function () {
    Route::post('/fetch-students-detail', 'Api\AttendanceController@fetch_students_detail');
    Route::post('/submit-attendance', 'Api\AttendanceController@submit_attendance');
});

Route::post('/register', 'Api\AuthController@register');
Route::post('/login', 'Api\AuthController@login');
