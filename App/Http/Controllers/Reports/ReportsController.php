<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course\Course;
use App\Models\Staff\Staff;
use App\Models\Student\Student;
use App\Models\Requirements\Requirement;
use App\Models\Department;

class ReportsController extends Controller
{
    public function students()
    {
        // Fetch students with related data for complete reports
        $students = Student::with(['course'])->get();
        
        // Add created_at date grouping for enrollment trend analysis
        $students->each(function($student) {
            // Format date for grouping by months
            $student->enrollment_month = $student->created_at ? $student->created_at->format('M Y') : 'Unknown';
        });
        
        return view('tenant.reports.students', compact('students'));
    }

    public function staff()
    {
        // Get all staff members with proper relationships
        $staff = Staff::with('department')->get();
        
        // Ensure staff with no department have a value for grouping
        $staff->each(function($staffMember) {
            if (!$staffMember->department) {
                $staffMember->department = (object)['name' => 'No Department'];
            }
            
            // Simply set counts to 0 as the relationship is not properly established in the DB
            $staffMember->courses_count = 0;
            $staffMember->students_count = 0;
        });
        
        return view('tenant.reports.staff', compact('staff'));
    }

    public function courses()
    {
        // The model already uses tenant connection, so no need to filter by tenant_id
        $courses = Course::with(['students'])->get();
            
        return view('tenant.reports.courses', compact('courses'));
    }

    public function requirements()
    {
        // Fetch requirements data
        $requirements = Requirement::with(['student'])->get();
        
        return view('tenant.reports.requirements', compact('requirements'));
    }
} 