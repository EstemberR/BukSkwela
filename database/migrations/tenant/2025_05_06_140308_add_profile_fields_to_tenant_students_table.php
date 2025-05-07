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
            // Helper function to check if column exists
            $hasColumn = function ($column) {
                return Schema::connection('tenant')->hasColumn('students', $column);
            };
            
            // Personal information fields
            if (!$hasColumn('first_name')) {
                $table->string('first_name')->nullable()->after('name');
            }
            if (!$hasColumn('middle_name')) {
                $table->string('middle_name')->nullable()->after('first_name');
            }
            if (!$hasColumn('last_name')) {
                $table->string('last_name')->nullable()->after('middle_name');
            }
            if (!$hasColumn('suffix_name')) {
                $table->string('suffix_name')->nullable()->after('last_name');
            }
            if (!$hasColumn('sex')) {
                $table->enum('sex', ['MALE', 'FEMALE'])->nullable()->after('suffix_name');
            }
            if (!$hasColumn('birth_date')) {
                $table->date('birth_date')->nullable()->after('sex');
            }
            if (!$hasColumn('civil_status')) {
                $table->string('civil_status')->nullable()->after('birth_date');
            }
            if (!$hasColumn('religion')) {
                $table->string('religion')->nullable()->after('civil_status');
            }
            if (!$hasColumn('blood_type')) {
                $table->string('blood_type', 10)->nullable()->after('religion');
            }
            if (!$hasColumn('facebook_username')) {
                $table->string('facebook_username')->nullable()->after('email');
            }
            if (!$hasColumn('has_indigenous_group')) {
                $table->boolean('has_indigenous_group')->default(false)->after('facebook_username');
            }
            if (!$hasColumn('indigenous_group')) {
                $table->string('indigenous_group')->nullable()->after('has_indigenous_group');
            }
            if (!$hasColumn('other_indigenous_group')) {
                $table->string('other_indigenous_group')->nullable()->after('indigenous_group');
            }
            if (!$hasColumn('dswd_number')) {
                $table->string('dswd_number')->nullable()->after('other_indigenous_group');
            }
            if (!$hasColumn('disability')) {
                $table->string('disability')->nullable()->after('dswd_number');
            }
            
            // Academic information fields
            if (!$hasColumn('educational_status')) {
                $table->string('educational_status')->nullable();
            }
            if (!$hasColumn('lrn')) {
                $table->string('lrn')->nullable();
            }
            if (!$hasColumn('school_name')) {
                $table->string('school_name')->nullable();
            }
            if (!$hasColumn('year_from')) {
                $table->string('year_from', 10)->nullable();
            }
            if (!$hasColumn('year_to')) {
                $table->string('year_to', 10)->nullable();
            }
            if (!$hasColumn('education_level')) {
                $table->string('education_level')->nullable();
            }
            if (!$hasColumn('school_type')) {
                $table->string('school_type')->nullable();
            }
            if (!$hasColumn('strand')) {
                $table->string('strand')->nullable();
            }
            if (!$hasColumn('is_philippines')) {
                $table->boolean('is_philippines')->default(true);
            }
            if (!$hasColumn('region')) {
                $table->string('region')->nullable();
            }
            if (!$hasColumn('province')) {
                $table->string('province')->nullable();
            }
            if (!$hasColumn('city')) {
                $table->string('city')->nullable();
            }
            if (!$hasColumn('barangay')) {
                $table->string('barangay')->nullable();
            }
            if (!$hasColumn('street')) {
                $table->string('street')->nullable();
            }
            if (!$hasColumn('year_level')) {
                $table->string('year_level')->nullable();
            }
            if (!$hasColumn('school_year')) {
                $table->string('school_year')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We won't drop columns in the down method to prevent data loss
        // If you need to roll back these changes, manually specify which columns to drop
    }
};
