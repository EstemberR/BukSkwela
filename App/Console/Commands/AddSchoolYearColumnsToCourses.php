<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;
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
        $tenant = $this->argument('tenant');
        $databaseName = 'tenant_' . strtolower($tenant);
        
        $this->info("Adding school year columns to courses table in tenant database: {$databaseName}");
        
        try {
            // Set the connection to use the tenant database
            Config::set('database.connections.tenant.database', $databaseName);
            Config::set('database.connections.tenant.username', env('DB_USERNAME'));
            Config::set('database.connections.tenant.password', env('DB_PASSWORD'));
            
            // Purge and reconnect to the tenant database
            DB::purge('tenant');
            DB::reconnect('tenant');
            
            // Test the connection
            DB::connection('tenant')->getPdo();
            $this->info("Successfully connected to tenant database");
            
            // Check if courses table exists
            if (Schema::connection('tenant')->hasTable('courses')) {
                // Check if school_year_start column exists
                if (!Schema::connection('tenant')->hasColumn('courses', 'school_year_start')) {
                    $this->info("Adding school_year_start column to courses table");
                    Schema::connection('tenant')->table('courses', function($table) {
                        $table->integer('school_year_start')->nullable();
                    });
                    $this->info("Added school_year_start column to courses table");
                } else {
                    $this->info("school_year_start column already exists in courses table");
                }
                
                // Check if school_year_end column exists
                if (!Schema::connection('tenant')->hasColumn('courses', 'school_year_end')) {
                    $this->info("Adding school_year_end column to courses table");
                    Schema::connection('tenant')->table('courses', function($table) {
                        $table->integer('school_year_end')->nullable();
                    });
                    $this->info("Added school_year_end column to courses table");
                } else {
                    $this->info("school_year_end column already exists in courses table");
                }
                
                $this->info("School year columns have been added successfully!");
            } else {
                $this->error("Courses table does not exist in the database. Consider running tenant migrations.");
            }
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Error adding school year columns: " . $e->getMessage());
            Log::error("Add school year columns error: " . $e->getMessage());
            return 1;
        }
    }
} 