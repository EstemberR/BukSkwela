<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SeedTenantDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:seed-database {tenant}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed a tenant database with initial data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenant = $this->argument('tenant');
        $databaseName = 'tenant_' . $tenant;
        
        $this->info("Seeding database: {$databaseName}");
        
        // Set the database connection for the tenant
        Config::set('database.connections.tenant.database', $databaseName);
        Config::set('database.connections.tenant.username', env('DB_USERNAME'));
        Config::set('database.connections.tenant.password', env('DB_PASSWORD'));
        
        // Purge and reconnect to the tenant database
        DB::purge('tenant');
        DB::reconnect('tenant');
        
        try {
            // Test the connection
            DB::connection('tenant')->getPdo();
            $this->info("Successfully connected to tenant database");
            
            // Check if we already have data
            $staffCount = DB::connection('tenant')->table('staff')->count();
            
            if ($staffCount > 0) {
                if (!$this->confirm("Database already contains {$staffCount} staff members. Do you want to add more test data?")) {
                    $this->info("Operation cancelled by user.");
                    return 0;
                }
            }
            
            // Create a sample department
            $departmentId = DB::connection('tenant')->table('departments')->insertGetId([
                'name' => 'Faculty',
                'code' => 'FAC',
                'description' => 'Main department for faculty members',
                'tenant_id' => $tenant,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            $this->info("Department created with ID: {$departmentId}");
            
            // Create a sample admin staff member
            $staffId = Str::random(8);
            $adminId = DB::connection('tenant')->table('staff')->insertGetId([
                'name' => 'Admin User',
                'staff_id' => $staffId,
                'email' => 'admin@'.$tenant.'.edu',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'department_id' => $departmentId,
                'status' => 'active',
                'tenant_id' => $tenant,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            $this->info("Admin staff created with ID: {$adminId}");
            
            // Create a few sample courses
            $courseIds = [];
            
            $courses = [
                [
                    'name' => 'Introduction to '.$tenant,
                    'code' => strtoupper($tenant).'-101',
                    'description' => 'An introductory course to '.$tenant,
                    'status' => 'active',
                    'tenant_id' => $tenant,
                ],
                [
                    'name' => 'Advanced '.$tenant,
                    'code' => strtoupper($tenant).'-201',
                    'description' => 'An advanced course in '.$tenant,
                    'status' => 'active',
                    'tenant_id' => $tenant,
                ]
            ];
            
            foreach ($courses as $course) {
                $course['created_at'] = now();
                $course['updated_at'] = now();
                $id = DB::connection('tenant')->table('courses')->insertGetId($course);
                $courseIds[] = $id;
                $this->info("Course created: {$course['name']} (ID: {$id})");
            }
            
            $this->info("Database seeded successfully!");
            return 0;
        } catch (\Exception $e) {
            $this->error("Error seeding database: " . $e->getMessage());
            return 1;
        }
    }
} 