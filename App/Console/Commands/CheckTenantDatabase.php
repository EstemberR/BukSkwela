<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class CheckTenantDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:check-database {tenant}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check tenant database structure and tables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenant = $this->argument('tenant');
        $databaseName = 'tenant_' . strtolower($tenant);
        
        $this->info("Checking database: {$databaseName}");
        
        // Set the database connection for the tenant
        Config::set('database.connections.tenant.database', $databaseName);
        
        // Get current database credentials
        $this->info("Current tenant database configuration:");
        $this->info("- Database: " . config('database.connections.tenant.database'));
        $this->info("- Host: " . config('database.connections.tenant.host'));
        $this->info("- Port: " . config('database.connections.tenant.port'));
        $this->info("- Username: " . config('database.connections.tenant.username'));
        
        // Purge and reconnect to the tenant database
        DB::purge('tenant');
        DB::reconnect('tenant');
        
        // Verify the connection works
        try {
            DB::connection('tenant')->select('SELECT 1 as test');
            $this->info("✅ Connection to database is working!");
        } catch (\Exception $e) {
            $this->error("❌ Failed to connect to database: " . $e->getMessage());
            return 1;
        }
        
        // Check which tables exist
        try {
            $tables = DB::connection('tenant')->select('SHOW TABLES');
            
            $this->info("\nTables in {$databaseName}:");
            foreach ($tables as $table) {
                $tableName = reset($table);
                $this->info("- {$tableName}");
            }
            
            // Check if student_applications table exists
            $studentApplicationsExists = false;
            foreach ($tables as $table) {
                $tableName = reset($table);
                if ($tableName === 'student_applications') {
                    $studentApplicationsExists = true;
                    break;
                }
            }
            
            if ($studentApplicationsExists) {
                $this->info("\n✅ student_applications table exists!");
                
                // Get the structure of the student_applications table
                $columns = DB::connection('tenant')->select('SHOW COLUMNS FROM student_applications');
                
                $this->info("\nStructure of student_applications table:");
                foreach ($columns as $column) {
                    $this->info("- {$column->Field}: {$column->Type} " . 
                        ($column->Null === 'NO' ? 'NOT NULL' : 'NULL') . 
                        ($column->Default ? " DEFAULT '{$column->Default}'" : '') .
                        ($column->Key ? " {$column->Key}" : ''));
                }
                
                // Check if any applications exist
                $applications = DB::connection('tenant')->table('student_applications')->count();
                $this->info("\nFound {$applications} application(s) in the database");
                
                if ($applications > 0) {
                    $latestApplications = DB::connection('tenant')->table('student_applications')
                        ->orderBy('id', 'desc')
                        ->limit(5)
                        ->get();
                    
                    $this->info("\nLatest applications:");
                    foreach ($latestApplications as $app) {
                        $this->info("- ID: {$app->id}, Student: {$app->student_id}, Status: {$app->status}, Created: {$app->created_at}");
                    }
                }
            } else {
                $this->error("\n❌ student_applications table does not exist!");
                
                // Offer to create the table
                if ($this->confirm('Do you want to create the student_applications table?')) {
                    $this->call('tenant:migrate-student-applications', ['tenant' => $tenant]);
                }
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Failed to check tables: " . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
} 