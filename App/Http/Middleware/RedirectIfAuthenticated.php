<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            if (tenant()) {
                return redirect()->route('tenant.dashboard');
            }
            
            $user = Auth::user();
            if ($user && $user->role === 'superadmin') {
                return redirect()->route('superadmin.dashboard');
            }
            
            // Check which guard was authenticated and redirect accordingly
            if ($guard === 'admin') {
                return redirect()->route('tenant.dashboard');
            } else if ($guard === 'student') {
                return redirect('/student/dashboard');
            } else if ($guard === 'staff') {
                return redirect('/staff/dashboard');
            }
            
            return redirect('/');
        }

        return $next($request);
    }
}
