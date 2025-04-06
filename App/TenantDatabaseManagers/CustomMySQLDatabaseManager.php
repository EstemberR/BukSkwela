<?php

namespace App\TenantDatabaseManagers;

use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Contracts\TenantDatabaseManager;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Exceptions\TenantDatabaseAlreadyExistsException;
use Illuminate\Support\Facades\Config;
use App\Models\TenantDatabase;

class CustomMySQLDatabaseManager implements TenantDatabaseManager
{
    /** @var string */
    protected $connection;

    public function __construct()
    {
        $this->connection = config('tenancy.database_manager_connections.mysql');
    }

    /**
     * Create a database for a tenant.
     *
     * @param TenantWithDatabase $tenant
     * @return bool
     */
    public function createDatabase(TenantWithDatabase $tenant): bool
    {
        $name = $tenant->getTenantKey();
        $database = 'tenant_' . $name;
        $username = 'tenant_' . $name;
        $password = $this->generateStrongPassword();

        // Database name with tenant prefix
        $databaseName = $database;

        try {
            // Check if database exists
            if ($this->databaseExists($databaseName)) {
                throw new TenantDatabaseAlreadyExistsException($databaseName);
            }

            // Create the database
            DB::connection($this->connection)->statement("CREATE DATABASE `{$databaseName}`");
            
            // Create a user for this tenant
            DB::connection($this->connection)->statement("CREATE USER '{$username}'@'%' IDENTIFIED BY '{$password}'");
            
            // Grant privileges
            DB::connection($this->connection)->statement("GRANT ALL PRIVILEGES ON `{$databaseName}`.* TO '{$username}'@'%'");
            DB::connection($this->connection)->statement("FLUSH PRIVILEGES");
            
            // Store database credentials in the central database
            if ($tenant instanceof \App\Models\Tenant) {
                $tenantDB = TenantDatabase::updateOrCreate(
                    ['tenant_id' => $name],
                    [
                        'database_name' => $databaseName,
                        'database_username' => $username,
                        'database_password' => $password,
                        'database_host' => Config::get('database.connections.mysql.host'),
                        'database_port' => Config::get('database.connections.mysql.port'),
                    ]
                );
            }
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Error creating tenant database: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete a tenant's database.
     *
     * @param TenantWithDatabase $tenant
     * @return bool
     */
    public function deleteDatabase(TenantWithDatabase $tenant): bool
    {
        $name = $tenant->getTenantKey();
        $database = 'tenant_' . $name;
        $username = 'tenant_' . $name;

        try {
            // Drop the database
            DB::connection($this->connection)->statement("DROP DATABASE IF EXISTS `{$database}`");
            
            // Drop the user
            DB::connection($this->connection)->statement("DROP USER IF EXISTS '{$username}'@'%'");
            DB::connection($this->connection)->statement("FLUSH PRIVILEGES");
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Error deleting tenant database: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Check if a database exists.
     *
     * @param string $name
     * @return bool
     */
    public function databaseExists(string $name): bool
    {
        $databaseExists = DB::connection($this->connection)
            ->select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$name]);
        
        return count($databaseExists) > 0;
    }

    /**
     * Make a tenant database connection config.
     *
     * @param array $baseConfig
     * @param string $databaseName
     * @return array
     */
    public function makeConnectionConfig(array $baseConfig, string $databaseName): array
    {
        // Get tenant database credentials
        $tenantId = str_replace('tenant_', '', $databaseName);
        $tenantDB = TenantDatabase::where('tenant_id', $tenantId)->first();
        
        if ($tenantDB) {
            // Return connection config with tenant's dedicated credentials
            return array_merge($baseConfig, [
                'database' => $tenantDB->database_name,
                'username' => $tenantDB->database_username,
                'password' => $tenantDB->database_password,
                'host' => $tenantDB->database_host,
                'port' => $tenantDB->database_port,
            ]);
        }
        
        // Fallback to default behavior
        return array_merge($baseConfig, [
            'database' => $databaseName,
        ]);
    }

    /**
     * Set the connection name that should be used.
     *
     * @param string $connection
     * @return void
     */
    public function setConnection(string $connection): void
    {
        $this->connection = $connection;
    }

    /**
     * Generate a secure random password
     *
     * @return string
     */
    protected function generateStrongPassword(): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_+=';
        $password = '';
        
        for ($i = 0; $i < 16; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }
        
        return $password;
    }
} 