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
            
            try {
                // Get the tenant database credentials from the tenant_databases table
                $tenantDB = \App\Models\TenantDatabase::where('tenant_id', $tenantId)->first();
                
                if ($tenantDB) {
                    // Set the database connection with proper credentials from tenant_databases table
                    Config::set('database.connections.tenant.database', $tenantDB->database_name);
                    Config::set('database.connections.tenant.username', $tenantDB->database_username);
                    Config::set('database.connections.tenant.password', $tenantDB->database_password);
                    Config::set('database.connections.tenant.host', $tenantDB->database_host);
                    Config::set('database.connections.tenant.port', $tenantDB->database_port);
                    
                    Log::info("TenantModelConnectionMiddleware: Setting tenant database connection using tenant-specific credentials");
                } else {
                    // Fallback to default credentials with tenant database name
                    Config::set('database.connections.tenant.database', $databaseName);
                    Config::set('database.connections.tenant.username', env('DB_USERNAME'));
                    Config::set('database.connections.tenant.password', env('DB_PASSWORD'));
                    
                    Log::warning("TenantModelConnectionMiddleware: No tenant database record found, using default credentials");
                }
                
                // Double-check that a database name is set before proceeding
                $configuredDbName = Config::get('database.connections.tenant.database');
                if (empty($configuredDbName)) {
                    Log::error("TenantModelConnectionMiddleware: Database name is empty for tenant {$tenantId}, forcing to {$databaseName}");
                    Config::set('database.connections.tenant.database', $databaseName);
                }
                
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
                    
                    // Try to recover by forcing a connection to the fallback database
                    try {
                        // Use standard MySQL credentials but with tenant database name
                        Config::set('database.connections.tenant.database', $databaseName);
                        Config::set('database.connections.tenant.username', env('DB_USERNAME'));
                        Config::set('database.connections.tenant.password', env('DB_PASSWORD'));
                        
                        DB::purge('tenant');
                        DB::reconnect('tenant');
                        DB::connection('tenant')->getPdo();
                        
                        // Set the default connection for all models to tenant
                        Model::setConnectionResolver(app('db'));
                        Model::setDefaultConnection('tenant');
                        
                        Log::info("TenantModelConnectionMiddleware: Recovered connection using fallback credentials");
                    } catch (\Exception $fallbackEx) {
                        Log::error("TenantModelConnectionMiddleware: Failed to recover connection: " . $fallbackEx->getMessage());
                    }
                }
            } catch (\Exception $e) {
                Log::error("Error in TenantModelConnectionMiddleware: " . $e->getMessage());
            }
        }

        return $next($request);
    }
} 