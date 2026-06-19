<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Symfony\Component\HttpFoundation\Response;

class AuthAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {   
        if(Auth::check())
        {
            // utype = usertype
           if(Auth::user()->usertype==='ADM')
           {
            return $next($request);
           } 
           else{
            // Log the user out and invalidate session instead of flushing everything abruptly
            \Illuminate\Support\Facades\Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Access denied: admin only area.');
           }
        }
        else{
            return redirect()->route('login');
        }
       
    }
}
