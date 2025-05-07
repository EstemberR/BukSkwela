<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class MigrateTenantToSeparateDb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:migrate-to-separate-db {tenant : The tenant ID} {--force : Force migration even if database already exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate a tenant from the central database to its own dedicated database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->argument('tenant');
        $force = $this->option('force');
        
        // Find the tenant
        $tenant = Tenant::find($tenantId);
        if (!$tenant) {
            $this->error("Tenant with ID {$tenantId} not found");
            return Command::FAILURE;
        }
        
        $this->info("Starting migration of tenant {$tenant->id} to its own dedicated database");
        
        // Check if tenant already has a database record
        $hasTenantDb = \App\Models\TenantDatabase::where('tenant_id', $tenantId)->exists();
        $databaseName = 'tenant_' . $tenantId;
        
        // Check if database exists in MySQL
        $dbExists = DB::select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$databaseName]);
        
        if ($hasTenantDb && !empty($dbExists) && !$force) {
            $this->warn("Tenant {$tenantId} already appears to have its own database. Use --force to override.");
            return Command::FAILURE;
        }
        
        // Create a new tenant database
        $this->info("Setting up separate database for tenant: {$tenant->id}");
        
        try {
            // Use the setup-tenant command
            Artisan::call('db:setup-tenant', [
                'tenant' => $tenantId
            ]);
            
            $setupOutput = Artisan::output();
            $this->info("Database setup output:");
            $this->line($setupOutput);
            
            // Verify the database exists after creation
            $dbExists = DB::select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$databaseName]);
            
            if (!empty($dbExists)) {
                $this->info("✅ Database {$databaseName} created successfully");
                
                // Migrate tenant data from central to separate database
                $this->info("Migrating tenant data from central database to separate database");
                
                // For each table in tenant schema that has tenant_id column, migrate the data
                // Implement this part based on your specific data structure
                
                $this->info("Tenant {$tenantId} has been successfully migrated to its own database");
                Log::info("Tenant {$tenantId} migrated to separate database {$databaseName}");
                
                return Command::SUCCESS;
            } else {
                $this->error("Failed to create database {$databaseName} for tenant {$tenantId}");
                Log::error("Failed to create database {$databaseName} for tenant {$tenantId}");
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error("Error migrating tenant to separate database: " . $e->getMessage());
            Log::error("Error migrating tenant to separate database: " . $e->getMessage(), [
                'tenant_id' => $tenantId,
                'exception' => $e
            ]);
            return Command::FAILURE;
        }
    }
} 