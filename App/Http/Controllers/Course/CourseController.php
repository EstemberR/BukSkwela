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
        try {
            // Start with a query explicitly on the tenant connection
            $query = Course::on('tenant');

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
            
            // Log that we're viewing the courses
            \Log::info('Viewing courses index', [
                'search' => $request->get('search'),
                'status' => $request->get('status'),
                'count' => $courses->count()
            ]);

            return view('tenant.courses.index', compact('courses'));
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
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            // Generate a course code from the name
            $code = $this->generateCourseCode($request->name);
            
            // Log creation attempt
            \Log::info('Creating new course', [
                'name' => $request->name,
                'code' => $code,
                'tenant_id' => tenant('id')
            ]);

            // Create with explicit connection
            $course = new Course();
            $course->setConnection('tenant');
            $course->name = $request->name;
            $course->code = $code;
            $course->description = $request->description;
            $course->status = 'active';
            $course->save();
            
            // Log successful creation
            \Log::info('Course created successfully', [
                'course_id' => $course->id,
                'name' => $course->name
            ]);

            return redirect()->route('tenant.courses.index')
                ->with('success', 'Course created successfully');
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error creating course', [
                'name' => $request->name ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->route('tenant.courses.index')
                ->with('error', 'Error creating course: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Course $course)
    {
        try {
            // Ensure we're using the tenant database connection
            $course->setConnection('tenant');
            
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'status' => 'required|in:active,inactive'
            ]);

            $updateData = [
                'name' => $request->name,
                'description' => $request->description,
                'status' => $request->status
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
            $course->setConnection('tenant');
            
            // Get course details before deletion for logging
            $courseDetails = [
                'id' => $course->id,
                'name' => $course->name,
                'code' => $course->code ?? 'N/A'
            ];
            
            // Log the deletion attempt
            \Log::info('Attempting to delete course', $courseDetails);
            
            $course->delete();
            
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