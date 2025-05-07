<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Models\TenantAdmin;
use App\Models\TenantDatabase;

class FixUserPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:fix-password {email?} {tenant_id?} {password?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix user password across all relevant tables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get arguments or use defaults
        $email = $this->argument('email') ?? 'jorellabeciatnt@gmail.com';
        $tenantId = $this->argument('tenant_id') ?? 'informationtechlogy';
        $newPassword = $this->argument('password') ?? 'NewPassword123';

        $this->info("Attempting to update password for {$email} in tenant {$tenantId}");

        // Get the tenant admin
        $user = TenantAdmin::where('tenant_id', $tenantId)
            ->where('email', $email)
            ->first();

        if (!$user) {
            $this->error("User not found with email {$email} in tenant {$tenantId}");
            return 1;
        }

        $this->info("Found user: {$user->email} (ID: {$user->id})");

        // Hash the new password
        $hashedPassword = Hash::make($newPassword);

        // Update the user's password in tenant_admins table
        $user->password = $hashedPassword;
        $user->save();

        $this->info("Updated password in tenant_admins table");
        
        // Configure the tenant database connection
        $this->configureTenantConnection($tenantId);
        
        // Verify connection to tenant database
        try {
            DB::connection('tenant')->getPdo();
            $this->info("Successfully connected to tenant database");
        } catch (\Exception $e) {
            $this->error("Failed to connect to tenant database: " . $e->getMessage());
            return 1;
        }
        
        // Let's try several approaches to ensure the password is set correctly
        try {
            // Get all tables in the tenant database
            $tables = DB::connection('tenant')->select('SHOW TABLES');
            $this->info("Found " . count($tables) . " tables in tenant database");
            
            foreach ($tables as $table) {
                $tableName = reset($table); // Get the first (and only) value from the table object
                $this->info("Found table: " . $tableName);
                
                // Check if this might be a credentials table
                if (strpos(strtolower($tableName), 'user') !== false || 
                    strpos(strtolower($tableName), 'admin') !== false || 
                    strpos(strtolower($tableName), 'cred') !== false) {
                    
                    $this->info("Examining potential credentials table: " . $tableName);
                    
                    // Check table structure
                    $columns = DB::connection('tenant')->select("SHOW COLUMNS FROM `$tableName`");
                    $columnNames = array_map(function($col) { return $col->Field; }, $columns);
                    $this->info("Table columns: " . implode(', ', $columnNames));
                    
                    // Check if this table has email and password columns
                    if (in_array('email', $columnNames) && in_array('password', $columnNames)) {
                        $this->info("Found matching table with email and password: " . $tableName);
                        
                        // Update the password where email matches
                        DB::connection('tenant')->update("
                            UPDATE `$tableName` SET password = ? WHERE email = ?
                        ", [$hashedPassword, $user->email]);
                        
                        $this->info("Updated password in table: " . $tableName);
                    }
                }
            }
            
            // Ensure we have a tenant_user_credentials table
            if (!in_array('tenant_user_credentials', array_map(function($table) { 
                return reset($table); 
            }, $tables))) {
                $this->info("Creating tenant_user_credentials table");
                
                // Create the table
                DB::connection('tenant')->statement("
                    CREATE TABLE tenant_user_credentials (
                        id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                        email VARCHAR(255),
                        password VARCHAR(255),
                        tenant_id VARCHAR(255),
                        user_id BIGINT UNSIGNED,
                        remember_token VARCHAR(100) NULL,
                        created_at TIMESTAMP NULL,
                        updated_at TIMESTAMP NULL
                    )
                ");
                
                // Insert the user record
                $now = now()->format('Y-m-d H:i:s');
                DB::connection('tenant')->insert("
                    INSERT INTO tenant_user_credentials 
                    (email, password, tenant_id, user_id, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?)
                ", [$user->email, $hashedPassword, $tenantId, $user->id, $now, $now]);
                
                $this->info("Created tenant_user_credentials table and inserted user record");
            }
            
            $this->info("Password update complete. The new password is: " . $newPassword);
            $this->info("This password should now work for login.");
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Error updating tenant credentials: " . $e->getMessage());
            return 1;
        }
    }
    
    /**
     * Configure the tenant database connection
     */
    private function configureTenantConnection($tenantId)
    {
        $this->info("Configuring tenant database connection...");
        
        // Get the tenant database from tenant_databases table
        $tenantDB = TenantDatabase::where('tenant_id', $tenantId)->first();
        
        if ($tenantDB) {
            // Set database connection configuration with proper credentials
            $this->info("Using tenant database: {$tenantDB->database_name}");
            
            Config::set('database.connections.tenant.database', $tenantDB->database_name);
            Config::set('database.connections.tenant.username', $tenantDB->database_username);
            Config::set('database.connections.tenant.password', $tenantDB->database_password);
            
            if ($tenantDB->database_host) {
                Config::set('database.connections.tenant.host', $tenantDB->database_host);
            }
            
            if ($tenantDB->database_port) {
                Config::set('database.connections.tenant.port', $tenantDB->database_port);
            }
        } else {
            // Fallback to default credentials with tenant database name
            $databaseName = 'tenant_' . $tenantId;
            $this->info("No tenant database record found. Using default database: {$databaseName}");
            
            Config::set('database.connections.tenant.database', $databaseName);
            Config::set('database.connections.tenant.username', env('DB_USERNAME'));
            Config::set('database.connections.tenant.password', env('DB_PASSWORD'));
        }
        
        // Purge and reconnect to the tenant database
        DB::purge('tenant');
        DB::reconnect('tenant');
    }
} 