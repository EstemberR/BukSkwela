@extends('tenant.layouts.app')

@section('title', 'Staff Reports')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h4>Staff Reports</h4>
            <div class="mt-2">
                <a href="{{ route('tenant.reports.staff.pdf', ['tenant' => tenant('id')]) }}" class="btn btn-danger">
                    <i class="fas fa-file-pdf me-1"></i> Download PDF
                </a>
            </div>
        </div>
        <div class="card-body">
            @if($staff->isEmpty())
                <div class="alert alert-info">
                    No staff data available to display.
                </div>
            @else
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Active vs Inactive Staff</h5>
                            </div>
                            <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 350px;">
                                <div style="position: relative; height: 100%; width: 100%;">
                                    <canvas id="staffStatusChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Staff Role Distribution</h5>
                            </div>
                            <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 350px;">
                                <div style="position: relative; height: 100%; width: 100%;">
                                    <canvas id="staffRoleChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Staff by Department</h5>
                            </div>
                            <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 350px;">
                                <div style="position: relative; height: 100%; width: 100%;">
                                    <canvas id="departmentChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card shadow-sm">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">Staff Summary</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="card bg-primary text-white">
                                            <div class="card-body text-center">
                                                <h3 class="mb-0">{{ $staff->count() }}</h3>
                                                <p class="mb-0">Total Staff</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-success text-white">
                                            <div class="card-body text-center">
                                                <h3 class="mb-0">{{ $staff->where('status', 'active')->count() }}</h3>
                                                <p class="mb-0">Active Staff</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-info text-white">
                                            <div class="card-body text-center">
                                                <h3 class="mb-0">{{ $staff->where('role', 'instructor')->count() }}</h3>
                                                <p class="mb-0">Instructors</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-warning text-white">
                                            <div class="card-body text-center">
                                                <h3 class="mb-0">{{ $staff->where('role', 'admin')->count() }}</h3>
                                                <p class="mb-0">Administrators</p>
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
                                <h5 class="mb-0">Department Analysis</h5>
                            </div>
                            <div class="card-body">
                                <div class="row" id="departmentCharts">
                                    @php
                                        $departments = $staff->groupBy(function($staffMember) {
                                            return $staffMember->department ? $staffMember->department->name : 'No Department';
                                        });
                                        $colorIndex = 0;
                                    @endphp
                                    
                                    @foreach($departments as $departmentName => $departmentStaff)
                                        <div class="col-md-4 mb-4">
                                            <div class="card shadow-sm h-100">
                                                <div class="card-header bg-light">
                                                    <h5 class="mb-0">{{ $departmentName }}</h5>
                                                </div>
                                                <div class="card-body d-flex flex-column">
                                                    <div class="text-center mb-3">
                                                        <h3 class="text-primary">{{ $departmentStaff->count() }}</h3>
                                                        <p class="text-muted mb-0">Staff Members</p>
                                                    </div>
                                                    <div class="flex-grow-1" style="min-height: 200px;">
                                                        <canvas id="departmentRoleChart{{ $colorIndex }}"></canvas>
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
    @if(!$staff->isEmpty())
    // Define professional color schemes
    const statusColors = {
        active: '#1cc88a',
        inactive: '#e74a3b'
    };
    
    const roleColors = {
        instructor: '#4e73df',
        admin: '#f6c23e',
        staff: '#36b9cc'
    };
    
    // Define a color palette for departments
    const departmentColorPalette = [
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
    
    // Staff status chart
    const staffStatusCtx = document.getElementById('staffStatusChart');
    const activeCount = {{ $staff->where('status', 'active')->count() }};
    const inactiveCount = {{ $staff->where('status', 'inactive')->count() }};
    
    const staffStatusChart = new Chart(staffStatusCtx, {
        type: 'pie',
        data: {
            labels: ['Active Staff', 'Inactive Staff'],
            datasets: [{
                data: [activeCount, inactiveCount],
                backgroundColor: [statusColors.active, statusColors.inactive],
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
                            size: 12,
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
    
    // Staff role distribution chart
    const staffRoleCtx = document.getElementById('staffRoleChart');
    const instructorCount = {{ $staff->where('role', 'instructor')->count() }};
    const adminCount = {{ $staff->where('role', 'admin')->count() }};
    const regularStaffCount = {{ $staff->where('role', 'staff')->count() }};
    
    const staffRoleChart = new Chart(staffRoleCtx, {
        type: 'pie',
        data: {
            labels: ['Instructors', 'Administrators', 'Support Staff'],
            datasets: [{
                data: [instructorCount, adminCount, regularStaffCount],
                backgroundColor: [roleColors.instructor, roleColors.admin, roleColors.staff],
                borderColor: '#ffffff',
                borderWidth: 2,
                hoverOffset: 10
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
                            size: 12,
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
    
    // Department distribution chart
    const departmentCtx = document.getElementById('departmentChart');
    const departmentData = {
        labels: [
            @php
                $departments = $staff->groupBy(function($staffMember) {
                    return $staffMember->department ? $staffMember->department->name : 'No Department';
                });
                
                foreach($departments as $department => $members) {
                    echo "'$department', ";
                }
            @endphp
        ],
        counts: [
            @php
                foreach($departments as $department => $members) {
                    echo $members->count() . ", ";
                }
            @endphp
        ]
    };
    
    const departmentChart = new Chart(departmentCtx, {
        type: 'pie',
        data: {
            labels: departmentData.labels,
            datasets: [{
                data: departmentData.counts,
                backgroundColor: departmentColorPalette.slice(0, departmentData.labels.length),
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
                            return `${label}: ${value} staff (${percentage}%)`;
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
    
    // Individual department charts
    @php
        $colorIndex = 0;
        foreach($departments as $departmentName => $departmentStaff) {
            $instructors = $departmentStaff->where('role', 'instructor')->count();
            $admins = $departmentStaff->where('role', 'admin')->count();
            $supportStaff = $departmentStaff->where('role', 'staff')->count();
    @endphp
            
            const deptRoleChart{{ $colorIndex }} = new Chart(
                document.getElementById('departmentRoleChart{{ $colorIndex }}'),
                {
                    type: 'pie',
                    data: {
                        labels: ['Instructors', 'Administrators', 'Support Staff'],
                        datasets: [{
                            data: [{{ $instructors }}, {{ $admins }}, {{ $supportStaff }}],
                            backgroundColor: [roleColors.instructor, roleColors.admin, roleColors.staff],
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
                                text: 'Staff Roles',
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