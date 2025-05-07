<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Http\Request;
use App\Models\Student;

class StudentController extends AdminController
{
    /**
     * Display a listing of the students.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Placeholder - in a real implementation, you would query the tenant's database
        // For now, we'll just display an empty view
        
        return view('admin.students.index', $this->withTenantData([
            'students' => []
        ]));
    }
} 