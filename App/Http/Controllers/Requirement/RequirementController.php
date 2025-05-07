<?php

namespace App\Http\Controllers\Requirement;

use App\Http\Controllers\Controller;
use App\Models\Requirements\Requirement;
use App\Models\Requirements\StudentRequirement;
use App\Models\Student\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RequirementController extends Controller
{
    public function index()
    {
        $requirements = Requirement::withCount([
            'students' => function($query) {
                $query->whereHas('requirements', function($q) {
                    $q->where('student_requirements.status', 'pending');
                });
            }
        ])
        ->where('tenant_id', tenant('id'))
        ->get()
        ->groupBy('student_category');

        $students = Student::with('requirements')
            ->where('tenant_id', tenant('id'))
            ->whereIn('status', ['Regular', 'Irregular', 'Probation'])
            ->get()
            ->groupBy(function($student) {
                return strtolower($student->status);
            });

        // Ensure all status groups exist even if empty
        $students = collect([
            'regular' => $students['regular'] ?? collect(),
            'irregular' => $students['irregular'] ?? collect(),
            'probation' => $students['probation'] ?? collect(),
        ]);

        return view('tenant.requirements.index', compact('requirements', 'students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file_type' => 'required|in:pdf,doc,image',
            'is_required' => 'required|boolean',
            'student_category' => 'required|in:Regular,Irregular,Probation'
        ]);

        // Create the requirement
        $requirement = Requirement::create([
            'name' => $request->name,
            'description' => $request->description,
            'student_category' => $request->student_category,
            'file_type' => $request->file_type,
            'is_required' => $request->boolean('is_required'),
            'tenant_id' => tenant('id')
        ]);

        // Get all students in the selected category
        $students = Student::where('status', $request->student_category)
            ->get();

        // Create student requirements for each student
        foreach ($students as $student) {
            StudentRequirement::create([
                'student_id' => $student->id,
                'requirement_id' => $requirement->id,
                'status' => 'pending',
                'tenant_id' => tenant('id'),
                'file_path' => null // Set default null value for file_path
            ]);
        }

        return redirect()->route('tenant.requirements.index', ['tenant' => tenant('id')])
            ->with('success', 'Requirement added successfully and assigned to ' . $students->count() . ' students.');
    }

    public function updateStatus(Request $request, StudentRequirement $studentRequirement)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'remarks' => 'nullable|string|required_if:status,rejected'
        ]);

        $studentRequirement->update([
            'status' => $request->status,
            'remarks' => $request->remarks
        ]);

        return response()->json(['success' => true]);
    }

    public function uploadFile(Request $request, Student $student, Requirement $requirement)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240'
        ]);

        $file = $request->file('file');
        $path = $file->store('requirements/' . tenant('id'), 'public');

        $studentRequirement = StudentRequirement::firstOrNew([
            'student_id' => $student->id,
            'requirement_id' => $requirement->id,
            'tenant_id' => tenant('id')
        ]);

        $studentRequirement->file_path = $path;
        $studentRequirement->status = 'pending';
        $studentRequirement->save();

        return response()->json(['success' => true]);
    }

    public function getStudentRequirements(Student $student)
    {
        try {
            // Get all requirements for the student's category
            $requirements = Requirement::where('student_category', $student->status)
                ->where('tenant_id', tenant('id'))
                ->get();

            // Get or create student requirements for each requirement
            foreach ($requirements as $requirement) {
                StudentRequirement::firstOrCreate([
                    'student_id' => $student->id,
                    'requirement_id' => $requirement->id,
                    'tenant_id' => tenant('id')
                ], [
                    'status' => 'pending',
                    'file_path' => null
                ]);
            }

            // Get the student's requirements with their status
            $studentRequirements = $student->requirements()
                ->where('student_category', $student->status)
                ->wherePivot('tenant_id', tenant('id'))
                ->get();

            // Log the requirements for debugging
            \Log::info('Student Requirements:', [
                'student_id' => $student->id,
                'student_status' => $student->status,
                'requirements_count' => $studentRequirements->count(),
                'requirements' => $studentRequirements->toArray()
            ]);

            return response()->json([
                'success' => true,
                'student' => [
                    'id' => $student->id,
                    'student_id' => $student->student_id,
                    'name' => $student->name,
                    'status' => $student->status,
                    'course' => $student->course ? $student->course->name : 'N/A',
                    'year_level' => $student->year_level
                ],
                'requirements' => $studentRequirements
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getStudentRequirements:', [
                'error' => $e->getMessage(),
                'student_id' => $student->id
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch student requirements',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Requirement $requirement)
    {
        // Delete associated files
        foreach ($requirement->studentRequirements as $studentRequirement) {
            Storage::disk('public')->delete($studentRequirement->file_path);
        }

        $requirement->delete();

        return response()->json(['success' => true]);
    }

    public function showStudentRequirements()
    {
        // Get the currently authenticated student
        $student = auth()->user();
        
        // Get all requirements for the student's category
        $requirements = Requirement::where('student_category', $student->status)
            ->where('tenant_id', tenant('id'))
            ->get();

        // Ensure all requirements are assigned to the student
        foreach ($requirements as $requirement) {
            StudentRequirement::firstOrCreate([
                'student_id' => $student->id,
                'requirement_id' => $requirement->id,
                'tenant_id' => tenant('id')
            ], [
                'status' => 'pending',
                'file_path' => null
            ]);
        }

        // Get the student's requirements with their status
        $studentRequirements = $student->requirements()
            ->where('student_category', $student->status)
            ->wherePivot('tenant_id', tenant('id'))
            ->get()
            ->groupBy(function($requirement) {
                return $requirement->pivot->status;
            });

        // Calculate completion statistics
        $totalRequirements = $requirements->count();
        $completedRequirements = $studentRequirements->get('approved', collect())->count();
        $pendingRequirements = $studentRequirements->get('pending', collect())->count();
        $rejectedRequirements = $studentRequirements->get('rejected', collect())->count();

        return view('tenant.requirements.student', compact(
            'student',
            'studentRequirements',
            'totalRequirements',
            'completedRequirements',
            'pendingRequirements',
            'rejectedRequirements'
        ));
    }
} 