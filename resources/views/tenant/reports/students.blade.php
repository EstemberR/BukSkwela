@extends('tenant.layouts.app')

@section('title', 'Student Reports')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h4>Student Reports</h4>
            <div class="mt-2">
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
                                <h5 class="mb-0">Course Enrollment Trend</h5>
                            </div>
                            <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 350px;">
                                <div style="position: relative; height: 100%; width: 100%;">
                                    <canvas id="enrollmentTrendChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
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
                
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card shadow-sm">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Course Analysis</h5>
                            </div>
                            <div class="card-body">
                                <div class="row" id="courseCharts">
                                    @php
                                        $courses = $students->groupBy(function($student) {
                                            return $student->course ? $student->course->name : 'No Course';
                                        });
                                        $colorIndex = 0;
                                    @endphp
                                    
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

<!-- Chart.js script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(!$students->isEmpty())
    // Define professional color schemes
    const statusColors = {
        active: '#1cc88a',
        inactive: '#e74a3b'
    };
    
    // Define a color palette for courses
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
    
    // Students by Course Chart
    const studentsByCourseCtx = document.getElementById('studentsByCourseChart');
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
    
    const studentsByCourseChart = new Chart(studentsByCourseCtx, {
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
            responsive: true,
            maintainAspectRatio: false,
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
            },
            animation: {
                animateScale: true,
                animateRotate: true,
                duration: 1200
            }
        }
    });
    
    // Student Status Chart
    const studentStatusCtx = document.getElementById('studentStatusChart');
    const activeCount = {{ $students->where('status', 'active')->count() }};
    const inactiveCount = {{ $students->where('status', 'inactive')->count() }};
    
    const studentStatusChart = new Chart(studentStatusCtx, {
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
            responsive: true,
            maintainAspectRatio: false,
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
            },
            animation: {
                animateScale: true,
                animateRotate: true,
                duration: 1000
            }
        }
    });
    
    // Enrollment Trend Chart (by top 5 courses)
    const enrollmentTrendCtx = document.getElementById('enrollmentTrendChart');
    
    // Get top 5 courses by enrollment
    const topCourses = Object.entries(courseData.labels)
        .map(([index, label]) => ({ 
            label, 
            count: courseData.counts[index] 
        }))
        .sort((a, b) => b.count - a.count)
        .slice(0, 5);
        
    const enrollmentTrendChart = new Chart(enrollmentTrendCtx, {
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
            responsive: true,
            maintainAspectRatio: false,
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
                    },
                    title: {
                        display: true,
                        text: 'Top 5 Courses'
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
            },
            animation: {
                animateScale: true,
                animateRotate: true,
                duration: 1200
            }
        }
    });
    
    // Individual course status charts
    @php
        $colorIndex = 0;
        foreach($courses as $courseName => $courseStudents) {
            if ($courseStudents->isEmpty()) continue;
            $activeStudents = $courseStudents->where('status', 'active')->count();
            $inactiveStudents = $courseStudents->where('status', 'inactive')->count();
    @endphp
            
            const courseStatusChart{{ $colorIndex }} = new Chart(
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
    @endif
});
</script>
@endsection 