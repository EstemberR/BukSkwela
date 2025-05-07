<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Stancl\Tenancy\Events\TenancyInitialized;
use App\Models\TenantDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Stancl\Tenancy\Events\TenantCreated;

class TenancyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->configureMiddleware();
        
        // Configure central domains
        config(['tenancy.central_domains' => [
            '127.0.0.1',
            '127.0.0.1:8000',
            'localhost',
            'localhost:8000'
        ]]);

        // Configure tenant identification
        config(['tenancy.identification_driver' => 'domain']);
        
        // Configure session
        config([
            'session.domain' => null,
            'session.same_site' => null,
            'session.secure' => false,
            'session.http_only' => true
        ]);

        // Listen for tenancy initialized event to set up custom database connection
        Event::listen(TenancyInitialized::class, function(TenancyInitialized $event) {
            $this->configureTenantDatabaseConnection($event->tenancy->tenant);
        });

        // Register tenant created event to create required tenant tables
        $this->registerTenantCreatedListener();
    }

    protected function configureMiddleware()
    {
        $this->app['router']->aliasMiddleware('tenant', InitializeTenancyByDomain::class);
        $this->app['router']->aliasMiddleware('prevent-access-from-central-domains', PreventAccessFromCentralDomains::class);
    }

    /**
     * Configure tenant database connection with separate credentials
     */
    protected function configureTenantDatabaseConnection($tenant): void
    {
        try {
            // Get the tenant database record with credentials
            $tenantDb = TenantDatabase::where('tenant_id', $tenant->getTenantKey())->first();
            
            if ($tenantDb) {
                // Get the database name for this tenant
                $databaseName = $tenantDb->database_name;
                
                // Get the tenant connection name
                $connectionName = Config::get('tenancy.database.tenant_connection_name', 'tenant');
                
                // Update the tenant connection config with dedicated credentials
                Config::set('database.connections.' . $connectionName, [
                    'driver' => 'mysql',
                    'host' => $tenantDb->database_host,
                    'port' => $tenantDb->database_port,
                    'database' => $tenantDb->database_name,
                    'username' => $tenantDb->database_username,
                    'password' => $tenantDb->database_password,
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'prefix' => '',
                    'prefix_indexes' => true,
                    'strict' => true,
                    'engine' => null,
                ]);
                
                // Refresh the tenant connection
                DB::purge($connectionName);
                DB::reconnect($connectionName);
                
                // Log connection success
                \Log::info("Connected to tenant database with separate credentials", [
                    'tenant_id' => $tenant->getTenantKey(),
                    'database' => $databaseName
                ]);
            } else {
                // Log error
                \Log::error("Could not find database credentials for tenant", [
                    'tenant_id' => $tenant->getTenantKey()
                ]);
            }
        } catch (\Exception $e) {
            // Log error
            \Log::error("Error configuring tenant database connection: " . $e->getMessage(), [
                'tenant_id' => $tenant->getTenantKey(),
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Register a listener for when a tenant is created to create required tenant tables
     */
    private function registerTenantCreatedListener(): void
    {
        Event::listen(TenantCreated::class, function (TenantCreated $event) {
            // Create the students_informations table in the tenant database
            Artisan::call('tenants:create-student-info-table', [
                'tenant_id' => $event->tenant->id
            ]);
        });
    }
}