<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Staff\Staff;
use App\Models\Student\Student;
use App\Models\Requirements\Requirement;
use App\Models\Course\Course;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'instructorCount' => Staff::where('tenant_id', tenant('id'))->count(),
            'studentCount' => Student::where('tenant_id', tenant('id'))->count(),
            'pendingRequirements' => \DB::table('student_requirements')
                ->where('tenant_id', tenant('id'))
                ->where('status', 'pending')
                ->count(),
            'activeCourses' => Course::where('tenant_id', tenant('id'))
                ->where('status', 'active')
                ->count(),
            'students' => Student::where('tenant_id', tenant('id'))
                ->select('id', 'student_id', 'status')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get(),
            'courses' => Course::where('tenant_id', tenant('id'))
                ->with('staff')
                ->where('status', 'active')
                ->select('id', 'title', 'staff_id', 'status')
                ->withCount('students')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get(),
            'instructors' => Staff::where('tenant_id', tenant('id'))
                ->with(['courses' => function($query) {
                    $query->withCount('students');
                }])
                ->withCount(['courses'])
                ->get()
                ->map(function($staff) {
                    $staff->students_count = $staff->courses->sum('students_count');
                    return $staff;
                }),
            'requirementCategories' => \App\Models\Requirements\RequirementCategory::with(['requirements' => function($query) {
                $query->withCount(['students' => function($q) {
                    $q->wherePivot('status', 'pending');
                }]);
            }])
            ->where('tenant_id', tenant('id'))
            ->get(),
            'studentsByStatus' => Student::where('tenant_id', tenant('id'))
                ->with(['requirements' => function($query) {
                    $query->withPivot(['status', 'file_path', 'remarks']);
                }])
                ->get()
                ->groupBy('status')
        ];

        return view('tenant.dashboard')->with($data);
    }

    public function staffDashboard()
    {
        return view('tenant.staff.dashboard');
    }
}