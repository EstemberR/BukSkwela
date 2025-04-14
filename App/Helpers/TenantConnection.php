<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Config;

class TenantConnection
{
    /**
     * Set up tenant database connection based on subdomain
     *
     * @return bool True if connection was successfully set up
     */
    public static function setup()
    {
        try {
            // Try to get from config first
            $tenantDb = config('app.tenant_db');
            
            // Then try to get from session
            if (!$tenantDb && session('tenant_db')) {
                $tenantDb = session('tenant_db');
            }
            
            // Final fallback: extract from host
            if (!$tenantDb) {
                $host = Request::getHost();
                $parts = explode('.', $host);
                $subdomain = null;
                
                // For localhost with port
                if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) {
                    if (count($parts) >= 2) {
                        $subdomain = $parts[0];
                    }
                } else if (count($parts) > 2) {
                    $subdomain = $parts[0];
                }
                
                if ($subdomain && $subdomain != 'www') {
                    $tenantDb = "tenant_{$subdomain}";
                }
            }
            
            if ($tenantDb) {
                // Configure database connection
                Config::set('database.connections.tenant.database', $tenantDb);
                DB::purge('tenant');
                DB::reconnect('tenant');
                
                // Set as default connection
                DB::setDefaultConnection('tenant');
                
                // Store for persistence
                Config::set('app.tenant_db', $tenantDb);
                
                // Store in session if available
                if (session()) {
                    session(['tenant_db' => $tenantDb]);
                }
                
                // Check connection by running a simple query
                DB::connection('tenant')->select('SELECT 1');
                
                Log::info('TenantConnection: Connection established', [
                    'database' => $tenantDb
                ]);
                
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error('TenantConnection: Error setting up connection', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return false;
        }
    }
    
    /**
     * Get the current tenant database name
     *
     * @return string|null
     */
    public static function getCurrentDatabase()
    {
        return config('app.tenant_db') ?? session('tenant_db') ?? null;
    }
    
    /**
     * Run a callback with tenant connection setup
     *
     * @param callable $callback
     * @return mixed
     */
    public static function run(callable $callback)
    {
        $previousConnection = DB::getDefaultConnection();
        
        try {
            self::setup();
            $result = $callback();
            DB::setDefaultConnection($previousConnection);
            return $result;
        } catch (\Exception $e) {
            DB::setDefaultConnection($previousConnection);
            throw $e;
        }
    }
} 