<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Tenant;
use Symfony\Component\HttpFoundation\Response;

class CheckTenantStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (tenant()) {
            // Skip status check if the user is already on the status page
            if ($request->route()->getName() === 'tenant.status') {
                return $next($request);
            }
            
            // Skip status check if the user is on the login page
            if ($request->route()->getName() === 'tenant.login') {
                return $next($request);
            }
            
            $tenant = Tenant::find(tenant('id'));
            
            if (!$tenant || $tenant->status !== 'approved') {
                // Instead of showing the inactive view directly, redirect to the status page
                return redirect()->route('tenant.status');
            }
        }
        
        return $next($request);
    }
} 