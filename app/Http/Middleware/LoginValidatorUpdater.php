<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App\LoginValidator;
use Carbon\Carbon;

class LoginValidatorUpdater
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $lv = LoginValidator::where([
            'user_id' => Auth::user()->id
        ])->latest('updated_at')->first();
        $lv->updated_at = Carbon::now();
        $lv->save();

        return $next($request);
    }
}
