<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use App\Models\TenantDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TenantDatabaseManagement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:db 
                            {tenant : The tenant ID}
                            {--create : Create the database for the tenant}
                            {--migrate : Run migrations for the tenant}
                            {--seed : Seed the tenant database}
                            {--fresh : Wipe the database and run fresh migrations}
                            {--drop : Drop the tenant database}
                            {--backup : Backup the tenant database}
                            {--info : Show information about the tenant database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage separate databases for tenants';

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

        // Show tenant info
        $this->info("Tenant: {$tenant->id} ({$tenant->tenant_name})");
        $this->info("Status: {$tenant->status}");
        
        // Process commands in order
        
        // Show database info
        if ($this->option('info')) {
            $this->showDatabaseInfo($tenant);
        }
        
        // Create database
        if ($this->option('create')) {
            $this->createDatabase($tenant);
        }
        
        // Process migrations
        if ($this->option('migrate') || $this->option('fresh')) {
            $this->runMigrations($tenant, $this->option('fresh'));
        }
        
        // Seed the database
        if ($this->option('seed')) {
            $this->seedDatabase($tenant);
        }
        
        // Drop database
        if ($this->option('drop')) {
            if ($this->confirm('Are you sure you want to drop the database for tenant ' . $tenant->id . '?')) {
                $this->dropDatabase($tenant);
            }
        }
        
        // Backup database
        if ($this->option('backup')) {
            $this->backupDatabase($tenant);
        }
        
        return Command::SUCCESS;
    }
    
    /**
     * Show information about the tenant database
     */
    private function showDatabaseInfo(Tenant $tenant)
    {
        $tenantDb = TenantDatabase::where('tenant_id', $tenant->id)->first();
        
        if ($tenantDb) {
            $this->info('Database Information:');
            $this->table(
                ['Property', 'Value'],
                [
                    ['Database Name', $tenantDb->database_name],
                    ['Host', $tenantDb->database_host],
                    ['Port', $tenantDb->database_port],
                    ['Username', $tenantDb->database_username],
                ]
            );
            
            // Check if database actually exists on server
            try {
                $databaseExists = DB::select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$tenantDb->database_name]);
                
                if (count($databaseExists) > 0) {
                    $this->info('✅ Database exists on the server');
                } else {
                    $this->error('❌ Database does not exist on the server');
                }
            } catch (\Exception $e) {
                $this->error('Error checking database existence: ' . $e->getMessage());
            }
        } else {
            $this->warn('No database information found for this tenant');
        }
    }
    
    /**
     * Create a database for the tenant
     */
    private function createDatabase(Tenant $tenant)
    {
        $this->info('Creating database for tenant: ' . $tenant->id);
        
        try {
            // Check if database already exists
            $tenantDb = TenantDatabase::where('tenant_id', $tenant->id)->first();
            
            if ($tenantDb) {
                $this->warn('Tenant already has a database configured: ' . $tenantDb->database_name);
                return;
            }
            
            // Create database
            $tenant->database()->create();
            
            $this->info('Database created successfully');
        } catch (\Exception $e) {
            $this->error('Error creating database: ' . $e->getMessage());
            Log::error("Error creating database for tenant {$tenant->id}: " . $e->getMessage());
        }
    }
    
    /**
     * Run migrations for the tenant
     */
    private function runMigrations(Tenant $tenant, bool $fresh = false)
    {
        $this->info(($fresh ? 'Running fresh migrations' : 'Running migrations') . ' for tenant: ' . $tenant->id);
        
        try {
            // Check if database exists
            $tenantDb = TenantDatabase::where('tenant_id', $tenant->id)->first();
            
            if (!$tenantDb) {
                $this->error('No database configured for this tenant');
                return;
            }
            
            // Run migrations
            $command = $fresh ? 'tenants:migrate-fresh' : 'tenants:migrate';
            
            Artisan::call($command, [
                '--tenants' => [$tenant->id],
                '--force' => true
            ]);
            
            $this->info(Artisan::output());
        } catch (\Exception $e) {
            $this->error('Error running migrations: ' . $e->getMessage());
            Log::error("Error running migrations for tenant {$tenant->id}: " . $e->getMessage());
        }
    }
    
    /**
     * Seed the tenant database
     */
    private function seedDatabase(Tenant $tenant)
    {
        $this->info('Seeding database for tenant: ' . $tenant->id);
        
        try {
            // Check if database exists
            $tenantDb = TenantDatabase::where('tenant_id', $tenant->id)->first();
            
            if (!$tenantDb) {
                $this->error('No database configured for this tenant');
                return;
            }
            
            // Run seeder
            Artisan::call('tenants:seed', [
                '--tenants' => [$tenant->id],
                '--force' => true
            ]);
            
            $this->info(Artisan::output());
        } catch (\Exception $e) {
            $this->error('Error seeding database: ' . $e->getMessage());
            Log::error("Error seeding database for tenant {$tenant->id}: " . $e->getMessage());
        }
    }
    
    /**
     * Drop the tenant database
     */
    private function dropDatabase(Tenant $tenant)
    {
        $this->info('Dropping database for tenant: ' . $tenant->id);
        
        try {
            // Delete database
            $tenant->database()->delete();
            
            $this->info('Database dropped successfully');
        } catch (\Exception $e) {
            $this->error('Error dropping database: ' . $e->getMessage());
            Log::error("Error dropping database for tenant {$tenant->id}: " . $e->getMessage());
        }
    }
    
    /**
     * Backup the tenant database
     */
    private function backupDatabase(Tenant $tenant)
    {
        $this->info('Backing up database for tenant: ' . $tenant->id);
        
        try {
            $tenantDb = TenantDatabase::where('tenant_id', $tenant->id)->first();
            
            if (!$tenantDb) {
                $this->error('No database configured for this tenant');
                return;
            }
            
            $backupFileName = 'tenant_' . $tenant->id . '_' . date('Y-m-d_H-i-s') . '.sql';
            $backupPath = storage_path('app/backups/' . $backupFileName);
            
            // Create backups directory if it doesn't exist
            if (!file_exists(storage_path('app/backups'))) {
                mkdir(storage_path('app/backups'), 0755, true);
            }
            
            // Build mysqldump command
            $command = sprintf(
                'mysqldump -h%s -P%s -u%s -p%s %s > %s',
                escapeshellarg($tenantDb->database_host),
                escapeshellarg($tenantDb->database_port),
                escapeshellarg($tenantDb->database_username),
                escapeshellarg($tenantDb->database_password),
                escapeshellarg($tenantDb->database_name),
                escapeshellarg($backupPath)
            );
            
            // Execute backup command
            exec($command, $output, $returnVar);
            
            if ($returnVar === 0) {
                $this->info('Backup created successfully: ' . $backupPath);
            } else {
                $this->error('Error creating backup: ' . implode("\n", $output));
            }
        } catch (\Exception $e) {
            $this->error('Error backing up database: ' . $e->getMessage());
            Log::error("Error backing up database for tenant {$tenant->id}: " . $e->getMessage());
        }
    }
} 