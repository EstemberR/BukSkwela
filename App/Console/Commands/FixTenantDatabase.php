<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use App\Models\TenantDatabase;

class FixTenantDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:fix-database {tenant}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix the tenant database structure and ensure data is saved to the correct database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenant = $this->argument('tenant');
        $databaseName = 'tenant_' . $tenant;
        
        $this->info("Fixing tenant database: {$databaseName}");
        
        try {
            // Get database credentials
            $username = env('DB_USERNAME');
            $password = env('DB_PASSWORD');
            $host = env('DB_HOST', '127.0.0.1');
            $port = env('DB_PORT', '3306');
            
            // First, make sure the tenant database record exists
            $this->updateTenantDatabaseRecord($tenant, $databaseName, $username, $password, $host, $port);
            
            // Set the connection to use the tenant database
            Config::set('database.connections.tenant.database', $databaseName);
            Config::set('database.connections.tenant.username', $username);
            Config::set('database.connections.tenant.password', $password);
            Config::set('database.connections.tenant.host', $host);
            Config::set('database.connections.tenant.port', $port);
            
            // Purge and reconnect to the tenant database
            DB::purge('tenant');
            DB::reconnect('tenant');
            
            // Test the connection
            DB::connection('tenant')->getPdo();
            $this->info("Successfully connected to tenant database");
            
            // Tables to check for tenant_id column
            $tables = [
                'staff', 
                'departments', 
                'students', 
                'courses', 
                'requirements', 
                'student_requirements',
                'requirement_categories'
            ];
            
            // Fix each table
            foreach ($tables as $table) {
                if (Schema::connection('tenant')->hasTable($table)) {
                    // Check if tenant_id column exists
                    if (!Schema::connection('tenant')->hasColumn($table, 'tenant_id')) {
                        $this->info("Adding tenant_id column to {$table} table");
                        Schema::connection('tenant')->table($table, function($t) {
                            $t->string('tenant_id')->after('status');
                        });
                        $this->info("Added tenant_id column to {$table} table");
                    }
                    
                    // Update all records to have the correct tenant_id
                    $count = DB::connection('tenant')
                        ->table($table)
                        ->whereNull('tenant_id')
                        ->orWhere('tenant_id', '')
                        ->update(['tenant_id' => $tenant]);
                        
                    $this->info("Updated {$count} {$table} records with tenant_id = {$tenant}");
                } else {
                    $this->warn("Table {$table} does not exist in {$databaseName}");
                }
            }
            
            // Check for title column in courses table
            if (Schema::connection('tenant')->hasTable('courses')) {
                if (!Schema::connection('tenant')->hasColumn('courses', 'title')) {
                    $this->info("Adding title column to courses table");
                    Schema::connection('tenant')->table('courses', function($table) {
                        $table->string('title')->after('id')->nullable();
                    });
                    $this->info("Added title column to courses table");
                    
                    // If there's a 'name' column, copy values to 'title'
                    if (Schema::connection('tenant')->hasColumn('courses', 'name')) {
                        $this->info("Copying data from 'name' column to 'title' column");
                        DB::connection('tenant')
                            ->table('courses')
                            ->whereNull('title')
                            ->update(['title' => DB::raw('`name`')]);
                    }
                }
            }
            
            // Run migrations to ensure all tables are properly created
            $this->info("Running tenant migrations to ensure all tables are properly created");
            $this->call('tenant:migrate-db', ['tenant' => $tenant]);
            
            $this->info("Fix completed successfully!");
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Error fixing tenant database: " . $e->getMessage());
            Log::error("Fix tenant database error: " . $e->getMessage());
            return 1;
        }
    }
    
    /**
     * Update or create the TenantDatabase record with the correct credentials
     */
    protected function updateTenantDatabaseRecord($tenant, $databaseName, $username, $password, $host, $port)
    {
        // Check if the tenant database record exists
        $tenantDatabase = TenantDatabase::where('tenant_id', $tenant)->first();
        
        if ($tenantDatabase) {
            $this->info("Updating existing TenantDatabase record for tenant {$tenant}");
            $tenantDatabase->update([
                'database_name' => $databaseName,
                'database_username' => $username,
                'database_password' => $password,
                'database_host' => $host,
                'database_port' => $port
            ]);
        } else {
            $this->info("Creating new TenantDatabase record for tenant {$tenant}");
            TenantDatabase::create([
                'tenant_id' => $tenant,
                'database_name' => $databaseName,
                'database_username' => $username,
                'database_password' => $password,
                'database_host' => $host,
                'database_port' => $port
            ]);
        }
        
        return true;
    }
} 