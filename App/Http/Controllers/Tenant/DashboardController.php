<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Staff\Staff;
use App\Models\Student\Student;
use App\Models\Requirements\Requirement;
use App\Models\Course\Course;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        $tenantId = tenant('id');
        $databaseName = 'tenant_' . $tenantId;
        
        // Set the database connection for the tenant with explicit credentials
        Config::set('database.connections.tenant.database', $databaseName);
        Config::set('database.connections.tenant.username', env('DB_USERNAME'));
        Config::set('database.connections.tenant.password', env('DB_PASSWORD'));
        
        // Log connection attempt
        Log::info("DashboardController: Setting database connection for tenant {$tenantId}");
        
        // Purge and reconnect to the tenant database
        DB::purge('tenant');
        DB::reconnect('tenant');
        
        try {
            // Test the connection
            DB::connection('tenant')->getPdo();
            Log::info("DashboardController: Successfully connected to tenant database");
            
            $data = [
                'instructorCount' => Staff::on('tenant')->where('tenant_id', (string)$tenantId)->count(),
                'studentCount' => Student::on('tenant')->where('tenant_id', (string)$tenantId)->count(),
                'pendingRequirements' => DB::connection('tenant')->table('student_requirements')
                    ->where('tenant_id', (string)$tenantId)
                    ->where('status', 'pending')
                    ->count(),
                'activeCourses' => Course::on('tenant')->where('tenant_id', (string)$tenantId)
                    ->where('status', 'active')
                    ->count(),
                'students' => Student::on('tenant')->where('tenant_id', (string)$tenantId)
                    ->select('id', 'student_id', 'status')
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get(),
                'courses' => Course::on('tenant')->where('tenant_id', (string)$tenantId)
                    ->with('staff')
                    ->where('status', 'active')
                    ->select('id', 'title', 'staff_id', 'status')
                    ->withCount('students')
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get(),
                'instructors' => Staff::on('tenant')->where('tenant_id', (string)$tenantId)
                    ->with(['courses' => function($query) {
                        $query->withCount('students');
                    }])
                    ->withCount(['courses'])
                    ->get()
                    ->map(function($staff) {
                        $staff->students_count = $staff->courses->sum('students_count');
                        return $staff;
                    }),
                'requirementCategories' => \App\Models\Requirements\RequirementCategory::on('tenant')
                    ->with(['requirements' => function($query) {
                        $query->withCount(['students' => function($q) {
                            $q->wherePivot('status', 'pending');
                        }]);
                    }])
                    ->where('tenant_id', (string)$tenantId)
                    ->get(),
                'studentsByStatus' => Student::on('tenant')->where('tenant_id', (string)$tenantId)
                    ->with(['requirements' => function($query) {
                        $query->withPivot(['status', 'file_path', 'remarks']);
                    }])
                    ->get()
                    ->groupBy('status')
            ];
            
            return view('tenant.dashboard', $data);
        } catch (\Exception $e) {
            Log::error("DashboardController: Database error: " . $e->getMessage());
            return view('tenant.dashboard-error', ['error' => $e->getMessage()]);
        }
    }

    public function staffDashboard()
    {
        return view('tenant.staff.dashboard');
    }
}