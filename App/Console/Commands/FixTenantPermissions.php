<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\TenantDatabase;

class FixTenantPermissions extends Command
{
    protected $signature = 'tenant:fix-permissions {tenant}';
    protected $description = 'Fix database permissions for a tenant';

    public function handle()
    {
        $tenantId = $this->argument('tenant');
        $this->info("Fixing permissions for tenant: {$tenantId}");
        
        // Find tenant database record
        $tenantDB = TenantDatabase::where('tenant_id', $tenantId)->first();
        
        if (!$tenantDB) {
            $this->error("No database record found for tenant: {$tenantId}");
            return 1;
        }
        
        $this->info("Found database: {$tenantDB->database_name}");
        $this->info("Username: {$tenantDB->database_username}");
        
        // Drop user if exists
        try {
            $this->info("Dropping user if exists...");
            DB::statement("DROP USER IF EXISTS '{$tenantDB->database_username}'@'%'");
            $this->info("User dropped");
        } catch (\Exception $e) {
            $this->warn("Error dropping user: " . $e->getMessage());
        }
        
        // Create user with the stored password
        try {
            $this->info("Creating user...");
            // The password is automatically decrypted by the model accessor
            DB::statement("CREATE USER '{$tenantDB->database_username}'@'%' IDENTIFIED BY '{$tenantDB->database_password}'");
            $this->info("User created");
        } catch (\Exception $e) {
            $this->error("Error creating user: " . $e->getMessage());
            return 1;
        }
        
        // Grant privileges
        try {
            $this->info("Granting permissions...");
            DB::statement("GRANT ALL PRIVILEGES ON `{$tenantDB->database_name}`.* TO '{$tenantDB->database_username}'@'%'");
            DB::statement("FLUSH PRIVILEGES");
            $this->info("Permissions granted and flushed");
        } catch (\Exception $e) {
            $this->error("Error granting permissions: " . $e->getMessage());
            return 1;
        }
        
        // Test the connection
        try {
            $this->info("Testing connection...");
            
            // Configure the connection explicitly
            config([
                'database.connections.test_tenant' => [
                    'driver' => 'mysql',
                    'host' => $tenantDB->database_host,
                    'port' => $tenantDB->database_port,
                    'database' => $tenantDB->database_name,
                    'username' => $tenantDB->database_username,
                    'password' => $tenantDB->database_password, // Password is automatically decrypted by model accessor
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'prefix' => '',
                ]
            ]);
            
            // Connect and test
            DB::purge('test_tenant');
            $pdo = DB::connection('test_tenant')->getPdo();
            $this->info("Connection successful!");
            
            // Test by counting tables
            $tables = DB::connection('test_tenant')->select('SHOW TABLES');
            $this->info("Found " . count($tables) . " tables in the database");
            
            foreach ($tables as $table) {
                $tableName = reset((array)$table);
                $this->info("- " . $tableName);
            }
            
            return 0;
        } catch (\Exception $e) {
            $this->error("Error testing connection: " . $e->getMessage());
            return 1;
        }
    }
} 