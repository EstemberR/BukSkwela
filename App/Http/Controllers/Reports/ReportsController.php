<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course\Course;
use App\Models\Staff\Staff;
use App\Models\Student\Student;

class ReportsController extends Controller
{
    public function students()
    {
        return view('tenant.reports.students');
    }

    public function staff()
    {
        return view('tenant.reports.staff');
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
        return view('tenant.reports.requirements');
    }
} 