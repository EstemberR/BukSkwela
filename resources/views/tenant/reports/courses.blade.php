@extends('tenant.layouts.app')

@section('title', 'Course Reports')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h4>Courses Reports</h4>
            <div class="mt-2">
                <a href="{{ route('tenant.reports.courses.pdf', ['tenant' => tenant('id')]) }}" class="btn btn-danger">
                    <i class="fas fa-file-pdf me-1"></i> Download PDF
                </a>
            </div>
        </div>
        <div class="card-body">
            @if($courses->isEmpty())
                <div class="alert alert-info">
                    No course data available to display.
                </div>
            @else
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Students Enrolled Distribution</h5>
                            </div>
                            <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 350px;">
                                <div style="position: relative; height: 100%; width: 100%;">
                                    <canvas id="studentsPerCourseChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Active vs Inactive Courses</h5>
                            </div>
                            <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 350px;">
                                <div style="position: relative; height: 100%; width: 100%;">
                                    <canvas id="courseStatusChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <h4 class="mt-5 mb-4">Individual Course Enrollment</h4>
                
                <div class="row">
                    @foreach($courses as $course)
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">{{ $course->code ?? '' }} {{ $course->name ?? 'Untitled Course' }}</h5>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <div class="mb-3">
                                    <span class="badge bg-{{ $course->status == 'active' ? 'success' : 'warning' }}">
                                        {{ ucfirst($course->status ?? 'unknown') }}
                                    </span>
                                </div>
                                <div class="d-flex align-items-center justify-content-center flex-grow-1" style="min-height: 250px;">
                                    <div style="position: relative; height: 100%; width: 100%;">
                                        <canvas id="courseChart{{ $course->id }}"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Move the Chart.js scripts directly into the content section -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(!$courses->isEmpty())
    // Define a professional color palette for the charts
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

    const studentsPerCourseCtx = document.getElementById('studentsPerCourseChart');
    const courseLabels = [
        @foreach($courses as $course)
            "{{ ($course->code ?? '') }} {{ substr(($course->name ?? 'Untitled Course'), 0, 20) }}{{ strlen($course->name ?? '') > 20 ? '...' : '' }}",
        @endforeach
    ];
    
    const studentCounts = [
        @foreach($courses as $course)
            {{ $course->students->count() }},
        @endforeach
    ];
    
    // Students per Course Chart - Professional configuration
    const studentsPerCourseChart = new Chart(studentsPerCourseCtx, {
        type: 'pie',
        data: {
            labels: courseLabels,
            datasets: [{
                data: studentCounts,
                backgroundColor: colorPalette.slice(0, courseLabels.length),
                borderColor: '#ffffff',
                borderWidth: 2,
                hoverBackgroundColor: colorPalette.map(color => {
                    // Make hover colors slightly brighter
                    return color.replace(/\d+(?=\))/g, m => Math.min(255, parseInt(m) + 20));
                }),
                hoverBorderColor: '#ffffff',
                hoverBorderWidth: 3
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
                        padding: 20,
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
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    padding: 15,
                    cornerRadius: 5,
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
                duration: 1000,
                easing: 'easeInOutQuart'
            },
            elements: {
                arc: {
                    borderWidth: 2
                }
            }
        }
    });

    // Course Status Chart - Professional configuration
    const courseStatusCtx = document.getElementById('courseStatusChart');
    const activeCount = {{ $courses->where('status', 'active')->count() }};
    const inactiveCount = {{ $courses->count() - $courses->where('status', 'active')->count() }};
    
    const courseStatusChart = new Chart(courseStatusCtx, {
        type: 'pie',
        data: {
            labels: ['Active Courses', 'Inactive Courses'],
            datasets: [{
                data: [activeCount, inactiveCount],
                backgroundColor: ['#1cc88a', '#e74a3b'],
                borderColor: '#ffffff',
                borderWidth: 2,
                hoverBackgroundColor: ['#25e6a1', '#ff5b4c'],
                hoverBorderColor: '#ffffff',
                hoverBorderWidth: 3
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
                        padding: 20,
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
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    padding: 15,
                    cornerRadius: 5,
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
                duration: 1000,
                easing: 'easeInOutQuart'
            },
            elements: {
                arc: {
                    borderWidth: 2
                }
            }
        }
    });
    
    // Individual Course Enrollment Charts
    @foreach($courses as $course)
    (function() {
        const courseCtx = document.getElementById('courseChart{{ $course->id }}');
        const studentCount = {{ $course->students->count() }};
        
        const courseChart = new Chart(courseCtx, {
            type: 'pie',
            data: {
                labels: studentCount > 0 ? ['Enrolled Students'] : ['No Students'],
                datasets: [{
                    data: studentCount > 0 ? [studentCount] : [1],
                    backgroundColor: studentCount > 0 ? ['#1cc88a'] : ['#e74a3b'],
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: 10
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                size: 11,
                                weight: 'bold'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        callbacks: {
                            label: function(context) {
                                if (studentCount > 0) {
                                    return `Enrolled Students: ${studentCount}`;
                                } else {
                                    return 'No students enrolled';
                                }
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: studentCount > 0 ? `${studentCount} Students Enrolled` : 'No Students Enrolled',
                        font: {
                            size: 14,
                            weight: 'bold'
                        },
                        padding: {
                            top: 10,
                            bottom: 20
                        },
                        color: studentCount > 0 ? '#1cc88a' : '#e74a3b'
                    }
                },
                animation: {
                    animateScale: true,
                    animateRotate: true,
                    duration: 800
                }
            }
        });
    })();
    @endforeach
    @endif
});
</script>
@endsection 