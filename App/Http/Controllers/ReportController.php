<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Staff\Staff;
use App\Models\Student\Student;
use App\Models\Course\Course;
use App\Models\Requirements\Requirement;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function students()
    {
        $students = Student::with(['staff', 'courses'])
            ->where('tenant_id', tenant('id'))
            ->get();
            
        return view('tenant.reports.students', compact('students'));
    }

    public function staff()
    {
        $staff = Staff::withCount(['courses', 'students'])
            ->where('tenant_id', tenant('id'))
            ->get();
            
        return view('tenant.reports.staff', compact('staff'));
    }

    public function courses()
    {
        $courses = Course::with(['staff', 'students'])
            ->where('tenant_id', tenant('id'))
            ->get();
            
        return view('tenant.reports.courses', compact('courses'));
    }

    public function requirements()
    {
        $requirements = Requirement::with(['staff', 'student'])
            ->where('tenant_id', tenant('id'))
            ->get();
            
        return view('tenant.reports.requirements', compact('requirements'));
    }
}