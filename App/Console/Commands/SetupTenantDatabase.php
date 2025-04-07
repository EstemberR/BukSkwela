<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Tenant;
use App\Models\TenantDatabase;
use Illuminate\Support\Str;

class SetupTenantDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:setup-tenant {tenant : The tenant ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up a tenant database with proper user and privileges';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->argument('tenant');
        
        // Find the tenant
        $tenant = Tenant::find($tenantId);
        if (!$tenant) {
            $this->error("Tenant with ID {$tenantId} not found");
            return Command::FAILURE;
        }
        
        $this->info("Setting up database for tenant: {$tenant->id}");
        
        // Generate database name and credentials
        $databaseName = 'tenant_' . Str::slug($tenant->id);
        $username = 'tenant_' . Str::slug($tenant->id);
        $password = Str::random(16);
        
        try {
            // Create database if it doesn't exist - use CREATE DATABASE to ensure it's a separate database
            $this->info("Creating SEPARATE database: {$databaseName}");
            DB::statement("CREATE DATABASE IF NOT EXISTS `{$databaseName}`");
            
            // Log confirmation that the database was created
            $dbExists = DB::select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$databaseName]);
            if (!empty($dbExists)) {
                $this->info("✅ Database {$databaseName} exists or was created successfully");
                \Log::info("Database {$databaseName} created successfully as a separate database");
            } else {
                $this->error("❌ Failed to find or create database {$databaseName}");
                \Log::error("Failed to find or create database {$databaseName}");
                return Command::FAILURE;
            }
            
            // Create/update user with privileges
            $this->info("Setting up database user: {$username}");
            
            // Drop user if it exists
            try {
                DB::statement("DROP USER IF EXISTS '{$username}'@'%'");
            } catch (\Exception $e) {
                // Ignore error if user doesn't exist
                $this->info("Note: User might not exist yet - continuing setup");
            }
            
            // Create user
            DB::statement("CREATE USER '{$username}'@'%' IDENTIFIED BY '{$password}'");
            
            // Grant privileges - Only to the specific database
            DB::statement("GRANT ALL PRIVILEGES ON `{$databaseName}`.* TO '{$username}'@'%'");
            DB::statement("FLUSH PRIVILEGES");
            
            // Update the tenant database record
            TenantDatabase::updateOrCreate(
                ['tenant_id' => $tenant->id],
                [
                    'database_name' => $databaseName,
                    'database_username' => $username,
                    'database_password' => $password,
                    'database_host' => config('database.connections.mysql.host'),
                    'database_port' => config('database.connections.mysql.port'),
                ]
            );
            
            $this->info("Database setup completed successfully");
            $this->info("Database: {$databaseName}");
            $this->info("Username: {$username}");
            $this->info("Password: {$password}");
            
            // Run migrations automatically
            $this->info("Running migrations for tenant database");
            try {
                // Run direct migration to create tables
                \Artisan::call('tenant:direct-migrate', [
                    'database' => $databaseName
                ]);
                
                $migrationOutput = \Artisan::output();
                $this->info("Migration output:");
                $this->line($migrationOutput);
                
                $this->info("Database setup and migration completed successfully");
                return Command::SUCCESS;
            } catch (\Exception $e) {
                $this->error("Error running migrations: " . $e->getMessage());
                $this->warn("Database was created but migrations failed. You can run migrations manually.");
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error("Error setting up database: " . $e->getMessage());
            Log::error("Error setting up database for tenant {$tenant->id}: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
} 