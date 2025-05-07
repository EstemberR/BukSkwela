<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\App;
use Stancl\Tenancy\Tenancy;

class MigrateTenantsBatched extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:migrate-batched {--batch-size=3} {--delay=5} {--fresh} {--seed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run migrations for all tenants in batches with delays to avoid overwhelming the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenants = Tenant::all();
        $batchSize = $this->option('batch-size');
        $delay = $this->option('delay');
        $fresh = $this->option('fresh');
        $seed = $this->option('seed');
        
        $totalTenants = count($tenants);
        $totalBatches = ceil($totalTenants / $batchSize);
        
        $this->info("Starting migration for {$totalTenants} tenants in {$totalBatches} batches");
        $this->info("Batch size: {$batchSize}, Delay between batches: {$delay} seconds");
        
        $tenantChunks = $tenants->chunk($batchSize);
        $currentBatch = 1;
        
        // Get tenancy manager directly
        $tenancy = App::make(Tenancy::class);
        
        foreach ($tenantChunks as $tenantBatch) {
            $this->info("Processing batch {$currentBatch} of {$totalBatches}");
            
            foreach ($tenantBatch as $tenant) {
                $this->info("Migrating tenant: {$tenant->id}");
                
                try {
                    // Initialize tenancy for this tenant
                    $tenancy->initialize($tenant);
                    
                    // Choose the appropriate migration command based on options
                    $command = 'migrate';
                    if ($fresh) {
                        $command .= ':fresh';
                    }
                    
                    // Run migration with --force
                    $params = ['--force' => true];
                    $this->info("Running: {$command} for tenant {$tenant->id}");
                    Artisan::call($command, $params);
                    $this->info(Artisan::output());
                    
                    // Seed if requested
                    if ($seed) {
                        $this->info("Seeding tenant: {$tenant->id}");
                        Artisan::call('db:seed', ['--force' => true]);
                        $this->info(Artisan::output());
                    }
                    
                    // End tenancy for this tenant
                    $tenancy->end();
                    
                    $this->info("Successfully migrated tenant: {$tenant->id}");
                } catch (\Exception $e) {
                    $this->error("Error migrating tenant {$tenant->id}: " . $e->getMessage());
                    try {
                        $tenancy->end();
                    } catch (\Exception $endEx) {
                        $this->error("Additional error when ending tenancy: " . $endEx->getMessage());
                    }
                }
            }
            
            if ($currentBatch < $totalBatches) {
                $this->info("Batch {$currentBatch} completed. Pausing for {$delay} seconds before next batch...");
                sleep($delay);
            }
            
            $currentBatch++;
        }
        
        $this->info("All tenant migrations completed");
        
        return Command::SUCCESS;
    }
} 