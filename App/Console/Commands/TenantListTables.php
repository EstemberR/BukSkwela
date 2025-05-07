<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class TenantListTables extends Command
{
    protected $signature = 'tenant:list-tables {tenant}';
    protected $description = 'List all tables in a tenant database';

    public function handle()
    {
        $tenant = $this->argument('tenant');
        $databaseName = 'tenant_' . $tenant;
        
        $this->info("Listing tables in database: {$databaseName}");
        
        // Set the database connection for the tenant
        Config::set('database.connections.tenant.database', $databaseName);
        Config::set('database.connections.tenant.username', env('DB_USERNAME', 'root'));
        Config::set('database.connections.tenant.password', env('DB_PASSWORD', ''));
        
        // Purge and reconnect to the tenant database
        DB::purge('tenant');
        DB::reconnect('tenant');
        
        try {
            DB::connection('tenant')->getPdo();
            $this->info("Successfully connected to tenant database");
            
            $tables = DB::connection('tenant')->select('SHOW TABLES');
            
            if (empty($tables)) {
                $this->info("No tables found in the database.");
                return 0;
            }
            
            $this->info("Tables in the database:");
            
            // The property name varies by database driver
            $propertyName = 'Tables_in_' . $databaseName;
            
            foreach ($tables as $table) {
                if (property_exists($table, $propertyName)) {
                    $this->info("- " . $table->$propertyName);
                } else {
                    // Fallback for different database drivers
                    $this->info("- " . json_encode($table));
                }
            }
            
            return 0;
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }
    }
} 