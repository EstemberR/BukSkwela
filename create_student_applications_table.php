<?php

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define path to the application
$basePath = __DIR__;

// Load Composer autoload file
require $basePath . '/vendor/autoload.php';

// Load .env file
$dotenv = Dotenv\Dotenv::createImmutable($basePath);
$dotenv->load();

// Database connection parameters
$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$port = $_ENV['DB_PORT'] ?? '3306';
$database = $_ENV['DB_DATABASE'] ?? 'bukskwela';
$username = $_ENV['DB_USERNAME'] ?? 'root';
$password = $_ENV['DB_PASSWORD'] ?? '';

// Connect to main database
try {
    $mainPdo = new PDO("mysql:host=$host;port=$port;dbname=$database", $username, $password);
    $mainPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected to main database successfully\n";
} catch (PDOException $e) {
    die("Connection to main database failed: " . $e->getMessage() . "\n");
}

// Specify tenant ID or get from command line
$tenantId = $argv[1] ?? null;

if (!$tenantId) {
    // Get the current domain from the browser if available
    $currentDomain = $_SERVER['HTTP_HOST'] ?? '';
    if (preg_match('/^([a-zA-Z0-9-]+)\./', $currentDomain, $matches)) {
        $tenantId = $matches[1];
        echo "Using tenant ID from domain: $tenantId\n";
    } else {
        // Try to fetch a list of tenants
        try {
            $stmt = $mainPdo->query("SELECT id FROM tenants LIMIT 1");
            $tenant = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($tenant) {
                $tenantId = $tenant['id'];
                echo "Using first tenant found: $tenantId\n";
            } else {
                die("No tenant found and no tenant ID specified. Usage: php create_student_applications_table.php [tenant_id]\n");
            }
        } catch (PDOException $e) {
            die("Error fetching tenants: " . $e->getMessage() . "\n");
        }
    }
}

// Determine tenant database name
$tenantDatabase = 'tenant_' . strtolower($tenantId);
echo "Working with tenant database: $tenantDatabase\n";

// Connect to tenant database
try {
    $tenantPdo = new PDO("mysql:host=$host;port=$port;dbname=$tenantDatabase", $username, $password);
    $tenantPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected to tenant database successfully\n";
} catch (PDOException $e) {
    die("Connection to tenant database failed: " . $e->getMessage() . "\n");
}

// Check if student_applications table exists
try {
    $stmt = $tenantPdo->query("SHOW TABLES LIKE 'student_applications'");
    if ($stmt->rowCount() > 0) {
        echo "student_applications table already exists in $tenantDatabase\n";
        exit(0);
    }
} catch (PDOException $e) {
    echo "Error checking for existing table: " . $e->getMessage() . "\n";
}

// Create student_applications table
$createTableSQL = "
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
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_applications_status_index` (`status`),
  KEY `student_applications_tenant_id_index` (`tenant_id`),
  KEY `student_applications_student_id_status_index` (`student_id`,`status`),
  CONSTRAINT `student_applications_program_id_foreign` FOREIGN KEY (`program_id`) REFERENCES `courses` (`id`),
  CONSTRAINT `student_applications_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

try {
    $tenantPdo->exec($createTableSQL);
    echo "student_applications table created successfully in $tenantDatabase\n";
} catch (PDOException $e) {
    echo "Error creating student_applications table: " . $e->getMessage() . "\n";
    
    // If the error is about foreign keys, try creating without foreign key constraints
    if (strpos($e->getMessage(), 'foreign key constraint') !== false) {
        echo "Attempting to create the table without foreign key constraints...\n";
        $createTableWithoutFKSQL = "
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
          `created_at` timestamp NULL DEFAULT NULL,
          `updated_at` timestamp NULL DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `student_applications_status_index` (`status`),
          KEY `student_applications_tenant_id_index` (`tenant_id`),
          KEY `student_applications_student_id_status_index` (`student_id`,`status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        
        try {
            $tenantPdo->exec($createTableWithoutFKSQL);
            echo "student_applications table created successfully without foreign key constraints in $tenantDatabase\n";
        } catch (PDOException $e2) {
            echo "Error creating simplified student_applications table: " . $e2->getMessage() . "\n";
        }
    }
}

echo "Script completed.\n"; 