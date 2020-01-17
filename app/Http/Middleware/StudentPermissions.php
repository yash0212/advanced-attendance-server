<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class StudentPermissions
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
        if((Auth::user())["user_type"] == 1){
            return $next($request);
        }
        return response('Unauthorized', 403);
    }
}
