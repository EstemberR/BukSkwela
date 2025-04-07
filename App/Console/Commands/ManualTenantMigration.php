<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Tenant;
use App\Models\TenantDatabase;

class ManualTenantMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:manual-migrate {tenant}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manually migrate tenant tables to separate database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->argument('tenant');
        $tenant = Tenant::with('tenantDatabase')->find($tenantId);
        
        if (!$tenant) {
            $this->error("Tenant {$tenantId} not found");
            return Command::FAILURE;
        }
        
        $tenantDb = $tenant->tenantDatabase;
        if (!$tenantDb) {
            $this->error("No database configuration found for tenant {$tenantId}");
            return Command::FAILURE;
        }
        
        $this->info("Manually migrating tables to database: {$tenantDb->database_name}");
        
        try {
            // Create a temporary connection to the tenant database
            config([
                'database.connections.tenant_manual' => [
                    'driver' => 'mysql',
                    'host' => $tenantDb->database_host,
                    'port' => $tenantDb->database_port,
                    'database' => $tenantDb->database_name,
                    'username' => $tenantDb->database_username ?: config('database.connections.mysql.username'),
                    'password' => $tenantDb->database_password ?: config('database.connections.mysql.password'),
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'prefix' => '',
                    'prefix_indexes' => true,
                    'strict' => true,
                    'engine' => null,
                ]
            ]);
            
            // Connect to the tenant database
            DB::purge('tenant_manual');
            DB::reconnect('tenant_manual');
            
            $connection = DB::connection('tenant_manual');
            $this->info("Connected to tenant database.");
            
            // Create tables manually
            $this->createRequirementCategoriesTable($connection);
            $this->createCoursesTable($connection);
            $this->createRequirementsTable($connection);
            $this->createStaffTable($connection);
            $this->createStudentsTable($connection);
            $this->createStudentRequirementsTable($connection);
            
            // Clean up connection
            DB::disconnect('tenant_manual');
            
            $this->info("Tables created successfully in tenant database.");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error migrating tenant tables: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
    
    /**
     * Create requirement categories table
     */
    protected function createRequirementCategoriesTable($connection)
    {
        $this->info("Creating requirement_categories table...");
        
        if (!$connection->getSchemaBuilder()->hasTable('requirement_categories')) {
            $connection->statement('
                CREATE TABLE `requirement_categories` (
                    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                    `name` VARCHAR(255) NOT NULL,
                    `description` TEXT NULL,
                    `is_required` TINYINT(1) NOT NULL DEFAULT 1,
                    `status` VARCHAR(255) NOT NULL DEFAULT "active",
                    `created_at` TIMESTAMP NULL,
                    `updated_at` TIMESTAMP NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ');
            $this->info("Created requirement_categories table.");
        } else {
            $this->info("requirement_categories table already exists.");
        }
    }
    
    /**
     * Create courses table
     */
    protected function createCoursesTable($connection)
    {
        $this->info("Creating courses table...");
        
        if (!$connection->getSchemaBuilder()->hasTable('courses')) {
            $connection->statement('
                CREATE TABLE `courses` (
                    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                    `code` VARCHAR(255) NOT NULL,
                    `name` VARCHAR(255) NOT NULL,
                    `description` TEXT NULL,
                    `status` VARCHAR(255) NOT NULL DEFAULT "active",
                    `created_at` TIMESTAMP NULL,
                    `updated_at` TIMESTAMP NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ');
            $this->info("Created courses table.");
        } else {
            $this->info("courses table already exists.");
        }
    }
    
    /**
     * Create requirements table
     */
    protected function createRequirementsTable($connection)
    {
        $this->info("Creating requirements table...");
        
        if (!$connection->getSchemaBuilder()->hasTable('requirements')) {
            $connection->statement('
                CREATE TABLE `requirements` (
                    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                    `category_id` BIGINT UNSIGNED NOT NULL,
                    `name` VARCHAR(255) NOT NULL,
                    `description` TEXT NULL,
                    `is_required` TINYINT(1) NOT NULL DEFAULT 1,
                    `file_type` VARCHAR(255) NULL,
                    `max_file_size` INT NULL,
                    `status` VARCHAR(255) NOT NULL DEFAULT "active",
                    `created_at` TIMESTAMP NULL,
                    `updated_at` TIMESTAMP NULL,
                    PRIMARY KEY (`id`),
                    FOREIGN KEY (`category_id`) REFERENCES `requirement_categories` (`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ');
            $this->info("Created requirements table.");
        } else {
            $this->info("requirements table already exists.");
        }
    }
    
    /**
     * Create staff table
     */
    protected function createStaffTable($connection)
    {
        $this->info("Creating staff table...");
        
        if (!$connection->getSchemaBuilder()->hasTable('staff')) {
            $connection->statement('
                CREATE TABLE `staff` (
                    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                    `name` VARCHAR(255) NOT NULL,
                    `email` VARCHAR(255) NOT NULL,
                    `password` VARCHAR(255) NOT NULL,
                    `position` VARCHAR(255) NULL,
                    `status` VARCHAR(255) NOT NULL DEFAULT "active",
                    `created_at` TIMESTAMP NULL,
                    `updated_at` TIMESTAMP NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ');
            $this->info("Created staff table.");
        } else {
            $this->info("staff table already exists.");
        }
    }
    
    /**
     * Create students table
     */
    protected function createStudentsTable($connection)
    {
        $this->info("Creating students table...");
        
        if (!$connection->getSchemaBuilder()->hasTable('students')) {
            $connection->statement('
                CREATE TABLE `students` (
                    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                    `student_id` VARCHAR(255) NOT NULL,
                    `name` VARCHAR(255) NOT NULL,
                    `email` VARCHAR(255) NOT NULL,
                    `password` VARCHAR(255) NOT NULL,
                    `course_id` BIGINT UNSIGNED NULL,
                    `year_level` VARCHAR(255) NULL,
                    `status` VARCHAR(255) NOT NULL DEFAULT "active",
                    `created_at` TIMESTAMP NULL,
                    `updated_at` TIMESTAMP NULL,
                    PRIMARY KEY (`id`),
                    FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE SET NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ');
            $this->info("Created students table.");
        } else {
            $this->info("students table already exists.");
        }
    }
    
    /**
     * Create student requirements table
     */
    protected function createStudentRequirementsTable($connection)
    {
        $this->info("Creating student_requirements table...");
        
        if (!$connection->getSchemaBuilder()->hasTable('student_requirements')) {
            $connection->statement('
                CREATE TABLE `student_requirements` (
                    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                    `student_id` BIGINT UNSIGNED NOT NULL,
                    `requirement_id` BIGINT UNSIGNED NOT NULL,
                    `file_path` VARCHAR(255) NULL,
                    `status` VARCHAR(255) NOT NULL DEFAULT "pending",
                    `remarks` TEXT NULL,
                    `created_at` TIMESTAMP NULL,
                    `updated_at` TIMESTAMP NULL,
                    PRIMARY KEY (`id`),
                    FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
                    FOREIGN KEY (`requirement_id`) REFERENCES `requirements` (`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ');
            $this->info("Created student_requirements table.");
        } else {
            $this->info("student_requirements table already exists.");
        }
    }
} 