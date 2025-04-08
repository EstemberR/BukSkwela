<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TenantDatabaseMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $tenant = tenant();
        
        if ($tenant) {
            $tenantId = $tenant->id;
            $databaseName = 'tenant_' . $tenantId;
            
            // Get the tenant database credentials from the tenant_databases table
            $tenantDB = \App\Models\TenantDatabase::where('tenant_id', $tenantId)->first();
            
            if ($tenantDB) {
                // Set the database connection with proper credentials from tenant_databases table
                Config::set('database.connections.tenant.database', $tenantDB->database_name);
                Config::set('database.connections.tenant.username', $tenantDB->database_username);
                Config::set('database.connections.tenant.password', $tenantDB->database_password);
                Config::set('database.connections.tenant.host', $tenantDB->database_host);
                Config::set('database.connections.tenant.port', $tenantDB->database_port);
                
                Log::info("Setting tenant database connection for tenant {$tenantId} using tenant-specific credentials");
            } else {
                // Fallback to default credentials with tenant database name
                Config::set('database.connections.tenant.database', $databaseName);
                Config::set('database.connections.tenant.username', env('DB_USERNAME'));
                Config::set('database.connections.tenant.password', env('DB_PASSWORD'));
                
                Log::warning("No tenant database record found for {$tenantId}, using default credentials");
            }
            
            // Purge the tenant connection to force a new connection with the updated config
            DB::purge('tenant');
            
            // Reconnect to the tenant database
            DB::reconnect('tenant');
            
            try {
                // Test the connection
                DB::connection('tenant')->getPdo();
                Log::info("Successfully connected to tenant database {$databaseName}");
            } catch (\Exception $e) {
                Log::error("Failed to connect to tenant database: " . $e->getMessage());
            }
        }

        return $next($request);
    }
} 