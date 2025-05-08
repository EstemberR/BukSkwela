<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (app()->runningInConsole()) {
            // Only when running from command line
            $this->command->info('Running DatabaseSeeder...');
        }
        
        // Check if this is a tenant database or central
        if (config('database.default') === 'tenant') {
            $this->call(TenantDatabaseSeeder::class);
        } else {
            // Central database seeding here if needed
        }
    }
} 