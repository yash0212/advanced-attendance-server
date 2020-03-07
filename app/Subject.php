<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'name'
    ];

    function attendance() {
        return $this->hasMany('App\Attendance');
    }
}
