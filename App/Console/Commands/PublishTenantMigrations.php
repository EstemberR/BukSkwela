<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Facades\Tenancy;

class PublishTenantMigrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:publish-migrations {tenant? : The tenant ID to publish migrations for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish migrations to the current tenant or a specific tenant';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenant = $this->argument('tenant');
        $tenantId = null;
        
        try {
            if ($tenant) {
                // Set the tenant explicitly
                $tenantId = $tenant;
                
                // Get tenant from the tenancy system
                $tenantObj = Tenancy::find($tenant);
                if (!$tenantObj) {
                    $this->error("Tenant {$tenant} not found");
                    return 1;
                }
                
                // Initialize tenant
                tenancy()->initialize($tenantObj);
                $this->info("Set current tenant to: $tenant");
            } else {
                // Use current tenant
                $tenantId = tenant('id');
                if (!$tenantId) {
                    $this->error('No current tenant set and no tenant ID provided');
                    return 1;
                }
                $this->info("Using current tenant: $tenantId");
            }
            
            // Set tenant database name
            $dbName = 'tenant_' . strtolower($tenantId);
            
            // Ensure database exists
            $this->info("Checking if database {$dbName} exists");
            try {
                $dbExists = DB::select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$dbName]);
                
                if (empty($dbExists)) {
                    $this->error("Database {$dbName} does not exist");
                    return 1;
                }
                
                $this->info("Database {$dbName} exists");
            } catch (\Exception $e) {
                $this->error("Error checking database: " . $e->getMessage());
                return 1;
            }
            
            // Set the tenant database name in the connection config
            config(['database.connections.tenant.database' => $dbName]);
            DB::connection('tenant')->reconnect();
            
            $this->info("Running migration for tenant database: {$dbName}");
            
            // Run migrations with tenant database
            $output = Artisan::call('migrate', [
                '--database' => 'tenant',
                '--path' => 'database/migrations'
            ]);
            
            $this->info("Migration output: " . Artisan::output());
            
            return 0;
        } catch (\Exception $e) {
            $this->error("Error publishing migrations: " . $e->getMessage());
            Log::error("Error publishing migrations: " . $e->getMessage(), [
                'tenant' => $tenantId,
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
} 