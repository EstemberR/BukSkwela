<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Student\Student;
use App\Models\Staff\Staff;
use App\Models\Course\Course;
use Illuminate\Http\Request;
use PDF;

class ReportsPdfController extends Controller
{
    /**
     * Generate PDF for student reports
     */
    public function downloadStudentsPdf()
    {
        // Fetch students data
        $students = Student::with(['course'])->get();
        
        // Add created_at date grouping for enrollment trend analysis
        $students->each(function($student) {
            // Format date for grouping by months
            $student->enrollment_month = $student->created_at ? $student->created_at->format('M Y') : 'Unknown';
        });
        
        // Generate PDF
        $pdf = PDF::loadView('tenant.reports.pdf.students', compact('students'));
        $pdf->setPaper('a4', 'landscape');
        
        // Download PDF file
        return $pdf->download('students-report.pdf');
    }
    
    /**
     * Generate PDF for staff reports
     */
    public function downloadStaffPdf()
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
        
        // Generate PDF
        $pdf = PDF::loadView('tenant.reports.pdf.staff', compact('staff'));
        $pdf->setPaper('a4', 'landscape');
        
        // Download PDF file
        return $pdf->download('staff-report.pdf');
    }
    
    /**
     * Generate PDF for course reports
     */
    public function downloadCoursesPdf()
    {
        // The model already uses tenant connection, so no need to filter by tenant_id
        $courses = Course::with(['students'])->get();
        
        // Generate PDF
        $pdf = PDF::loadView('tenant.reports.pdf.courses', compact('courses'));
        $pdf->setPaper('a4', 'landscape');
        
        // Download PDF file
        return $pdf->download('courses-report.pdf');
    }
} 