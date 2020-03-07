<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'regno', 'degree_id', 'department_id', 'section', 'year', 'user_type', 'secret', 
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    //Function to fetch degree of students
    function degree() {
        return $this->belongsTo('App\Degree');
    }

    //Function to fetch department of students
    function department() {
        return $this->belongsTo('App\Department');
    }

    //Function to fetch outing requests for students
    function outings(){
        return $this->hasMany('App\Outing', 'applied_by', 'id');
    }

    //Function to getch leave requests for students
    function leaves(){
        return $this->hasMany('App\Leave', 'applied_by', 'id');
    }

    //Function to fetch outing requests for admin
    function admin_outing(){
        return $this->hasMany('App\Outing', 'approved_by', 'id');
    }

    //Function to fetch leave requests for admin
    function admin_leave(){
        return $this->hasMany('App\Leave', 'approved_by', 'id');
    }

    //Function to fetch attendance marked by teacher
    function marked_attendances(){
        return $this->hasMany('App\Attendance', 'marked_by', 'id');
    }

    // Function to fetch student's attendance
    function attendances(){
        return $this->hasMany('App\Attendance', 'student_id', 'id');
    }
}
