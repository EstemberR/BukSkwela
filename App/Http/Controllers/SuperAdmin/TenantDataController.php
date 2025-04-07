<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\TenantDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TenantDataController extends Controller
{
    /**
     * Display a list of tenants
     */
    public function index()
    {
        $tenants = Tenant::with('tenantDatabase')->get();
        return view('super-admin.tenant-data.index', compact('tenants'));
    }
    
    /**
     * View a tenant's data
     */
    public function viewTenantData($tenantId)
    {
        $tenant = Tenant::with('tenantDatabase')->findOrFail($tenantId);
        
        // Get all tables for this tenant from separate database
        if ($tenant->tenantDatabase) {
            $databaseName = $tenant->tenantDatabase->database_name;
            $tables = [];
            
            try {
                // First method: Use SHOW TABLES command
                $results = DB::select("SHOW TABLES FROM `{$databaseName}`");
                
                if (!empty($results)) {
                    foreach ($results as $table) {
                        $values = get_object_vars($table);
                        $tableName = reset($values);
                        $tables[$tableName] = $tableName;
                    }
                    \Log::info("Found " . count($tables) . " tables in {$databaseName} using SHOW TABLES");
                }
                
                // If no tables found, try alternate method
                if (empty($tables)) {
                    $query = "SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema = ? AND table_type = 'BASE TABLE'";
                    $tableResults = DB::select($query, [$databaseName]);
                    
                    foreach ($tableResults as $table) {
                        $tableName = $table->TABLE_NAME;
                        $tables[$tableName] = $tableName;
                    }
                    \Log::info("Found " . count($tables) . " tables in {$databaseName} using information_schema");
                }
                
                return view('super-admin.tenant-data.view', compact('tenant', 'tables', 'databaseName'));
            } catch (\Exception $e) {
                \Log::error("Error accessing tenant database {$databaseName}: " . $e->getMessage());
                return view('super-admin.tenant-data.view', compact('tenant', 'tables'))
                    ->with('error', 'Error accessing tenant database: ' . $e->getMessage());
            }
        } else {
            $tables = [];
            return view('super-admin.tenant-data.view', compact('tenant', 'tables'))
                ->with('error', 'No database configuration found for this tenant');
        }
    }
    
    /**
     * Show table data
     */
    public function viewTableData($tenantId, $table)
    {
        $tenant = Tenant::with('tenantDatabase')->findOrFail($tenantId);
        
        if (!$tenant->tenantDatabase) {
            return redirect()->back()->with('error', 'No database configuration found for this tenant');
        }
        
        $databaseName = $tenant->tenantDatabase->database_name;
        
        try {
            // Check if table exists in tenant database
            $tableExists = DB::select("SELECT TABLE_NAME FROM information_schema.tables 
                WHERE table_schema = ? AND table_name = ?", 
                [$databaseName, $table]);
                
            if (empty($tableExists)) {
                return redirect()->back()->with('error', 'Table not found in tenant database');
            }
            
            // Create a connection to the tenant database
            config([
                'database.connections.tenant_view' => [
                    'driver' => 'mysql',
                    'host' => $tenant->tenantDatabase->database_host,
                    'port' => $tenant->tenantDatabase->database_port,
                    'database' => $databaseName,
                    'username' => $tenant->tenantDatabase->database_username ?: config('database.connections.mysql.username'),
                    'password' => $tenant->tenantDatabase->database_password ?: config('database.connections.mysql.password'),
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'prefix' => '',
                ]
            ]);
            
            // Connect to the tenant database
            DB::purge('tenant_view');
            DB::reconnect('tenant_view');
            
            // Get column information
            $columns = [];
            $columnResults = DB::connection('tenant_view')
                ->select("SHOW COLUMNS FROM `{$table}`");
                
            foreach ($columnResults as $column) {
                $columns[] = $column->Field;
            }
            
            // Get table data with pagination
            $records = DB::connection('tenant_view')
                ->table($table)
                ->paginate(10);
                
            // Disconnect to cleanup
            DB::disconnect('tenant_view');
            
            return view('super-admin.tenant-data.table', compact('tenant', 'table', 'columns', 'records'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error viewing table data: ' . $e->getMessage());
        }
    }
    
    /**
     * Get all tables for a tenant by separate database
     */
    private function getTenantTables(string $prefix): array
    {
        $tables = [];
        // Extract tenant ID from prefix (tenant_ID_)
        $tenantId = trim(str_replace('tenant_', '', $prefix), '_');
        
        $tenant = Tenant::with('tenantDatabase')->findOrFail($tenantId);
        
        if (!$tenant || !$tenant->tenantDatabase) {
            \Log::error("Tenant not found or no database config for tenant: {$tenantId}");
            return $tables;
        }
        
        $databaseName = $tenant->tenantDatabase->database_name;
        \Log::info("Getting tables for tenant database: {$databaseName} using direct query");
        
        try {
            // Use direct query with root credentials instead of a connection
            $results = DB::select("SHOW TABLES FROM `{$databaseName}`");
            
            if (empty($results)) {
                \Log::info("No tables found in database {$databaseName}");
            } else {
                \Log::info("Found " . count($results) . " tables in {$databaseName}");
                
                foreach ($results as $table) {
                    $values = get_object_vars($table);
                    $tableName = reset($values);
                    $tables[$tableName] = $tableName;
                }
            }
        } catch (\Exception $e) {
            \Log::error("Error getting tenant tables from {$databaseName}: " . $e->getMessage());
            
            // If the first method fails, try another approach
            try {
                $query = "SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema = '{$databaseName}' AND table_type = 'BASE TABLE'";
                $tableResults = DB::select($query);
                
                foreach ($tableResults as $table) {
                    $tableName = $table->TABLE_NAME;
                    $tables[$tableName] = $tableName;
                }
                
                \Log::info("Second approach found " . count($tables) . " tables");
            } catch (\Exception $e2) {
                \Log::error("Both methods failed: " . $e2->getMessage());
            }
        }
        
        return $tables;
    }
    
    /**
     * Edit a record in a tenant table
     */
    public function editRecord($tenantId, $table, $id)
    {
        $tenant = Tenant::with('tenantDatabase')->findOrFail($tenantId);
        
        if (!$tenant->tenantDatabase) {
            return redirect()->back()->with('error', 'No database configuration found for this tenant');
        }
        
        $databaseName = $tenant->tenantDatabase->database_name;
        
        try {
            // Create a connection to the tenant database
            config([
                'database.connections.tenant_edit' => [
                    'driver' => 'mysql',
                    'host' => $tenant->tenantDatabase->database_host,
                    'port' => $tenant->tenantDatabase->database_port,
                    'database' => $databaseName,
                    'username' => $tenant->tenantDatabase->database_username ?: config('database.connections.mysql.username'),
                    'password' => $tenant->tenantDatabase->database_password ?: config('database.connections.mysql.password'),
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'prefix' => '',
                ]
            ]);
            
            // Connect to the tenant database
            DB::purge('tenant_edit');
            DB::reconnect('tenant_edit');
            
            // Get the record
            $record = DB::connection('tenant_edit')
                ->table($table)
                ->where('id', $id)
                ->first();
                
            if (!$record) {
                return redirect()->back()->with('error', 'Record not found');
            }
            
            // Get column information
            $columns = [];
            $columnResults = DB::connection('tenant_edit')
                ->select("SHOW COLUMNS FROM `{$table}`");
                
            foreach ($columnResults as $column) {
                $columns[] = $column->Field;
            }
            
            // Disconnect to cleanup
            DB::disconnect('tenant_edit');
            
            return view('super-admin.tenant-data.edit', compact('tenant', 'table', 'record', 'columns'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error editing record: ' . $e->getMessage());
        }
    }
    
    /**
     * Update a record in a tenant table
     */
    public function updateRecord(Request $request, $tenantId, $table, $id)
    {
        $tenant = Tenant::with('tenantDatabase')->findOrFail($tenantId);
        
        if (!$tenant->tenantDatabase) {
            return redirect()->back()->with('error', 'No database configuration found for this tenant');
        }
        
        $databaseName = $tenant->tenantDatabase->database_name;
        
        try {
            // Create a connection to the tenant database
            config([
                'database.connections.tenant_update' => [
                    'driver' => 'mysql',
                    'host' => $tenant->tenantDatabase->database_host,
                    'port' => $tenant->tenantDatabase->database_port,
                    'database' => $databaseName,
                    'username' => $tenant->tenantDatabase->database_username ?: config('database.connections.mysql.username'),
                    'password' => $tenant->tenantDatabase->database_password ?: config('database.connections.mysql.password'),
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'prefix' => '',
                ]
            ]);
            
            // Connect to the tenant database
            DB::purge('tenant_update');
            DB::reconnect('tenant_update');
            
            // Validate and update record
            $data = $request->except(['_token', '_method']);
            
            DB::connection('tenant_update')
                ->table($table)
                ->where('id', $id)
                ->update($data);
                
            // Disconnect to cleanup
            DB::disconnect('tenant_update');
            
            return redirect()
                ->route('super-admin.tenant-data.table', ['tenant' => $tenantId, 'table' => $table])
                ->with('success', 'Record updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating record: ' . $e->getMessage());
        }
    }
    
    /**
     * Run tenant migration to create prefixed tables
     */
    public function runMigration()
    {
        try {
            // Call the artisan command
            \Artisan::call('tenant:migrate-prefixed');
            
            // Get the output
            $output = \Artisan::output();
            
            return redirect()
                ->route('super-admin.tenant-data.index')
                ->with('success', 'Tenant tables created successfully. ' . nl2br($output));
        } catch (\Exception $e) {
            return redirect()
                ->route('super-admin.tenant-data.index')
                ->with('error', 'Error creating tenant tables: ' . $e->getMessage());
        }
    }
    
    /**
     * Run tenant migration in batches to avoid overwhelming the database
     */
    public function runBatchedMigration()
    {
        try {
            // Call the domain-based migration command which avoids tenancy helper issues
            \Artisan::call('tenants:migrate-by-domain', [
                '--batch-size' => 2,
                '--delay' => 5,
                '--skip-domain-check' => true,
                '--create-db' => true
            ]);
            
            // Get the output
            $output = \Artisan::output();
            
            // Log the full output for debugging
            \Log::info("Batched migration output: " . $output);
            
            // Check if there were any successful migrations
            if (strpos($output, 'All tenant migrations completed') !== false) {
                return redirect()
                    ->route('super-admin.tenant-data.index')
                    ->with('success', 'Tenant databases created and migrated. ' . nl2br(htmlspecialchars($output)));
            } else {
                return redirect()
                    ->route('super-admin.tenant-data.index')
                    ->with('error', 'Error migrating tenant databases: ' . nl2br(htmlspecialchars($output)));
            }
        } catch (\Exception $e) {
            \Log::error("Exception in runBatchedMigration: " . $e->getMessage());
            return redirect()
                ->route('super-admin.tenant-data.index')
                ->with('error', 'Error migrating tenant databases: ' . $e->getMessage());
        }
    }
    
    /**
     * Manage a tenant's database
     */
    public function manageTenantDatabase($tenantId)
    {
        $tenant = Tenant::with('tenantDatabase')->findOrFail($tenantId);
        
        // Check if database exists on server
        $databaseExists = false;
        $databaseName = $tenant->tenantDatabase->database_name ?? null;
        
        if ($databaseName) {
            try {
                $databaseExistsCheck = DB::select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$databaseName]);
                $databaseExists = count($databaseExistsCheck) > 0;
            } catch (\Exception $e) {
                \Log::error("Error checking database existence: " . $e->getMessage());
            }
        }
        
        return view('super-admin.tenant-data.manage-database', compact('tenant', 'databaseExists'));
    }
    
    /**
     * Run a database action for a tenant
     */
    public function runDatabaseAction(Request $request, $tenantId)
    {
        $tenant = Tenant::findOrFail($tenantId);
        $action = $request->input('action');
        
        $validActions = ['create', 'migrate', 'fresh', 'seed', 'backup', 'drop'];
        
        if (!in_array($action, $validActions)) {
            return redirect()
                ->route('super-admin.tenant-data.manage-database', $tenant->id)
                ->with('error', 'Invalid database action');
        }
        
        try {
            // Build the artisan command
            $options = [];
            
            switch ($action) {
                case 'create':
                    // Use our new setup command instead
                    \Artisan::call('db:setup-tenant', [
                        'tenant' => $tenant->id
                    ]);
                    break;
                    
                case 'migrate':
                    try {
                        // First try standard migration
                        \Artisan::call('tenant:db', [
                            'tenant' => $tenant->id,
                            '--migrate' => true
                        ]);
                        
                        // Then use direct migration to ensure all tables exist
                        $tenantDb = $tenant->tenantDatabase;
                        if ($tenantDb) {
                            \Artisan::call('tenant:direct-migrate', [
                                'database' => $tenantDb->database_name
                            ]);
                        }
                    } catch (\Exception $e) {
                        \Log::error("Error in migration: " . $e->getMessage());
                    }
                    break;
                    
                case 'fresh':
                    // Confirm before running destructive action
                    if (!$request->has('confirmed')) {
                        return view('super-admin.tenant-data.confirm-action', [
                            'tenant' => $tenant,
                            'action' => $action,
                            'message' => 'This will wipe the database and run fresh migrations. All data will be lost.',
                            'actionText' => 'Run Fresh Migrations'
                        ]);
                    }
                    
                    // Run fresh migrations
                    try {
                        // Standard migration first
                        \Artisan::call('tenant:db', [
                            'tenant' => $tenant->id,
                            '--fresh' => true
                        ]);
                        
                        // Then use direct migration to ensure all tables exist
                        $tenantDb = $tenant->tenantDatabase;
                        if ($tenantDb) {
                            \Artisan::call('tenant:direct-migrate', [
                                'database' => $tenantDb->database_name
                            ]);
                        }
                    } catch (\Exception $e) {
                        \Log::error("Error in fresh migration: " . $e->getMessage());
                    }
                    break;
                    
                case 'seed':
                    \Artisan::call('tenant:seed', [
                        'tenant' => $tenant->id
                    ]);
                    break;
                    
                case 'backup':
                    \Artisan::call('tenant:db', [
                        'tenant' => $tenant->id,
                        '--backup' => true
                    ]);
                    break;
                    
                case 'drop':
                    // Confirm before running destructive action
                    if (!$request->has('confirmed')) {
                        return view('super-admin.tenant-data.confirm-action', [
                            'tenant' => $tenant,
                            'action' => $action,
                            'message' => 'This will completely delete the tenant database. All data will be lost.',
                            'actionText' => 'Drop Database'
                        ]);
                    }
                    
                    \Artisan::call('tenant:db', [
                        'tenant' => $tenant->id,
                        '--drop' => true
                    ]);
                    break;
            }
            
            $output = \Artisan::output();
            
            return redirect()
                ->route('super-admin.tenant-data.manage-database', $tenant->id)
                ->with('success', "Database action '{$action}' completed: " . nl2br(htmlspecialchars($output)));
                
        } catch (\Exception $e) {
            return redirect()
                ->route('super-admin.tenant-data.manage-database', $tenant->id)
                ->with('error', "Error executing database action: " . $e->getMessage());
        }
    }
    
    /**
     * Directly check database tables
     */
    public function checkDatabase($tenantId)
    {
        $tenant = Tenant::with('tenantDatabase')->findOrFail($tenantId);
        
        if (!$tenant->tenantDatabase) {
            return redirect()
                ->route('super-admin.tenant-data.view', $tenant->id)
                ->with('error', 'No database configuration found for this tenant');
        }
        
        $databaseName = $tenant->tenantDatabase->database_name;
        $results = [];
        
        try {
            // Check if database exists
            $dbExists = DB::select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$databaseName]);
            $results['database_exists'] = !empty($dbExists);
            
            if ($results['database_exists']) {
                // Check tables
                $tables = DB::select("SHOW TABLES FROM `{$databaseName}`");
                $tableNames = [];
                
                foreach ($tables as $table) {
                    $values = get_object_vars($table);
                    $tableNames[] = reset($values);
                }
                
                $results['tables'] = $tableNames;
                
                // Check for specific tables
                $expectedTables = [
                    'requirement_categories',
                    'requirements',
                    'staff',
                    'students',
                    'courses',
                    'student_requirements'
                ];
                
                $missingTables = array_diff($expectedTables, $tableNames);
                $results['missing_tables'] = $missingTables;
                
                // Run direct migration if tables are missing
                if (!empty($missingTables)) {
                    \Artisan::call('tenant:direct-migrate', [
                        'database' => $databaseName
                    ]);
                    
                    $results['migration_output'] = \Artisan::output();
                }
            }
        } catch (\Exception $e) {
            return redirect()
                ->route('super-admin.tenant-data.view', $tenant->id)
                ->with('error', 'Error checking database: ' . $e->getMessage());
        }
        
        return view('super-admin.tenant-data.check', compact('tenant', 'results'));
    }
    
    /**
     * Auto setup databases for all tenants
     */
    public function autoSetupDatabases()
    {
        try {
            // Call our custom artisan command to auto-setup tenant databases
            \Artisan::call('tenants:auto-setup', [
                '--new-only' => true,
                '--batch-size' => 3,
                '--delay' => 3
            ]);
            
            // Get the output
            $output = \Artisan::output();
            
            // Log the full output for debugging
            \Log::info("Auto setup output: " . $output);
            
            return redirect()
                ->route('super-admin.tenant-data.index')
                ->with('success', 'Auto setup of tenant databases completed. ' . nl2br(htmlspecialchars($output)));
        } catch (\Exception $e) {
            \Log::error("Exception in autoSetupDatabases: " . $e->getMessage());
            return redirect()
                ->route('super-admin.tenant-data.index')
                ->with('error', 'Error auto-setting up tenant databases: ' . $e->getMessage());
        }
    }
} 