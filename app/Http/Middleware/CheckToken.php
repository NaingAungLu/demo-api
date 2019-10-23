<?php

namespace App\Http\Middleware;

use Log;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckToken
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
        if(!$request->bearerToken() && $request->query('token')) {
            $request->headers->set('Authorization', 'Bearer ' . $request->query('token'));
        }
        
        $token = trim($request->bearerToken());
        if(!empty($token)) {
            if(Auth::guard('api')->check()) {
                return $next($request);
            }
            return response()->json('Invalid Token', 403);
        }

        return response()->json('Authentication Required', 403);
    }
}
