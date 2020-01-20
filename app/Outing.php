<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Outing extends Model
{
    protected $fillable = [
        'date', 'out_time', 'in_time', 'visit_to', 'reason',
    ];

    function applied_by(){
        return $this->belongsTo('App\User', 'applied_by');
    }

    function approved_by(){
        return $this->belongsTo('App\User', 'approved_by');
    }
}
