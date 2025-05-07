<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

class TenantMigrateDb extends Command
{
    protected $signature = 'tenant:migrate-db {tenant}';
    protected $description = 'Migrate tables for a specific tenant with explicit database name';

    public function handle()
    {
        $tenant = $this->argument('tenant');
        $databaseName = 'tenant_' . $tenant;
        
        $this->info("Migrating database: {$databaseName}");
        
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
            
            // Create departments table
            if (!Schema::connection('tenant')->hasTable('departments')) {
                $this->info("Creating departments table");
                Schema::connection('tenant')->create('departments', function ($table) {
                    $table->id();
                    $table->string('name');
                    $table->string('code');
                    $table->text('description')->nullable();
                    $table->string('tenant_id');
                    $table->enum('status', ['active', 'inactive'])->default('active');
                    $table->timestamps();
                });
                $this->info("Departments table created successfully");
            }
            
            // Create staff table
            if (!Schema::connection('tenant')->hasTable('staff')) {
                $this->info("Creating staff table");
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
                $this->info("Staff table created successfully");
            }
            
            // Create courses table
            if (!Schema::connection('tenant')->hasTable('courses')) {
                $this->info("Creating courses table");
                Schema::connection('tenant')->create('courses', function ($table) {
                    $table->id();
                    $table->string('title');
                    $table->text('description')->nullable();
                    $table->unsignedBigInteger('staff_id')->nullable();
                    $table->string('tenant_id');
                    $table->timestamps();
                });
                $this->info("Courses table created successfully");
            }
            
            // Create requirement_categories table
            if (!Schema::connection('tenant')->hasTable('requirement_categories')) {
                $this->info("Creating requirement_categories table");
                Schema::connection('tenant')->create('requirement_categories', function ($table) {
                    $table->id();
                    $table->string('name');
                    $table->text('description')->nullable();
                    $table->string('tenant_id');
                    $table->timestamps();
                });
                $this->info("Requirement categories table created successfully");
            }
            
            // Create requirements table
            if (!Schema::connection('tenant')->hasTable('requirements')) {
                $this->info("Creating requirements table");
                Schema::connection('tenant')->create('requirements', function ($table) {
                    $table->id();
                    $table->string('name');
                    $table->text('description')->nullable();
                    $table->string('student_category')->nullable();
                    $table->string('file_type')->nullable();
                    $table->boolean('is_required')->default(true);
                    $table->string('tenant_id');
                    $table->timestamps();
                });
                $this->info("Requirements table created successfully");
            }
            
            // Create students table
            if (!Schema::connection('tenant')->hasTable('students')) {
                $this->info("Creating students table");
                Schema::connection('tenant')->create('students', function ($table) {
                    $table->id();
                    $table->string('student_id')->unique();
                    $table->string('name');
                    $table->string('email')->unique();
                    $table->string('password');
                    $table->unsignedBigInteger('course_id')->nullable();
                    $table->enum('status', ['active', 'inactive'])->default('active');
                    $table->string('tenant_id');
                    $table->timestamps();
                });
                $this->info("Students table created successfully");
            }
            
            // Create student_requirements table
            if (!Schema::connection('tenant')->hasTable('student_requirements')) {
                $this->info("Creating student_requirements table");
                Schema::connection('tenant')->create('student_requirements', function ($table) {
                    $table->id();
                    $table->unsignedBigInteger('student_id');
                    $table->unsignedBigInteger('requirement_id');
                    $table->string('file_path')->nullable();
                    $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                    $table->text('remarks')->nullable();
                    $table->string('tenant_id');
                    $table->timestamps();
                });
                $this->info("Student requirements table created successfully");
            }
            
            $this->info("All tables created successfully");
            return 0;
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }
    }
} 