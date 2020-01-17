<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $fillable = [
        'out_date', 'in_date', 'visit_to', 'reason',
    ];

    function applied_by(){
        return $this->belongsTo('App\User');
    }

    function approved_by(){
        return $this->belongsTo('App\User');
    }
}
