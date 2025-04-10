@extends('tenant.layouts.app')

@section('title', 'Course Reports')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h4>Course Reports</h4>
        </div>
        <div class="card-body">
            @if($courses->isEmpty())
                <div class="alert alert-info">
                    No course data available to display.
                </div>
            @else
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h5>Students per Course</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="studentsPerCourseChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h5>Course Status Distribution</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="courseStatusChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h5>Instructor Assignment</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="instructorAssignmentChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h5>Course Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Course Code</th>
                                                <th>Title</th>
                                                <th>Description</th>
                                                <th>Instructor</th>
                                                <th>Students</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($courses as $course)
                                            <tr>
                                                <td>{{ $course->code }}</td>
                                                <td>{{ $course->title }}</td>
                                                <td>{{ $course->description }}</td>
                                                <td>{{ $course->staff->name ?? 'No Instructor Assigned' }}</td>
                                                <td>{{ $course->students->count() }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $course->status == 'active' ? 'success' : 'warning' }}">
                                                        {{ ucfirst($course->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(!$courses->isEmpty())
    // Students per Course Chart
    const studentsPerCourseCtx = document.getElementById('studentsPerCourseChart').getContext('2d');
    const studentsPerCourseChart = new Chart(studentsPerCourseCtx, {
        type: 'pie',
        data: {
            labels: [
                @foreach($courses as $course)
                    "{{ $course->code ?? 'Unknown' }} - {{ $course->title ?? 'Untitled' }}",
                @endforeach
            ],
            datasets: [{
                data: [
                    @foreach($courses as $course)
                        {{ $course->students->count() }},
                    @endforeach
                ],
                backgroundColor: [
                    @foreach($courses as $index => $course)
                        'hsl({{ ($index * 360 / max(1, count($courses))) }}, 70%, 60%)',
                    @endforeach
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12
                    }
                }
            }
        }
    });

    // Course Status Chart
    const statusCounts = {
        @php
            $statuses = $courses->groupBy('status')->map->count();
            $statusArray = [];
            foreach($statuses as $status => $count) {
                $statusArray[] = "'" . ($status ?? 'Unknown') . "': " . $count;
            }
            echo implode(', ', $statusArray);
        @endphp
    };

    const courseStatusCtx = document.getElementById('courseStatusChart').getContext('2d');
    const courseStatusChart = new Chart(courseStatusCtx, {
        type: 'pie',
        data: {
            labels: Object.keys(statusCounts).map(status => status.charAt(0).toUpperCase() + status.slice(1)),
            datasets: [{
                data: Object.values(statusCounts),
                backgroundColor: [
                    'hsl(120, 70%, 60%)', 
                    'hsl(40, 70%, 60%)', 
                    'hsl(0, 70%, 60%)',
                    'hsl(200, 70%, 60%)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Instructor Assignment Chart
    const instructorAssignmentCtx = document.getElementById('instructorAssignmentChart').getContext('2d');
    const instructorCounts = {
        @php
            $instructors = $courses->groupBy(function($course) {
                return $course->staff ? $course->staff->name : 'No Instructor Assigned';
            })->map->count();
            $instructorArray = [];
            foreach($instructors as $instructor => $count) {
                $instructorArray[] = "'" . ($instructor ?? 'Unknown') . "': " . $count;
            }
            echo implode(', ', $instructorArray);
        @endphp
    };

    const instructorAssignmentChart = new Chart(instructorAssignmentCtx, {
        type: 'pie',
        data: {
            labels: Object.keys(instructorCounts),
            datasets: [{
                data: Object.values(instructorCounts),
                backgroundColor: [
                    @foreach($instructors as $index => $count)
                        'hsl({{ (200 + $index * 40) % 360 }}, 70%, 60%)',
                    @endforeach
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12
                    }
                }
            }
        }
    });
    @endif
});
</script>
@endpush
@endsection 