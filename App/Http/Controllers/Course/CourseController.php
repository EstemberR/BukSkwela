<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Models\Course\Course;
use App\Models\Staff\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::query();

        // Apply search filter
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if ($request->has('status') && $request->get('status') !== '') {
            $query->where('status', $request->get('status'));
        }

        $courses = $query->paginate(10);
        $instructors = Staff::where('role', 'instructor')->get();

        return view('tenant.courses.index', compact('courses', 'instructors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Generate a course code from the name
        $code = $this->generateCourseCode($request->name);

        Course::create([
            'title' => $request->name,       // Map 'name' from form to 'title' in database
            'name' => $request->name,        // Also store in 'name' for backward compatibility
            'code' => $code,                 // Auto-generated course code
            'description' => $request->description,
            'staff_id' => null,
            'status' => 'active',
            'tenant_id' => tenant('id')
        ]);

        return redirect()->route('tenant.courses.index', ['tenant' => tenant('id')])
            ->with('success', 'Course created successfully');
    }

    public function update(Request $request, Course $course)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'staff_id' => 'required|exists:staff,id',
            'status' => 'required|in:active,inactive'
        ]);

        $course->update([
            'title' => $request->name,      // Map 'name' from form to 'title' in database
            'name' => $request->name,       // Also update 'name' field
            'description' => $request->description,
            'staff_id' => $request->staff_id,
            'status' => $request->status
        ]);

        return redirect()->route('tenant.courses.index', ['tenant' => tenant('id')])
            ->with('success', 'Course updated successfully');
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return response()->json(['success' => true]);
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
} 