<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class VerifyTableStructure extends Command
{
    protected $signature = 'tenant:verify-structure {tenant} {table}';
    protected $description = 'Check the structure of a table in a tenant database';

    public function handle()
    {
        $tenant = $this->argument('tenant');
        $tableName = $this->argument('table');
        $databaseName = 'tenant_' . $tenant;
        
        $this->info("Checking structure of {$tableName} table in database: {$databaseName}");
        
        // Set the database connection for the tenant
        Config::set('database.connections.tenant.database', $databaseName);
        Config::set('database.connections.tenant.username', env('DB_USERNAME'));
        Config::set('database.connections.tenant.password', env('DB_PASSWORD'));
        
        // Purge and reconnect to the tenant database
        DB::purge('tenant');
        DB::reconnect('tenant');
        
        try {
            // Test the connection
            $connection = DB::connection('tenant');
            $connection->getPdo();
            $this->info("Successfully connected to tenant database {$databaseName}");
            
            // Check if the table exists
            if (!Schema::connection('tenant')->hasTable($tableName)) {
                $this->error("Table {$tableName} does not exist in database {$databaseName}");
                return 1;
            }
            
            // Get the table structure
            $columns = $connection->select("SHOW COLUMNS FROM {$tableName}");
            
            $this->info("Table structure for {$tableName}:");
            $headers = ['Field', 'Type', 'Null', 'Key', 'Default', 'Extra'];
            $rows = [];
            
            foreach ($columns as $column) {
                $rows[] = [
                    $column->Field,
                    $column->Type,
                    $column->Null,
                    $column->Key,
                    $column->Default,
                    $column->Extra
                ];
            }
            
            $this->table($headers, $rows);
            
            return 0;
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }
    }
} 