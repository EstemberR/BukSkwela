<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Requirements\RequirementCategory;

class RequirementCategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Regular',
                'description' => 'Requirements for Regular students',
                'tenant_id' => tenant('id')
            ],
            [
                'name' => 'Irregular',
                'description' => 'Requirements for Irregular students',
                'tenant_id' => tenant('id')
            ],
            [
                'name' => 'Probation',
                'description' => 'Requirements for students under Probation',
                'tenant_id' => tenant('id')
            ]
        ];

        foreach ($categories as $category) {
            RequirementCategory::firstOrCreate(
                ['name' => $category['name'], 'tenant_id' => $category['tenant_id']],
                $category
            );
        }
    }
} 