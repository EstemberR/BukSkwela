<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class TenantDatabaseSeeder extends Seeder
{
    /**
     * Seed the tenant's database.
     */
    public function run(): void
    {
        if (app()->runningInConsole()) {
            $this->command->info('Seeding tenant database...');
        }
        
        try {
            // Only create categories if the table exists
            if (Schema::hasTable('requirement_categories')) {
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
                
                if (app()->runningInConsole()) {
                    $this->command->info('Created requirement categories');
                }
                
                // Only create requirements if both tables exist
                if (Schema::hasTable('requirements')) {
                    try {
                        $academicCategory = DB::table('requirement_categories')->where('name', 'Academic Documents')->first();
                        $idCategory = DB::table('requirement_categories')->where('name', 'Identification Documents')->first();
                        
                        if ($academicCategory && $idCategory) {
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
                            
                            if (app()->runningInConsole()) {
                                $this->command->info('Created requirements');
                            }
                        } else {
                            if (app()->runningInConsole()) {
                                $this->command->warn('Cannot find required categories - skipping requirements creation');
                            }
                            Log::warning('Cannot find required categories for requirements - skipping creation');
                        }
                    } catch (\Exception $ex) {
                        $message = "Error creating requirements: " . $ex->getMessage();
                        Log::error($message);
                        
                        if (app()->runningInConsole()) {
                            $this->command->error($message);
                        }
                    }
                } else {
                    if (app()->runningInConsole()) {
                        $this->command->warn('Skipping requirements - table not found');
                    }
                }
            } else {
                if (app()->runningInConsole()) {
                    $this->command->warn('Skipping requirement_categories - table not found');
                }
            }
            
            if (app()->runningInConsole()) {
                $this->command->info('Tenant database seeding completed successfully');
            }
        } catch (\Exception $e) {
            $message = "Error seeding tenant database: " . $e->getMessage();
            Log::error($message);
            
            if (app()->runningInConsole()) {
                $this->command->error($message);
            }
        }
    }
} 