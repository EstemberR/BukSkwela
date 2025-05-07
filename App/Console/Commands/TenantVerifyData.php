<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class TenantVerifyData extends Command
{
    protected $signature = 'tenant:verify-data {tenant}';
    protected $description = 'Verify if data exists in the tenant database';

    public function handle()
    {
        $tenant = $this->argument('tenant');
        $databaseName = 'tenant_' . $tenant;
        
        $this->info("Verifying data in database: {$databaseName}");
        
        // Set the database connection for the tenant
        Config::set('database.connections.tenant.database', $databaseName);
        Config::set('database.connections.tenant.username', env('DB_USERNAME', 'root'));
        Config::set('database.connections.tenant.password', env('DB_PASSWORD', ''));
        
        // Purge and reconnect to the tenant database
        DB::purge('tenant');
        DB::reconnect('tenant');
        
        try {
            DB::connection('tenant')->getPdo();
            $this->info("Successfully connected to tenant database");
            
            // Check staff
            $staffCount = DB::connection('tenant')->table('staff')->count();
            $this->info("Staff count: " . $staffCount);
            
            if ($staffCount > 0) {
                $this->info("Listing staff records:");
                $staffRecords = DB::connection('tenant')->table('staff')->get();
                foreach ($staffRecords as $index => $staff) {
                    $this->info(($index + 1) . ". ID: {$staff->id}, Name: {$staff->name}, Email: {$staff->email}, Tenant ID: {$staff->tenant_id}");
                }
            }
            
            // Check departments
            $departmentCount = DB::connection('tenant')->table('departments')->count();
            $this->info("Department count: " . $departmentCount);
            
            if ($departmentCount > 0) {
                $this->info("Listing department records:");
                $departmentRecords = DB::connection('tenant')->table('departments')->get();
                foreach ($departmentRecords as $index => $department) {
                    $this->info(($index + 1) . ". ID: {$department->id}, Name: {$department->name}, Code: {$department->code}, Tenant ID: {$department->tenant_id}");
                }
            }
            
            return 0;
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }
    }
} 