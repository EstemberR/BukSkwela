@extends('tenant.layouts.student-app')

@section('title', 'Student Dashboard')

@section('content')
@php
// Import required classes
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Helpers\TenantConnection;

// Ensure database connection is correct
try {
    // Set up tenant connection
    TenantConnection::setup();
    
    // Try to get student data
    try {
        $student = Auth::guard('student')->user();
        if (!$student && session('student_id')) {
            $student = DB::connection('tenant')
                ->table('students')
                ->where('id', session('student_id'))
                ->first();
        }
        
        if ($student) {
            Log::info('Student retrieved for dashboard', [
                'id' => $student->id,
                'method' => isset($student->getAuthIdentifierName) ? 'Auth' : 'Direct DB'
            ]);
        } else {
            Log::warning('No student data found for dashboard');
        }
    } catch (\Exception $e) {
        Log::error('Error getting student data for dashboard: ' . $e->getMessage());
        $student = null;
    }
} catch (\Exception $e) {
    Log::error('Dashboard database connection error: ' . $e->getMessage());
    echo '<div class="alert alert-danger">Error connecting to database: ' . $e->getMessage() . '</div>';
    $student = null;
}

// If still can't get student, try using session data
if (!isset($student) || !$student) {
    $student = (object) [
        'name' => session('student_name', 'Student'),
        'student_id' => session('student_id', 'Unknown'),
        'email' => session('student_email', 'student@example.com')
    ];
}
@endphp

<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex align-items-center">
                    <img src="{{ asset('images/avatar-placeholder.jpg') }}" alt="User" class="avatar me-3" style="width: 60px; height: 60px;">
                    <div>
                        <h4 class="mb-1">Welcome, {{ $student->name }}</h4>
                        <p class="text-muted mb-0">Student ID: {{ $student->student_id }} | Email: {{ $student->email }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Enrolled Courses</h5>
                        <i class="fas fa-book-open fa-2x text-primary"></i>
                    </div>
                    <h2 class="mb-0">5</h2>
                    <p class="text-muted">Current Semester</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Attendance</h5>
                        <i class="fas fa-calendar-check fa-2x text-success"></i>
                    </div>
                    <h2 class="mb-0">92%</h2>
                    <p class="text-muted">Overall</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">GPA</h5>
                        <i class="fas fa-chart-line fa-2x text-warning"></i>
                    </div>
                    <h2 class="mb-0">3.75</h2>
                    <p class="text-muted">Current Semester</p>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">Coming Due</h5>
                        <i class="fas fa-calendar-alt fa-2x text-danger"></i>
                    </div>
                    <h2 class="mb-0">3</h2>
                    <p class="text-muted">Assignments</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Courses Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Current Courses</h5>
                    <a href="#" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Course Code</th>
                                    <th>Course Name</th>
                                    <th>Instructor</th>
                                    <th>Schedule</th>
                                    <th>Progress</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>CS101</td>
                                    <td>Introduction to Computer Science</td>
                                    <td>Dr. John Smith</td>
                                    <td>Mon, Wed 10:00-11:30 AM</td>
                                    <td>
                                        <div class="progress" style="height: 5px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: 75%;" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <small class="text-muted">75% Complete</small>
                                    </td>
                                    <td><a href="#" class="btn btn-sm btn-outline-primary">View</a></td>
                                </tr>
                                <tr>
                                    <td>MATH201</td>
                                    <td>Calculus II</td>
                                    <td>Prof. Maria Garcia</td>
                                    <td>Tue, Thu 1:00-2:30 PM</td>
                                    <td>
                                        <div class="progress" style="height: 5px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: 60%;" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <small class="text-muted">60% Complete</small>
                                    </td>
                                    <td><a href="#" class="btn btn-sm btn-outline-primary">View</a></td>
                                </tr>
                                <tr>
                                    <td>ENG105</td>
                                    <td>Technical Writing</td>
                                    <td>Dr. Robert Johnson</td>
                                    <td>Wed, Fri 3:00-4:30 PM</td>
                                    <td>
                                        <div class="progress" style="height: 5px;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: 85%;" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <small class="text-muted">85% Complete</small>
                                    </td>
                                    <td><a href="#" class="btn btn-sm btn-outline-primary">View</a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Sections -->
    <div class="row">
        <!-- Upcoming Deadlines Section -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Upcoming Deadlines</h5>
                    <a href="#" class="btn btn-sm btn-primary">View Calendar</a>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Algorithm Analysis Assignment</h6>
                                <small class="text-muted">CS202 - Due in 2 days</small>
                            </div>
                            <span class="badge bg-danger p-2">Urgent</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Research Paper Draft</h6>
                                <small class="text-muted">ENG105 - Due in 5 days</small>
                            </div>
                            <span class="badge bg-warning p-2">Medium</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Midterm Exam</h6>
                                <small class="text-muted">MATH201 - Due in 7 days</small>
                            </div>
                            <span class="badge bg-primary p-2">Important</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Group Project Submission</h6>
                                <small class="text-muted">CS101 - Due in 10 days</small>
                            </div>
                            <span class="badge bg-info p-2">Upcoming</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Recent Activities Section -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Recent Activities</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item pb-3">
                            <div class="d-flex">
                                <div class="timeline-icon me-3">
                                    <i class="fas fa-file-alt p-2 bg-primary text-white rounded-circle"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Assignment Graded</h6>
                                    <p class="mb-1">Your CS101 Programming Assignment was graded: 95/100</p>
                                    <small class="text-muted">Today, 10:30 AM</small>
                                </div>
                            </div>
                        </div>
                        <div class="timeline-item pb-3">
                            <div class="d-flex">
                                <div class="timeline-icon me-3">
                                    <i class="fas fa-book p-2 bg-success text-white rounded-circle"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">New Course Material</h6>
                                    <p class="mb-1">New learning materials uploaded for MATH201</p>
                                    <small class="text-muted">Yesterday, 3:45 PM</small>
                                </div>
                            </div>
                        </div>
                        <div class="timeline-item pb-3">
                            <div class="d-flex">
                                <div class="timeline-icon me-3">
                                    <i class="fas fa-comment p-2 bg-info text-white rounded-circle"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Forum Discussion</h6>
                                    <p class="mb-1">Your post received 5 replies in ENG105 discussion board</p>
                                    <small class="text-muted">2 days ago, 11:20 AM</small>
                                </div>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="d-flex">
                                <div class="timeline-icon me-3">
                                    <i class="fas fa-calendar-check p-2 bg-warning text-white rounded-circle"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Attendance Recorded</h6>
                                    <p class="mb-1">Your attendance was marked for CS202 lecture</p>
                                    <small class="text-muted">3 days ago, 9:15 AM</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
