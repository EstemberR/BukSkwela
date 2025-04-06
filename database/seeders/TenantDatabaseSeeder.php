<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TenantDatabaseSeeder extends Seeder
{
    /**
     * Seed the tenant's database.
     */
    public function run(): void
    {
        // Create default requirement categories
        \DB::table('requirement_categories')->insert([
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

        // Create default requirements
        $academicCategoryId = \DB::table('requirement_categories')->where('name', 'Academic Documents')->first()->id;
        $idCategoryId = \DB::table('requirement_categories')->where('name', 'Identification Documents')->first()->id;
        
        \DB::table('requirements')->insert([
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
    }
} 