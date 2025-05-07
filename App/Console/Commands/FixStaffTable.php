<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class FixStaffTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:fix-staff {tenant} {--force : Skip confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix staff table structure in tenant database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenant = $this->argument('tenant');
        $databaseName = 'tenant_' . $tenant;
        $force = $this->option('force');
        
        $this->info("Fixing staff table in database: {$databaseName}");
        
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
            $this->info("Successfully connected to tenant database {$databaseName}");
            
            // Check if staff table exists
            if (Schema::connection('tenant')->hasTable('staff')) {
                $this->info("Staff table exists - recreating with proper structure");
                
                // Drop the staff table and recreate it with proper structure
                Schema::connection('tenant')->dropIfExists('staff');
                $this->info("Dropped staff table");
            } else {
                $this->info("Staff table does not exist - creating new table");
            }
            
            // Create new staff table with proper structure
            Schema::connection('tenant')->create('staff', function ($table) {
                $table->id();
                $table->string('staff_id')->unique();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('password');
                $table->enum('role', ['instructor', 'admin', 'staff'])->default('instructor');
                $table->unsignedBigInteger('department_id')->nullable();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->string('tenant_id');
                $table->rememberToken();
                $table->timestamps();
            });
            
            $this->info("Created staff table with proper structure");
            
            // Display the current column structure
            $columns = Schema::connection('tenant')->getColumnListing('staff');
            $this->info("Columns in staff table:");
            foreach ($columns as $column) {
                $this->line("- {$column}");
            }
            
            $this->info("Staff table fix completed!");
            return 0;
        } catch (\Exception $e) {
            $this->error("Error fixing staff table: " . $e->getMessage());
            Log::error("Fix staff table error: " . $e->getMessage());
            return 1;
        }
    }
} 