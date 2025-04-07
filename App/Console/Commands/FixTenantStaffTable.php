<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class FixTenantStaffTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:fix-staff-table {tenant}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix the staff table structure for a tenant database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenant = $this->argument('tenant');
        $databaseName = 'tenant_' . $tenant;
        
        $this->info("Fixing staff table for tenant database: {$databaseName}");
        
        try {
            // Set the connection to use the tenant database
            config()->set('database.connections.tenant.database', $databaseName);
            DB::purge('tenant');
            DB::reconnect('tenant');
            
            // Check if staff table exists
            if (!Schema::connection('tenant')->hasTable('staff')) {
                $this->error("Staff table does not exist in {$databaseName}");
                return 1;
            }
            
            // Check if tenant_id column exists
            if (!Schema::connection('tenant')->hasColumn('staff', 'tenant_id')) {
                $this->info("Adding tenant_id column to staff table");
                Schema::connection('tenant')->table('staff', function($table) {
                    $table->string('tenant_id')->after('status');
                });
            }
            
            // Update all records to have the correct tenant_id
            $count = DB::connection('tenant')
                ->table('staff')
                ->whereNull('tenant_id')
                ->orWhere('tenant_id', '')
                ->update(['tenant_id' => $tenant]);
                
            $this->info("Updated {$count} staff records with tenant_id = {$tenant}");
            
            // Check departments table if it exists
            if (Schema::connection('tenant')->hasTable('departments')) {
                if (!Schema::connection('tenant')->hasColumn('departments', 'tenant_id')) {
                    $this->info("Adding tenant_id column to departments table");
                    Schema::connection('tenant')->table('departments', function($table) {
                        $table->string('tenant_id')->after('name');
                    });
                }
                
                // Update all departments records to have the correct tenant_id
                $deptCount = DB::connection('tenant')
                    ->table('departments')
                    ->whereNull('tenant_id')
                    ->orWhere('tenant_id', '')
                    ->update(['tenant_id' => $tenant]);
                    
                $this->info("Updated {$deptCount} department records with tenant_id = {$tenant}");
            }
            
            // Display the current structure of the staff table
            $columns = Schema::connection('tenant')->getColumnListing('staff');
            $this->info("Current staff table columns:");
            foreach ($columns as $column) {
                $this->line("- {$column}");
            }
            
            $this->info("Fix completed successfully!");
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Error fixing staff table: " . $e->getMessage());
            Log::error("Fix tenant staff table error: " . $e->getMessage());
            return 1;
        }
    }
} 