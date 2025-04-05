<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
        return view('tenant.reports.courses');
    }

    public function requirements()
    {
        return view('tenant.reports.requirements');
    }
} 