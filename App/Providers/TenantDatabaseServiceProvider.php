<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class TenantDatabaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        try {
            // Get host and extract subdomain
            $host = Request::getHost();
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
            
            Log::info('TenantDatabaseServiceProvider: Tenant detection', [
                'host' => $host,
                'parts' => $parts,
                'subdomain' => $subdomain
            ]);
            
            if ($subdomain && $subdomain != 'www') {
                // Database name for tenant
                $tenantDb = "tenant_{$subdomain}";
                
                Log::info('TenantDatabaseServiceProvider: Setting up tenant connection', [
                    'subdomain' => $subdomain,
                    'database' => $tenantDb
                ]);
                
                // Configure database connection to use tenant database
                config(['database.connections.tenant.database' => $tenantDb]);
                
                // Make sure the tenant connection is established for application-wide use
                DB::purge('tenant');
                DB::reconnect('tenant');
                
                // Store in session for persistence between requests
                if (session()) {
                    session(['tenant_db' => $tenantDb]);
                    session(['tenant_subdomain' => $subdomain]);
                }
                
                // Store in config for access from anywhere
                config(['app.tenant_db' => $tenantDb]);
                config(['app.tenant_subdomain' => $subdomain]);
                
                // Configure auth to use tenant connection
                config(['auth.guards.student.provider' => 'students']);
                config(['auth.providers.students.driver' => 'eloquent']);
                config(['auth.providers.students.model' => '\App\Models\Student']);
            }
        } catch (\Exception $e) {
            Log::error('TenantDatabaseServiceProvider: Error setting up tenant connection', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
} 