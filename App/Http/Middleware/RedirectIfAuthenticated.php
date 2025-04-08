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
            if (Auth::user()->role === 'superadmin') {
                return redirect()->route('superadmin.dashboard');
            }
            return redirect('/');
        }

        return $next($request);
    }
}
