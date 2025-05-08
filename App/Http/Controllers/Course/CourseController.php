<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Models\Course\Course;
use App\Models\Staff\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Start with a query explicitly on the tenant connection
            $query = Course::on('tenant');

            // Apply search filter
            if ($request->has('search') && !empty($request->get('search'))) {
                $search = $request->get('search');
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Apply status filter
            if ($request->has('status') && !empty($request->get('status')) && $request->get('status') !== 'all') {
                $query->where('status', $request->get('status'));
            }

            $courses = $query->paginate(10);
            
            // Get instructors for dropdown
            $instructors = Staff::on('tenant')->where('role', 'instructor')->get();
            
            // Log that we're viewing the courses
            \Log::info('Viewing courses index', [
                'search' => $request->get('search'),
                'status' => $request->get('status'),
                'count' => $courses->count()
            ]);

            return view('tenant.courses.index', compact('courses', 'instructors'));
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error viewing courses index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->view('tenant.error', [
                'message' => 'Error loading courses: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Log the request details for debugging
            \Log::info('Course store method called', [
                'request_data' => $request->all(),
                'tenant_id' => tenant('id'),
                'tenant_connection' => config('database.connections.tenant.database'),
                'request_url' => $request->fullUrl(),
                'request_method' => $request->method(),
                'is_ajax' => $request->ajax()
            ]);

            // Validate the request
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'school_year_start' => 'nullable|numeric|min:2000|max:2100',
                'school_year_end' => 'nullable|numeric|min:2000|max:2100|gte:school_year_start',
            ]);

            // Generate a course code from the name
            $code = $this->generateCourseCode($request->name);
            
            // Log creation attempt
            \Log::info('Creating new course', [
                'name' => $request->name,
                'code' => $code,
                'tenant_id' => tenant('id'),
                'school_year' => $request->school_year_start . '-' . $request->school_year_end
            ]);

            // Ensure connection is properly set
            $tenantId = tenant('id');
            $dbName = 'tenant_' . $tenantId;
            
            // Configure database connection
            $tenantDB = \App\Models\TenantDatabase::where('tenant_id', $tenantId)->first();
            if ($tenantDB) {
                \Illuminate\Support\Facades\Config::set('database.connections.tenant.database', $tenantDB->database_name);
            } else {
                \Illuminate\Support\Facades\Config::set('database.connections.tenant.database', $dbName);
            }
            
            // Ensure connection is refreshed
            DB::purge('tenant');
            DB::reconnect('tenant');
            
            // Verify the connection is working
            try {
                DB::connection('tenant')->getPdo();
                \Log::info('Tenant connection established', [
                    'database' => config('database.connections.tenant.database')
                ]);
            } catch (\Exception $e) {
                \Log::error('Tenant connection failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw new \Exception('Database connection error: ' . $e->getMessage());
            }

            // Check if columns exist in the courses table
            $columns = DB::connection('tenant')->getSchemaBuilder()->getColumnListing('courses');
            
            // Create new course data with careful handling of column existence
            $courseData = [
                'name' => $request->name,
                'code' => $code,
                'description' => $request->description,
                'status' => 'active',
                'tenant_id' => $tenantId,
            ];
            
            // Add title if column exists
            if (in_array('title', $columns)) {
                $courseData['title'] = $request->name;
            }
            
            // Add school year fields if columns exist
            if (in_array('school_year_start', $columns) && $request->filled('school_year_start')) {
                $courseData['school_year_start'] = $request->school_year_start;
            }
            
            if (in_array('school_year_end', $columns) && $request->filled('school_year_end')) {
                $courseData['school_year_end'] = $request->school_year_end;
            }

            // Create with explicit connection
            $course = new Course();
            $course->setConnection('tenant');
            $course->fill($courseData);
            $course->save();
            
            // Log successful creation
            \Log::info('Course created successfully', [
                'course_id' => $course->id,
                'name' => $course->name
            ]);

            // If this is an AJAX request, return a JSON response
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Course created successfully',
                    'course' => $course
                ]);
            }

            return redirect()->route('tenant.courses.index')
                ->with('success', 'Course created successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Log validation errors
            \Log::warning('Course creation validation failed', [
                'errors' => $e->errors()
            ]);
            
            // If this is an AJAX request, return a JSON response
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            
            // Re-throw for the normal form submission flow
            throw $e;
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error creating course', [
                'name' => $request->name ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // If this is an AJAX request, return a JSON response
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error creating course: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('tenant.courses.index')
                ->with('error', 'Error creating course: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Course $course)
    {
        try {
            // Force explicit tenant connection with better error handling
            $tenantId = tenant('id');
            \Log::info("Updating course for tenant {$tenantId}", [
                'course_id' => $course->id,
                'tenant_id' => $tenantId
            ]);
            
            // Get the tenant database connection
            $dbName = 'tenant_' . $tenantId;
            
            // Configure database connection
            $tenantDB = \App\Models\TenantDatabase::where('tenant_id', $tenantId)->first();
            if ($tenantDB) {
                \Illuminate\Support\Facades\Config::set('database.connections.tenant.database', $tenantDB->database_name);
            } else {
                \Illuminate\Support\Facades\Config::set('database.connections.tenant.database', $dbName);
            }
            
            // Ensure connection is refreshed
            DB::purge('tenant');
            DB::reconnect('tenant');
            
            // Explicitly set the course's connection
            $course->setConnection('tenant');
            
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'status' => 'required|in:active,inactive',
                'school_year_start' => 'nullable|numeric|min:2000|max:2100',
                'school_year_end' => 'nullable|numeric|min:2000|max:2100|gte:school_year_start',
            ]);

            $updateData = [
                'name' => $request->name,
                'description' => $request->description,
                'status' => $request->status,
                'school_year_start' => $request->school_year_start,
                'school_year_end' => $request->school_year_end
            ];

            // Log update attempt
            \Log::info('Updating course', [
                'course_id' => $course->id,
                'data' => $updateData
            ]);

            // Update the course
            $course->update($updateData);
            
            // Log successful update
            \Log::info('Course updated successfully', [
                'course_id' => $course->id
            ]);

            return redirect()->route('tenant.courses.index')
                ->with('success', 'Course updated successfully');
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error updating course', [
                'course_id' => $course->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('tenant.courses.index')
                ->with('error', 'Error updating course: ' . $e->getMessage());
        }
    }

    public function destroy(Course $course)
    {
        try {
            // Ensure we're using the tenant database connection
            // Force explicit tenant connection
            $tenantId = tenant('id');
            \Log::info("Deleting course for tenant {$tenantId}", [
                'course_id' => $course->id,
                'tenant_id' => $tenantId
            ]);
            
            // Get course details before deletion for logging
            $courseDetails = [
                'id' => $course->id,
                'name' => $course->name,
                'code' => $course->code ?? 'N/A',
                'tenant_id' => $tenantId
            ];
            
            // Log the deletion attempt
            \Log::info('Attempting to delete course', $courseDetails);
            
            // Get the tenant database connection
            $dbName = 'tenant_' . $tenantId;
            // Configure database connection
            $tenantDB = \App\Models\TenantDatabase::where('tenant_id', $tenantId)->first();
            if ($tenantDB) {
                \Illuminate\Support\Facades\Config::set('database.connections.tenant.database', $tenantDB->database_name);
            } else {
                \Illuminate\Support\Facades\Config::set('database.connections.tenant.database', $dbName);
            }
            
            // Ensure connection is refreshed
            DB::purge('tenant');
            DB::reconnect('tenant');
            
            // Test connection
            DB::connection('tenant')->getPdo();
            
            // Explicitly set the course's connection
            $course->setConnection('tenant');
            
            // Check if there are students enrolled in this course
            $studentsEnrolled = DB::connection('tenant')
                ->table('students')
                ->where('course_id', $course->id)
                ->count();
                
            if ($studentsEnrolled > 0) {
                \Log::warning("Cannot delete course ID {$course->id} because {$studentsEnrolled} students are enrolled");
                
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false, 
                        'message' => "Cannot delete this course because {$studentsEnrolled} students are enrolled. Please reassign these students to another course first."
                    ], 400);
                }
                
                return redirect()->route('tenant.courses.index')
                    ->with('error', "Cannot delete this course because {$studentsEnrolled} students are enrolled. Please reassign these students to another course first.");
            }
            
            // Delete the course
            $result = $course->delete();
            
            // Log successful deletion
            \Log::info('Course deleted successfully', $courseDetails);
            
            if (request()->expectsJson()) {
                return response()->json(['success' => true]);
            }
            
            return redirect()->route('tenant.courses.index')
                ->with('success', 'Course deleted successfully');
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error deleting course', [
                'course_id' => $course->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            
            return redirect()->route('tenant.courses.index')
                ->with('error', 'Error deleting course: ' . $e->getMessage());
        }
    }
    
    /**
     * Delete a course directly using ID without route model binding
     */
    public function destroyDirect($id)
    {
        try {
            // Force explicit tenant connection
            $tenantId = tenant('id');
            \Log::info("Direct deleting course ID {$id} for tenant {$tenantId}");
            
            // Get the tenant database connection
            $dbName = 'tenant_' . $tenantId;
            
            // Configure database connection
            $tenantDB = \App\Models\TenantDatabase::where('tenant_id', $tenantId)->first();
            if ($tenantDB) {
                \Illuminate\Support\Facades\Config::set('database.connections.tenant.database', $tenantDB->database_name);
            } else {
                \Illuminate\Support\Facades\Config::set('database.connections.tenant.database', $dbName);
            }
            
            // Ensure connection is refreshed
            DB::purge('tenant');
            DB::reconnect('tenant');
            
            // Find the course directly using the tenant connection
            $course = Course::on('tenant')->find($id);
            
            if (!$course) {
                \Log::warning("Course ID {$id} not found for deletion");
                if (request()->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Course not found'], 404);
                }
                return redirect()->route('tenant.courses.index')
                    ->with('error', 'Course not found');
            }
            
            // Check if there are students enrolled in this course
            $studentsEnrolled = DB::connection('tenant')
                ->table('students')
                ->where('course_id', $id)
                ->count();
                
            if ($studentsEnrolled > 0) {
                \Log::warning("Cannot delete course ID {$id} because {$studentsEnrolled} students are enrolled");
                
                if (request()->expectsJson()) {
                    return response()->json([
                        'success' => false, 
                        'message' => "Cannot delete this course because {$studentsEnrolled} students are enrolled. Please reassign these students to another course first."
                    ], 400);
                }
                
                return redirect()->route('tenant.courses.index')
                    ->with('error', "Cannot delete this course because {$studentsEnrolled} students are enrolled. Please reassign these students to another course first.");
            }
            
            // Delete the course
            $result = $course->delete();
            
            // Log successful deletion
            \Log::info("Course ID {$id} deleted successfully via direct method");
            
            if (request()->expectsJson()) {
                return response()->json(['success' => true]);
            }
            
            return redirect()->route('tenant.courses.index')
                ->with('success', 'Course deleted successfully');
        } catch (\Exception $e) {
            // Log the error
            \Log::error("Error in direct course deletion for ID {$id}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            
            return redirect()->route('tenant.courses.index')
                ->with('error', 'Error deleting course: ' . $e->getMessage());
        }
    }
    
    /**
     * Generate a unique course code from the course name
     */
    private function generateCourseCode($name)
    {
        // Get first letters of each word
        $words = explode(' ', $name);
        $code = '';
        
        foreach ($words as $word) {
            if (!empty($word)) {
                $code .= strtoupper(substr($word, 0, 1));
            }
        }
        
        // Add random numbers to ensure uniqueness
        $code .= '-' . random_int(100, 999);
        
        // Check if code already exists and regenerate if needed
        while (Course::where('code', $code)->exists()) {
            $code = substr($code, 0, strrpos($code, '-')) . '-' . random_int(100, 999);
        }
        
        return $code;
    }

    /**
     * Direct update method that doesn't rely on route model binding
     */
    public function updateDirect(Request $request, $id)
    {
        try {
            // Force explicit tenant connection
            $tenantId = tenant('id');
            \Log::info("Direct updating course ID {$id} for tenant {$tenantId}");
            
            // Get the tenant database connection
            $dbName = 'tenant_' . $tenantId;
            
            // Configure database connection
            $tenantDB = \App\Models\TenantDatabase::where('tenant_id', $tenantId)->first();
            if ($tenantDB) {
                \Illuminate\Support\Facades\Config::set('database.connections.tenant.database', $tenantDB->database_name);
            } else {
                \Illuminate\Support\Facades\Config::set('database.connections.tenant.database', $dbName);
            }
            
            // Ensure connection is refreshed
            DB::purge('tenant');
            DB::reconnect('tenant');
            
            // Find the course directly using the tenant connection
            $course = Course::on('tenant')->find($id);
            
            if (!$course) {
                \Log::warning("Course ID {$id} not found for update");
                return redirect()->route('tenant.courses.index')
                    ->with('error', 'Course not found');
            }
            
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'status' => 'required|in:active,inactive',
                'school_year_start' => 'nullable|numeric|min:2000|max:2100',
                'school_year_end' => 'nullable|numeric|min:2000|max:2100|gte:school_year_start',
            ]);

            $updateData = [
                'name' => $request->name,
                'description' => $request->description,
                'status' => $request->status,
                'school_year_start' => $request->school_year_start,
                'school_year_end' => $request->school_year_end
            ];

            // Log update attempt
            \Log::info('Direct updating course', [
                'course_id' => $id,
                'data' => $updateData
            ]);

            // Update the course
            $course->update($updateData);
            
            // Log successful update
            \Log::info('Course updated successfully via direct method', [
                'course_id' => $id
            ]);

            return redirect()->route('tenant.courses.index')
                ->with('success', 'Course updated successfully');
        } catch (\Exception $e) {
            // Log the error
            \Log::error("Error in direct course update for ID {$id}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('tenant.courses.index')
                ->with('error', 'Error updating course: ' . $e->getMessage());
        }
    }
} 