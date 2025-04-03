<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TenantAdmin;
use Symfony\Component\HttpFoundation\Response;

class TenantAuthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('admin')->check()) {
            $admin = Auth::guard('admin')->user();
            if ($admin->tenant_id !== tenant('id')) {
                Auth::guard('admin')->logout();
                return redirect()->route('tenant.login')
                    ->with('error', 'You are not authorized for this tenant.');
            }
        }
        return $next($request);
    }
}
