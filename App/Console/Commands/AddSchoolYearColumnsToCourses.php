<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AddSchoolYearColumnsToCourses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:add-school-year-columns {tenant}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add school_year_start and school_year_end columns to the courses table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->argument('tenant');
        $this->info("Adding school year columns to courses table for tenant: {$tenantId}");
        
        try {
            // Get the tenant database name
            $dbName = 'tenant_' . $tenantId;
            
            // Check if we have a specific tenant database record
            $tenantDB = \App\Models\TenantDatabase::where('tenant_id', $tenantId)->first();
            if ($tenantDB) {
                $dbName = $tenantDB->database_name;
            }
            
            // Configure the tenant connection
            config(['database.connections.tenant.database' => $dbName]);
            
            // Purge the tenant connection to ensure we're using the updated configuration
            DB::purge('tenant');
            DB::reconnect('tenant');
            
            // Check if the connection is working
            DB::connection('tenant')->getPdo();
            $this->info("Successfully connected to tenant database: {$dbName}");

            // Check if columns exist
            $columns = DB::connection('tenant')->getSchemaBuilder()->getColumnListing('courses');
            
            // Add school_year_start if doesn't exist
            if (!in_array('school_year_start', $columns)) {
                DB::connection('tenant')->statement('ALTER TABLE courses ADD COLUMN school_year_start YEAR NULL');
                $this->info('Added school_year_start column to courses table');
            } else {
                $this->info('school_year_start column already exists');
            }
            
            // Add school_year_end if doesn't exist
            if (!in_array('school_year_end', $columns)) {
                DB::connection('tenant')->statement('ALTER TABLE courses ADD COLUMN school_year_end YEAR NULL');
                $this->info('Added school_year_end column to courses table');
            } else {
                $this->info('school_year_end column already exists');
            }
            
            $this->info('Successfully added school year columns to courses table');
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error("Error adding school year columns: " . $e->getMessage());
            Log::error("Error adding school year columns for tenant {$tenantId}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }
    }
} 