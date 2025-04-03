<?php

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Models\Course\Course;
use App\Models\Staff\Staff;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with(['staff'])
            ->where('tenant_id', tenant('id'))
            ->paginate(10);
            
        $instructors = Staff::where('role', 'instructor')
            ->where('tenant_id', tenant('id'))
            ->get();

        return view('tenant.courses.index', compact('courses', 'instructors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'staff_id' => 'required|exists:staff,id'
        ]);

        Course::create([
            'title' => $request->title,
            'description' => $request->description,
            'staff_id' => $request->staff_id,
            'status' => 'active',
            'tenant_id' => tenant('id')
        ]);

        return redirect()->route('tenant.courses.index', ['tenant' => tenant('id')])
            ->with('success', 'Course created successfully');
    }

    public function update(Request $request, Course $course)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'staff_id' => 'required|exists:staff,id',
            'status' => 'required|in:active,inactive'
        ]);

        $course->update([
            'title' => $request->title,
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
} 