<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use App\Models\TenantDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;

class CreateTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:create {name} {email} {--password=} {--plan=free}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new tenant with proper database setup';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $password = $this->option('password') ?: bcrypt('password123');
        $plan = $this->option('plan');
        
        // Generate tenant ID from name (URL-friendly version of name)
        $tenantId = $this->generateTenantId($name);
        
        $this->info("Creating tenant with ID: {$tenantId}");
        
        try {
            // Create the tenant record
            $tenant = Tenant::create([
                'id' => $tenantId,
                'tenant_name' => $name,
                'tenant_email' => $email,
                'status' => 'approved',
                'subscription_plan' => $plan,
                'data' => json_encode([
                    'name' => $name,
                    'email' => $email,
                    'password' => $password
                ])
            ]);
            
            $this->info("Tenant record created successfully");
            
            // Create a domain for the tenant
            $tenant->domains()->create(['domain' => "{$tenantId}.localhost"]);
            $this->info("Tenant domain created: {$tenantId}.localhost");
            
            // Set up the tenant database
            $databaseName = "tenant_{$tenantId}";
            
            // Create database record
            TenantDatabase::create([
                'tenant_id' => $tenantId,
                'database_name' => $databaseName,
                'database_username' => env('DB_USERNAME'),
                'database_password' => env('DB_PASSWORD'),
                'database_host' => env('DB_HOST', '127.0.0.1'),
                'database_port' => env('DB_PORT', '3306')
            ]);
            
            $this->info("Tenant database record created");
            
            // Create physical database if it doesn't exist
            DB::statement("CREATE DATABASE IF NOT EXISTS `{$databaseName}`");
            $this->info("Database '{$databaseName}' created successfully");
            
            // Run database migrations for this tenant
            $this->call('tenant:migrate-db', ['tenant' => $tenantId]);
            
            // Fix the tenant database (add required columns, etc.)
            $this->call('tenant:fix-database', ['tenant' => $tenantId]);
            
            // Fix relationships for the tenant
            $this->call('tenant:fix-relationships', ['tenant' => $tenantId]);
            
            // Fix the courses table
            $this->call('tenant:fix-courses-table', ['tenant' => $tenantId]);
            
            $this->info("Tenant {$tenantId} created successfully with all database tables and relationships!");
            $this->info("You can access it at: http://{$tenantId}.localhost:8000");
            return 0;
            
        } catch (\Exception $e) {
            $this->error("Error creating tenant: " . $e->getMessage());
            return 1;
        }
    }
    
    /**
     * Generate a tenant ID from the tenant name
     */
    private function generateTenantId($name)
    {
        // Convert to lowercase and replace spaces/special chars with hyphens
        $tenantId = strtolower(trim($name));
        $tenantId = preg_replace('/[^a-z0-9]/', '-', $tenantId);
        $tenantId = preg_replace('/-+/', '-', $tenantId); // Replace multiple hyphens with a single one
        $tenantId = trim($tenantId, '-'); // Remove hyphens from start and end
        
        // If the ID already exists, add a unique suffix
        $originalId = $tenantId;
        $counter = 1;
        
        while (Tenant::find($tenantId)) {
            $tenantId = $originalId . '-' . $counter;
            $counter++;
        }
        
        return $tenantId;
    }
} 