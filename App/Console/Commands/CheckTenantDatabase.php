<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckTenantDatabase extends Command
{
    protected $signature = 'tenant:check-db {tenant}';
    protected $description = 'Check tenant database structure and content';

    public function handle()
    {
        $tenantId = $this->argument('tenant');
        $this->info("Checking database for tenant: $tenantId");
        
        // Configure connection to the tenant database
        $tenantDatabaseName = 'tenant_' . $tenantId;
        config(['database.connections.tenant.database' => $tenantDatabaseName]);
        DB::purge('tenant');
        DB::reconnect('tenant');
        
        $this->info("Connected to database: " . DB::connection('tenant')->getDatabaseName());
        
        // List all tables
        $this->info("Tables in tenant database:");
        $tables = DB::connection('tenant')->select('SHOW TABLES');
        foreach($tables as $table) {
            $tableColumn = 'Tables_in_' . $tenantDatabaseName;
            $this->line("- " . $table->$tableColumn);
        }
        
        // Create tenant_user_credentials table if it doesn't exist
        if (!Schema::connection('tenant')->hasTable('tenant_user_credentials')) {
            $this->info("Creating tenant_user_credentials table...");
            DB::connection('tenant')->statement("
                CREATE TABLE tenant_user_credentials (
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
            $this->info("Table created successfully!");
        }
        
        // Check admin credentials
        $this->info("\nCredentials in tenant_user_credentials:");
        $credentials = DB::connection('tenant')->table('tenant_user_credentials')->get();
        if (count($credentials) > 0) {
            foreach($credentials as $credential) {
                $this->line("- Email: " . $credential->email);
            }
        } else {
            $this->warn("No credentials found.");
            
            // Insert a test admin credentials
            $this->info("\nInserting test admin credentials...");
            DB::connection('tenant')->table('tenant_user_credentials')->insert([
                'email' => 'admin@' . $tenantId . '.test',
                'password' => bcrypt('password'),
                'user_type' => 'admin',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $this->info("Test credentials inserted with email: admin@" . $tenantId . ".test and password: password");
        }
        
        return 0;
    }
} 