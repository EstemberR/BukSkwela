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
        try {
            // Only create if it doesn't exist
            if (!Schema::connection('tenant')->hasTable('departments')) {
                Schema::connection('tenant')->create('departments', function (Blueprint $table) {
                    $table->id();
                    $table->string('name');
                    $table->string('code');
                    $table->text('description')->nullable();
                    $table->enum('status', ['active', 'inactive'])->default('active');
                    $table->string('tenant_id');
                    $table->timestamps();
                });
                
                // Create a default department
                DB::connection('tenant')->table('departments')->insert([
                    'name' => 'General',
                    'code' => 'GEN',
                    'description' => 'General department',
                    'tenant_id' => tenant('id'),
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        } catch (\Exception $e) {
            // Log error but don't fail migration
            \Illuminate\Support\Facades\Log::error('Failed to create departments table: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            Schema::connection('tenant')->dropIfExists('departments');
        } catch (\Exception $e) {
            // Log error but don't fail migration rollback
            \Illuminate\Support\Facades\Log::error('Failed to drop departments table: ' . $e->getMessage());
        }
    }
};
