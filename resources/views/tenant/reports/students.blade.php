@extends('tenant.layouts.app')

@section('title', 'Student Reports')

@section('content')
<div class="container">
    <!-- Main Report Card -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Student Reports</h4>
            <div>
                <a href="{{ route('tenant.reports.students.pdf', ['tenant' => tenant('id')]) }}" class="btn btn-danger">
                    <i class="fas fa-file-pdf me-1"></i> Download PDF
                </a>
            </div>
        </div>
        
        <div class="card-body">
            @if($students->isEmpty())
                <div class="alert alert-info">
                    No student data available to display.
                </div>
            @else
                <!-- Summary Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card shadow-sm">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Student Summary</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="card bg-primary text-white">
                                            <div class="card-body text-center">
                                                <h3 class="mb-0">{{ $students->count() }}</h3>
                                                <p class="mb-0">Total Students</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-success text-white">
                                            <div class="card-body text-center">
                                                <h3 class="mb-0">{{ $students->where('status', 'active')->count() }}</h3>
                                                <p class="mb-0">Active Students</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-info text-white">
                                            <div class="card-body text-center">
                                                <h3 class="mb-0">{{ $students->groupBy('course_id')->count() }}</h3>
                                                <p class="mb-0">Courses with Students</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-warning text-white">
                                            <div class="card-body text-center">
                                                <h3 class="mb-0">{{ round($students->where('status', 'active')->count() / max(1, $students->count()) * 100) }}%</h3>
                                                <p class="mb-0">Active Rate</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Overview Charts Section -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Students by Course</h5>
                            </div>
                            <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 350px;">
                                <div style="position: relative; height: 100%; width: 100%;">
                                    <canvas id="studentsByCourseChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Student Status</h5>
                            </div>
                            <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 350px;">
                                <div style="position: relative; height: 100%; width: 100%;">
                                    <canvas id="studentStatusChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Top 5 Courses</h5>
                            </div>
                            <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 350px;">
                                <div style="position: relative; height: 100%; width: 100%;">
                                    <canvas id="enrollmentTrendChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Course Analysis Section -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card shadow-sm">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Course Analysis</h5>
                            </div>
                            <div class="card-body">
                                <!-- PHP Data Preparation -->
                                @php
                                    $courses = $students->groupBy(function($student) {
                                        return $student->course ? $student->course->name : 'No Course';
                                    });
                                    $colorIndex = 0;
                                @endphp
                                
                                <div class="row" id="courseCharts">
                                    @foreach($courses as $courseName => $courseStudents)
                                        @php
                                            // Skip if there are no students in this course
                                            if ($courseStudents->isEmpty()) continue;
                                        @endphp
                                        <div class="col-md-4 mb-4">
                                            <div class="card shadow-sm h-100">
                                                <div class="card-header bg-light">
                                                    <h5 class="mb-0">{{ $courseName }}</h5>
                                                </div>
                                                <div class="card-body d-flex flex-column">
                                                    <div class="text-center mb-3">
                                                        <h3 class="text-primary">{{ $courseStudents->count() }}</h3>
                                                        <p class="text-muted mb-0">Students Enrolled</p>
                                                    </div>
                                                    <div class="d-flex align-items-center justify-content-center flex-grow-1" style="min-height: 200px;">
                                                        <canvas id="courseStatusChart{{ $colorIndex }}"></canvas>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @php $colorIndex++; @endphp
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Chart.js dependency -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Charts initialization script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(!$students->isEmpty())
    // ----- Configuration & Utility -----
    // Color schemes
    const statusColors = {
        active: '#1cc88a',
        inactive: '#e74a3b'
    };
    
    // Color palette for charts
    const colorPalette = [
        '#4e73df', // Primary blue
        '#1cc88a', // Success green
        '#36b9cc', // Info teal
        '#f6c23e', // Warning yellow
        '#e74a3b', // Danger red
        '#6f42c1', // Purple
        '#fd7e14', // Orange
        '#20c9a6', // Teal variant
        '#5a5c69', // Gray
        '#858796', // Light gray
        '#2e59d9', // Royal blue
        '#17a673', // Forest green
    ];
    
    // Shared chart configuration
    const chartDefaults = {
        responsive: true,
        maintainAspectRatio: false,
        animation: {
            animateScale: true,
            animateRotate: true,
            duration: 1200
        }
    };
    
    // ----- Data Preparation -----
    // Course distribution data
    const courseData = {
        labels: [
            @php
                $courseGroups = $students->groupBy(function($student) {
                    return $student->course ? $student->course->name : 'No Course';
                });
                
                foreach($courseGroups as $course => $students) {
                    echo "'$course', ";
                }
            @endphp
        ],
        counts: [
            @php
                foreach($courseGroups as $course => $students) {
                    echo $students->count() . ", ";
                }
            @endphp
        ]
    };
    
    // Status data
    const activeCount = {{ $students->where('status', 'active')->count() }};
    const inactiveCount = {{ $students->where('status', 'inactive')->count() }};
    
    // ----- Chart Initialization -----
    // 1. Students by Course Chart
    initStudentsByCourseChart();
    
    // 2. Student Status Chart
    initStudentStatusChart();
    
    // 3. Top 5 Courses Chart
    initTopCoursesChart();
    
    // 4. Individual Course Status Charts
    initCourseStatusCharts();
    
    // ----- Chart Functions -----
    function initStudentsByCourseChart() {
        const ctx = document.getElementById('studentsByCourseChart');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: courseData.labels,
                datasets: [{
                    data: courseData.counts,
                    backgroundColor: colorPalette.slice(0, courseData.labels.length),
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                ...chartDefaults,
                layout: {
                    padding: 20
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            boxWidth: 12,
                            font: {
                                size: 11,
                                weight: 'bold'
                            },
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} students (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
    
    function initStudentStatusChart() {
        const ctx = document.getElementById('studentStatusChart');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Active Students', 'Inactive Students'],
                datasets: [{
                    data: [activeCount, inactiveCount],
                    backgroundColor: [statusColors.active, statusColors.inactive],
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                ...chartDefaults,
                layout: {
                    padding: 20
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            font: {
                                size: 11,
                                weight: 'bold'
                            },
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
    
    function initTopCoursesChart() {
        const ctx = document.getElementById('enrollmentTrendChart');
        
        // Get top 5 courses by enrollment
        const topCourses = Object.entries(courseData.labels)
            .map(([index, label]) => ({ 
                label, 
                count: courseData.counts[index] 
            }))
            .sort((a, b) => b.count - a.count)
            .slice(0, 5);
            
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: topCourses.map(course => course.label),
                datasets: [{
                    data: topCourses.map(course => course.count),
                    backgroundColor: colorPalette.slice(0, 5),
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                ...chartDefaults,
                layout: {
                    padding: 20
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            boxWidth: 12,
                            font: {
                                size: 11,
                                weight: 'bold'
                            },
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} students (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }
    
    function initCourseStatusCharts() {
        @php
            $colorIndex = 0;
            foreach($courses as $courseName => $courseStudents) {
                if ($courseStudents->isEmpty()) continue;
                $activeStudents = $courseStudents->where('status', 'active')->count();
                $inactiveStudents = $courseStudents->where('status', 'inactive')->count();
        @endphp
                
                new Chart(
                    document.getElementById('courseStatusChart{{ $colorIndex }}'),
                    {
                        type: 'pie',
                        data: {
                            labels: ['Active', 'Inactive'],
                            datasets: [{
                                data: [{{ $activeStudents }}, {{ $inactiveStudents }}],
                                backgroundColor: [statusColors.active, statusColors.inactive],
                                borderColor: '#ffffff',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        font: { size: 10 }
                                    }
                                },
                                title: {
                                    display: true,
                                    text: 'Student Status',
                                    font: { size: 12 }
                                }
                            }
                        }
                    }
                );
        
        @php
                $colorIndex++;
            }
        @endphp
    }
    @endif
});
</script>
@endsection 