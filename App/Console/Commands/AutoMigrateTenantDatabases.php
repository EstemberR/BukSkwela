<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use App\Models\Tenant;

class AutoMigrateTenantDatabases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:auto-migrate 
                           {tenant? : Optional tenant ID to migrate only one tenant}
                           {--force : Force migration even if database already has tables}
                           {--batch-size=5 : Number of tenants to process in each batch}
                           {--delay=2 : Delay in seconds between batches}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically create and migrate all tenant databases';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $force = $this->option('force');
        $batchSize = $this->option('batch-size');
        $delay = $this->option('delay');
        $tenantOption = $this->argument('tenant');
        
        if ($tenantOption) {
            $this->info("Starting automatic database migration for tenant: {$tenantOption}");
            
            // Get specific tenant
            $tenant = Tenant::where('id', $tenantOption)->first();
            
            if (!$tenant) {
                $this->error("Tenant with ID {$tenantOption} not found");
                return Command::FAILURE;
            }
            
            $tenants = collect([$tenant]);
        } else {
            $this->info("Starting automatic database migration for all tenants");
            
            // Get all tenants with database configuration
            $tenants = Tenant::with('tenantDatabase')->get();
        }
        
        if ($tenants->isEmpty()) {
            $this->warn("No tenants found to process");
            return Command::SUCCESS;
        }
        
        $this->info("Found " . $tenants->count() . " tenants to process");
        
        $totalCount = $tenants->count();
        $success = 0;
        $skipped = 0;
        $failed = 0;
        $created = 0;
        
        // Process in batches to prevent overloading the server
        $tenantChunks = $tenants->chunk($batchSize);
        $batchCount = count($tenantChunks);
        $currentBatch = 1;
        
        foreach ($tenantChunks as $tenantBatch) {
            $this->info("Processing batch {$currentBatch} of {$batchCount}");
            
            foreach ($tenantBatch as $tenant) {
                $this->info("\n----- Tenant: {$tenant->id} -----");
                
                if (!$tenant->tenantDatabase) {
                    $this->warn("No database configuration for tenant {$tenant->id}. Setting up...");
                    
                    try {
                        // Call the setup command for the tenant
                        $this->info("Running db:setup-tenant for {$tenant->id}");
                        Artisan::call('db:setup-tenant', [
                            'tenant' => $tenant->id
                        ]);
                        
                        // Output the result
                        $this->line(Artisan::output());
                        $this->info("Successfully set up database for tenant: {$tenant->id}");
                        $created++;
                    } catch (\Exception $e) {
                        $this->error("Error setting up database for tenant {$tenant->id}: " . $e->getMessage());
                        Log::error("Error setting up database for tenant {$tenant->id}: " . $e->getMessage());
                        $failed++;
                        continue;
                    }
                } else {
                    $databaseName = $tenant->tenantDatabase->database_name;
                    
                    // Check if the database exists
                    $dbExists = DB::select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$databaseName]);
                    if (empty($dbExists)) {
                        $this->warn("Database {$databaseName} does not exist. Creating...");
                        
                        try {
                            // Call the setup command for the tenant
                            $this->info("Running db:setup-tenant for {$tenant->id}");
                            Artisan::call('db:setup-tenant', [
                                'tenant' => $tenant->id
                            ]);
                            
                            // Output the result
                            $this->line(Artisan::output());
                            $this->info("Successfully set up database for tenant: {$tenant->id}");
                            $created++;
                        } catch (\Exception $e) {
                            $this->error("Error setting up database for tenant {$tenant->id}: " . $e->getMessage());
                            Log::error("Error setting up database for tenant {$tenant->id}: " . $e->getMessage());
                            $failed++;
                            continue;
                        }
                    } else {
                        // Check if database has tables already
                        $tables = DB::select("SHOW TABLES FROM `{$databaseName}`");
                        
                        if (!empty($tables) && !$force) {
                            $this->info("Database {$databaseName} already has tables. Skipping migration.");
                            $skipped++;
                            continue;
                        }
                        
                        // Run direct migration
                        $this->info("Running direct migration for database: {$databaseName}");
                        try {
                            Artisan::call('tenant:direct-migrate', [
                                'database' => $databaseName
                            ]);
                            
                            // Output the result
                            $this->line(Artisan::output());
                            $this->info("Successfully migrated database: {$databaseName}");
                            $success++;
                            
                            // Run additional required setup commands
                            $this->info("Running additional required setup for tenant: {$tenant->id}");
                            
                            try {
                                // Create student_applications table
                                $this->info("Creating student_applications table...");
                                Artisan::call('tenant:migrate-student-applications', [
                                    'tenant' => $tenant->id
                                ]);
                                $this->line(Artisan::output());
                                
                                // Fix courses table
                                $this->info("Fixing courses table...");
                                Artisan::call('tenant:fix-courses-table', [
                                    'tenant' => $tenant->id
                                ]);
                                $this->line(Artisan::output());
                                
                                // Add school year columns
                                $this->info("Adding school year columns...");
                                Artisan::call('tenant:add-school-year-columns', [
                                    'tenant' => $tenant->id
                                ]);
                                $this->line(Artisan::output());
                                
                                $this->info("Additional setup completed successfully for tenant: {$tenant->id}");
                            } catch (\Exception $e) {
                                $this->warn("Some additional setup failed for tenant {$tenant->id}: " . $e->getMessage());
                                Log::warning("Additional setup failed for tenant {$tenant->id}: " . $e->getMessage());
                                // We don't count this as a full failure since the main migration succeeded
                            }
                        } catch (\Exception $e) {
                            $this->error("Error running migration for tenant {$tenant->id}: " . $e->getMessage());
                            Log::error("Error running migration for tenant {$tenant->id}: " . $e->getMessage());
                            $failed++;
                        }
                    }
                }
            }
            
            if ($currentBatch < $batchCount) {
                $this->info("Batch {$currentBatch} completed. Pausing for {$delay} seconds before next batch...");
                sleep($delay);
            }
            
            $currentBatch++;
        }
        
        // Show summary
        $this->newLine();
        $this->info("==== MIGRATION SUMMARY ====");
        $this->info("Total tenants: {$totalCount}");
        $this->info("✅ New databases created: {$created}");
        $this->info("✅ Databases successfully migrated: {$success}");
        $this->info("ℹ️ Skipped (already had tables): {$skipped}");
        $this->error("❌ Failed operations: {$failed}");
        
        if ($failed > 0) {
            $this->warn("Some tenant database operations failed. Check the logs for details.");
            return Command::FAILURE;
        }
        
        $this->info("All tenant database operations completed successfully");
        return Command::SUCCESS;
    }
} 