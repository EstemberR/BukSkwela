<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

class MoveDataToTenants extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:move-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move data from central database to tenant databases';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting data migration to tenant databases...');

        // Get all tenants
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            $this->info("Processing tenant: {$tenant->id}");

            // Connect to tenant database
            $tenant->run(function () use ($tenant) {
                // Move departments
                $this->moveDepartments($tenant->id);

                // Move staff
                $this->moveStaff($tenant->id);

                // Move courses
                $this->moveCourses($tenant->id);

                // Move requirement categories
                $this->moveRequirementCategories($tenant->id);

                // Move requirements
                $this->moveRequirements($tenant->id);

                // Move students
                $this->moveStudents($tenant->id);

                // Move student requirements
                $this->moveStudentRequirements($tenant->id);
            });
        }

        $this->info('Data migration completed successfully!');
    }

    private function moveDepartments($tenantId)
    {
        $departments = DB::connection('mysql')
            ->table('departments')
            ->where('tenant_id', $tenantId)
            ->get();

        foreach ($departments as $department) {
            $data = (array) $department;
            unset($data['id']); // Remove ID to avoid conflicts
            DB::table('departments')->insert($data);
        }
    }

    private function moveStaff($tenantId)
    {
        $staff = DB::connection('mysql')
            ->table('staff')
            ->where('tenant_id', $tenantId)
            ->get();

        foreach ($staff as $member) {
            $data = (array) $member;
            unset($data['id']); // Remove ID to avoid conflicts
            DB::table('staff')->insert($data);
        }
    }

    private function moveCourses($tenantId)
    {
        $courses = DB::connection('mysql')
            ->table('courses')
            ->where('tenant_id', $tenantId)
            ->get();

        foreach ($courses as $course) {
            $data = (array) $course;
            unset($data['id']); // Remove ID to avoid conflicts
            DB::table('courses')->insert($data);
        }
    }

    private function moveRequirementCategories($tenantId)
    {
        $categories = DB::connection('mysql')
            ->table('requirement_categories')
            ->where('tenant_id', $tenantId)
            ->get();

        foreach ($categories as $category) {
            $data = (array) $category;
            unset($data['id']); // Remove ID to avoid conflicts
            DB::table('requirement_categories')->insert($data);
        }
    }

    private function moveRequirements($tenantId)
    {
        $requirements = DB::connection('mysql')
            ->table('requirements')
            ->where('tenant_id', $tenantId)
            ->get();

        foreach ($requirements as $requirement) {
            $data = (array) $requirement;
            unset($data['id']); // Remove ID to avoid conflicts
            DB::table('requirements')->insert($data);
        }
    }

    private function moveStudents($tenantId)
    {
        $students = DB::connection('mysql')
            ->table('students')
            ->where('tenant_id', $tenantId)
            ->get();

        foreach ($students as $student) {
            $data = (array) $student;
            unset($data['id']); // Remove ID to avoid conflicts
            DB::table('students')->insert($data);
        }
    }

    private function moveStudentRequirements($tenantId)
    {
        $studentRequirements = DB::connection('mysql')
            ->table('student_requirements')
            ->where('tenant_id', $tenantId)
            ->get();

        foreach ($studentRequirements as $requirement) {
            $data = (array) $requirement;
            unset($data['id']); // Remove ID to avoid conflicts
            DB::table('student_requirements')->insert($data);
        }
    }
}
