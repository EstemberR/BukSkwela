<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SeedTenantDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:seed {tenant : The tenant ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed a tenant database directly';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenantId = $this->argument('tenant');
        
        $tenant = Tenant::find($tenantId);
        if (!$tenant) {
            $this->error("Tenant {$tenantId} not found");
            return Command::FAILURE;
        }
        
        $this->info("Seeding database for tenant: {$tenant->id}");
        
        try {
            // Initialize tenancy for this tenant
            tenancy()->initialize($tenant);
            
            // Manual seeding
            $this->seedRequirementCategories();
            $this->seedRequirements();
            
            // End tenancy
            tenancy()->end();
            
            $this->info("Database seeding completed for tenant: {$tenant->id}");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error seeding tenant database: " . $e->getMessage());
            Log::error("Error seeding tenant database {$tenant->id}: " . $e->getMessage());
            
            // End tenancy if it was initialized
            try {
                tenancy()->end();
            } catch (\Exception $e) {
                // Ignore errors when ending tenancy
            }
            
            return Command::FAILURE;
        }
    }
    
    /**
     * Seed the requirements categories table
     */
    private function seedRequirementCategories()
    {
        if (!Schema::hasTable('requirement_categories')) {
            $this->warn('requirement_categories table not found - skipping');
            return;
        }
        
        $this->info('Seeding requirement categories...');
        
        DB::table('requirement_categories')->insert([
            [
                'name' => 'Academic Documents',
                'description' => 'Documents related to academic records and achievements',
                'is_required' => true,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Identification Documents',
                'description' => 'Official identification documents',
                'is_required' => true,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Financial Records',
                'description' => 'Documents related to financial aid and payments',
                'is_required' => false,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        
        $this->info('Requirement categories seeded successfully');
    }
    
    /**
     * Seed the requirements table
     */
    private function seedRequirements()
    {
        if (!Schema::hasTable('requirements')) {
            $this->warn('requirements table not found - skipping');
            return;
        }
        
        if (!Schema::hasTable('requirement_categories')) {
            $this->warn('requirement_categories table not found - cannot seed requirements');
            return;
        }
        
        $this->info('Seeding requirements...');
        
        try {
            $academicCategory = DB::table('requirement_categories')->where('name', 'Academic Documents')->first();
            $idCategory = DB::table('requirement_categories')->where('name', 'Identification Documents')->first();
            
            if (!$academicCategory || !$idCategory) {
                $this->warn('Required categories not found - skipping requirements');
                return;
            }
            
            $academicCategoryId = $academicCategory->id;
            $idCategoryId = $idCategory->id;
            
            DB::table('requirements')->insert([
                [
                    'category_id' => $academicCategoryId,
                    'name' => 'Transcript of Records',
                    'description' => 'Official transcript of academic records',
                    'is_required' => true,
                    'file_type' => 'pdf',
                    'max_file_size' => 5000, // 5MB
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'category_id' => $academicCategoryId,
                    'name' => 'High School Diploma',
                    'description' => 'Copy of high school diploma or certificate',
                    'is_required' => true,
                    'file_type' => 'pdf,jpg,png',
                    'max_file_size' => 2000, // 2MB
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'category_id' => $idCategoryId,
                    'name' => 'Valid ID',
                    'description' => 'Any government-issued ID',
                    'is_required' => true,
                    'file_type' => 'pdf,jpg,png',
                    'max_file_size' => 1000, // 1MB
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'category_id' => $idCategoryId,
                    'name' => 'Recent Photo',
                    'description' => '2x2 ID picture with white background',
                    'is_required' => true,
                    'file_type' => 'jpg,png',
                    'max_file_size' => 500, // 500KB
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
            
            $this->info('Requirements seeded successfully');
        } catch (\Exception $e) {
            $this->error('Error seeding requirements: ' . $e->getMessage());
            Log::error('Error seeding requirements: ' . $e->getMessage());
        }
    }
} 