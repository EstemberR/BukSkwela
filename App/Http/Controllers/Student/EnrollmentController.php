<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentApplication;
use App\Models\Course\Course;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class EnrollmentController extends Controller
{
    /**
     * Show the enrollment page
     */
    public function index()
    {
        try {
            Log::info('EnrollmentController@index: Started loading enrollment page');
            
            // Get programs/courses for cards display
            try {
                Log::info('EnrollmentController@index: Attempting to load courses from tenant database', [
                    'tenant_id' => tenant('id')
                ]);
                
                // Force the tenant connection
                DB::connection('tenant')->reconnect();
                
                // First check if courses table exists and has records
                $coursesCount = DB::connection('tenant')->table('courses')->count();
                Log::info('EnrollmentController@index: Total courses in database', ['count' => $coursesCount]);
                
                // If no courses exist, create a default course for demo purposes
                if ($coursesCount == 0) {
                    Log::info('No courses found, creating a default course');
            try {
                        $courseId = DB::connection('tenant')->table('courses')->insertGetId([
                            'name' => 'Bachelor of Science in Computer Science',
                            'code' => 'BSCS',
                            'description' => 'A comprehensive program covering programming, algorithms, and computer systems.',
                            'status' => 'active',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        
                        Log::info('Created default course', ['course_id' => $courseId]);
                        
                        // Add another course for selection options
                        $courseId2 = DB::connection('tenant')->table('courses')->insertGetId([
                            'name' => 'Bachelor of Science in Information Technology',
                            'code' => 'BSIT',
                            'description' => 'A program focused on practical IT skills and system administration.',
                            'status' => 'active',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        
                        Log::info('Created second default course', ['course_id' => $courseId2]);
                        
                    } catch (\Exception $createEx) {
                        Log::error('Failed to create default course: ' . $createEx->getMessage(), [
                            'trace' => $createEx->getTraceAsString()
                        ]);
                    }
                }
                
                // Use direct DB query to get all courses
                $rawCourses = DB::connection('tenant')->table('courses')->get();
                
                $programs = collect($rawCourses)->map(function($course) {
                    // Convert to object with consistent properties
                    $courseObj = new \stdClass();
                    $courseObj->id = $course->id;
                    
                    // Determine the name field (could be name or title)
                    if (property_exists($course, 'name') && !empty($course->name)) {
                        $courseObj->name = $course->name;
                    } elseif (property_exists($course, 'title') && !empty($course->title)) {
                        $courseObj->name = $course->title;
                    } else {
                        $courseObj->name = 'Course #' . $course->id;
                    }
                    
                    // Add description if available
                    if (property_exists($course, 'description')) {
                        $courseObj->description = $course->description;
                    } else {
                        $courseObj->description = 'No description available';
                    }
                    
                    // Add code if available
                    if (property_exists($course, 'code')) {
                        $courseObj->code = $course->code;
                    } else {
                        $courseObj->code = 'CODE' . $course->id;
                    }
                    
                    return $courseObj;
                });
                
                Log::info('EnrollmentController@index: Final programs for view', [
                    'count' => $programs->count(),
                    'first' => $programs->first()
                ]);
                
                // If this is an AJAX request, return JSON data for debugging
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'debug' => true,
                        'programs_count' => $programs->count(),
                        'programs' => $programs,
                        'tenant_id' => tenant('id'),
                        'connection' => config('database.connections.tenant')
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Error loading programs: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
                $programs = [];
                
                // If this is an AJAX request, return error info for debugging
                if (request()->ajax() || request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ], 500);
                }
            }
            
            // Get student's applications if any
            $student_id = Auth::guard('student')->id();
            Log::info('EnrollmentController@index: Looking for applications for student', ['student_id' => $student_id]);
            
            try {
                $applications = StudentApplication::on('tenant')
                    ->where('student_id', $student_id)
                    ->orderBy('created_at', 'desc')
                    ->get();
                
                Log::info('EnrollmentController@index: Loaded applications', ['count' => $applications->count()]);
            } catch (\Exception $e) {
                Log::error('Error loading applications: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
                $applications = collect([]);
            }

            // Get available requirement folders
            try {
                $requirementFolders = $this->getRequirementFolders();
                Log::info('EnrollmentController@index: Loaded requirement folders', ['count' => count($requirementFolders)]);
            } catch (\Exception $e) {
                Log::error('Error loading requirement folders: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
                $requirementFolders = [];
            }
            
            return view('tenant.students.enrollment', [
                'programs' => $programs, 
                'applications' => $applications,
                'hasApplications' => $applications->count() > 0,
                'requirementFolders' => $requirementFolders
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading enrollment page: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return view('tenant.students.enrollment', [
                'programs' => [],
                'applications' => [],
                'hasApplications' => false,
                'requirementFolders' => [],
                'error' => 'There was an error loading your enrollment information. Please try again later.'
            ]);
        }
    }
    
    /**
     * Submit a new enrollment application
     */
    public function apply(Request $request)
    {
        try {
            Log::info('Enrollment application submission started');
            
            // Ensure tenant connection is properly set up
            $tenantId = tenant('id');
            $dbName = 'tenant_' . strtolower($tenantId);
            
            // Set the database name for the tenant connection
            config(['database.connections.tenant.database' => $dbName]);
            DB::connection('tenant')->reconnect();
            
            Log::info('Tenant database connection established', [
                'tenant_id' => $tenantId,
                'db_name' => $dbName
            ]);
            
            // Get available requirement folders first
            $requirementFolders = $this->getRequirementFolders();
            $folderIds = array_column($requirementFolders, 'id');
            
            // Build validation rules based on requirement folders
            $rules = [
                'program_id' => 'required|exists:tenant.courses,id',
                'year_level' => 'required|in:1,2,3,4',
                'notes' => 'nullable|string|max:1000',
            ];
            
            // Add validation rules for each folder's file upload
            foreach ($folderIds as $folderId) {
                $rules["folder_file_" . $folderId] = 'required|file|mimes:pdf,jpg,jpeg,png|max:5120';
            }
            
            // Validate the request
            $validated = $request->validate($rules);
            
            // Create a new application
            DB::connection('tenant')->beginTransaction();
            
            try {
            $application = new StudentApplication();
            $application->setConnection('tenant');
            $application->student_id = Auth::guard('student')->id();
            $application->program_id = $validated['program_id'];
            $application->year_level = $validated['year_level'];
            $application->notes = $validated['notes'] ?? null;
            $application->status = 'pending'; // Initial status
                $application->tenant_id = $tenantId;
            $application->save();
                
                Log::info('Application created successfully', [
                    'id' => $application->id,
                    'student_id' => $application->student_id
                ]);
            
            // Handle document uploads for each requirement folder
            $uploadedFiles = [];
            $requirementsController = app(\App\Http\Controllers\Requirements\RequirementsController::class);
            
            foreach ($requirementFolders as $folder) {
                $folderId = $folder['id'];
                $folderName = $folder['name'] ?? 'Unknown Folder';
                $fileInputName = "folder_file_" . $folderId;
                
                if ($request->hasFile($fileInputName)) {
                    try {
                        // Create a new request object for this file
                        $fileRequest = new \Illuminate\Http\Request();
                        $fileRequest->files->set('file', $request->file($fileInputName));
                            
                            // Add application ID to filename for better tracking
                            $originalFilename = $request->file($fileInputName)->getClientOriginalName();
                            $customFilename = "App{$application->id}_{$originalFilename}";
                            $fileRequest->merge(['custom_filename' => $customFilename]);
                        
                        // Use the requirements controller to upload the file to the folder
                        $uploadResponse = $requirementsController->uploadFile($fileRequest, $folderId);
                        
                        $responseData = json_decode($uploadResponse->getContent(), true);
                        
                        if (isset($responseData['success']) && $responseData['success']) {
                            $uploadedFiles[$folderName] = $responseData['file'] ?? 'Uploaded successfully';
                            Log::info("Uploaded file to folder {$folderName}", [
                                'folder_id' => $folderId,
                                'application_id' => $application->id,
                                'response' => $responseData
                            ]);
                        } else {
                                throw new \Exception("Failed to upload file to folder {$folderName}: " . 
                                    (isset($responseData['message']) ? $responseData['message'] : 'Unknown error'));
                        }
                    } catch (\Exception $e) {
                            Log::error("Error uploading file to folder {$folderName}", [
                            'folder_id' => $folderId,
                                'application_id' => $application->id,
                                'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                            throw $e;
                    }
                }
            }
            
                DB::connection('tenant')->commit();
                
            return redirect()->route('tenant.student.enrollment', ['tenant' => tenant('id')])
                    ->with('success', 'Your enrollment application has been submitted successfully.');
            } catch (\Exception $e) {
                DB::connection('tenant')->rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Error submitting enrollment application: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::guard('student')->id(),
                'tenant' => tenant('id')
            ]);
            
            return redirect()->route('tenant.student.enrollment', ['tenant' => tenant('id')])
                ->with('error', 'There was an error submitting your application. Please try again: ' . $e->getMessage());
        }
    }
    
    /**
     * Get program-specific requirements.
     */
    public function getProgramRequirements($programId)
    {
        try {
            Log::info('Fetching program requirements', ['program_id' => $programId]);
            
            $program = Course::on('tenant')->findOrFail($programId);
            
            // This could be enhanced to fetch actual program-specific requirements
            // For now, we'll return a basic set of requirements
            $requirements = [
                'transcript' => true,
                'id_photo' => true,
                'medical_certificate' => $program->requires_medical_certificate ?? false,
                'recommendation_letter' => $program->requires_recommendation ?? false,
            ];
            
            Log::info('Program requirements loaded', ['program' => $program->name]);
            
            return response()->json([
                'success' => true,
                'requirements' => $requirements,
                'program' => [
                    'id' => $program->id,
                    'name' => $program->name
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch program requirements: ' . $e->getMessage(), [
                'program_id' => $programId,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch program requirements'
            ], 500);
        }
    }

    /**
     * Debug method to help diagnose issues (remove in production)
     */
    public function debugCourses()
    {
        try {
            // Force the tenant connection
            DB::connection('tenant')->reconnect();
            
            // Get table structure
            $columns = DB::connection('tenant')->getSchemaBuilder()->getColumnListing('courses');
            
            // Get all courses without any filters
            $courses = DB::connection('tenant')->table('courses')->get();
            
            // Create test course if none exist
            if ($courses->count() == 0) {
                $id = DB::connection('tenant')->table('courses')->insertGetId([
                    'name' => 'Test Course ' . date('Y-m-d H:i:s'),
                    'code' => 'TEST' . rand(100, 999),
                    'description' => 'Test course created for debugging',
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                $courses = DB::connection('tenant')->table('courses')->get();
            }
            
            return response()->json([
                'success' => true,
                'tenant_id' => tenant('id'),
                'database' => config('database.connections.tenant.database'),
                'table_columns' => $columns,
                'courses_count' => $courses->count(),
                'courses' => $courses
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Get available requirement folders from the requirements system
     */
    private function getRequirementFolders()
    {
        Log::info('Fetching requirement folders for enrollment');
        
        // Get the student's status (Regular, Irregular, Probation)
        $student = Auth::guard('student')->user();
        $studentStatus = $student->status ?? 'Regular';
        
        Log::info('Student status for requirement folders', ['status' => $studentStatus]);
        
        try {
            // Create a mock request with the category parameter
            $mockRequest = new \Illuminate\Http\Request();
            $mockRequest->merge([
                'category' => $studentStatus,
                'page' => 1
            ]);
            
            // Use the App\Http\Controllers\Requirements\RequirementsController to fetch folders
            $requirementsController = app(\App\Http\Controllers\Requirements\RequirementsController::class);
            $response = $requirementsController->listFolderContents($mockRequest);
            
            if ($response->getStatusCode() === 200) {
                $data = json_decode($response->getContent(), true);
                
                if (isset($data['success']) && $data['success'] && isset($data['files'])) {
                    // Filter to only include folders, not files
                    $folders = array_filter($data['files'], function($item) {
                        return isset($item['mimeType']) && $item['mimeType'] === 'application/vnd.google-apps.folder';
                    });
                    
                    Log::info('Found requirement folders', ['count' => count($folders)]);
                    return $folders;
                }
            }
            
            Log::warning('No requirement folders found or invalid response');
            return [];
        } catch (\Exception $e) {
            Log::error('Error fetching requirement folders: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }
}
