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
        Schema::connection('tenant')->create('student_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students');
            $table->foreignId('program_id')->constrained('courses');
            $table->integer('year_level')->comment('Year level the student is applying for (1-4)');
            $table->string('student_status')->default('Regular')->comment('Student status: Regular, Probation, Irregular');
            $table->text('notes')->nullable()->comment('Additional notes from the student');
            $table->string('status')->default('pending')->comment('Application status: pending, reviewing, approved, rejected');
            $table->text('admin_notes')->nullable()->comment('Notes from the admin reviewing the application');
            $table->foreignId('reviewed_by')->nullable()->comment('Admin user ID who reviewed the application');
            $table->timestamp('reviewed_at')->nullable()->comment('When the application was reviewed');
            $table->json('document_files')->nullable()->comment('JSON data of uploaded document files');
            $table->string('tenant_id');
            $table->timestamps();
            
            // Index for faster queries
            $table->index('status');
            $table->index('tenant_id');
            $table->index(['student_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('tenant')->dropIfExists('student_applications');
    }
}; 