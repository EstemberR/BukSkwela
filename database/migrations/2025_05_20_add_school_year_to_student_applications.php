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
        Schema::connection('tenant')->table('student_applications', function (Blueprint $table) {
            if (!Schema::connection('tenant')->hasColumn('student_applications', 'school_year_start')) {
                $table->year('school_year_start')->nullable()->after('tenant_id');
            }
            
            if (!Schema::connection('tenant')->hasColumn('student_applications', 'school_year_end')) {
                $table->year('school_year_end')->nullable()->after('school_year_start');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('tenant')->table('student_applications', function (Blueprint $table) {
            $table->dropColumn(['school_year_start', 'school_year_end']);
        });
    }
}; 