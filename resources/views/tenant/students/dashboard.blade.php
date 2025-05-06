@extends('tenant.layouts.app')

@section('title', 'Student Dashboard')

@section('content')
<div class="container">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body d-flex align-items-center">
                    <img src="https://ui-avatars.com/api/?name={{ session('student_name', 'Student') }}&background=random&color=fff" alt="User" class="rounded-circle me-3" style="width: 60px; height: 60px; object-fit: cover;">
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
                            <thead class="bg-primary text-white">
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
                                    <td>MATH202</td>
                                    <td>Advanced Calculus</td>
                                    <td>Dr. Jane Wilson</td>
                                    <td>Tue, Thu 1:00-2:30 PM</td>
                                    <td>
                                        <div class="progress" style="height: 5px;">
                                            <div class="progress-bar bg-warning" role="progressbar" style="width: 45%;" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <small class="text-muted">45% Complete</small>
                                    </td>
                                    <td><a href="#" class="btn btn-sm btn-outline-primary">View</a></td>
                                </tr>
                                <tr>
                                    <td>ENG101</td>
                                    <td>Academic Writing</td>
                                    <td>Prof. Sarah Johnson</td>
                                    <td>Mon, Fri 8:30-10:00 AM</td>
                                    <td>
                                        <div class="progress" style="height: 5px;">
                                            <div class="progress-bar bg-info" role="progressbar" style="width: 60%;" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <small class="text-muted">60% Complete</small>
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

    <!-- Upcoming Assignments Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Upcoming Assignments</h5>
                    <a href="#" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Term Paper - Academic Writing</h6>
                                <small class="text-muted">Due: Oct 15, 2023</small>
                            </div>
                            <span class="badge bg-danger rounded-pill">2 days left</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Problem Set 3 - Advanced Calculus</h6>
                                <small class="text-muted">Due: Oct 20, 2023</small>
                            </div>
                            <span class="badge bg-warning rounded-pill">7 days left</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Programming Project - Introduction to Computer Science</h6>
                                <small class="text-muted">Due: Oct 25, 2023</small>
                            </div>
                            <span class="badge bg-info rounded-pill">12 days left</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Any specific JavaScript for the student dashboard can go here
        console.log('Student dashboard loaded');
    });
</script>
@endpush 