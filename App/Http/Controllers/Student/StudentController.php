<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student\Student;
use App\Models\Course\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Mail\StudentRegistered;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Helpers\PasswordGenerator;
use App\Mail\StudentCredentialsUpdated;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with('course');
        
        // Apply search filter if provided
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('student_id', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }
        
        // Apply course filter if provided
        if ($request->has('course_id') && !empty($request->course_id)) {
            $query->where('course_id', $request->course_id);
        }
        
        // Get paginated results
        $students = $query->paginate(10)->appends($request->query());
            
        // Ensure we're fetching with the correct connection
        $courses = Course::on('tenant')->where('status', 'active')->get();

        return view('tenant.students.index', compact('students', 'courses'));
    }

    public function store(Request $request)
    {
        // Use a custom validator to specify the connection
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|unique:tenant.students,student_id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenant.students,email',
            'course_id' => 'required|exists:tenant.courses,id',
            'status' => 'required|in:regular,probation,irregular,active,inactive',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Generate a secure password
        $password = PasswordGenerator::generate(random_int(10, 15));

        // Make sure we're connected to the tenant database
        DB::connection('tenant')->getPdo();

        try {
            // Create the student
            $student = Student::create([
                'student_id' => $request->student_id,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($password),
                'course_id' => $request->course_id,
                'status' => $request->status ?? 'regular',
            ]);

            // Send welcome email with credentials
            try {
                Mail::to($student->email)->send(new StudentRegistered($student, $password));
                return redirect()->route('tenant.students.index')
                    ->with('success', 'Student created successfully and welcome email sent');
            } catch (\Exception $e) {
                Log::error('Failed to send student registration email', ['error' => $e->getMessage()]);
                return redirect()->route('tenant.students.index')
                    ->with('warning', 'Student created but failed to send welcome email');
            }
        } catch (\Exception $e) {
            Log::error('Failed to create student', ['error' => $e->getMessage()]);
            return redirect()->back()
                ->with('error', 'Error creating student: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function update(Request $request, Student $student)
    {
        // Use a custom validator to specify the connection
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|unique:tenant.students,student_id,' . $student->id,
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenant.students,email,' . $student->id,
            'course_id' => 'required|exists:tenant.courses,id',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Track what fields were updated
        $updatedFields = [];
        $originalValues = $student->getOriginal();

        if ($student->student_id != $request->student_id) {
            $updatedFields['student_id'] = $request->student_id;
        }
        
        if ($student->name != $request->name) {
            $updatedFields['name'] = $request->name;
        }
        
        if ($student->email != $request->email) {
            $updatedFields['email'] = $request->email;
        }
        
        if ($student->course_id != $request->course_id) {
            $course = Course::find($request->course_id);
            if ($course) {
                $updatedFields['course'] = $course->name;
            }
        }

        // Update the student
        $student->update([
            'student_id' => $request->student_id,
            'name' => $request->name,
            'email' => $request->email,
            'course_id' => $request->course_id,
        ]);

        // Password update
        if ($request->filled('password')) {
            $student->update([
                'password' => Hash::make($request->password),
            ]);
            $updatedFields['password'] = 'Password has been updated';
        }

        // Send email notification if anything was updated
        try {
            if (!empty($updatedFields)) {
                Log::info('Sending credential update email', [
                    'student_id' => $student->id,
                    'email' => $student->email,
                    'updated_fields' => array_keys($updatedFields)
                ]);
                
                Mail::to($student->email)->send(new StudentCredentialsUpdated($student, $updatedFields));
                
                Log::info('Credential update email sent successfully', [
                    'student_id' => $student->id,
                    'email' => $student->email
                ]);
                
                return redirect()->route('tenant.students.index')
                    ->with('success', 'Student updated successfully and notification email sent');
            }
        } catch (\Exception $e) {
            Log::error('Failed to send credential update email', [
                'student_id' => $student->id,
                'email' => $student->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('tenant.students.index')
                ->with('warning', 'Student updated successfully but failed to send notification email');
        }

        return redirect()->route('tenant.students.index')
            ->with('success', 'Student updated successfully');
    }

    public function destroy(Student $student)
    {
        try {
            // Tenant ID check is removed since column doesn't exist
            
            Log::info('Deleting student', [
                'student_id' => $student->id,
                'student_name' => $student->name,
            ]);
            
            $student->delete();
            
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Student deleted successfully'
                ]);
            }
            
            return redirect()->route('tenant.students.index')
                ->with('success', 'Student deleted successfully');
        } catch (\Exception $e) {
            Log::error('Failed to delete student', [
                'student_id' => $student->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete student: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('tenant.students.index')
                ->with('error', 'Failed to delete student: ' . $e->getMessage());
        }
    }

    /**
     * Direct delete method that doesn't rely on model binding.
     */
    public function deleteDirectly($studentId)
    {
        try {
            // Ensure we're dealing with a number
            $studentId = intval($studentId);
            
            // Log received parameters
            Log::info('Delete request received', [
                'student_id' => $studentId,
                'request_url' => request()->fullUrl(),
                'request_method' => request()->method()
            ]);
            
            // Check if student exists
            $student = Student::where('id', $studentId)->first();
            
            if (!$student) {
                Log::warning('Student not found', [
                    'requested_id' => $studentId
                ]);
                
                return redirect()->route('tenant.students.index')
                    ->with('error', 'Student not found');
            }
            
            // Log the deletion
            Log::info('Directly deleting student', [
                'student_id' => $student->id,
                'student_name' => $student->name
            ]);
            
            // Perform the delete operation
            $student->delete();
            
            // Always redirect back to index
            return redirect()->route('tenant.students.index')
                ->with('success', 'Student deleted successfully');
                
        } catch (\Exception $e) {
            Log::error('Failed to directly delete student', [
                'student_id' => $studentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('tenant.students.index')
                ->with('error', 'Error deleting student: ' . $e->getMessage());
        }
    }

    /**
     * Test method to debug student lookup issues
     */
    public function testStudentLookup($studentId = null)
    {
        // Ensure we're dealing with a number if provided
        if ($studentId !== null) {
            $studentId = intval($studentId);
        }
        
        // Output format
        $output = [
            'requested_student_id' => $studentId,
            'request_url' => request()->fullUrl(),
            'request_method' => request()->method(),
            'all_students' => []
        ];
        
        // Get all students to check if any exist
        $allStudents = Student::all();
        foreach ($allStudents as $student) {
            $output['all_students'][] = [
                'id' => $student->id,
                'student_id' => $student->student_id,
                'name' => $student->name
            ];
        }
        
        // Check if specified student exists
        if ($studentId) {
            $studentFound = Student::where('id', $studentId)->first();
            
            if ($studentFound) {
                $output['student_exists_in_database'] = true;
                $output['student_details'] = [
                    'id' => $studentFound->id,
                    'student_id' => $studentFound->student_id,
                    'name' => $studentFound->name
                ];
            } else {
                $output['student_exists_in_database'] = false;
            }
        }
        
        // Return as JSON for easy reading
        return response()->json($output);
    }

    /**
     * Most direct delete method that takes the student_id from the request body.
     */
    public function deleteSimple(Request $request)
    {
        try {
            // Get student ID from request body
            $studentId = intval($request->input('student_id'));
            
            if (!$studentId) {
                return redirect()->route('tenant.students.index')
                    ->with('error', 'No student ID provided');
            }
            
            // Log received parameters
            Log::info('Simple delete request received', [
                'student_id' => $studentId,
                'student_id_type' => gettype($studentId),
                'request_url' => request()->fullUrl(),
                'request_method' => request()->method()
            ]);
            
            // Find the student
            $student = Student::where('id', $studentId)->first();
                
            if (!$student) {
                Log::warning('Student not found', [
                    'requested_id' => $studentId
                ]);
                
                return redirect()->route('tenant.students.index')
                    ->with('error', 'Student not found');
            }
            
            // Log the deletion
            Log::info('Simple delete for student', [
                'student_id' => $student->id,
                'student_name' => $student->name
            ]);
            
            // Perform the delete operation
            $student->delete();
            
            // Always redirect back to index
            return redirect()->route('tenant.students.index')
                ->with('success', 'Student deleted successfully');
                
        } catch (\Exception $e) {
            Log::error('Failed to delete student via simple method', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('tenant.students.index')
                ->with('error', 'Error deleting student: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified student directly via a simple route.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateDirectly(Request $request, $id)
    {
        // Find the student (try multiple approaches)
        $student = null;
        
        // First try the route parameter
        $student = Student::where('id', $id)->first();
        
        // If not found and we have student_db_id in the request, try that
        if (!$student && $request->has('student_db_id')) {
            $student = Student::where('id', $request->student_db_id)->first();
        }
        
        // If still not found, return with error
        if (!$student) {
            return redirect()->back()
                ->with('error', "Student not found with ID: {$id}");
        }

        // Validate student data with tenant prefix
        $validated = $request->validate([
            'student_id' => 'required|string|max:255|unique:tenant.students,student_id,'.$student->id,
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:tenant.students,email,'.$student->id,
            'course_id' => 'required|exists:tenant.courses,id',
            'status' => 'required|in:regular,probation,irregular,active,inactive',
        ]);

        // Track if any fields were updated
        $updated = false;

        // Update only if values are different
        foreach ($validated as $field => $value) {
            if ($student->{$field} != $value) {
                $student->{$field} = $value;
                $updated = true;
            }
        }

        // Save if there were changes
        if ($updated) {
            $student->save();
            
            // Send email if email is changed
            if ($student->wasChanged('email')) {
                // Send email notification about updated credentials
                try {
                    Mail::to($student->email)->send(new StudentCredentialsUpdated($student));
                } catch (\Exception $e) {
                    // Log the error but don't stop the process
                    \Log::error('Failed to send email to student: ' . $e->getMessage());
                }
            }
            
            return redirect()->route('tenant.students.index')
                ->with('success', 'Student updated successfully');
        }

        return redirect()->route('tenant.students.index')
            ->with('info', 'No changes were made to the student');
    }

    /**
     * Store a new student record directly without relying on model binding.
     */
    public function storeDirectly(Request $request)
    {
        try {
            // Log received parameters
            Log::info('Direct store request received', [
                'tenant_id' => tenant('id'),
                'request_url' => request()->fullUrl(),
                'request_method' => request()->method(),
                'course_id' => $request->course_id
            ]);
            
            // Check for available courses first
            $coursesCount = Course::on('tenant')->count();
            if ($coursesCount == 0) {
                return redirect()->route('tenant.students.index')
                    ->with('error', 'No courses available. Please create a course first.');
            }
            
            // Check if requested course exists
            $courseExists = Course::on('tenant')->where('id', $request->course_id)->exists();
            if (!$courseExists) {
                $availableCourses = Course::on('tenant')->get()->pluck('name', 'id')->toArray();
                $courseInfo = "Available courses: " . json_encode($availableCourses);
                Log::error('Invalid course selected', [
                    'requested_course_id' => $request->course_id,
                    'available_courses' => $availableCourses
                ]);
                return redirect()->route('tenant.students.index')
                    ->with('error', "The selected course_id ({$request->course_id}) is invalid. $courseInfo");
            }
            
            $request->validate([
                'student_id' => 'required|unique:tenant.students,student_id',
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:tenant.students,email',
                'course_id' => 'required|exists:tenant.courses,id',
                'status' => 'required|in:regular,probation,irregular,active,inactive',
            ]);

            Log::info('Creating new student directly', [
                'student_id' => $request->student_id,
                'email' => $request->email
            ]);

            // Generate a secure password
            $password = PasswordGenerator::generate(random_int(10, 15));

            $student = Student::create([
                'student_id' => $request->student_id,
                'name' => $request->name,
                'email' => $request->email,
                'course_id' => $request->course_id,
                'password' => Hash::make($password),
                'status' => $request->status ?? 'regular',
            ]);

            // Send welcome email to the student with their password
            try {
                Log::info('Attempting to send welcome email', [
                    'to' => $student->email,
                    'student_id' => $student->student_id
                ]);
                
                Mail::to($student->email)->send(new StudentRegistered($student, $password));
                
                Log::info('Welcome email sent successfully', [
                    'to' => $student->email,
                    'student_id' => $student->student_id
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send welcome email to student', [
                    'student_id' => $student->id,
                    'email' => $student->email,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                return redirect()->route('tenant.students.index')
                    ->with('warning', 'Student added successfully but failed to send welcome email. Error: ' . $e->getMessage());
            }

            return redirect()->route('tenant.students.index')
                ->with('success', 'Student added successfully and welcome email sent');
        } catch (\Exception $e) {
            Log::error('Failed to directly create student', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('tenant.students.index')
                ->with('error', 'Error creating student: ' . $e->getMessage());
        }
    }
} 