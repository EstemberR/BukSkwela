<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use App\Models\TenantAdmin;
use App\Models\Tenant;

class FixTenantLoginCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:fix-admin-login {tenant} {--email=} {--password=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix tenant admin login by adding credentials to tenant_user_credentials table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->argument('tenant');
        $email = $this->option('email');
        $password = $this->option('password');

        // Find the tenant
        $tenant = Tenant::find($tenantId);
        if (!$tenant) {
            $this->error("Tenant '{$tenantId}' not found.");
            return 1;
        }

        // Find tenant admin
        $admin = TenantAdmin::where('tenant_id', $tenantId)->first();
        if (!$admin) {
            $this->error("No admin found for tenant '{$tenantId}'.");
            return 1;
        }

        // Use admin email if none provided
        if (!$email) {
            $email = $admin->email;
            $this->info("Using admin email: {$email}");
        }

        // Connect to tenant database
        $tenantDatabaseName = 'tenant_' . $tenantId;
        config(['database.connections.tenant.database' => $tenantDatabaseName]);
        DB::purge('tenant');
        DB::reconnect('tenant');

        try {
            $this->info("Connected to tenant database: {$tenantDatabaseName}");

            // Check if tenant_user_credentials table exists
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

            // Check if credentials already exist
            $credential = DB::connection('tenant')
                ->table('tenant_user_credentials')
                ->where('email', $email)
                ->first();

            if ($credential) {
                if ($password) {
                    // Update existing credential
                    DB::connection('tenant')
                        ->table('tenant_user_credentials')
                        ->where('email', $email)
                        ->update([
                            'password' => Hash::make($password),
                            'updated_at' => now()
                        ]);
                    $this->info("Updated existing credentials for {$email} with new password.");
                } else {
                    $this->info("Credentials for {$email} already exist.");
                }
            } else {
                // Create new credential
                if (!$password) {
                    // For demonstration, use a fixed password that matches the original emailed password
                    $password = "adminpassword"; // Replace with your original password if you know it
                    $this->warn("Using default password: {$password}");
                }

                DB::connection('tenant')
                    ->table('tenant_user_credentials')
                    ->insert([
                        'email' => $email,
                        'password' => Hash::make($password),
                        'user_type' => 'admin',
                        'user_id' => $admin->id,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                $this->info("Created credentials for {$email} with the provided password.");
            }

            $this->info("Tenant admin login credentials have been fixed.");
            $this->info("Login URL: http://{$tenantId}.localhost:8000/login");
            $this->info("Email: {$email}");
            if ($password) {
                $this->info("Password: {$password}");
            }

            return 0;
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }
    }
}
