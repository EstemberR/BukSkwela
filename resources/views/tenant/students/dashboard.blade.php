@extends('tenant.layouts.student-app')

@section('title', 'Student Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex align-items-center">
                    <img src="{{ asset('images/avatar-placeholder.jpg') }}" alt="User" class="avatar me-3" style="width: 60px; height: 60px;">
                    <div>
                        <h4 class="mb-1">Welcome, {{ session('student_name', 'Student') }}</h4>
                        <p class="text-muted mb-0">Email: {{ session('student_email', 'student@example.com') }}</p>
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
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 