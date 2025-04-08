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
use Illuminate\Support\Facades\Schema;

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
            
            // Initialize empty data array
            $data = [
                'instructorCount' => 0,
                'studentCount' => 0,
                'pendingRequirements' => 0,
                'activeCourses' => 0,
                'students' => collect([]),
                'courses' => collect([]),
                'instructors' => collect([]),
                'requirementCategories' => collect([]),
                'studentsByStatus' => collect([])
            ];
            
            // Get instructor count
            try {
                $data['instructorCount'] = Staff::on('tenant')->count();
                Log::info("Fetched instructor count: {$data['instructorCount']}");
            } catch (\Exception $e) {
                Log::error("Failed to get instructor count: " . $e->getMessage());
            }
            
            // Get student count
            try {
                $data['studentCount'] = Student::on('tenant')->count();
                Log::info("Fetched student count: {$data['studentCount']}");
            } catch (\Exception $e) {
                Log::error("Failed to get student count: " . $e->getMessage());
            }
            
            // Get pending requirements count
            try {
                if (Schema::connection('tenant')->hasTable('student_requirements')) {
                    $data['pendingRequirements'] = DB::connection('tenant')
                        ->table('student_requirements')
                        ->where('status', 'pending')
                        ->count();
                    Log::info("Fetched pending requirements: {$data['pendingRequirements']}");
                }
            } catch (\Exception $e) {
                Log::error("Failed to get pending requirements: " . $e->getMessage());
            }
            
            // Get active courses
            try {
                $data['activeCourses'] = Course::on('tenant')
                    ->where('status', 'active')
                    ->count();
                Log::info("Fetched active courses: {$data['activeCourses']}");
            } catch (\Exception $e) {
                Log::error("Failed to get active courses: " . $e->getMessage());
            }
            
            // Get recent students
            try {
                $data['students'] = Student::on('tenant')
                    ->select('id', 'student_id', 'status')
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();
                Log::info("Fetched recent students: {$data['students']->count()}");
            } catch (\Exception $e) {
                Log::error("Failed to get recent students: " . $e->getMessage());
            }
            
            // Get courses without potentially problematic relationships or counts
            try {
                $data['courses'] = Course::on('tenant')
                    ->where('status', 'active')
                    ->select('id', 'name', 'status')
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();
                Log::info("Fetched courses: {$data['courses']->count()}");
            } catch (\Exception $e) {
                Log::error("Failed to get courses: " . $e->getMessage());
            }
            
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