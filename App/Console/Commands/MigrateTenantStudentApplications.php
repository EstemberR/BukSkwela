<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class MigrateTenantStudentApplications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:migrate-student-applications {tenant}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create student_applications table in tenant database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenant = $this->argument('tenant');
        $databaseName = 'tenant_' . strtolower($tenant);
        
        $this->info("Setting up student_applications table in: {$databaseName}");
        
        // Set the database connection for the tenant
        Config::set('database.connections.tenant.database', $databaseName);
        
        // Purge and reconnect to the tenant database
        DB::purge('tenant');
        DB::reconnect('tenant');
        
        // Verify the connection works
        try {
            DB::connection('tenant')->select('SELECT 1 as test');
            $this->info("✅ Connection to database is working!");
        } catch (\Exception $e) {
            $this->error("❌ Failed to connect to database: " . $e->getMessage());
            return 1;
        }
        
        // Check if student_applications table already exists
        if (Schema::connection('tenant')->hasTable('student_applications')) {
            $this->error("⚠️ student_applications table already exists!");
            
            if (!$this->confirm('Do you want to drop and recreate the table? All existing data will be lost!')) {
                $this->info("Operation cancelled.");
                return 0;
            }
            
            Schema::connection('tenant')->drop('student_applications');
            $this->info("Table dropped and will be recreated.");
        }
        
        // Create the table with proper schema
        try {
            // Use direct SQL to create the table to handle foreign key constraints properly
            DB::connection('tenant')->statement("
                CREATE TABLE `student_applications` (
                  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                  `student_id` bigint(20) UNSIGNED NOT NULL,
                  `program_id` bigint(20) UNSIGNED NOT NULL,
                  `year_level` int(11) NOT NULL COMMENT 'Year level the student is applying for (1-4)',
                  `student_status` varchar(255) NOT NULL DEFAULT 'Regular' COMMENT 'Student status: Regular, Probation, Irregular',
                  `notes` text DEFAULT NULL COMMENT 'Additional notes from the student',
                  `status` varchar(255) NOT NULL DEFAULT 'pending' COMMENT 'Application status: pending, reviewing, approved, rejected',
                  `admin_notes` text DEFAULT NULL COMMENT 'Notes from the admin reviewing the application',
                  `reviewed_by` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'Admin user ID who reviewed the application',
                  `reviewed_at` timestamp NULL DEFAULT NULL COMMENT 'When the application was reviewed',
                  `document_files` json DEFAULT NULL COMMENT 'JSON data of uploaded document files',
                  `tenant_id` varchar(255) NOT NULL,
                  `school_year_start` int(11) DEFAULT NULL,
                  `school_year_end` int(11) DEFAULT NULL,
                  `created_at` timestamp NULL DEFAULT NULL,
                  `updated_at` timestamp NULL DEFAULT NULL,
                  PRIMARY KEY (`id`),
                  KEY `student_applications_status_index` (`status`),
                  KEY `student_applications_tenant_id_index` (`tenant_id`),
                  KEY `student_applications_student_id_status_index` (`student_id`,`status`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ");
            
            $this->info("✅ student_applications table created successfully!");
            
        } catch (\Exception $e) {
            $this->error("❌ Failed to create table: " . $e->getMessage());
            Log::error("Failed to create student_applications table: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
        
        return 0;
    }
} 