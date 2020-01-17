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
        'name', 'email', 'password', 'regno', 'user_type', 'secret'
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

    function outings(){
        return $this->hasMany('App\Outing', 'applied_by', 'id');
    }

    function leaves(){
        return $this->hasMany('App\Leave', 'applied_by', 'id');
    }

    function admin_outing(){
        return $this->hasMany('App\Outing', 'approved_by', 'id');
    }

    function admin_leave(){
        return $this->hasMany('App\Leave', 'approved_by', 'id');
    }
}
