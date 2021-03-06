<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [ 
        'lecture_number', 'subject_id', 'marked_by', 'student_id', 'date', 'attendance_status'
    ];

    function marked_by(){
        return $this->belongsTo('App\User', 'marked_by');
    }

    function marked_for(){
        return $this->belongsTo('App\User', 'student_id');
    }

    function subject(){
        return $this->belongsTo('App\Subject');
    }
}
