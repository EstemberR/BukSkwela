<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Staff\Staff;
use Illuminate\Support\Facades\Schema;

class MigrateStaffToTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:staff-to-tenant {tenant_id=informationtechnology}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate staff data from global to tenant database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->argument('tenant_id');
        $tenantDbName = 'tenant_' . $tenantId;
        
        $this->info("Starting migration of staff data to {$tenantDbName} database");
        
        // Get staff for the specific tenant from the main database
        $staffMembers = DB::table('staff')
            ->where('tenant_id', $tenantId)
            ->get();
            
        if ($staffMembers->isEmpty()) {
            $this->error("No staff records found for tenant {$tenantId}");
            return 1;
        }
        
        $this->info("Found " . $staffMembers->count() . " staff records to migrate");
        
        // Configure the tenant database connection
        config(['database.connections.tenant.database' => $tenantDbName]);
        DB::purge('tenant');
        
        // Check if the tenant database exists
        try {
            DB::connection('tenant')->getPdo();
            $this->info("Successfully connected to tenant database: {$tenantDbName}");
        } catch (\Exception $e) {
            $this->error("Tenant database {$tenantDbName} does not exist or cannot connect: " . $e->getMessage());
            return 1;
        }
        
        // Check if staff table exists in tenant database
        if (!Schema::connection('tenant')->hasTable('staff')) {
            $this->error("Staff table does not exist in tenant database");
            return 1;
        }
        
        $this->info("Migrating staff data to tenant database...");
        
        // Begin transaction
        DB::beginTransaction();
        
        try {
            // Get the columns in the tenant staff table
            $tenantColumns = Schema::connection('tenant')->getColumnListing('staff');
            
            // Insert each staff member into tenant database
            $migratedCount = 0;
            
            foreach ($staffMembers as $staff) {
                // Convert object to array and remove ID to let the DB auto-increment
                $staffData = (array) $staff;
                $originalId = $staffData['id'];
                unset($staffData['id']);
                unset($staffData['tenant_id']); // Remove tenant_id as it's not needed in tenant DB
                
                // Filter data to only include columns that exist in tenant table
                $filteredData = [];
                foreach ($staffData as $column => $value) {
                    if (in_array($column, $tenantColumns)) {
                        $filteredData[$column] = $value;
                    } else {
                        $this->comment("Column '$column' does not exist in tenant staff table - skipping");
                    }
                }
                
                // Check if we have required columns
                $requiredColumns = ['name', 'email', 'password', 'role'];
                $missingColumns = array_diff($requiredColumns, array_keys($filteredData));
                if (!empty($missingColumns)) {
                    $this->error("Missing required columns in data: " . implode(', ', $missingColumns));
                    continue;
                }
                
                // Insert into tenant database
                $newId = DB::connection('tenant')->table('staff')->insertGetId($filteredData);
                
                $this->info("Migrated staff ID {$originalId} to new ID {$newId} in tenant database");
                $migratedCount++;
            }
            
            if ($migratedCount === 0) {
                $this->error("No staff records were migrated");
                DB::rollBack();
                return 1;
            }
            
            // Delete records from the main database
            $deletedCount = DB::table('staff')->where('tenant_id', $tenantId)->delete();
            
            $this->info("Deleted {$deletedCount} staff records from the main database");
            
            // Commit transaction
            DB::commit();
            
            $this->info("Successfully migrated {$migratedCount} staff records to {$tenantDbName}");
            
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            
            $this->error("Failed to migrate staff data: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
