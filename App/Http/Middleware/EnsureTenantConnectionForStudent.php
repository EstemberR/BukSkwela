<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EnsureTenantConnectionForStudent
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
        try {
            // Get host and extract subdomain
            $host = $request->getHost();
            $parts = explode('.', $host);
            $subdomain = count($parts) > 1 ? $parts[0] : null;
            
            // For localhost with port (e.g., testing.localhost:8000)
            if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) {
                if (count($parts) >= 2) {
                    $subdomain = $parts[0];
                }
            }
            
            Log::info('Tenant detection', [
                'host' => $host,
                'parts' => $parts,
                'subdomain' => $subdomain,
                'path' => $request->path(),
                'uri' => $request->getRequestUri()
            ]);
            
            if ($subdomain && $subdomain != 'www') {
                // Database name for tenant
                $tenantDb = "tenant_{$subdomain}";
                
                Log::info('Setting up tenant connection', [
                    'subdomain' => $subdomain,
                    'database' => $tenantDb,
                    'path' => $request->path()
                ]);
                
                // Configure database connection to use tenant database
                config(['database.connections.tenant.database' => $tenantDb]);
                DB::purge('tenant');
                DB::reconnect('tenant');
                
                // Set connection for auth guards and model
                config(['auth.guards.student.connection' => 'tenant']);
                config(['auth.providers.students.connection' => 'tenant']);
                
                // Set default connection to ensure any model queries use this connection
                DB::setDefaultConnection('tenant');
                
                // Store in session for persistence between requests
                session(['tenant_db' => $tenantDb]);
                session(['tenant_subdomain' => $subdomain]);
                
                // Verify database connection is working
                try {
                    $tables = DB::connection('tenant')->getDoctrineSchemaManager()->listTableNames();
                    Log::info('Tenant connection established', [
                        'tables' => $tables,
                        'database' => $tenantDb
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to connect to tenant database', [
                        'error' => $e->getMessage(),
                        'database' => $tenantDb
                    ]);
                    
                    if ($request->expectsJson()) {
                        return response()->json(['error' => 'Database connection failed: ' . $e->getMessage()], 500);
                    } else {
                        abort(500, 'Cannot connect to tenant database: ' . $e->getMessage());
                    }
                }
            } else {
                Log::info('No subdomain detected or using www subdomain', [
                    'host' => $host
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error in EnsureTenantConnectionForStudent middleware', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Tenant connection error: ' . $e->getMessage()], 500);
            }
        }

        return $next($request);
    }
} 