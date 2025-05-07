<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class FixAllTenantDatabases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:fix-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix all tenant databases structure for existing approved tenants';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Fixing all approved tenant databases");
        
        try {
            // Get all approved tenants
            $tenants = Tenant::where('status', 'approved')->get();
            
            if ($tenants->isEmpty()) {
                $this->info("No approved tenants found");
                return 0;
            }
            
            $this->info("Found " . $tenants->count() . " approved tenants");
            
            foreach ($tenants as $tenant) {
                $this->info("Processing tenant: {$tenant->id}");
                
                $databaseName = 'tenant_' . $tenant->id;
                
                // Check if the database exists first
                try {
                    // Check if database exists by trying to select its name from information_schema
                    $databaseExists = DB::select(
                        "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", 
                        [$databaseName]
                    );
                    
                    if (empty($databaseExists)) {
                        $this->warn("Database {$databaseName} does not exist in MySQL - creating it");
                        DB::statement("CREATE DATABASE IF NOT EXISTS `{$databaseName}`");
                        $this->info("Database {$databaseName} created");
                    } else {
                        $this->info("Database {$databaseName} exists - proceeding with fix");
                    }
                    
                    // Call the fix-staff command for each tenant
                    $this->call('tenant:fix-staff', [
                        'tenant' => $tenant->id,
                        '--force' => true
                    ]);
                    
                } catch (\Exception $e) {
                    $this->error("Error processing database {$databaseName}: " . $e->getMessage());
                    continue;
                }
            }
            
            $this->info("All tenant database structures fixed");
            return 0;
        } catch (\Exception $e) {
            $this->error("Error fixing tenant databases: " . $e->getMessage());
            Log::error("Fix all tenant databases error: " . $e->getMessage());
            return 1;
        }
    }
} 