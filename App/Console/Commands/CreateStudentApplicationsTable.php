<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;

class CreateStudentApplicationsTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:create-applications-table {tenant : The tenant ID to create the table for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the student_applications table for a specific tenant';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->argument('tenant');
        
        // Get the database name for this tenant
        $tenantDB = DB::table('tenants')
            ->where('id', $tenantId)
            ->first();
            
        if (!$tenantDB) {
            $this->error("Tenant {$tenantId} not found!");
            return 1;
        }
        
        // Set the tenant database name
        $databaseName = 'tenant_' . $tenantId;
        
        // Set the database connection config temporarily
        config([
            'database.connections.tenant.database' => $databaseName
        ]);
        
        // Reconnect with the new config
        DB::reconnect('tenant');
        
        $this->info("Connected to tenant database: {$databaseName}");
        
        // Check if the table already exists
        if (Schema::connection('tenant')->hasTable('student_applications')) {
            $this->warn("The student_applications table already exists in tenant {$tenantId} database.");
            return 0;
        }
        
        $this->info("Creating student_applications table in tenant {$tenantId} database...");
        
        try {
            Schema::connection('tenant')->create('student_applications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('students');
                $table->foreignId('program_id')->constrained('courses');
                $table->integer('year_level')->comment('Year level the student is applying for (1-4)');
                $table->string('student_status')->default('Regular')->comment('Student status: Regular, Probation, Irregular');
                $table->text('notes')->nullable()->comment('Additional notes from the student');
                $table->string('status')->default('pending')->comment('Application status: pending, reviewing, approved, rejected');
                $table->text('admin_notes')->nullable()->comment('Notes from the admin reviewing the application');
                $table->foreignId('reviewed_by')->nullable()->comment('Admin user ID who reviewed the application');
                $table->timestamp('reviewed_at')->nullable()->comment('When the application was reviewed');
                $table->json('document_files')->nullable()->comment('JSON data of uploaded document files');
                $table->string('tenant_id');
                $table->timestamps();
                
                // Index for faster queries
                $table->index('status');
                $table->index('tenant_id');
                $table->index(['student_id', 'status']);
            });
            
            $this->info("Table student_applications created successfully!");
            Log::info("Created student_applications table for tenant {$tenantId}");
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Error creating student_applications table: " . $e->getMessage());
            Log::error("Error creating student_applications table for tenant {$tenantId}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
} 