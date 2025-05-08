<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;

class VerifyTenantDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:verify {tenant}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify that tenant database is properly created outside the main database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->argument('tenant');
        
        // Find the tenant
        $tenant = Tenant::where('id', $tenantId)->first();
        if (!$tenant) {
            $this->error("Tenant with ID {$tenantId} not found");
            return Command::FAILURE;
        }
        
        $this->info("Verifying database for tenant: {$tenantId}");
        
        if (!$tenant->tenantDatabase) {
            $this->error("No database configuration found for tenant {$tenantId}");
            return Command::FAILURE;
        }
        
        $databaseName = $tenant->tenantDatabase->database_name;
        $this->info("Database name: {$databaseName}");
        
        // Check if the database exists
        $dbExists = DB::select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$databaseName]);
        if (empty($dbExists)) {
            $this->error("❌ Database {$databaseName} does not exist!");
            return Command::FAILURE;
        }
        
        $this->info("✅ Database {$databaseName} exists");
        
        // Get current database name
        $currentDb = DB::connection()->getDatabaseName();
        $this->info("Current application database: {$currentDb}");
        
        // Verify it's a different database
        if ($currentDb === $databaseName) {
            $this->error("❌ CRITICAL ERROR: Tenant database is the same as the main application database!");
            return Command::FAILURE;
        }
        
        $this->info("✅ Tenant database is separate from main application database");
        
        // List tenant tables
        $tables = DB::select("SHOW TABLES FROM `{$databaseName}`");
        if (empty($tables)) {
            $this->warn("⚠️ No tables found in tenant database");
        } else {
            $this->info("Tables in tenant database:");
            foreach ($tables as $table) {
                $values = get_object_vars($table);
                $tableName = reset($values);
                $this->line(" - {$tableName}");
            }
            $this->info("Total tables: " . count($tables));
        }
        
        $this->info("--------");
        $this->info("SUMMARY: Tenant database {$databaseName} is properly created as a separate database");
        $this->info("--------");
        
        return Command::SUCCESS;
    }
} 