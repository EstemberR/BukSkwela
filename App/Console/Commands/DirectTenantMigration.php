<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class DirectTenantMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:direct-migrate {database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Directly create tables in a tenant database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $databaseName = $this->argument('database');
        $this->info("Directly creating tables in database: {$databaseName}");
        
        try {
            // Use the root MySQL credentials for better privileges
            $host = Config::get('database.connections.mysql.host', '127.0.0.1');
            $username = Config::get('database.connections.mysql.username', 'root');
            $password = Config::get('database.connections.mysql.password', '');
            
            $this->info("Using database credentials: {$username}@{$host}");
            
            // Create requirement_categories table
            $this->info("Creating requirement_categories table...");
            $sql = "
                CREATE TABLE IF NOT EXISTS `{$databaseName}`.`requirement_categories` (
                    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                    `name` VARCHAR(255) NOT NULL,
                    `description` TEXT NULL,
                    `is_required` TINYINT(1) NOT NULL DEFAULT 1,
                    `status` VARCHAR(255) NOT NULL DEFAULT 'active',
                    `created_at` TIMESTAMP NULL,
                    `updated_at` TIMESTAMP NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
            DB::statement($sql);
            $this->info("requirement_categories table created.");
            
            // Create courses table
            $this->info("Creating courses table...");
            $sql = "
                CREATE TABLE IF NOT EXISTS `{$databaseName}`.`courses` (
                    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                    `code` VARCHAR(255) NOT NULL,
                    `name` VARCHAR(255) NOT NULL,
                    `description` TEXT NULL,
                    `status` VARCHAR(255) NOT NULL DEFAULT 'active',
                    `created_at` TIMESTAMP NULL,
                    `updated_at` TIMESTAMP NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
            DB::statement($sql);
            $this->info("courses table created.");
            
            // Create requirements table
            $this->info("Creating requirements table...");
            $sql = "
                CREATE TABLE IF NOT EXISTS `{$databaseName}`.`requirements` (
                    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                    `category_id` BIGINT UNSIGNED NOT NULL,
                    `name` VARCHAR(255) NOT NULL,
                    `description` TEXT NULL,
                    `is_required` TINYINT(1) NOT NULL DEFAULT 1,
                    `file_type` VARCHAR(255) NULL,
                    `max_file_size` INT NULL,
                    `status` VARCHAR(255) NOT NULL DEFAULT 'active',
                    `created_at` TIMESTAMP NULL,
                    `updated_at` TIMESTAMP NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
            DB::statement($sql);
            $this->info("requirements table created.");
            
            // Create staff table
            $this->info("Creating staff table...");
            $sql = "
                CREATE TABLE IF NOT EXISTS `{$databaseName}`.`staff` (
                    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                    `name` VARCHAR(255) NOT NULL,
                    `email` VARCHAR(255) NOT NULL,
                    `password` VARCHAR(255) NOT NULL,
                    `position` VARCHAR(255) NULL,
                    `status` VARCHAR(255) NOT NULL DEFAULT 'active',
                    `created_at` TIMESTAMP NULL,
                    `updated_at` TIMESTAMP NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
            DB::statement($sql);
            $this->info("staff table created.");
            
            // Create students table
            $this->info("Creating students table...");
            $sql = "
                CREATE TABLE IF NOT EXISTS `{$databaseName}`.`students` (
                    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                    `student_id` VARCHAR(255) NOT NULL,
                    `name` VARCHAR(255) NOT NULL,
                    `email` VARCHAR(255) NOT NULL,
                    `password` VARCHAR(255) NOT NULL,
                    `course_id` BIGINT UNSIGNED NULL,
                    `year_level` VARCHAR(255) NULL,
                    `status` VARCHAR(255) NOT NULL DEFAULT 'active',
                    `created_at` TIMESTAMP NULL,
                    `updated_at` TIMESTAMP NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
            DB::statement($sql);
            $this->info("students table created.");
            
            // Create student_requirements table
            $this->info("Creating student_requirements table...");
            $sql = "
                CREATE TABLE IF NOT EXISTS `{$databaseName}`.`student_requirements` (
                    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                    `student_id` BIGINT UNSIGNED NOT NULL,
                    `requirement_id` BIGINT UNSIGNED NOT NULL,
                    `file_path` VARCHAR(255) NULL,
                    `status` VARCHAR(255) NOT NULL DEFAULT 'pending',
                    `remarks` TEXT NULL,
                    `created_at` TIMESTAMP NULL,
                    `updated_at` TIMESTAMP NULL,
                    PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
            DB::statement($sql);
            $this->info("student_requirements table created.");
            
            $this->info("All tables created successfully.");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error creating tables: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
} 