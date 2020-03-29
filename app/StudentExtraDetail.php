<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentExtraDetail extends Model
{
    protected $fillable = [
        'user_id', 'student_phone_number', 'parent_phone_number', 'parent_email'
    ];

    function student() {
        return $this->belongsTo('App\User');
    }
}
