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
            $tenantId = tenant('id');
            Log::info('EnrollmentController@index: Started loading enrollment page', [
                'tenant_id' => $tenantId,
                'student_id' => Auth::guard('student')->id()
            ]);
            
            // Ensure tenant connection is properly set up
            $dbName = 'tenant_' . strtolower($tenantId);
            
            // Set the database name for the tenant connection
            config(['database.connections.tenant.database' => $dbName]);
            DB::connection('tenant')->reconnect();
            
            // Verify database connection works and is using the correct database
            try {
                $currentDb = DB::connection('tenant')->getDatabaseName();
                if ($currentDb !== $dbName) {
                    throw new \Exception("Database mismatch: Connected to {$currentDb} instead of {$dbName}");
                }
                
                Log::info('Tenant database connection established', [
                    'tenant_id' => $tenantId,
                    'db_name' => $dbName
                ]);
            } catch (\Exception $dbCheckException) {
                Log::error('Database verification failed', [
                    'error' => $dbCheckException->getMessage(),
                    'trace' => $dbCheckException->getTraceAsString()
                ]);
                
                throw new \Exception('Error connecting to tenant database: ' . $dbCheckException->getMessage());
            }
            
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
            Log::info('EnrollmentController@index: Looking for applications for student', [
                'student_id' => $student_id,
                'tenant_id' => $tenantId,
                'database' => $dbName
            ]);
            
            try {
                // Verify student_applications table exists
                $tableExists = DB::connection('tenant')->getSchemaBuilder()->hasTable('student_applications');
                if (!$tableExists) {
                    Log::warning('student_applications table does not exist in tenant database', [
                        'tenant_id' => $tenantId,
                        'database' => $dbName
                    ]);
                    throw new \Exception("student_applications table does not exist");
                }
                
                // Get applications with explicit tenant connection
                $applications = DB::connection('tenant')
                    ->table('student_applications')
                    ->where('student_id', $student_id)
                    ->orderBy('created_at', 'desc')
                    ->get();
                
                // Convert to model instances for better handling
                $applications = collect($applications)->map(function($application) {
                    $model = new \App\Models\StudentApplication();
                    $model->setConnection('tenant');
                    
                    foreach($application as $key => $value) {
                        $model->{$key} = $value;
                    }
                    
                    // Handle JSON fields
                    if (isset($application->document_files) && is_string($application->document_files)) {
                        $model->document_files = json_decode($application->document_files, true);
                    }
                    
                    // Handle date fields
                    if (isset($application->created_at)) {
                        $model->created_at = new \DateTime($application->created_at);
                    }
                    
                    if (isset($application->updated_at)) {
                        $model->updated_at = new \DateTime($application->updated_at);
                    }
                    
                    return $model;
                });
                
                Log::info('EnrollmentController@index: Loaded applications', [
                    'count' => $applications->count(),
                    'tenant_id' => $tenantId,
                    'sample_ids' => $applications->take(3)->pluck('id')
                ]);
            } catch (\Exception $e) {
                Log::error('Error loading applications: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                    'tenant_id' => $tenantId,
                    'student_id' => $student_id
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
                'trace' => $e->getTraceAsString(),
                'tenant_id' => tenant('id')
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
     * Get available requirement folders from the requirements system
     * based on the student status (Regular, Irregular, Probation)
     */
    private function getRequirementFolders($status = null)
    {
        Log::info('Fetching requirement folders for enrollment', [
            'requested_status' => $status
        ]);
        
        // Get the student's status (Regular, Irregular, Probation)
        $student = Auth::guard('student')->user();
        $studentStatus = $status ?? $student->status ?? 'Regular';
        
        Log::info('Student status for folder filtering', [
            'student_id' => $student ? $student->id : null,
            'status' => $studentStatus
        ]);
        
        try {
            // Create a request object for the folder listing endpoint
            $request = new \Illuminate\Http\Request();
            $request->merge([
                'page' => 1,
                'category' => $studentStatus
            ]);
            
            // Use the App\Http\Controllers\Requirements\RequirementsController to fetch folders
            $requirementsController = app(\App\Http\Controllers\Requirements\RequirementsController::class);
            $response = $requirementsController->listFolderContents($request);
            
            if ($response->getStatusCode() === 200) {
                $data = json_decode($response->getContent(), true);
                
                if (isset($data['success']) && $data['success'] && isset($data['files'])) {
                    // Filter to only include folders, not files
                    $folders = array_filter($data['files'], function($item) {
                        return isset($item['mimeType']) && $item['mimeType'] === 'application/vnd.google-apps.folder';
                    });
                    
                    // Get all folders first, then we'll filter by status
                    $tenantId = tenant('id');
                    $tenantFolders = array_filter($folders, function($folder) use ($tenantId) {
                        $folderName = $folder['name'] ?? '';
                        
                        // First, check if folder belongs to this tenant
                        $isTenantFolder = false;
                        
                        // Check for tenant prefix [tenantId]
                        if (stripos($folderName, "[$tenantId]") !== false) {
                            $isTenantFolder = true;
                        }
                        // For backward compatibility, also check if the name contains the tenant ID
                        else if (stripos($folderName, $tenantId) !== false) {
                            $isTenantFolder = true;
                        }
                        // If no tenant-specific folders exist, allow generic folders
                        else if (!preg_match('/\[[a-zA-Z0-9_-]+\]/', $folderName)) {
                            $isTenantFolder = true;
                        }
                        
                        return $isTenantFolder;
                    });
                    
                    Log::info('Found tenant folders before status filtering', [
                        'total_folders' => count($folders),
                        'tenant_folders' => count($tenantFolders)
                    ]);
                    
                    // If we have no folders at all, there's a setup issue
                    if (count($tenantFolders) === 0) {
                        Log::warning('No tenant folders found at all');
                        return [];
                    }
                    
                    // Now filter by student status only if requested
                    if ($studentStatus) {
                        $statusFolders = array_filter($tenantFolders, function($folder) use ($studentStatus) {
                            $folderName = $folder['name'] ?? '';
                            
                            // Check for status tag [Regular], [Irregular], [Probation]
                            $categoryMatch = preg_match('/\[(Regular|Irregular|Probation)\]/i', $folderName, $matches);
                            
                            if ($categoryMatch) {
                                $folderCategory = $matches[1];
                                return strcasecmp($folderCategory, $studentStatus) === 0;
                            } else {
                                // If no category tag, include for Regular status by default
                                return strcasecmp($studentStatus, 'Regular') === 0;
                            }
                        });
                        
                        Log::info('Filtered folders by status', [
                            'status' => $studentStatus,
                            'before_count' => count($tenantFolders),
                            'after_count' => count($statusFolders)
                        ]);
                        
                        // If no folders for this status, just return all tenant folders
                        if (count($statusFolders) === 0) {
                            Log::warning('No folders found for status: ' . $studentStatus . '. Using all tenant folders.');
                            return array_values($tenantFolders);
                        }
                        
                        return array_values($statusFolders);
                    }
                    
                    // If no status filter requested, return all tenant folders
                    return array_values($tenantFolders);
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
    
    /**
     * Apply method to handle student enrollment applications
     */
    public function apply(Request $request)
    {
        try {
            // Get tenant information for proper database targeting
            $tenantId = tenant('id');
            
            Log::info('Enrollment application submission started', [
                'tenant_id' => $tenantId,
                'student_id' => Auth::guard('student')->id(),
                'request_data' => $request->only(['program_id', 'year_level', 'student_status', 'school_year_start', 'school_year_end'])
            ]);
            
            // Get student status from request
            $studentStatus = $request->student_status ?? 'Regular';
            
            // Ensure tenant connection is properly set up
            $dbName = 'tenant_' . strtolower($tenantId);
            
            // Set the database name for the tenant connection
            config(['database.connections.tenant.database' => $dbName]);
            
            // Log current configuration
            Log::info('Tenant database configuration', [
                'connection' => config('database.connections.tenant'),
                'tenant_id' => $tenantId
            ]);
            
            // Purge and reconnect to ensure clean connection
            DB::purge('tenant');
            DB::reconnect('tenant');
            
            // Verify connection is working and using correct database
            try {
                $currentDb = DB::connection('tenant')->getDatabaseName();
                Log::info('Current database name', ['db_name' => $currentDb]);
                
                if ($currentDb !== $dbName) {
                    throw new \Exception("Database mismatch: Connected to {$currentDb} instead of {$dbName}");
                }
                
                // Check if StudentApplication table exists in tenant database
                $tableExists = DB::connection('tenant')->getSchemaBuilder()->hasTable('student_applications');
                if (!$tableExists) {
                    throw new \Exception("student_applications table does not exist in tenant database");
                }
                
                // Check if we can run a simple query to verify connection
                $testQuery = DB::connection('tenant')->select('SELECT 1 as test');
                Log::info('Test query result', ['result' => $testQuery]);
                
                // Get list of tables for debugging
                $tables = DB::connection('tenant')->select('SHOW TABLES');
                Log::info('Tables in tenant database', ['tables' => $tables]);
                
                Log::info('Tenant database connection established and verified', [
                    'tenant_id' => $tenantId,
                    'db_name' => $dbName,
                    'student_applications_table_exists' => $tableExists
                ]);
            } catch (\Exception $e) {
                Log::error('Error verifying tenant database connection', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw new \Exception('Error connecting to tenant database: ' . $e->getMessage());
            }
            
            // Define validation rules
            $rules = [
                'program_id' => 'required|integer|exists:tenant.courses,id',
                'year_level' => 'required|integer|min:1|max:4',
                'student_status' => 'required|string|in:Regular,Irregular,Probation',
                'notes' => 'nullable|string|max:1000',
                'school_year_start' => 'required|integer',
                'school_year_end' => 'required|integer|gte:school_year_start'
            ];
            
            $validated = $request->validate($rules);
            
            // Create a new application
            DB::connection('tenant')->beginTransaction();
            
            try {
                $application = new \App\Models\StudentApplication();
                $application->setConnection('tenant');
                $application->student_id = Auth::guard('student')->id();
                $application->program_id = $validated['program_id'];
                $application->year_level = $validated['year_level'];
                $application->student_status = $validated['student_status'];
                $application->notes = $validated['notes'] ?? null;
                $application->status = 'pending'; // Initial status
                $application->tenant_id = $tenantId;
                $application->school_year_start = $validated['school_year_start'];
                $application->school_year_end = $validated['school_year_end'];
                
                // Log the application data before saving
                Log::info('Creating enrollment application', [
                    'connection' => $application->getConnectionName(),
                    'database' => config('database.connections.tenant.database'),
                    'application_data' => [
                        'student_id' => $application->student_id,
                        'program_id' => $application->program_id,
                        'year_level' => $application->year_level,
                        'student_status' => $application->student_status,
                        'school_year_start' => $application->school_year_start,
                        'school_year_end' => $application->school_year_end,
                        'tenant_id' => $application->tenant_id
                    ]
                ]);
                
                // Save to get an ID before uploading files
                try {
                    $saved = $application->save();
                    Log::info('Save result', ['saved' => $saved]);
                    
                    // Verify the save worked by querying the database directly
                    $check = DB::connection('tenant')->table('student_applications')
                        ->where('student_id', $application->student_id)
                        ->where('program_id', $application->program_id)
                        ->orderBy('id', 'desc')
                        ->first();
                    
                    Log::info('Database check after save', ['check' => $check]);
                    
                    if (!$check) {
                        throw new \Exception('Application was saved but not found in database');
                    }
                } catch (\Exception $e) {
                    Log::error('Error saving application', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw new \Exception('Error saving application: ' . $e->getMessage());
                }
                
                // Verify application was saved with the correct ID
                if (!$application->id) {
                    throw new \Exception('Application was not saved correctly - no ID generated');
                }
                
                Log::info('Application record created successfully', [
                    'application_id' => $application->id
                ]);
                
                // Store student status in document_files json
                $documentFiles = [
                    'student_status' => $validated['student_status'],
                    'files' => []
                ];
                
                // Get student info for filename prefixing
                $student = Auth::guard('student')->user();
                $studentName = $student ? preg_replace('/[^a-zA-Z0-9]/', '', $student->name) : 'UnknownStudent';
                
                // Handle document uploads for each requirement folder
                $uploadedFiles = [];
                $requirementsController = app(\App\Http\Controllers\Requirements\RequirementsController::class);
                
                // Handle uploads using requirement folders
                foreach ($this->getRequirementFolders($studentStatus) as $folder) {
                    $folderId = $folder['id'];
                    $folderName = $folder['name'] ?? 'Unknown Folder';
                    $fileInputName = "folder_file_" . $folderId;
                
                // Remove tenant prefix and status tag from folder name
                $displayFolderName = preg_replace(['/^\[[^\]]+\]\s*/', '/\[(Regular|Irregular|Probation)\]\s*/'], '', $folderName);
                    
                    if ($request->hasFile($fileInputName)) {
                        try {
                            // Create a new request object for this file
                            $fileRequest = new \Illuminate\Http\Request();
                            $fileRequest->files->set('file', $request->file($fileInputName));
                            
                        // Add application ID and tenant ID to filename for better tracking
                            $originalFilename = $request->file($fileInputName)->getClientOriginalName();
                        $customFilename = "[{$tenantId}]_App{$application->id}_{$studentName}_{$originalFilename}";
                            $fileRequest->merge(['custom_filename' => $customFilename]);
                            
                            // Use the requirements controller to upload the file to the folder
                            $uploadResponse = $requirementsController->uploadFile($fileRequest, $folderId);
                            
                            $responseData = json_decode($uploadResponse->getContent(), true);
                            
                            if (isset($responseData['success']) && $responseData['success']) {
                                $fileData = $responseData['file'] ?? [];
                                $uploadedFiles[$folderName] = $fileData;
                                
                            // Add to document files array with more metadata
                                $documentFiles['files'][] = [
                                        'folder_id' => $folderId,
                                    'folder_name' => $displayFolderName,
                                        'file_id' => $fileData['id'] ?? null,
                                        'file_name' => $fileData['name'] ?? $customFilename,
                                    'display_name' => $originalFilename,
                                        'file_path' => $fileData['webViewLink'] ?? null,
                                        'mime_type' => $fileData['mimeType'] ?? $request->file($fileInputName)->getMimeType(),
                                    'size' => $fileData['size'] ?? $request->file($fileInputName)->getSize(),
                                    'uploaded_at' => now()->toDateTimeString(),
                                    'student_status' => $validated['student_status'],
                                    'tenant_id' => $tenantId
                                    ];
                                    
                                    Log::info("Uploaded file to folder {$folderName}", [
                                        'folder_id' => $folderId,
                                        'application_id' => $application->id,
                                        'file_data' => $fileData
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
                
                // Save document files JSON at the end
                $application->document_files = $documentFiles;
                $application->save();
                
                Log::info('Application created successfully', [
                    'id' => $application->id,
                    'student_id' => $application->student_id,
                    'document_count' => count($documentFiles['files']),
                    'tenant_id' => $tenantId, 
                    'database' => config('database.connections.tenant.database')
                ]);
                
                DB::connection('tenant')->commit();
                
                return redirect()->route('tenant.student.enrollment', ['tenant' => tenant('id')])
                    ->with('success', 'Your enrollment application has been submitted successfully.');
            } catch (\Exception $e) {
                DB::connection('tenant')->rollBack();
                Log::error('Exception in application creation transaction', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
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
     * Get requirement folders regardless of status
     * This is used as a fallback for finding matching folders
     */
    private function getAllRequirementFolders()
    {
        Log::info('Fetching all requirement folders regardless of status');
        
        try {
            // Create a mock request with no category filter
            $mockRequest = new \Illuminate\Http\Request();
            $mockRequest->merge([
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
                    
                    // Filter by the tenant ID to ensure only tenant-specific folders are shown
                    $tenantId = tenant('id');
                    $tenantFolders = array_filter($folders, function($folder) use ($tenantId) {
                        $folderName = $folder['name'] ?? '';
                        
                        // Check if folder belongs to this tenant by looking for [tenantId] pattern
                        if (stripos($folderName, "[$tenantId]") !== false) {
                            return true;
                        }
                        
                        // For backward compatibility, also check if the name contains the tenant ID
                        if (stripos($folderName, $tenantId) !== false) {
                            return true;
                        }
                        
                        // If no tenant-specific folders exist, allow generic folders to be shown
                        if (!preg_match('/\[[a-zA-Z0-9_-]+\]/', $folderName)) {
                            return true;
                        }
                        
                        return false;
                    });
                    
                    Log::info('Found all requirement folders after tenant filtering', [
                        'total_found' => count($folders),
                        'tenant_folders' => count($tenantFolders)
                    ]);
                    
                    return array_values($tenantFolders);
                }
            }
            
            Log::warning('No requirement folders found or invalid response');
            return [];
        } catch (\Exception $e) {
            Log::error('Error fetching all requirement folders: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }
    
    /**
     * Get program requirements for a specific program
     */
    public function getProgramRequirements($programId)
    {
        try {
            // Get student's status from request (Regular, Probation, Irregular)
            $studentStatus = request()->get('status', 'Regular');
            
            Log::info('Getting program requirements', [
                'program_id' => $programId,
                'student_status' => $studentStatus
            ]);
            
            // Try to load program from database for validation
            $program = Course::on('tenant')->find($programId);
            
            if (!$program) {
                Log::warning('Program not found for requirements', ['program_id' => $programId]);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid program ID'
                ]);
            }
            
            // Get requirement folders filtered by student status
            $requirementFolders = $this->getRequirementFolders($studentStatus);
            
            // Check if we have ANY folders in the system
            $allFolders = $this->getAllRequirementFolders();
            $hasSetupFolders = count($allFolders) > 0;
            
            // For better user experience, add metadata about the program and status
            $responseData = [
                'success' => true,
                'program' => [
                    'id' => $program->id,
                    'name' => $program->name,
                    'code' => $program->code
                ],
                'status' => $studentStatus,
                'requirementFolders' => $requirementFolders,
                'hasSetupFolders' => $hasSetupFolders
            ];
            
            // If we have folders, include them in the response
            if (count($requirementFolders) > 0) {
                Log::info('Found requirement folders for program', [
                    'program_id' => $programId,
                    'count' => count($requirementFolders)
                ]);
                
                // Format folder names for display
                $formattedFolders = [];
                foreach ($requirementFolders as $folder) {
                    // Remove tenant prefix and status tag from folder name for display
                    $displayName = preg_replace(['/^\[[^\]]+\]\s*/', '/\[(Regular|Irregular|Probation)\]\s*/'], '', $folder['name']);
                    
                    $formattedFolders[] = [
                        'id' => $folder['id'],
                        'name' => $displayName,
                        'original_name' => $folder['name'],
                        'url' => $folder['webViewLink'] ?? null
                    ];
                }
                
                $responseData['requirementFolders'] = $formattedFolders;
            } else {
                // If no folders found, inform client to use client-side fallback
                Log::warning('No requirement folders found for program and status', [
                    'program_id' => $programId,
                    'student_status' => $studentStatus,
                    'has_any_folders' => $hasSetupFolders
                ]);
                
                if ($hasSetupFolders) {
                    $responseData['message'] = "No folders found for '{$studentStatus}' status. Please ask your administrator to create folders with [{$studentStatus}] in the name.";
                } else {
                    $responseData['message'] = 'No requirement folders set up in the system. Please contact your administrator to set up document requirements.';
                }
            }
            
            return response()->json($responseData);
        } catch (\Exception $e) {
            Log::error('Error getting program requirements: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'program_id' => $programId
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load program requirements: ' . $e->getMessage()
            ]);
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
     * Get application details
     */
    public function getApplicationDetails($applicationId)
    {
        try {
            $studentId = Auth::guard('student')->id();
            
            // Get the application with proper tenant connection
            $application = StudentApplication::on('tenant')
                ->where('id', $applicationId)
                ->where('student_id', $studentId)
                ->first();
            
            if (!$application) {
                Log::warning('Student tried to access invalid application', [
                    'student_id' => $studentId,
                    'application_id' => $applicationId
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ]);
            }
            
            // Get program details if available
            $program = null;
            if ($application->program_id) {
                try {
                    $program = Course::on('tenant')->find($application->program_id);
                } catch (\Exception $e) {
                    Log::error('Error loading program for application: ' . $e->getMessage());
                }
            }
            
            // Format the response
            $response = [
                'success' => true,
                'application' => [
                    'id' => $application->id,
                    'program_id' => $application->program_id,
                    'program_name' => $program ? $program->name : 'Unknown Program',
                    'year_level' => $application->year_level,
                    'status' => $application->status,
                    'notes' => $application->notes,
                    'admin_notes' => $application->admin_notes,
                    'reviewed_at' => $application->reviewed_at ? $application->reviewed_at->format('Y-m-d H:i:s') : null,
                    'student_status' => $application->student_status,
                    'school_year_start' => $application->school_year_start,
                    'school_year_end' => $application->school_year_end,
                    'created_at' => $application->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $application->updated_at->format('Y-m-d H:i:s')
                ]
            ];
            
            return response()->json($response);
        } catch (\Exception $e) {
            Log::error('Error getting application details: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load application details'
            ]);
        }
    }
    
    /**
     * Get documents for a specific application
     */
    public function getApplicationDocuments($applicationId)
    {
        try {
            $studentId = Auth::guard('student')->id();
            $tenantId = tenant('id');
            
            // Get the application with proper tenant connection
            $application = StudentApplication::on('tenant')
                ->where('id', $applicationId)
                ->where('student_id', $studentId)
                ->first();
            
            if (!$application) {
                Log::warning('Student tried to access invalid application', [
                    'student_id' => $studentId,
                    'application_id' => $applicationId
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ]);
            }
            
            // Get documents from the application
            $documents = [];
            
            if (!empty($application->document_files_list)) {
                $documents = $application->document_files_list;
                
                // Filter out documents that don't belong to this tenant
                $documents = array_filter($documents, function($doc) use ($tenantId) {
                    // If the file_name exists and contains the tenant ID, include it
                    if (isset($doc['file_name']) && stripos($doc['file_name'], "[$tenantId]") !== false) {
                        return true;
                    }
                    
                    // Check folder_name too for tenant match
                    if (isset($doc['folder_name']) && stripos($doc['folder_name'], "[$tenantId]") !== false) {
                        return true;
                    }
                    
                    // If the document doesn't have tenant-specific info but is part of this application, include it
                    return !isset($doc['tenant_id']) || $doc['tenant_id'] === $tenantId;
                });
                
                // Process document metadata for display
                $documents = array_map(function($doc) use ($application) {
                    // Add uploaded_at if not exists
                    if (!isset($doc['uploaded_at'])) {
                        $doc['uploaded_at'] = $application->created_at->toIso8601String();
                    }
                    
                    // Clean up file name for display (remove tenant prefix)
                    if (isset($doc['file_name']) && isset($doc['display_name'])) {
                        // Keep as is if display name already exists
                    } else if (isset($doc['file_name'])) {
                        // Remove tenant prefix pattern for display
                        $displayName = preg_replace('/\[[^\]]+\]_App\d+_\w+_/', '', $doc['file_name']);
                        $doc['display_name'] = $displayName;
                    }
                    
                    return $doc;
                }, $documents);
                
                // Reset array keys after filtering
                $documents = array_values($documents);
            }
            
            Log::info('Retrieved application documents', [
                'application_id' => $application->id,
                'document_count' => count($documents)
            ]);
            
            // Add student status to the response
            return response()->json([
                'success' => true,
                'application_id' => $application->id,
                'documents' => $documents,
                'student_status' => $application->student_status,
                'tenant_id' => $tenantId
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting application documents: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load application documents'
            ]);
        }
    }

    /**
     * Debug method to check applications in the current tenant database
     */
    public function debugApplications()
    {
        try {
            $tenantId = tenant('id');
            $dbName = 'tenant_' . strtolower($tenantId);
            
            // Set the database connection config for tenant
            config([
                'database.connections.tenant.database' => $dbName
            ]);
            
            // Reconnect with the new config
            DB::reconnect('tenant');
            
            // Get the current student ID
            $student_id = Auth::guard('student')->id();
            
            // Check if the table exists
            $tableExists = DB::connection('tenant')->getSchemaBuilder()->hasTable('student_applications');
            
            // Debug info
            $debug = [
                'tenant_id' => $tenantId,
                'database' => $dbName,
                'table_exists' => $tableExists,
                'student_id' => $student_id
            ];
            
            // If table exists, get applications for this student
            if ($tableExists) {
                // Get raw query results
                $applications = DB::connection('tenant')
                    ->table('student_applications')
                    ->where('student_id', $student_id)
                    ->orderBy('created_at', 'desc')
                    ->get();
                
                $debug['applications_count'] = count($applications);
                $debug['applications'] = $applications;
            }
            
            return response()->json($debug);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
    
    /**
     * Check Google Drive connection status
     */
    public function checkDriveStatus()
    {
        try {
            // Get the Google Drive service 
            $driveService = app(\App\Services\GoogleDriveService::class);
            
            // Check connectivity by attempting to list files
            $testResult = $driveService->testConnection();
            
            return response()->json([
                'success' => $testResult['success'],
                'message' => $testResult['message'],
                'connected' => $testResult['success']
            ]);
        } catch (\Exception $e) {
            Log::error('Error checking Google Drive status: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to connect to Google Drive: ' . $e->getMessage(),
                'connected' => false
            ]);
        }
    }
}
