<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class AutoSetupTenantDatabases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:auto-setup 
                            {--tenant= : Optional tenant ID to setup only one tenant}
                            {--new-only : Only setup tenants without databases}
                            {--batch-size=3 : Number of tenants to process in each batch}
                            {--delay=5 : Delay in seconds between batches}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically create databases and run migrations for tenants';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantOption = $this->option('tenant');
        $newOnly = $this->option('new-only');
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
            // Get all tenants or only those without databases
            if ($newOnly) {
                $tenants = Tenant::whereDoesntHave('tenantDatabase')->get();
                $this->info("Found " . count($tenants) . " tenants without database configuration");
            } else {
                $tenants = Tenant::all();
                $this->info("Processing all " . count($tenants) . " tenants");
            }
        }
        
        $totalTenants = count($tenants);
        if ($totalTenants === 0) {
            $this->info("No tenants to process.");
            return Command::SUCCESS;
        }
        
        $this->info("Starting automatic database setup for {$totalTenants} tenants");
        
        $totalBatches = ceil($totalTenants / $batchSize);
        $tenantChunks = $tenants->chunk($batchSize);
        $currentBatch = 1;
        $success = 0;
        $failed = 0;
        $skipped = 0;
        
        foreach ($tenantChunks as $tenantBatch) {
            $this->info("Processing batch {$currentBatch} of {$totalBatches}");
            
            foreach ($tenantBatch as $tenant) {
                $this->info("Setting up database for tenant: {$tenant->id}");
                
                try {
                    // Check if the tenant already has a database and we're not forcing setup
                    if ($tenant->tenantDatabase && $newOnly) {
                        $this->info("Tenant {$tenant->id} already has a database configured. Skipping.");
                        $skipped++;
                        continue;
                    }
                    
                    // Call the setup command for the tenant
                    $this->info("Running db:setup-tenant for {$tenant->id}");
                    Artisan::call('db:setup-tenant', [
                        'tenant' => $tenant->id
                    ]);
                    
                    // Output the result
                    $this->line(Artisan::output());
                    
                    $this->info("Successfully set up database for tenant: {$tenant->id}");
                    $success++;
                } catch (\Exception $e) {
                    $this->error("Error setting up database for tenant {$tenant->id}: " . $e->getMessage());
                    Log::error("Error setting up database for tenant {$tenant->id}: " . $e->getMessage());
                    $failed++;
                }
            }
            
            if ($currentBatch < $totalBatches) {
                $this->info("Batch {$currentBatch} completed. Pausing for {$delay} seconds before next batch...");
                sleep($delay);
            }
            
            $currentBatch++;
        }
        
        $this->info("Database setup completed");
        $this->info("Summary: {$success} created, {$skipped} skipped, {$failed} failed");
        
        return Command::SUCCESS;
    }
} 