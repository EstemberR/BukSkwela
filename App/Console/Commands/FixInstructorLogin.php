<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class FixInstructorLogin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:fix-instructor-login {tenant}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix instructor login for tenant by ensuring credentials are in the tenant database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->argument('tenant');
        $this->info("Fixing instructor login for tenant: {$tenantId}");
        
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

            // Ensure tenant_user_credentials table exists
            if (!Schema::connection('tenant')->hasTable('tenant_user_credentials')) {
                $this->info("Creating tenant_user_credentials table...");
                DB::connection('tenant')->statement("
                    CREATE TABLE IF NOT EXISTS tenant_user_credentials (
                        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                        email VARCHAR(255) NOT NULL,
                        password VARCHAR(255) NOT NULL, 
                        user_type ENUM('admin', 'staff', 'student') DEFAULT 'admin',
                        user_id BIGINT UNSIGNED NULL,
                        is_active TINYINT(1) DEFAULT 1,
                        remember_token VARCHAR(100) NULL,
                        created_at TIMESTAMP NULL,
                        updated_at TIMESTAMP NULL,
                        UNIQUE(email)
                    ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
                ");
            }

            // Find all instructor staff from the main database
            $instructors = DB::table('staff')
                ->where('tenant_id', $tenantId)
                ->where('role', 'instructor')
                ->get();
                
            $this->info("Found {$instructors->count()} instructors to fix");
            
            // Ensure their credentials are in the tenant database
            foreach ($instructors as $instructor) {
                // Check if credentials already exist in tenant database
                $existingCredential = DB::connection('tenant')
                    ->table('tenant_user_credentials')
                    ->where('email', $instructor->email)
                    ->first();
                    
                if ($existingCredential) {
                    $this->info("Credentials already exist for {$instructor->email} - updating");
                    
                    // Update the existing credentials
                    DB::connection('tenant')
                        ->table('tenant_user_credentials')
                        ->where('email', $instructor->email)
                        ->update([
                            'password' => $instructor->password,
                            'user_type' => 'staff',
                            'user_id' => $instructor->id,
                            'updated_at' => now()
                        ]);
                } else {
                    $this->info("Creating credentials for {$instructor->email}");
                    
                    // Create new credentials
                    DB::connection('tenant')
                        ->table('tenant_user_credentials')
                        ->insert([
                            'email' => $instructor->email,
                            'password' => $instructor->password,
                            'user_type' => 'staff',
                            'user_id' => $instructor->id,
                            'is_active' => 1,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                }
            }
            
            // Check if we need to create the student_applications table for dashboard
            if (!Schema::connection('tenant')->hasTable('student_applications')) {
                $this->info("Creating student_applications table to prevent dashboard errors...");
                DB::connection('tenant')->statement("
                    CREATE TABLE IF NOT EXISTS student_applications (
                        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                        student_id BIGINT UNSIGNED NULL,
                        program_id BIGINT UNSIGNED NULL,
                        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
                        year_level VARCHAR(20) NULL,
                        document_files JSON NULL,
                        admin_notes TEXT NULL,
                        reviewed_by BIGINT UNSIGNED NULL,
                        reviewed_at TIMESTAMP NULL,
                        tenant_id VARCHAR(255) NULL,
                        created_at TIMESTAMP NULL,
                        updated_at TIMESTAMP NULL
                    ) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
                ");
                
                // Set tenant_id for all records
                $this->info("Setting tenant_id for student_applications records...");
                DB::connection('tenant')->statement("UPDATE student_applications SET tenant_id = '{$tenantId}'");
            } else if (!Schema::connection('tenant')->hasColumn('student_applications', 'tenant_id')) {
                // Add tenant_id column if the table exists but column doesn't
                $this->info("Adding tenant_id column to existing student_applications table...");
                DB::connection('tenant')->statement("ALTER TABLE student_applications ADD COLUMN tenant_id VARCHAR(255) NULL");
                
                // Set tenant_id for all records
                $this->info("Setting tenant_id for existing student_applications records...");
                DB::connection('tenant')->statement("UPDATE student_applications SET tenant_id = '{$tenantId}'");
            }
            
            $this->info("✅ Successfully fixed instructor login for tenant: {$tenantId}");
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error("Error fixing instructor login: " . $e->getMessage());
            Log::error("Error fixing instructor login for tenant {$tenantId}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }
    }
}
