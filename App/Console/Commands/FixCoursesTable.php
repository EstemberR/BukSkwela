<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class FixCoursesTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:fix-courses-table {tenant}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix the courses table structure to ensure it has the title column';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenant = $this->argument('tenant');
        $databaseName = 'tenant_' . $tenant;
        
        $this->info("Fixing courses table in tenant database: {$databaseName}");
        
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
                // Check if title column exists
                if (!Schema::connection('tenant')->hasColumn('courses', 'title')) {
                    $this->info("Adding title column to courses table");
                    Schema::connection('tenant')->table('courses', function($table) {
                        $table->string('title')->after('id')->nullable();
                    });
                    $this->info("Added title column to courses table");
                    
                    // If there's a 'name' or 'course_name' column, copy values to 'title'
                    if (Schema::connection('tenant')->hasColumn('courses', 'name')) {
                        $this->info("Copying data from 'name' column to 'title' column");
                        DB::connection('tenant')
                            ->table('courses')
                            ->whereNull('title')
                            ->update(['title' => DB::raw('`name`')]);
                    } elseif (Schema::connection('tenant')->hasColumn('courses', 'course_name')) {
                        $this->info("Copying data from 'course_name' column to 'title' column");
                        DB::connection('tenant')
                            ->table('courses')
                            ->whereNull('title')
                            ->update(['title' => DB::raw('`course_name`')]);
                    } else {
                        $this->warn("No source column found to copy data to 'title' column. You may need to add course titles manually.");
                    }
                } else {
                    $this->info("Title column already exists in courses table");
                }
                
                // Check for other required columns that might be referenced in the query
                $requiredColumns = ['id', 'staff_id', 'status', 'tenant_id'];
                
                foreach ($requiredColumns as $column) {
                    if (!Schema::connection('tenant')->hasColumn('courses', $column)) {
                        $this->info("Adding {$column} column to courses table");
                        Schema::connection('tenant')->table('courses', function($table) use ($column) {
                            if ($column === 'id') {
                                $table->id();
                            } elseif ($column === 'staff_id') {
                                $table->unsignedBigInteger('staff_id')->nullable();
                            } elseif ($column === 'status') {
                                $table->string('status')->default('active');
                            } elseif ($column === 'tenant_id') {
                                $table->string('tenant_id')->nullable();
                            }
                        });
                        
                        if ($column === 'tenant_id') {
                            DB::connection('tenant')
                                ->table('courses')
                                ->whereNull('tenant_id')
                                ->orWhere('tenant_id', '')
                                ->update(['tenant_id' => $tenant]);
                        }
                    }
                }
                
                // Add created_at column if not exists (needed for the ORDER BY in the query)
                if (!Schema::connection('tenant')->hasColumn('courses', 'created_at')) {
                    Schema::connection('tenant')->table('courses', function($table) {
                        $table->timestamps();
                    });
                    $this->info("Added timestamps to courses table");
                }
                
                $this->info("Courses table structure has been fixed successfully!");
            } else {
                $this->error("Courses table does not exist in the database. Consider running tenant migrations.");
            }
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Error fixing courses table: " . $e->getMessage());
            Log::error("Fix courses table error: " . $e->getMessage());
            return 1;
        }
    }
} 