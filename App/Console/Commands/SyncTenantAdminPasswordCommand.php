<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\TenantAdmin;
use App\Models\TenantCredential;
use App\Models\Tenant;

class SyncTenantAdminPasswordCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:sync-admin-password {tenant}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync the tenant admin password from central database to tenant-specific database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->argument('tenant');

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

        // Find the admin's credentials in the central database
        $credential = TenantCredential::where('tenant_admin_id', $admin->id)->first();
        if (!$credential) {
            $this->error("No credentials found for admin '{$admin->email}' in central database.");
            return 1;
        }

        $this->info("Found admin email: {$admin->email}");

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

            // Get the password hash from central database
            $passwordHash = $credential->password;

            // Check if credentials already exist in tenant database
            $tenantCredential = DB::connection('tenant')
                ->table('tenant_user_credentials')
                ->where('email', $admin->email)
                ->first();

            if ($tenantCredential) {
                // Update existing credential with central database hash
                DB::connection('tenant')
                    ->table('tenant_user_credentials')
                    ->where('email', $admin->email)
                    ->update([
                        'password' => $passwordHash,
                        'updated_at' => now()
                    ]);
                $this->info("Updated existing credentials for {$admin->email} with the password from central database.");
            } else {
                // Create new credential with central database hash
                DB::connection('tenant')
                    ->table('tenant_user_credentials')
                    ->insert([
                        'email' => $admin->email,
                        'password' => $passwordHash,
                        'user_type' => 'admin',
                        'user_id' => $admin->id,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                $this->info("Created credentials for {$admin->email} with the password from central database.");
            }

            $this->info("Tenant admin password has been synced successfully.");
            $this->info("Login URL: http://{$tenantId}.localhost:8000/login");
            $this->info("Email: {$admin->email}");
            $this->info("Now you can log in with your original emailed password.");

            return 0;
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }
    }
}
