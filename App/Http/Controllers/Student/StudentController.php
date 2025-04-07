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

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with('course')
            ->where('tenant_id', tenant('id'))
            ->paginate(10);
            
        $courses = Course::where('tenant_id', tenant('id'))->get();

        return view('tenant.students.index', compact('students', 'courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|unique:students,student_id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email',
            'course_id' => 'required|exists:courses,id',
        ]);

        Log::info('Creating new student', [
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
            'tenant_id' => tenant('id'),
            'status' => 'Regular',
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
            
            return redirect()->route('tenant.students.index', ['tenant' => tenant('id')])
                ->with('warning', 'Student added successfully but failed to send welcome email. Error: ' . $e->getMessage());
        }

        return redirect()->route('tenant.students.index', ['tenant' => tenant('id')])
            ->with('success', 'Student added successfully and welcome email sent');
    }

    public function update(Request $request, Student $student)
    {
        // Ensure the student belongs to the current tenant
        if ($student->tenant_id != tenant('id')) {
            return redirect()->route('tenant.students.index', ['tenant' => tenant('id')])
                ->with('error', 'Unauthorized access to student from another tenant');
        }

        $request->validate([
            'student_id' => 'required|unique:students,student_id,' . $student->id,
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email,' . $student->id,
            'course_id' => 'required|exists:courses,id',
        ]);

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
                $updatedFields['course'] = $course->title;
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
                
                return redirect()->route('tenant.students.index', ['tenant' => tenant('id')])
                    ->with('success', 'Student updated successfully and notification email sent');
            }
        } catch (\Exception $e) {
            Log::error('Failed to send credential update email', [
                'student_id' => $student->id,
                'email' => $student->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('tenant.students.index', ['tenant' => tenant('id')])
                ->with('warning', 'Student updated successfully but failed to send notification email');
        }

        return redirect()->route('tenant.students.index', ['tenant' => tenant('id')])
            ->with('success', 'Student updated successfully');
    }

    public function destroy(Student $student)
    {
        try {
            // Check if the student belongs to the current tenant
            if ($student->tenant_id !== tenant('id')) {
                Log::warning('Unauthorized deletion attempt for student', [
                    'student_id' => $student->id,
                    'requested_tenant' => tenant('id'),
                    'student_tenant' => $student->tenant_id
                ]);
                
                if (request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized deletion attempt'
                    ], 403);
                }
                
                return redirect()->route('tenant.students.index', ['tenant' => tenant('id')])
                    ->with('error', 'Unauthorized deletion attempt');
            }
            
            Log::info('Deleting student', [
                'student_id' => $student->id,
                'student_name' => $student->name,
                'tenant_id' => tenant('id')
            ]);
            
            $student->delete();
            
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Student deleted successfully'
                ]);
            }
            
            return redirect()->route('tenant.students.index', ['tenant' => tenant('id')])
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
            
            return redirect()->route('tenant.students.index', ['tenant' => tenant('id')])
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
                'tenant_id' => tenant('id'),
                'request_url' => request()->fullUrl(),
                'request_method' => request()->method()
            ]);
            
            // Check if student exists without tenant filter first
            $studentWithoutTenant = Student::where('id', $studentId)->first();
            
            if ($studentWithoutTenant) {
                Log::info('Student exists in database but may be in wrong tenant', [
                    'student_id' => $studentWithoutTenant->id,
                    'student_tenant_id' => $studentWithoutTenant->tenant_id,
                    'current_tenant_id' => tenant('id')
                ]);
            } else {
                Log::warning('Student does not exist in database at all', [
                    'requested_id' => $studentId
                ]);
            }
            
            // Find the student with tenant filter
            $student = Student::where('id', $studentId)
                ->where('tenant_id', tenant('id'))
                ->first();
                
            if (!$student) {
                Log::warning('Student not found or not authorized', [
                    'requested_id' => $studentId,
                    'tenant_id' => tenant('id')
                ]);
                
                return redirect()->route('tenant.students.index', ['tenant' => tenant('id')])
                    ->with('error', 'Student not found or not authorized');
            }
            
            // Log the deletion
            Log::info('Directly deleting student', [
                'student_id' => $student->id,
                'student_name' => $student->name,
                'tenant_id' => tenant('id')
            ]);
            
            // Perform the delete operation
            $student->delete();
            
            // Always redirect back to index
            return redirect()->route('tenant.students.index', ['tenant' => tenant('id')])
                ->with('success', 'Student deleted successfully');
                
        } catch (\Exception $e) {
            Log::error('Failed to directly delete student', [
                'student_id' => $studentId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('tenant.students.index', ['tenant' => tenant('id')])
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
        
        // Get current tenant
        $tenantId = tenant('id');
        
        // Output format
        $output = [
            'tenant_id' => $tenantId,
            'requested_student_id' => $studentId,
            'request_url' => request()->fullUrl(),
            'request_method' => request()->method(),
            'all_tenant_students' => []
        ];
        
        // Get all students for this tenant to check if any exist
        $allTenantStudents = Student::where('tenant_id', $tenantId)->get();
        foreach ($allTenantStudents as $student) {
            $output['all_tenant_students'][] = [
                'id' => $student->id,
                'student_id' => $student->student_id,
                'name' => $student->name,
                'tenant_id' => $student->tenant_id
            ];
        }
        
        // Check if specified student exists without tenant filter
        if ($studentId) {
            $studentWithoutTenant = Student::where('id', $studentId)->first();
            
            if ($studentWithoutTenant) {
                $output['student_exists_in_database'] = true;
                $output['student_details'] = [
                    'id' => $studentWithoutTenant->id,
                    'student_id' => $studentWithoutTenant->student_id,
                    'name' => $studentWithoutTenant->name,
                    'tenant_id' => $studentWithoutTenant->tenant_id
                ];
                
                if ($studentWithoutTenant->tenant_id === $tenantId) {
                    $output['student_belongs_to_current_tenant'] = true;
                } else {
                    $output['student_belongs_to_current_tenant'] = false;
                    $output['student_tenant_id'] = $studentWithoutTenant->tenant_id;
                }
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
                return redirect()->route('tenant.students.index', ['tenant' => tenant('id')])
                    ->with('error', 'No student ID provided');
            }
            
            // Log received parameters
            Log::info('Simple delete request received', [
                'student_id' => $studentId,
                'student_id_type' => gettype($studentId),
                'tenant_id' => tenant('id'),
                'request_url' => request()->fullUrl(),
                'request_method' => request()->method()
            ]);
            
            // Find the student
            $student = Student::where('id', $studentId)
                ->where('tenant_id', tenant('id'))
                ->first();
                
            if (!$student) {
                Log::warning('Student not found or not authorized', [
                    'requested_id' => $studentId,
                    'tenant_id' => tenant('id')
                ]);
                
                return redirect()->route('tenant.students.index', ['tenant' => tenant('id')])
                    ->with('error', 'Student not found or not authorized');
            }
            
            // Log the deletion
            Log::info('Simple delete for student', [
                'student_id' => $student->id,
                'student_name' => $student->name,
                'tenant_id' => tenant('id')
            ]);
            
            // Perform the delete operation
            $student->delete();
            
            // Always redirect back to index
            return redirect()->route('tenant.students.index', ['tenant' => tenant('id')])
                ->with('success', 'Student deleted successfully');
                
        } catch (\Exception $e) {
            Log::error('Failed to delete student via simple method', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('tenant.students.index', ['tenant' => tenant('id')])
                ->with('error', 'Error deleting student: ' . $e->getMessage());
        }
    }

    /**
     * The simplest possible update method with debugging.
     */
    public function updateDirectly(Request $request, $studentId)
    {
        // Log everything to help diagnose
        Log::info('Update request received', [
            'studentId' => $studentId,
            'student_db_id' => $request->input('student_db_id'),
            'debug_student_id' => $request->input('debug_student_id'),
            'all_params' => $request->all(),
            'server_params' => $_SERVER,
            'tenant_id' => tenant('id')
        ]);
        
        // Convert to integer
        $id = (int)$studentId;
        
        try {
            // First find all students to debug
            $allStudents = Student::where('tenant_id', tenant('id'))->get(['id', 'name', 'student_id', 'tenant_id']);
            Log::info('All students', ['count' => $allStudents->count(), 'students' => $allStudents->toArray()]);
            
            // Find the specific student
            $student = null;
            
            // Try with route parameter
            $student = Student::where('id', $id)
                ->where('tenant_id', tenant('id'))
                ->first();
                
            if (!$student && $request->has('student_db_id')) {
                // Try with form field
                $formId = (int)$request->input('student_db_id');
                $student = Student::where('id', $formId)
                    ->where('tenant_id', tenant('id'))
                    ->first();
                Log::info('Tried student_db_id from form', ['id' => $formId, 'found' => ($student ? 'yes' : 'no')]);
            }
            
            if (!$student) {
                // Everything failed
                return redirect()->route('tenant.students.index', ['tenant' => tenant('id')])
                    ->with('error', 'Student not found. ID: ' . $id);
            }
            
            // We found the student, now update
            $request->validate([
                'student_id' => 'required|unique:students,student_id,' . $student->id,
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:students,email,' . $student->id,
                'course_id' => 'required|exists:courses,id',
            ]);

            // Track what fields were updated
            $updatedFields = [];

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
                    $updatedFields['course'] = $course->title;
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
            
            // Log the successful update
            Log::info('Student updated successfully', [
                'student_id' => $student->id,
                'name' => $student->name,
                'updated_fields' => $updatedFields
            ]);

            // Send email notification if anything was updated
            if (!empty($updatedFields)) {
                try {
                    Mail::to($student->email)->send(new StudentCredentialsUpdated($student, $updatedFields));
                    return redirect()->route('tenant.students.index', ['tenant' => tenant('id')])
                        ->with('success', 'Student updated successfully and notification email sent');
                } catch (\Exception $e) {
                    Log::error('Failed to send email', ['error' => $e->getMessage()]);
                    return redirect()->route('tenant.students.index', ['tenant' => tenant('id')])
                        ->with('warning', 'Student updated but failed to send email');
                }
            }

            return redirect()->route('tenant.students.index', ['tenant' => tenant('id')])
                ->with('success', 'Student updated successfully');
                
        } catch (\Exception $e) {
            Log::error('Failed to update student', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('tenant.students.index', ['tenant' => tenant('id')])
                ->with('error', 'Error updating student: ' . $e->getMessage());
        }
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
                'request_method' => request()->method()
            ]);
            
            $request->validate([
                'student_id' => 'required|unique:students,student_id',
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:students,email',
                'course_id' => 'required|exists:courses,id',
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
                'tenant_id' => tenant('id'),
                'status' => 'Regular',
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
                
                return redirect()->route('tenant.students.index', ['tenant' => tenant('id')])
                    ->with('warning', 'Student added successfully but failed to send welcome email. Error: ' . $e->getMessage());
            }

            return redirect()->route('tenant.students.index', ['tenant' => tenant('id')])
                ->with('success', 'Student added successfully and welcome email sent');
        } catch (\Exception $e) {
            Log::error('Failed to directly create student', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('tenant.students.index', ['tenant' => tenant('id')])
                ->with('error', 'Error creating student: ' . $e->getMessage());
        }
    }
} 