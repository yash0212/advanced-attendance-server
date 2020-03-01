<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [ 
        'lecture_number', 'subject_code', 'degree', 'department', 'section', 'year', 'marked_by', 'student_id',
    ];

    function marked_by(){
        return $this->belongsTo('App\User', 'marked_by');
    }

    function marked_for(){
        return $this->belongsTo('App\User', 'student_id');
    }
}
