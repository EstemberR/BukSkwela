<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update subscription_upgrades table to accept 'ultimate' as a valid to_plan value
        if (Schema::hasTable('subscription_upgrades')) {
            // Modify to_plan column to accept 'ultimate'
            try {
                // For MySQL/MariaDB:
                DB::statement("ALTER TABLE subscription_upgrades MODIFY to_plan VARCHAR(20) DEFAULT 'premium'");
            } catch (\Exception $e) {
                // Log error but continue with other migrations
                DB::statement("ALTER TABLE subscription_upgrades MODIFY to_plan VARCHAR(191) DEFAULT 'premium'");
            }
        }
        
        // Update subscription_requests table to accept 'ultimate' as a valid requested_plan value
        if (Schema::hasTable('subscription_requests')) {
            try {
                // Modify requested_plan column
                // For MySQL/MariaDB:
                DB::statement("ALTER TABLE subscription_requests MODIFY requested_plan VARCHAR(20)");
            } catch (\Exception $e) {
                // Log error but continue with other migrations
                DB::statement("ALTER TABLE subscription_requests MODIFY requested_plan VARCHAR(191)");
            }
        }
        
        // Update validation data in existing controllers
        // Note: This won't be done in the migration, but through controller updates
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: No action required for down migration
        // as we're only expanding existing fields
    }
};
