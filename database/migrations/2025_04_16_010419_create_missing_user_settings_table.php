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
        if (!Schema::hasTable('user_settings')) {
            Schema::create('user_settings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('user_type'); // For polymorphic relationships (User, TenantAdmin, Staff, etc.)
                $table->boolean('dark_mode')->default(false);
                $table->string('card_style')->default('square'); // square, rounded, glass
                $table->string('font_family')->default('Work Sans, sans-serif');
                $table->string('font_size')->default('14px');
                $table->json('settings_json')->nullable(); // For additional settings
                $table->unsignedBigInteger('tenant_id')->nullable(); // Added tenant_id
                $table->timestamps();
                
                // Create a composite index on user_id and user_type for the polymorphic relationship
                $table->index(['user_id', 'user_type']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't drop the table here to prevent accidental data loss
    }
};
