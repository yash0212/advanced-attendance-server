<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoginValidator extends Model
{
    protected $fillable = [
        'device_id', 'user_id',
    ];
}
