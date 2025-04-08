<?php

namespace App\Http\Controllers\Requirement;

use App\Http\Controllers\Controller;
use App\Models\Requirements\Requirement;
use App\Models\Requirements\RequirementCategory;
use App\Models\Requirements\StudentRequirement;
use App\Models\Student\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RequirementController extends Controller
{
    public function index()
    {
        $categories = RequirementCategory::with(['requirements' => function ($query) {
            $query->withCount(['students' => function ($q) {
                $q->where('student_requirements.status', 'pending');
            }]);
        }])
        ->where('tenant_id', tenant('id'))
        ->get();

        $students = Student::with(['requirements' => function ($query) {
            $query->withPivot(['status', 'file_path', 'remarks']);
        }])
        ->get()
        ->groupBy('status'); // Group by student status (Probation, Irregular, Regular)

        return view('tenant.requirements.index', compact('categories', 'students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:requirement_categories,id',
            'file_type' => 'required|string',
            'is_required' => 'boolean'
        ]);

        Requirement::create([
            'name' => $request->name,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'file_type' => $request->file_type,
            'is_required' => $request->is_required ?? true,
            'tenant_id' => tenant('id')
        ]);

        return redirect()->route('tenant.requirements.index', ['tenant' => tenant('id')])
            ->with('success', 'Requirement added successfully');
    }

    public function uploadRequirement(Request $request, Student $student, Requirement $requirement)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240' // 10MB max
        ]);

        $file = $request->file('file');
        $path = $file->store('requirements/' . tenant('id'), 'public');

        StudentRequirement::updateOrCreate(
            [
                'student_id' => $student->id,
                'requirement_id' => $requirement->id,
                'tenant_id' => tenant('id')
            ],
            [
                'file_path' => $path,
                'status' => 'pending'
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Requirement uploaded successfully'
        ]);
    }

    public function updateStatus(Request $request, StudentRequirement $studentRequirement)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'remarks' => 'required_if:status,rejected|nullable|string'
        ]);

        $studentRequirement->update([
            'status' => $request->status,
            'remarks' => $request->remarks
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Requirement status updated successfully'
        ]);
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
} 