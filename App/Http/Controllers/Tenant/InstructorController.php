<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course\Course;
use App\Models\StudentApplication;
use App\Models\Student\Student;
use App\Models\Staff;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;

class InstructorController extends Controller
{
    /**
     * Constructor to share pending application count with all views
     */
    public function __construct()
    {
        // Share pending applications count with all views
        $this->middleware(function ($request, $next) {
            $pendingCount = $this->getPendingApplicationsCount();
            View::share('pendingApplicationsCount', $pendingCount);
            return $next($request);
        });
    }
    
    /**
     * Get the count of pending enrollment applications
     */
    protected function getPendingApplicationsCount()
    {
        try {
            if (\Illuminate\Support\Facades\Schema::connection('tenant')->hasTable('student_applications')) {
                $query = StudentApplication::where('status', 'pending');
                
                // Only add tenant_id constraint if the column exists
                if (\Illuminate\Support\Facades\Schema::connection('tenant')->hasColumn('student_applications', 'tenant_id')) {
                    $query->where('tenant_id', tenant('id'));
                }
                
                return $query->count();
            }
            return 0;
        } catch (\Exception $e) {
            Log::error('Error getting pending applications count: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get programs for the authenticated instructor
     */
    public function getPrograms()
    {
        try {
            // Get authenticated instructor
            $instructor = Auth::guard('staff')->user();
            
            // Get programs assigned to the instructor
            // You may need to adjust this based on your database structure
            $programs = Course::all();
            
            return response()->json([
                'success' => true,
                'programs' => $programs
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting instructor programs: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load programs'
            ]);
        }
    }
    
    /**
     * Get enrollment applications with filters
     */
    public function getApplications(Request $request)
    {
        try {
            // Get query parameters
            $status = $request->input('status', 'pending');
            $programId = $request->input('program', 'all');
            $yearLevel = $request->input('year', 'all');
            $page = $request->input('page', 1);
            $perPage = 10;
            
            // Build query
            $query = StudentApplication::with(['student', 'program'])
                ->orderBy('created_at', 'desc');
            
            // Apply filters
            if ($status !== 'all') {
                $query->where('status', $status);
            }
            
            if ($programId !== 'all') {
                $query->where('program_id', $programId);
            }
            
            if ($yearLevel !== 'all') {
                $query->where('year_level', $yearLevel);
            }
            
            // Safety check for tenant_id
            if (\Illuminate\Support\Facades\Schema::connection('tenant')->hasColumn('student_applications', 'tenant_id')) {
                $query->where('tenant_id', tenant('id'));
            }
            
            // Paginate the results
            $applications = $query->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'applications' => $applications
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting enrollment applications: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to load applications: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get a specific enrollment application
     */
    public function getApplication($id)
    {
        try {
            // First check if the table exists
            if (!\Illuminate\Support\Facades\Schema::connection('tenant')->hasTable('student_applications')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student applications functionality is not available. Please contact the administrator.'
                ]);
            }
            
            // Get the application with related data
            $application = StudentApplication::findOrFail($id);
            
            // Check if student relation exists
            try {
                $application->load('student');
            } catch (\Exception $e) {
                Log::warning('Error loading student relation: ' . $e->getMessage());
            }
            
            // Check if program relation exists
            try {
                $application->load('program');
            } catch (\Exception $e) {
                Log::warning('Error loading program relation: ' . $e->getMessage());
            }
            
            // Get any documents from the document_files attribute
            $documents = [];
            if (is_array($application->document_files)) {
                if (isset($application->document_files['files'])) {
                    $documents = $application->document_files['files'];
                } else {
                    $documents = $application->document_files;
                }
            }
            
            // Add documents to the application
            $application->documents = $documents;
            
            return response()->json([
                'success' => true,
                'application' => $application
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting enrollment application: ' . $e->getMessage(), [
                'application_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to load application details: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Update application status (approve/reject)
     */
    public function updateApplicationStatus(Request $request, $id)
    {
        try {
            // Get the authenticated instructor
            $instructor = Auth::guard('staff')->user();
            
            // Get the application
            $application = StudentApplication::findOrFail($id);
            
            // Get the action and notes
            $action = $request->input('action');
            $notes = $request->input('notes');
            
            // Validate action
            if (!in_array($action, ['approve', 'reject'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid action'
                ]);
            }
            
            // Update application status
            $application->status = ($action === 'approve') ? 'approved' : 'rejected';
            $application->admin_notes = $notes;
            $application->reviewed_by = $instructor->id;
            $application->reviewed_at = now();
            $application->save();
            
            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Application ' . ($action === 'approve' ? 'approved' : 'rejected') . ' successfully',
                'application' => $application
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating application status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update application status'
            ]);
        }
    }

    /**
     * Display the instructor dashboard
     */
    public function dashboard()
    {
        try {
            // Get the authenticated instructor
            $instructor = Auth::guard('staff')->user();
            
            // Default values in case we can't access student applications
            $pendingCount = 0;
            $enrolledCount = 0;
            $rejectedCount = 0;
            
            // Safely check if student_applications table exists
            try {
                if (\Illuminate\Support\Facades\Schema::connection('tenant')->hasTable('student_applications')) {
            // Get pending applications count
            $pendingCount = StudentApplication::where('status', 'pending')->count();
            
            // Get enrolled (approved) applications count
            $enrolledCount = StudentApplication::where('status', 'approved')->count();
            
            // Get rejected applications count
            $rejectedCount = StudentApplication::where('status', 'rejected')->count();
                } else {
                    Log::warning('student_applications table does not exist in tenant database');
                }
            } catch (\Exception $e) {
                Log::warning('Error accessing StudentApplication: ' . $e->getMessage());
                // Continue with default values (0)
            }
            
            // Placeholder values for requirements counts
            $regularRequirementsCount = 5;   // Placeholder for Regular requirements
            $irregularRequirementsCount = 4; // Placeholder for Irregular requirements
            $probationRequirementsCount = 3; // Placeholder for Probation requirements
            
            return view('tenant.Instructors.Instructors', compact(
                'pendingCount',
                'enrolledCount',
                'rejectedCount',
                'regularRequirementsCount',
                'irregularRequirementsCount',
                'probationRequirementsCount'
            ));
        } catch (\Exception $e) {
            Log::error('Error loading instructor dashboard: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->with('error', 'Failed to load dashboard data');
        }
    }
}