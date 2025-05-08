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
        // First, update existing values
        DB::connection('tenant')->table('students')
            ->where('status', 'active')
            ->update(['status' => 'regular']);
            
        DB::connection('tenant')->table('students')
            ->where('status', 'inactive')
            ->update(['status' => 'probation']);
        
        // Then modify the enum
        DB::connection('tenant')->statement("ALTER TABLE students MODIFY COLUMN status ENUM('regular', 'probation', 'irregular') DEFAULT 'regular'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First, update existing values back
        DB::connection('tenant')->table('students')
            ->where('status', 'regular')
            ->update(['status' => 'active']);
            
        DB::connection('tenant')->table('students')
            ->where('status', 'probation')
            ->update(['status' => 'inactive']);
            
        DB::connection('tenant')->table('students')
            ->where('status', 'irregular')
            ->update(['status' => 'inactive']);
        
        // Then revert the enum
        DB::connection('tenant')->statement("ALTER TABLE students MODIFY COLUMN status ENUM('active', 'inactive') DEFAULT 'active'");
    }
};
