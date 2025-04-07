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
        Schema::table('staff', function (Blueprint $table) {
            // Add staff_id column if it doesn't exist
            if (!Schema::hasColumn('staff', 'staff_id')) {
                $table->string('staff_id')->after('id')->unique();
            }
            
            // Add role column if it doesn't exist
            if (!Schema::hasColumn('staff', 'role')) {
                $table->enum('role', ['instructor', 'admin', 'staff'])->after('email')->default('instructor');
            }
            
            // Add department_id column if it doesn't exist
            if (!Schema::hasColumn('staff', 'department_id')) {
                $table->foreignId('department_id')->nullable()->after('role');
            }
            
            // Add status column if it doesn't exist
            if (!Schema::hasColumn('staff', 'status')) {
                $table->enum('status', ['active', 'inactive'])->default('active')->after('department_id');
            }
            
            // Add remember_token column if it doesn't exist
            if (!Schema::hasColumn('staff', 'remember_token')) {
                $table->rememberToken()->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropColumn([
                'staff_id',
                'role',
                'department_id',
                'status',
                'remember_token'
            ]);
        });
    }
};
