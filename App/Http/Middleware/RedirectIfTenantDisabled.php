<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use App\Models\Tenant;

class RedirectIfTenantDisabled
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
        try {
            // Get host and extract subdomain
            $host = $request->getHost();
            $parts = explode('.', $host);
            $subdomain = null;
            
            // For localhost with port (e.g., testing.localhost:8000)
            if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) {
                if (count($parts) >= 2) {
                    $subdomain = $parts[0];
                }
            } else if (count($parts) > 2) {
                $subdomain = $parts[0];
            }
            
            Log::info('RedirectIfTenantDisabled: Checking tenant', [
                'host' => $host,
                'parts' => $parts,
                'subdomain' => $subdomain
            ]);
            
            if ($subdomain && $subdomain != 'www') {
                // Check tenant status in central database
                $tenant = Tenant::find($subdomain);
                
                if ($tenant && in_array($tenant->status, ['disabled', 'rejected', 'denied'])) {
                    Log::info('RedirectIfTenantDisabled: Tenant is disabled', [
                        'subdomain' => $subdomain,
                        'status' => $tenant->status
                    ]);
                    
                    return response()->view('disabled');
                }
            }
        } catch (\Exception $e) {
            Log::error('RedirectIfTenantDisabled: Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
        
        return $next($request);
    }
} 