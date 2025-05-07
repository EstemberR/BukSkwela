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
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'student_id')) {
                $table->string('student_id')->unique();
            }
            if (!Schema::hasColumn('students', 'name')) {
                $table->string('name');
            }
            if (!Schema::hasColumn('students', 'email')) {
                $table->string('email')->unique();
            }
            if (!Schema::hasColumn('students', 'password')) {
                $table->string('password');
            }
            if (!Schema::hasColumn('students', 'course_id')) {
                $table->foreignId('course_id')->constrained('courses');
            }
            if (!Schema::hasColumn('students', 'status')) {
                $table->enum('status', ['regular', 'probation', 'irregular'])->default('regular');
            }
            if (!Schema::hasColumn('students', 'remember_token')) {
                $table->rememberToken();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't want to drop these columns in the down migration
        // as they are essential for the application
    }
};
