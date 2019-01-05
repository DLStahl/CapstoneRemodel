<?php

namespace App\Http\Middleware;

use Closure;

use App\Resident;
use App\Admin;

class CheckResident
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
        // If the user is not a resident or an admin
        $email = $_SERVER["HTTP_EMAIL"];
        if (Admin::where('email', $email)->where('exists', '1')->doesntExist() && 
            Resident::where('email', $email)->where('exists', '1')->doesntExist()) {
           
            return response('Unauthorized!', 401);            
        }

        return $next($request);
    }
}
