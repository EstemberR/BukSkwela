<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class CreateStudentApplicationsTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:create-student-applications-table {tenant? : The tenant ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the student_applications table directly in the tenant database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenant = $this->argument('tenant');
        
        if (!$tenant) {
            $tenant = tenant('id');
            if (!$tenant) {
                $this->error('No tenant specified and no active tenant context');
                return 1;
            }
        }
        
        $this->info("Creating student_applications table for tenant: $tenant");
        
        try {
            // Set up the database connection
            $dbName = 'tenant_' . strtolower($tenant);
            config(['database.connections.tenant.database' => $dbName]);
            DB::connection('tenant')->reconnect();
            
            // Check if the table already exists
            if (Schema::connection('tenant')->hasTable('student_applications')) {
                $this->info("Table student_applications already exists in $dbName");
                return 0;
            }
            
            // Create the table
            Schema::connection('tenant')->create('student_applications', function ($table) {
                $table->id();
                $table->foreignId('student_id');
                $table->foreignId('program_id');
                $table->integer('year_level');
                $table->text('notes')->nullable();
                $table->string('status')->default('pending'); // pending, reviewing, approved, rejected
                $table->text('admin_notes')->nullable();
                $table->foreignId('reviewed_by')->nullable();
                $table->timestamp('reviewed_at')->nullable();
                $table->string('tenant_id');
                $table->timestamps();
            });
            
            $this->info("Successfully created student_applications table in $dbName");
            
            return 0;
        } catch (\Exception $e) {
            $this->error("Error creating table: " . $e->getMessage());
            Log::error("Error creating student_applications table: " . $e->getMessage(), [
                'tenant' => $tenant,
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
} 