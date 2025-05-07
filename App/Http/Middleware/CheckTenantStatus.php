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
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Get the current tenant
        $tenant = tenant();
        
        // If no tenant is resolved, let the request pass
        if (!$tenant) {
            return $next($request);
        }

        // For API requests, return a JSON response for disabled tenants
        if ($request->expectsJson() && $tenant->status === 'disabled') {
            return response()->json([
                'error' => 'This account has been disabled.',
                'status' => 'disabled'
            ], 403);
        }
        
        // Check if the tenant is disabled - show the custom disabled page
        if ($tenant->status === 'disabled') {
            return response()->view('disabled', [
                'tenant' => $tenant
            ], 403);
        }
        
        // Handle other statuses (pending, etc.) with your existing logic
        if ($tenant->status !== 'approved') {
            // Skip status check if the user is on the status page or login page
            if ($request->route() && 
                ($request->route()->getName() === 'tenant.status' || 
                 $request->route()->getName() === 'tenant.login')) {
                return $next($request);
            }
            
            // Redirect to the status page for non-approved, non-disabled tenants
            if ($request->route() && $request->route()->getName() !== 'tenant.status') {
                return redirect()->route('tenant.status');
            }
        }
        
        return $next($request);
    }
} 