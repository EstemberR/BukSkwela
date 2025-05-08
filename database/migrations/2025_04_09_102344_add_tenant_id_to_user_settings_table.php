<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_settings', function (Blueprint $table) {
            // Check if tenant_id column exists first
            if (!Schema::hasColumn('user_settings', 'tenant_id')) {
                // Add tenant_id column
                $table->string('tenant_id')->after('user_type')->nullable();
                
                // Add index for better performance
                $table->index('tenant_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_settings', function (Blueprint $table) {
            // Check if tenant_id exists before dropping
            if (Schema::hasColumn('user_settings', 'tenant_id')) {
                // Drop the index first
                $table->dropIndex(['tenant_id']);
                
                // Drop the tenant_id column
                $table->dropColumn('tenant_id');
            }
        });
    }
};
