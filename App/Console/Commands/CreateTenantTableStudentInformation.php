<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\Tenant;

class CreateTenantTableStudentInformation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:create-student-info-table {tenant_id? : The ID of a specific tenant to create the table for}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the students_informations table in all tenant databases or a specific tenant database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Directly run for the john tenant
        try {
            $tenant = Tenant::find('john');
            
            if ($tenant) {
                $this->info("Creating students_informations table for tenant: {$tenant->id}");
                $this->createTableForTenant($tenant);
                $this->info("Students information table created successfully for tenant: {$tenant->id}");
            } else {
                $this->error("Tenant 'john' not found!");
            }
            
            return 0;
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Create the students_informations table for a specific tenant.
     *
     * @param Tenant $tenant
     * @return void
     */
    private function createTableForTenant($tenant)
    {
        // Initialize tenancy for this tenant to switch to its database
        tenancy()->initialize($tenant);
        
        // Check if table already exists
        if (Schema::hasTable('students_informations')) {
            $this->line("Table 'students_informations' already exists for tenant: {$tenant->id}");
            return;
        }
        
        // Create the table
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
        
        // End tenancy to switch back to central database
        tenancy()->end();
    }
}
