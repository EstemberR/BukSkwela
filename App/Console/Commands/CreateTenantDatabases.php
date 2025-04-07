<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Exceptions\TenantDatabaseAlreadyExistsException;

class CreateTenantDatabases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:create-databases {--tenant=} {--batch-size=3} {--delay=5}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create separate databases for tenants';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantOption = $this->option('tenant');
        $batchSize = $this->option('batch-size');
        $delay = $this->option('delay');
        
        // Process a single tenant if specified
        if ($tenantOption) {
            $tenant = Tenant::find($tenantOption);
            if (!$tenant) {
                $this->error("Tenant with ID {$tenantOption} not found");
                return Command::FAILURE;
            }
            
            $tenants = collect([$tenant]);
        } else {
            // Get all tenants
            $tenants = Tenant::all();
        }
        
        $totalTenants = count($tenants);
        $this->info("Starting database creation for {$totalTenants} tenants");
        
        $totalBatches = ceil($totalTenants / $batchSize);
        $tenantChunks = $tenants->chunk($batchSize);
        $currentBatch = 1;
        $success = 0;
        $failed = 0;
        $skipped = 0;
        
        foreach ($tenantChunks as $tenantBatch) {
            $this->info("Processing batch {$currentBatch} of {$totalBatches}");
            
            foreach ($tenantBatch as $tenant) {
                $this->info("Creating database for tenant: {$tenant->id}");
                
                try {
                    // Check if the tenant already has a database
                    if ($tenant->tenantDatabase) {
                        $this->info("Tenant {$tenant->id} already has a database configured. Skipping.");
                        $skipped++;
                        continue;
                    }
                    
                    // Create database for the tenant
                    $tenant->database()->create();
                    
                    $this->info("Successfully created database for tenant: {$tenant->id}");
                    $success++;
                } catch (TenantDatabaseAlreadyExistsException $e) {
                    $this->warn("Database for tenant {$tenant->id} already exists. Skipping.");
                    $skipped++;
                } catch (\Exception $e) {
                    $this->error("Error creating database for tenant {$tenant->id}: " . $e->getMessage());
                    Log::error("Error creating database for tenant {$tenant->id}: " . $e->getMessage());
                    $failed++;
                }
            }
            
            if ($currentBatch < $totalBatches) {
                $this->info("Batch {$currentBatch} completed. Pausing for {$delay} seconds before next batch...");
                sleep($delay);
            }
            
            $currentBatch++;
        }
        
        $this->info("Database creation completed");
        $this->info("Summary: {$success} created, {$skipped} skipped, {$failed} failed");
        
        return Command::SUCCESS;
    }
} 