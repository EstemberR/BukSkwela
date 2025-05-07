<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateTenants extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:migrate-prefixed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create prefixed tenant tables in the main database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenants = Tenant::all();
        $this->info('Starting migration for ' . count($tenants) . ' tenants with prefixed tables');
        
        foreach ($tenants as $tenant) {
            $this->info('Migrating tables for tenant: ' . $tenant->id);
            
            $prefix = 'tenant_' . $tenant->id . '_';
            
            // Create students table
            $this->createStudentsTable($prefix);
            
            // Create staff table
            $this->createStaffTable($prefix);
            
            // Create courses table
            $this->createCoursesTable($prefix);
            
            // Create requirement_categories table
            $this->createRequirementCategoriesTable($prefix);
            
            // Create requirements table
            $this->createRequirementsTable($prefix);
            
            // Create student_requirements table
            $this->createStudentRequirementsTable($prefix);
            
            // Update tenant database record with the prefix
            DB::table('tenant_databases')
                ->updateOrInsert(
                    ['tenant_id' => $tenant->id],
                    [
                        'database_name' => config('database.connections.mysql.database'),
                        'table_prefix' => $prefix,
                        'database_host' => config('database.connections.mysql.host'),
                        'database_port' => config('database.connections.mysql.port'),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
                
            $this->info('Migration completed for tenant: ' . $tenant->id);
        }
        
        $this->info('All tenant migrations completed successfully');
        
        return Command::SUCCESS;
    }
    
    private function createStudentsTable($prefix)
    {
        $tableName = $prefix . 'students';
        
        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function ($table) {
                $table->id();
                $table->string('student_id')->unique()->nullable();
                $table->string('first_name');
                $table->string('middle_name')->nullable();
                $table->string('last_name');
                $table->string('email')->unique();
                $table->string('phone')->nullable();
                $table->string('address')->nullable();
                $table->string('city')->nullable();
                $table->string('state')->nullable();
                $table->string('postal_code')->nullable();
                $table->string('country')->nullable();
                $table->date('date_of_birth')->nullable();
                $table->enum('gender', ['male', 'female', 'other'])->nullable();
                $table->string('course_id')->nullable();
                $table->string('department_id')->nullable();
                $table->string('year_level')->nullable();
                $table->string('semester')->nullable();
                $table->string('school_year')->nullable();
                $table->string('status')->default('active');
                $table->text('notes')->nullable();
                $table->string('google_drive_folder_id')->nullable();
                $table->timestamps();
            });
            
            $this->info('Created table: ' . $tableName);
        } else {
            $this->info('Table already exists: ' . $tableName);
        }
    }
    
    private function createStaffTable($prefix)
    {
        $tableName = $prefix . 'staff';
        
        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function ($table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('password');
                $table->string('phone')->nullable();
                $table->string('position')->nullable();
                $table->string('department')->nullable();
                $table->string('role')->default('staff');
                $table->boolean('active')->default(true);
                $table->rememberToken();
                $table->timestamps();
            });
            
            $this->info('Created table: ' . $tableName);
        } else {
            $this->info('Table already exists: ' . $tableName);
        }
    }
    
    private function createCoursesTable($prefix)
    {
        $tableName = $prefix . 'courses';
        
        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function ($table) {
                $table->id();
                $table->string('course_code')->unique();
                $table->string('course_name');
                $table->text('description')->nullable();
                $table->string('department_id')->nullable();
                $table->integer('units')->default(0);
                $table->string('status')->default('active');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
            
            $this->info('Created table: ' . $tableName);
        } else {
            $this->info('Table already exists: ' . $tableName);
        }
    }
    
    private function createRequirementCategoriesTable($prefix)
    {
        $tableName = $prefix . 'requirement_categories';
        
        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function ($table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->boolean('is_required')->default(true);
                $table->string('status')->default('active');
                $table->timestamps();
            });
            
            // Seed default categories
            DB::table($tableName)->insert([
                [
                    'name' => 'Academic Documents',
                    'description' => 'Documents related to academic records and achievements',
                    'is_required' => true,
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Identification Documents',
                    'description' => 'Official identification documents',
                    'is_required' => true,
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
            
            $this->info('Created table: ' . $tableName);
        } else {
            $this->info('Table already exists: ' . $tableName);
        }
    }
    
    private function createRequirementsTable($prefix)
    {
        $tableName = $prefix . 'requirements';
        $categoriesTable = $prefix . 'requirement_categories';
        
        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function ($table) use ($categoriesTable) {
                $table->id();
                $table->unsignedBigInteger('category_id');
                $table->string('name');
                $table->text('description')->nullable();
                $table->boolean('is_required')->default(true);
                $table->string('file_type')->nullable();
                $table->integer('max_file_size')->nullable();
                $table->string('status')->default('active');
                $table->timestamps();
                
                $table->foreign('category_id')
                      ->references('id')
                      ->on($categoriesTable)
                      ->onDelete('cascade');
            });
            
            $this->info('Created table: ' . $tableName);
        } else {
            $this->info('Table already exists: ' . $tableName);
        }
    }
    
    private function createStudentRequirementsTable($prefix)
    {
        $tableName = $prefix . 'student_requirements';
        $studentsTable = $prefix . 'students';
        $requirementsTable = $prefix . 'requirements';
        
        if (!Schema::hasTable($tableName)) {
            Schema::create($tableName, function ($table) use ($studentsTable, $requirementsTable) {
                $table->id();
                $table->unsignedBigInteger('student_id');
                $table->unsignedBigInteger('requirement_id');
                $table->string('file_path')->nullable();
                $table->string('file_name')->nullable();
                $table->string('file_type')->nullable();
                $table->integer('file_size')->nullable();
                $table->string('status')->default('pending');
                $table->text('remarks')->nullable();
                $table->string('verified_by')->nullable();
                $table->timestamp('verified_at')->nullable();
                $table->string('google_drive_file_id')->nullable();
                $table->timestamps();
                
                $table->foreign('student_id')
                      ->references('id')
                      ->on($studentsTable)
                      ->onDelete('cascade');
                      
                $table->foreign('requirement_id')
                      ->references('id')
                      ->on($requirementsTable)
                      ->onDelete('cascade');
            });
            
            $this->info('Created table: ' . $tableName);
        } else {
            $this->info('Table already exists: ' . $tableName);
        }
    }
} 