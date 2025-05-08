<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Tenant;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (tenant()) {
            // Skip subscription check for certain routes
            $exemptRoutes = [
                'tenant.login',
                'tenant.status',
                'tenant.subscription.expired',
                'tenant.subscription.payment'
            ];
            
            if (in_array($request->route()->getName(), $exemptRoutes)) {
                return $next($request);
            }
            
            $tenant = Tenant::find(tenant('id'));
            
            if (!$tenant) {
                return redirect()->route('tenant.status');
            }
            
            // Check if subscription_plan is null or 'free'
            if ($tenant->subscription_plan === null || $tenant->subscription_plan === 'free') {
                return $next($request);
            }
            
            // Check if subscription has expired
            if ($tenant->data && isset($tenant->data['subscription_ends_at'])) {
                $subscriptionEndsAt = \Carbon\Carbon::parse($tenant->data['subscription_ends_at']);
                
                if ($subscriptionEndsAt->isPast()) {
                    return redirect()->route('tenant.subscription.expired');
                }
            }
        }
        
        return $next($request);
    }
} 