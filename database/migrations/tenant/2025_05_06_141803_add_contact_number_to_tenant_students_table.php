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
        Schema::connection('tenant')->table('students', function (Blueprint $table) {
            // Check if the column exists first
            if (!Schema::connection('tenant')->hasColumn('students', 'contact_number')) {
                $table->string('contact_number')->nullable()->after('email');
            }
            
            // Also, check if we have the phone column and need to migrate data
            if (Schema::connection('tenant')->hasColumn('students', 'phone') && 
                Schema::connection('tenant')->hasColumn('students', 'contact_number')) {
                // Migrate existing phone data to contact_number
                DB::connection('tenant')
                    ->statement('UPDATE students SET contact_number = phone WHERE contact_number IS NULL AND phone IS NOT NULL');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('tenant')->table('students', function (Blueprint $table) {
            if (Schema::connection('tenant')->hasColumn('students', 'contact_number')) {
                $table->dropColumn('contact_number');
            }
        });
    }
};
