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
        // Only create the table if we're in a tenant context
        if (tenancy()->initialized) {
            Schema::create('students_informations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
                $table->string('first_name')->nullable();
                $table->string('middle_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('suffix')->nullable();
                $table->enum('sex', ['MALE', 'FEMALE'])->nullable();
                $table->date('birth_date')->nullable();
                $table->enum('civil_status', ['SINGLE', 'MARRIED', 'DIVORCED', 'WIDOWED'])->nullable();
                $table->string('religion')->nullable();
                $table->string('blood_type')->nullable();
                $table->string('contact_number')->nullable();
                $table->string('email')->nullable();
                $table->string('facebook')->nullable();
                $table->boolean('has_indigenous')->default(false);
                $table->string('indigenous_group')->nullable();
                $table->string('other_indigenous')->nullable();
                $table->string('dswd_4ps')->nullable();
                $table->string('disability')->nullable();
                // Academic information
                $table->string('educational_status')->nullable();
                $table->string('lrn')->nullable(); // Learner's Reference Number
                $table->string('school_name')->nullable();
                $table->year('year_from')->nullable();
                $table->year('year_to')->nullable();
                $table->string('academic_level')->nullable();
                $table->string('school_type')->nullable();
                $table->string('strand')->nullable();
                // School address fields
                $table->string('region')->nullable();
                $table->string('province')->nullable();
                $table->string('city')->nullable();
                $table->string('barangay')->nullable();
                $table->string('street')->nullable();
                $table->timestamps();
            });
        } else {
            // In central database context, the table will be created 
            // using the dedicated artisan command for each tenant
            // php artisan tenants:create-student-info-table
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Only drop the table if we're in a tenant context
        if (tenancy()->initialized) {
            Schema::dropIfExists('students_informations');
        }
    }
};
