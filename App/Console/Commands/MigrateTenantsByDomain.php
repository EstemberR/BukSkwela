<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use App\Models\Domain;
use App\Models\TenantDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class MigrateTenantsByDomain extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:migrate-by-domain {--tenant=} {--batch-size=3} {--delay=5} {--fresh} {--seed} {--skip-domain-check} {--create-db}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run migrations for all tenants by domain in batches with delays';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantOption = $this->option('tenant');
        $batchSize = $this->option('batch-size');
        $delay = $this->option('delay');
        $fresh = $this->option('fresh');
        $seed = $this->option('seed');
        $skipDomainCheck = $this->option('skip-domain-check');
        $createDb = $this->option('create-db');
        
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
        $totalBatches = ceil($totalTenants / $batchSize);
        
        $this->info("Starting migration for {$totalTenants} tenants in {$totalBatches} batches");
        $this->info("Using separate tenant databases for better isolation");
        $this->info("Batch size: {$batchSize}, Delay between batches: {$delay} seconds");
        
        // Create databases first if requested
        if ($createDb) {
            $this->info("Creating databases for tenants first...");
            Artisan::call('tenants:create-databases', [
                '--batch-size' => $batchSize,
                '--delay' => $delay
            ]);
            $this->info(Artisan::output());
        }
        
        $tenantChunks = $tenants->chunk($batchSize);
        $currentBatch = 1;
        $success = 0;
        $failed = 0;
        
        foreach ($tenantChunks as $tenantBatch) {
            $this->info("Processing batch {$currentBatch} of {$totalBatches}");
            
            foreach ($tenantBatch as $tenant) {
                $this->info("Migrating tenant: {$tenant->id}");
                
                try {
                    // Check if tenant has a database record
                    $tenantDb = TenantDatabase::where('tenant_id', $tenant->id)->first();
                    if (!$tenantDb) {
                        $this->warn("Tenant {$tenant->id} has no database record. Creating database before migrating.");
                        
                        try {
                            // Create database for the tenant
                            $tenant->database()->create();
                            $this->info("Database created for tenant: {$tenant->id}");
                            
                            // Fetch the newly created database record
                            $tenantDb = TenantDatabase::where('tenant_id', $tenant->id)->first();
                            if (!$tenantDb) {
                                $this->error("Failed to create database record for tenant {$tenant->id}. Skipping migrations.");
                                continue;
                            }
                        } catch (\Exception $createEx) {
                            $this->error("Error creating database for tenant {$tenant->id}: " . $createEx->getMessage());
                            continue;
                        }
                    }
                    
                    // Check for domain if required
                    if (!$skipDomainCheck) {
                        $domain = Domain::where('tenant_id', $tenant->id)->first();
                        
                        if (!$domain) {
                            $this->warn("No domain found for tenant {$tenant->id}, using tenant ID directly");
                        } else {
                            $this->info("Using domain: {$domain->domain}");
                        }
                    }
                    
                    // Choose the appropriate migration command based on options
                    $command = 'tenants:migrate';
                    if ($fresh) {
                        $command = 'tenants:migrate-fresh';
                    }
                    
                    // Run migration directly for the tenant
                    $params = [
                        '--tenants' => [$tenant->id],
                        '--force' => true
                    ];
                    
                    $this->info("Running: {$command} for tenant {$tenant->id}");
                    Artisan::call($command, $params);
                    $output = Artisan::output();
                    $this->info($output);
                    
                    // Log the output for debugging
                    Log::info("Migration output for tenant {$tenant->id}: " . $output);
                    
                    // Seed if requested but skip on errors
                    if ($seed) {
                        $this->info("Seeding tenant: {$tenant->id}");
                        try {
                            Artisan::call('tenants:seed', [
                                '--tenants' => [$tenant->id],
                                '--force' => true
                            ]);
                            $seedOutput = Artisan::output();
                            $this->info($seedOutput);
                            
                            // Log the seeding output
                            Log::info("Seeding output for tenant {$tenant->id}: " . $seedOutput);
                        } catch (\Exception $seedEx) {
                            $this->error("Error seeding tenant {$tenant->id}: " . $seedEx->getMessage());
                            Log::error("Error seeding tenant {$tenant->id}: " . $seedEx->getMessage());
                            // Continue even if seeding fails
                        }
                    }
                    
                    $this->info("Successfully processed tenant: {$tenant->id}");
                    $success++;
                } catch (\Exception $e) {
                    $this->error("Error migrating tenant {$tenant->id}: " . $e->getMessage());
                    Log::error("Error migrating tenant {$tenant->id}: " . $e->getMessage());
                    $failed++;
                }
            }
            
            if ($currentBatch < $totalBatches) {
                $this->info("Batch {$currentBatch} completed. Pausing for {$delay} seconds before next batch...");
                sleep($delay);
            }
            
            $currentBatch++;
        }
        
        $this->info("All tenant migrations completed");
        $this->info("Summary: {$success} tenants processed successfully, {$failed} failed");
        
        return Command::SUCCESS;
    }

    /**
     * Check if migration has already been run for this tenant
     */
    private function checkTenantTables($tenantId): bool
    {
        try {
            // Choose the tenant connection
            $connectionName = config('tenancy.database.tenant_connection_name', 'tenant');
            
            // Initialize tenancy for this tenant for accurate connection
            tenancy()->initialize(Tenant::find($tenantId));
            
            // Try to check if a basic table exists
            $hasTable = Schema::connection($connectionName)->hasTable('migrations');
            
            // End tenancy
            tenancy()->end();
            
            if (!$hasTable) {
                $this->info("Tenant {$tenantId} needs migration - tables not found");
                return false;
            }
            
            $this->info("Tenant {$tenantId} already has tables, proceeding with seeding");
            return true;
        } catch (\Exception $e) {
            $this->error("Error checking tenant tables: " . $e->getMessage());
            tenancy()->end();
            return false;
        }
    }
} 