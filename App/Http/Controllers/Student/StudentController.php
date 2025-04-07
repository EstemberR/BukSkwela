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
        // Use a custom validator to specify the connection
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|unique:tenant.students,student_id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenant.students,email',
            'course_id' => 'required|exists:tenant.courses,id',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Log::info('Creating new student', [
            'student_id' => $request->student_id,
            'email' => $request->email,
            'tenant_id' => tenant('id')
        ]);

        // Generate a secure password
        $password = PasswordGenerator::generate(random_int(10, 15));

        // Make sure we're connected to the tenant database
        DB::connection('tenant')->getPdo();

        $student = Student::create([
            'student_id' => $request->student_id,
            'name' => $request->name,
            'email' => $request->email,
            'course_id' => $request->course_id,
            'password' => Hash::make($password),
            'tenant_id' => tenant('id'),
            'status' => 'active', // Changed from 'Regular' to match status options
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
                $updatedFields['course'] = $course->title ?: $course->name;
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
            // Log the deletion attempt
            Log::info('Attempting to delete student', [
                'student_id' => $student->student_id,
                'name' => $student->name,
                'email' => $student->email,
                'tenant_id' => tenant('id'),
                'student_tenant_id' => $student->tenant_id
            ]);

            // Check if the student belongs to the current tenant
            if ($student->tenant_id !== tenant('id')) {
                Log::warning('Unauthorized deletion attempt - tenant mismatch', [
                    'student_tenant_id' => $student->tenant_id,
                    'current_tenant_id' => tenant('id')
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized deletion attempt'
                ], 403);
            }

            $student->delete();

            // Log successful deletion
            Log::info('Student deleted successfully', [
                'student_id' => $student->student_id,
                'tenant_id' => tenant('id')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Student deleted successfully'
            ]);

        } catch (\Exception $e) {
            // Log the error
            Log::error('Failed to delete student', [
                'student_id' => $student->student_id ?? null,
                'tenant_id' => tenant('id'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete student: ' . $e->getMessage()
            ], 500);
        }
    }
} 