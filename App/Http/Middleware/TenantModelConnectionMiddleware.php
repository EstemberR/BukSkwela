<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class TenantModelConnectionMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $tenant = tenant();
        
        if ($tenant) {
            $tenantId = $tenant->id;
            $databaseName = 'tenant_' . $tenantId;
            
            // Set the database connection configuration for tenant with proper credentials
            Config::set('database.connections.tenant.database', $databaseName);
            Config::set('database.connections.tenant.username', env('DB_USERNAME'));
            Config::set('database.connections.tenant.password', env('DB_PASSWORD'));
            
            // Log connection attempt for debugging
            Log::info("TenantModelConnectionMiddleware: Setting tenant database connection for tenant {$tenantId} to database {$databaseName}");
            
            // Purge the tenant connection to force a new connection with the updated config
            DB::purge('tenant');
            
            // Reconnect to the tenant database
            DB::reconnect('tenant');
            
            try {
                // Test the connection
                DB::connection('tenant')->getPdo();
                
                // Set the default connection for all models to tenant
                Model::setConnectionResolver(app('db'));
                Model::setDefaultConnection('tenant');
                
                Log::info("TenantModelConnectionMiddleware: Successfully connected to tenant database {$databaseName} and set default model connection");
            } catch (\Exception $e) {
                Log::error("TenantModelConnectionMiddleware: Failed to connect to tenant database: " . $e->getMessage());
            }
        }

        return $next($request);
    }
} 