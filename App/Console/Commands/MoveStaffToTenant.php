<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use App\Models\Staff;
use App\Models\Department;
use App\Models\Tenant;

class MoveStaffToTenant extends Command
{
    protected $signature = 'tenant:move-staff';
    protected $description = 'Move staff data from central database to tenant database';

    public function handle()
    {
        $this->info("Moving staff data from central to tenant databases");
        
        // Get all tenants
        $tenants = Tenant::all();
        
        if ($tenants->isEmpty()) {
            $this->error("No tenants found");
            return 1;
        }
        
        $this->info("Found " . $tenants->count() . " tenants");
        
        foreach ($tenants as $tenant) {
            $tenantId = $tenant->id;
            $databaseName = 'tenant_' . $tenantId;
            
            $this->info("Processing tenant: {$tenantId} with database: {$databaseName}");
            
            // Set the database connection for the tenant
            Config::set('database.connections.tenant.database', $databaseName);
            Config::set('database.connections.tenant.username', env('DB_USERNAME', 'root'));
            Config::set('database.connections.tenant.password', env('DB_PASSWORD', ''));
            
            // Purge and reconnect to the tenant database
            DB::purge('tenant');
            DB::reconnect('tenant');
            
            try {
                // Check if tenant database exists
                DB::connection('tenant')->getPdo();
                $this->info("Successfully connected to tenant database");
                
                // Get staff for this tenant from central database
                $staffMembers = DB::table('staff')->where('tenant_id', $tenantId)->get();
                
                $this->info("Found " . $staffMembers->count() . " staff records for this tenant in central database");
                
                if ($staffMembers->isEmpty()) {
                    $this->info("No staff to migrate for this tenant");
                    continue;
                }
                
                // Process departments first
                $departmentMap = [];
                $departmentNames = $staffMembers->pluck('department_id')->unique();
                
                $this->info("Processing " . $departmentNames->count() . " departments");
                
                foreach ($departmentNames as $departmentId) {
                    if (!$departmentId) continue;
                    
                    $centralDepartment = DB::table('departments')->where('id', $departmentId)->first();
                    
                    if (!$centralDepartment) {
                        $this->warn("Department ID {$departmentId} not found in central database");
                        continue;
                    }
                    
                    // Check if department already exists in tenant database
                    $tenantDepartment = DB::connection('tenant')
                        ->table('departments')
                        ->where('name', $centralDepartment->name)
                        ->where('tenant_id', $tenantId)
                        ->first();
                    
                    if ($tenantDepartment) {
                        $departmentMap[$departmentId] = $tenantDepartment->id;
                        $this->info("Department '{$centralDepartment->name}' already exists in tenant database with ID: {$tenantDepartment->id}");
                    } else {
                        // Create department in tenant database
                        $newDepartmentId = DB::connection('tenant')->table('departments')->insertGetId([
                            'name' => $centralDepartment->name,
                            'code' => $centralDepartment->code ?? strtoupper(substr($centralDepartment->name, 0, 5)),
                            'description' => $centralDepartment->description ?? 'Department for ' . $centralDepartment->name,
                            'status' => $centralDepartment->status ?? 'active',
                            'tenant_id' => $tenantId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        
                        $departmentMap[$departmentId] = $newDepartmentId;
                        $this->info("Created department '{$centralDepartment->name}' in tenant database with ID: {$newDepartmentId}");
                    }
                }
                
                // Now process staff members
                $createdCount = 0;
                $existingCount = 0;
                
                foreach ($staffMembers as $staff) {
                    // Check if staff already exists in tenant database
                    $tenantStaff = DB::connection('tenant')
                        ->table('staff')
                        ->where('email', $staff->email)
                        ->first();
                    
                    if ($tenantStaff) {
                        $existingCount++;
                        $this->info("Staff '{$staff->name}' already exists in tenant database with ID: {$tenantStaff->id}");
                        continue;
                    }
                    
                    // Get the mapped department ID
                    $tenantDepartmentId = $staff->department_id ? ($departmentMap[$staff->department_id] ?? null) : null;
                    
                    // Create staff in tenant database
                    $newStaffId = DB::connection('tenant')->table('staff')->insertGetId([
                        'staff_id' => $staff->staff_id ?? 'STAFF' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                        'name' => $staff->name,
                        'email' => $staff->email,
                        'password' => $staff->password,
                        'role' => $staff->role ?? 'instructor',
                        'department_id' => $tenantDepartmentId,
                        'status' => $staff->status ?? 'active',
                        'tenant_id' => $tenantId,
                        'remember_token' => $staff->remember_token,
                        'created_at' => $staff->created_at ?? now(),
                        'updated_at' => $staff->updated_at ?? now(),
                    ]);
                    
                    $createdCount++;
                    $this->info("Created staff '{$staff->name}' in tenant database with ID: {$newStaffId}");
                }
                
                $this->info("Migration completed for tenant {$tenantId}. Created {$createdCount} staff records, {$existingCount} already existed.");
                
            } catch (\Exception $e) {
                $this->error("Error processing tenant {$tenantId}: " . $e->getMessage());
            }
        }
        
        $this->info("Staff migration completed for all tenants");
        return 0;
    }
} 