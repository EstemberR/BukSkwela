<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;
use App\Models\TenantDatabase;

class ShowTenantTables extends Command
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
    protected $description = 'Show tables in tenant database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->argument('tenant');
        
        // Find tenant
        $tenant = Tenant::with('tenantDatabase')->find($tenantId);
        if (!$tenant) {
            $this->error("Tenant {$tenantId} not found");
            return Command::FAILURE;
        }
        
        $this->info("Checking tables for tenant: {$tenant->id}");
        
        // Get tenant database configuration
        $tenantDb = $tenant->tenantDatabase;
        if (!$tenantDb) {
            $this->error("No database configuration found for tenant");
            return Command::FAILURE;
        }
        
        // Database name
        $databaseName = $tenantDb->database_name;
        $this->info("Database: {$databaseName}");
        
        try {
            // Get tables
            $tableResults = DB::select("SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema = ?", [$databaseName]);
            
            if (empty($tableResults)) {
                $this->info("No tables found in database");
                return Command::SUCCESS;
            }
            
            $tables = [];
            foreach ($tableResults as $table) {
                $tables[] = [$table->TABLE_NAME];
            }
            
            $this->info("Tables in database:");
            $this->table(['Table Name'], $tables);
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error listing tables: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
} 