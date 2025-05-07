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
            $table->string('school_year')->nullable()->after('year_level')->comment('School year format: YYYY-YYYY');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('tenant')->table('student_applications', function (Blueprint $table) {
            $table->dropColumn('school_year');
        });
    }
}; 