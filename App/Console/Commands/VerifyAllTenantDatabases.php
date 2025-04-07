<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use App\Models\Tenant;

class VerifyAllTenantDatabases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:verify-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify all tenant databases are properly created outside the main database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Verifying all tenant databases");
        
        // Get all tenants
        $tenants = Tenant::with('tenantDatabase')->get();
        
        if ($tenants->isEmpty()) {
            $this->warn("No tenants found in the system");
            return Command::SUCCESS;
        }
        
        $this->info("Found " . $tenants->count() . " tenants");
        
        $success = 0;
        $failed = 0;
        $noDatabase = 0;
        
        // Check each tenant database
        foreach ($tenants as $tenant) {
            $this->info("\n----- Tenant: {$tenant->id} -----");
            
            if (!$tenant->tenantDatabase) {
                $this->warn("⚠️ No database configuration for tenant {$tenant->id}");
                $noDatabase++;
                continue;
            }
            
            $databaseName = $tenant->tenantDatabase->database_name;
            $this->info("Database name: {$databaseName}");
            
            // Check if the database exists
            $dbExists = DB::select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$databaseName]);
            if (empty($dbExists)) {
                $this->error("❌ Database {$databaseName} does not exist!");
                $failed++;
                continue;
            }
            
            $this->info("✅ Database {$databaseName} exists");
            
            // Get current database name
            $currentDb = DB::connection()->getDatabaseName();
            
            // Verify it's a different database
            if ($currentDb === $databaseName) {
                $this->error("❌ CRITICAL ERROR: Tenant database is the same as the main application database!");
                $failed++;
                continue;
            }
            
            $this->info("✅ Tenant database is separate from main application database ({$currentDb})");
            
            // List tenant tables
            $tables = DB::select("SHOW TABLES FROM `{$databaseName}`");
            if (empty($tables)) {
                $this->warn("⚠️ No tables found in tenant database");
            } else {
                $this->info("Found " . count($tables) . " tables in tenant database");
            }
            
            $success++;
        }
        
        // Show summary
        $this->newLine();
        $this->info("==== SUMMARY ====");
        $this->info("Total tenants: " . $tenants->count());
        $this->info("✅ Properly configured tenant databases: " . $success);
        $this->warn("⚠️ Tenants without database configuration: " . $noDatabase);
        $this->error("❌ Improperly configured tenant databases: " . $failed);
        
        if ($failed > 0 || $noDatabase > 0) {
            $this->warn("Some tenant databases require attention. Run 'php artisan tenant:verify {tenant-id}' for details.");
            if ($noDatabase > 0) {
                $this->info("To set up databases for tenants without configuration, run 'php artisan tenants:auto-setup --new-only'");
            }
        } else {
            $this->info("All tenant databases are properly configured as separate databases");
        }
        
        return Command::SUCCESS;
    }
} 