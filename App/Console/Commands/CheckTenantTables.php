<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Models\Tenant;

class CheckTenantTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:tables {tenant : The tenant ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check tables in a tenant database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->argument('tenant');
        $this->info("Checking tables for tenant: {$tenantId}");
        
        // Find the tenant
        $tenant = Tenant::where('id', $tenantId)->first();
        if (!$tenant) {
            $this->error("Tenant with ID {$tenantId} not found");
            return Command::FAILURE;
        }
        
        if (!$tenant->tenantDatabase) {
            $this->error("No database configuration found for tenant {$tenantId}");
            return Command::FAILURE;
        }
        
        $databaseName = $tenant->tenantDatabase->database_name;
        $this->info("Database name: {$databaseName}");
        
        try {
            // Check if the database exists as a separate database
            $dbExists = DB::select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$databaseName]);
            if (empty($dbExists)) {
                $this->error("Database {$databaseName} does not exist!");
                return Command::FAILURE;
            }
            
            $this->info("✅ Database {$databaseName} exists as a separate database");

            // Get current database
            $currentDb = DB::connection()->getDatabaseName();
            $this->info("Current connected database: {$currentDb}");
            
            // Get tables from the tenant database
            $tables = DB::select("SHOW TABLES FROM `{$databaseName}`");
            
            if (empty($tables)) {
                $this->info("No tables found in database {$databaseName}");
                return Command::SUCCESS;
            }
            
            $this->info("Tables in {$databaseName}:");
            $tenantTables = [];
            
            // Format table output
            $this->table(
                ['Table Name'],
                collect($tables)->map(function ($table) use (&$tenantTables) {
                    $values = get_object_vars($table);
                    $tableName = reset($values);
                    $tenantTables[] = $tableName;
                    return [$tableName];
                })->toArray()
            );
            
            // Get current database tables
            $mainDbTables = DB::select("SHOW TABLES FROM `{$currentDb}`");
            $mainTables = [];
            foreach ($mainDbTables as $table) {
                $values = get_object_vars($table);
                $tableName = reset($values);
                $mainTables[] = $tableName;
            }
            
            // Print main database tables
            $this->info("Tables in main database ({$currentDb}):");
            $this->table(
                ['Table Name'],
                collect($mainTables)->map(function ($tableName) {
                    return [$tableName];
                })->toArray()
            );
            
            // Compare tables to check for isolation
            $this->info("Checking table isolation:");
            
            $duplicated = [];
            $isolated = [];
            
            foreach ($tenantTables as $tableName) {
                if (in_array($tableName, $mainTables)) {
                    $duplicated[] = $tableName;
                    $this->warn("⚠️ Table {$tableName} exists in BOTH databases - NOT ISOLATED");
                } else {
                    $isolated[] = $tableName;
                    $this->info("✅ Table {$tableName} exists ONLY in tenant database - PROPERLY ISOLATED");
                }
            }
            
            // Summary
            $this->newLine();
            $this->info("SUMMARY:");
            $this->info("Total tenant tables: " . count($tenantTables));
            $this->info("Properly isolated tables: " . count($isolated));
            $this->warn("Non-isolated (duplicated) tables: " . count($duplicated));
            
            if (count($duplicated) > 0) {
                $this->warn("⚠️ Some tables exist in both databases. The tenant database should have completely isolated tables.");
            } else {
                $this->info("✅ All tenant tables are properly isolated in a separate database.");
            }
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error checking tables: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
} 