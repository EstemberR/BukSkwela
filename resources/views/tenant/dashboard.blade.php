@extends('tenant.layouts.app')

@section('title', 'Department Dashboard')

@section('content')
<div class="container-fluid py-2">
    <div class="row">
        <!-- Main Content -->
        <div class="col-12">
            <!-- Statistics Cards -->
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="card h-100 border-0 shadow-sm hover-shadow">
                        <div class="card-body d-flex align-items-center">
                            <div class="rounded-circle p-3 bg-primary bg-opacity-10 me-3">
                                <i class="fas fa-chalkboard-teacher fs-4 text-primary"></i>
                            </div>
                            <div>
                                <h6 class="card-subtitle mb-1 text-muted">Total Instructors</h6>
                                <h2 class="card-title mb-0 fw-bold">{{ $instructorCount ?? 0 }}</h2>
                            </div>
                        </div>
                        <div class="card-footer border-0 bg-primary bg-opacity-10 py-3">
                            <small class="text-primary">
                                <i class="fas fa-chart-line me-1"></i>
                                Active Teaching Staff
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100 border-0 shadow-sm hover-shadow">
                        <div class="card-body d-flex align-items-center">
                            <div class="rounded-circle p-3 bg-success bg-opacity-10 me-3">
                                <i class="fas fa-user-graduate fs-4 text-success"></i>
                            </div>
                            <div>
                                <h6 class="card-subtitle mb-1 text-muted">Total Students</h6>
                                <h2 class="card-title mb-0 fw-bold">{{ $studentCount ?? 0 }}</h2>
                            </div>
                        </div>
                        <div class="card-footer border-0 bg-success bg-opacity-10 py-3">
                            <small class="text-success">
                                <i class="fas fa-users me-1"></i>
                                Enrolled Learners
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100 border-0 shadow-sm hover-shadow">
                        <div class="card-body d-flex align-items-center">
                            <div class="rounded-circle p-3 bg-info bg-opacity-10 me-3">
                                <i class="fas fa-clipboard-list fs-4 text-info"></i>
                            </div>
                            <div>
                                <h6 class="card-subtitle mb-1 text-muted">Pending Requirements</h6>
                                <h2 class="card-title mb-0 fw-bold">{{ $pendingRequirements ?? 0 }}</h2>
                            </div>
                        </div>
                        <div class="card-footer border-0 bg-info bg-opacity-10 py-3">
                            <small class="text-info">
                                <i class="fas fa-clock me-1"></i>
                                Awaiting Review
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card h-100 border-0 shadow-sm hover-shadow">
                        <div class="card-body d-flex align-items-center">
                            <div class="rounded-circle p-3 bg-warning bg-opacity-10 me-3">
                                <i class="fas fa-book fs-4 text-warning"></i>
                            </div>
                            <div>
                                <h6 class="card-subtitle mb-1 text-muted">Active Courses</h6>
                                <h2 class="card-title mb-0 fw-bold">{{ $activeCourses ?? 0 }}</h2>
                            </div>
                        </div>
                        <div class="card-footer border-0 bg-warning bg-opacity-10 py-3">
                            <small class="text-warning">
                                <i class="fas fa-graduation-cap me-1"></i>
                                Current Semester
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cards Row for Students and Courses -->
            <div class="d-flex flex-wrap justify-content-between mt-4">
                <!-- Recent Enrolled Students Section -->
                <div class="enrolled-card flex-grow-1 mx-2" style="min-width: 280px; max-width: 32%;">
                    <p class="title p-2 mb-0" style="font-size: 1em; color: #111827;">Recent Enrolled Students</p>
                    <div class="user__container custom-scrollbar overflow-y-auto" style="max-height: 200px;">
                        @forelse($students ?? [] as $student)
                            <div class="user">
                                <div class="image">
                                    @if($student->avatar)
                                        <img src="{{ asset('storage/' . $student->avatar) }}" 
                                             alt="{{ $student->student_id }}" 
                                             class="w-full h-full rounded-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center student-image">
                                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="user__content">
                                    <div class="text">
                                        <span class="name" style="font-size: 0.85em;">{{ $student->student_id }}</span>
                                        <p class="username {{ $student->status === 'Regular' ? 'text-green-500' : ($student->status === 'Irregular' ? 'text-yellow-500' : 'text-red-500') }}" 
                                           style="font-size: 0.75em; margin: 0;">
                                            {{ $student->status }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="user">
                                <p class="text-xs text-coolGray-500 text-center">No students enrolled yet</p>
                            </div>
                        @endforelse
                    </div>
                    <a class="more" href="{{ route('tenant.students.index', ['tenant' => tenant('id')]) }}" style="font-size: 0.85em;">See more</a>
                </div>

                <!-- Available Courses Section -->
                <div class="enrolled-card flex-grow-1 mx-2" style="min-width: 280px; max-width: 32%;">
                    <p class="title p-2 mb-0" style="font-size: 1em; color: #111827;">Available Courses</p>
                    <div class="user__container custom-scrollbar overflow-y-auto" style="max-height: 200px;">
                        @forelse($courses ?? [] as $course)
                            <div class="user">
                                <div class="image">
                                    <div class="w-full h-full flex items-center justify-center course-image">
                                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M21 5c-1.11-.35-2.33-.5-3.5-.5-1.95 0-4.05.4-5.5 1.5-1.45-1.1-3.55-1.5-5.5-1.5S2.45 4.9 1 6v14.65c0 .25.25.5.5.5.1 0 .15-.05.25-.05C3.1 20.45 5.05 20 6.5 20c1.95 0 4.05.4 5.5 1.5 1.35-.85 3.8-1.5 5.5-1.5 1.65 0 3.35.3 4.75 1.05.1.05.15.05.25.05.25 0 .5-.25.5-.5V6c-.6-.45-1.25-.75-2-1zm0 13.5c-1.1-.35-2.3-.5-3.5-.5-1.7 0-4.15.65-5.5 1.5V8c1.35-.85 3.8-1.5 5.5-1.5 1.2 0 2.4.15 3.5.5v11.5z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="user__content">
                                    <div class="text">
                                        <span class="name" style="font-size: 0.85em;">{{ $course->title }}</span>
                                        <div class="d-flex align-items-center gap-2" style="font-size: 0.75em; margin: 0;">
                                            <span class="username text-warning">
                                                {{ $course->staff->name ?? 'No Instructor' }}
                                            </span>
                                            <span class="badge bg-{{ $course->status === 'active' ? 'success' : 'secondary' }} rounded-pill" style="font-size: 0.65em; padding: 0.25em 0.5em;">
                                                {{ ucfirst($course->status) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div style="margin-left: auto; text-align: right;">
                                        <span class="badge bg-info rounded-pill">
                                            <i class="fas fa-users fa-xs me-1"></i>{{ $course->students_count }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="user">
                                <p class="text-xs text-coolGray-500 text-center">No courses available</p>
                            </div>
                        @endforelse
                    </div>
                    <a class="more" href="{{ route('tenant.courses.index', ['tenant' => tenant('id')]) }}" style="font-size: 0.85em;">See more</a>
                </div>

                <!-- Requirements Submitted Graph -->
                <div class="enrolled-card flex-grow-1 mx-2" style="min-width: 280px; max-width: 32%;">
                    <p class="title p-2 mb-0" style="font-size: 1em; color: #111827;">Requirements Submitted</p>
                    <div class="chart-container" style="position: relative; height: 200px; width: 100%; padding: 10px;">
                        <canvas id="requirementsChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Student Requirements Tables -->
          


<!-- View Requirements Modal -->
<div class="modal fade" id="viewRequirementsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Student Requirements</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="requirementsList"></div>
            </div>
        </div>
    </div>
</div>

<!-- Add Requirement Modal -->
<div class="modal fade" id="addRequirementModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Requirement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('tenant.requirements.store', ['tenant' => tenant('id')]) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Student Categories</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="student_categories[]" value="Regular" id="regularCheck" checked>
                            <label class="form-check-label" for="regularCheck">
                                Regular Students
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="student_categories[]" value="Irregular" id="irregularCheck">
                            <label class="form-check-label" for="irregularCheck">
                                Irregular Students
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="student_categories[]" value="Probation" id="probationCheck">
                            <label class="form-check-label" for="probationCheck">
                                Probation Students
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select class="form-select" name="category_id" required>
                            <option value="">Select Category</option>
                            @foreach($requirementCategories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">File Type</label>
                        <select class="form-select" name="file_type" required>
                            <option value="pdf">PDF</option>
                            <option value="doc">Word Document</option>
                            <option value="image">Image</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_required" id="isRequired" checked>
                            <label class="form-check-label" for="isRequired">
                                Required
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Requirement</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function viewRequirements(studentId) {
    fetch(`/tenant/{{ tenant('id') }}/admin/students/${studentId}/requirements`)
        .then(response => response.json())
        .then(data => {
            let html = '<div class="list-group">';
            data.requirements.forEach(req => {
                html += `
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-1">${req.name}</h6>
                            <span class="badge bg-${req.pivot.status === 'approved' ? 'success' : (req.pivot.status === 'rejected' ? 'danger' : 'warning')}">
                                ${req.pivot.status}
                            </span>
                        </div>
                        ${req.pivot.file_path ? `
                            <div class="mt-2">
                                <a href="/storage/${req.pivot.file_path}" target="_blank" class="btn btn-sm btn-primary">View File</a>
                                <button class="btn btn-sm btn-success" onclick="updateStatus(${req.pivot.id}, 'approved')">Approve</button>
                                <button class="btn btn-sm btn-danger" onclick="updateStatus(${req.pivot.id}, 'rejected')">Reject</button>
                            </div>
                        ` : `
                            <p class="text-muted mb-0">No file uploaded yet</p>
                        `}
                        ${req.pivot.remarks ? `<p class="text-muted mt-2 mb-0">Remarks: ${req.pivot.remarks}</p>` : ''}
                    </div>
                `;
            });
            html += '</div>';
            document.getElementById('requirementsList').innerHTML = html;
            new bootstrap.Modal(document.getElementById('viewRequirementsModal')).show();
        });
}

function updateStatus(studentRequirementId, status) {
    const remarks = status === 'rejected' ? prompt('Please enter rejection remarks:') : null;
    if (status === 'rejected' && !remarks) return;

    fetch(`/tenant/{{ tenant('id') }}/admin/student-requirements/${studentRequirementId}/status`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ status, remarks })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

// Requirements Chart
const setupRequirementsChart = () => {
    const ctx = document.getElementById('requirementsChart');
    if (!ctx) return;

    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Form 137', 'Good Moral', 'Birth Cert', 'Medical Cert', 'Photo', 'Enrollment'],
            datasets: [{
                data: [85, 92, 78, 65, 95, 70],
                backgroundColor: [
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)',
                    'rgba(255, 159, 64, 0.7)',
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(46, 204, 113, 0.7)'
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(46, 204, 113, 1)'
                ],
                borderWidth: 2,
                borderRadius: 4,
                maxBarThickness: 25
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                    titleColor: '#111827',
                    bodyColor: '#111827',
                    titleFont: {
                        size: 12,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 11
                    },
                    padding: 12,
                    borderColor: '#e5e7eb',
                    borderWidth: 1,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            return `Submitted: ${context.parsed.y}%`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    grid: {
                        display: true,
                        color: 'rgba(0, 0, 0, 0.05)',
                        drawBorder: false
                    },
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        },
                        font: {
                            size: 10
                        },
                        color: '#6B7280'
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 9
                        },
                        color: '#6B7280'
                    }
                }
            },
            animation: {
                duration: 2000,
                easing: 'easeOutQuart'
            }
        }
    });
};

// Initialize chart when DOM is loaded
document.addEventListener('DOMContentLoaded', setupRequirementsChart);
</script>
@endsection

@push('styles')
<style>
    /* Dashboard Cards Styles */
    .hover-shadow {
        transition: all 0.3s ease;
    }
    
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    
    .card {
        overflow: hidden;
        border-radius: 1rem;
    }
    
    .card .rounded-circle {
        transition: all 0.3s ease;
    }
    
    .card:hover .rounded-circle {
        transform: scale(1.1);
    }
    
    .card-title {
        font-size: 2rem;
        color: rgb(3, 1, 43);
    }
    
    .card-subtitle {
        font-size: 0.875rem;
        font-weight: 500;
    }
    
    .card-footer {
        border-bottom-left-radius: 1rem;
        border-bottom-right-radius: 1rem;
    }
    
    .card-footer small {
        font-weight: 500;
    }
    
    /* Existing modal styles */
    .modal-dialog {
        max-width: 80%;
    }
    
    .nav-tabs .nav-link {
        color: #4b5563;
        border: none;
        border-bottom: 2px solid transparent;
        padding: 0.75rem 1rem;
    }
    
    .nav-tabs .nav-link.active {
        color: rgb(3, 1, 43);
        border-bottom: 2px solid rgb(3, 1, 43);
        font-weight: 600;
    }
    
    .list-group-item {
        border: 1px solid rgba(0,0,0,0.1);
        margin-bottom: 0.5rem;
        border-radius: 0.5rem !important;
        transition: all 0.2s ease;
    }
    
    .list-group-item:hover {
        background-color: rgba(3, 1, 43, 0.05);
        border-color: rgb(3, 1, 43);
    }
    
    .badge {
        font-weight: 500;
        padding: 0.5em 1em;
    }
    
    /* Recent Enrolled Students Section Styles */
    .shadow-dashboard {
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }
    
    .text-coolGray-900 {
        color: rgb(17, 24, 39);
    }
    
    .text-coolGray-500 {
        color: rgb(107, 114, 128);
    }
    
    .bg-blue-50 {
        background-color: rgba(59, 130, 246, 0.05);
    }
    
    .border-coolGray-100 {
        border-color: rgb(243, 244, 246);
    }
    
    .text-green-500 {
        color: rgb(16, 185, 129);
    }
    
    .text-green-600:hover {
        color: rgb(5, 150, 105);
    }
    
    .rounded-md {
        border-radius: 0.375rem;
    }
    
    .rounded-full {
        border-radius: 9999px;
    }
    
    /* Status Colors */
    .bg-green-100 {
        background-color: rgba(16, 185, 129, 0.1);
    }
    
    .text-green-800 {
        color: rgb(6, 95, 70);
    }
    
    .bg-yellow-100 {
        background-color: rgba(245, 158, 11, 0.1);
    }
    
    .text-yellow-800 {
        color: rgb(146, 64, 14);
    }

    /* Scrollbar Styles */
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: #ffffff;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #9CA3AF;
        border-radius: 20px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #6B7280;
    }

    .overflow-y-auto {
        scrollbar-width: thin;
        scrollbar-color: #9CA3AF #ffffff;
    }

    /* Last item in list */
    .last\:border-b-0:last-child {
        border-bottom-width: 0;
    }

    .card {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .title {
        font-weight: 800;
    }

    .user {
        display: flex;
        flex-direction: row;
        align-items: center;
        padding: 8px 12px;
    }

    .user__content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-grow: 1;
    }

    .user__container {
        display: flex;
        flex-direction: column;
    }

    .name {
        font-weight: 800;
    }

    .username {
        font-size: .9em;
        color: #64696e;
    }

    .image {
        width: 40px;
        height: 40px;
        background: rgb(22,19,70);
        background: linear-gradient(295deg, rgba(22,19,70,1) 41%, rgba(89,177,237,1) 100%);
        border-radius: 50%;
        margin-right: 10px;
        overflow: hidden;
    }

    .more {
        display: block;
        text-decoration: none;
        color: rgb(29, 155, 240);
        font-weight: 800;
        padding: 8px 12px;
    }

    .user:hover {
        background-color: rgba(0, 0, 0, 0.03);
    }

    .more:hover {
        background-color: #b3b6b6;
        border-radius: 0px 0px 15px 15px;
    }

    .enrolled-card {
        padding: 15px;
        display: flex;
        flex-direction: column;
        height: 280px;
        background: rgb(255, 255, 255);
        border-radius: 1rem;
        box-shadow: rgba(50, 50, 93, 0.25) 0px 6px 12px -2px,
            rgba(0, 0, 0, 0.3) 0px 3px 7px -3px;
        transition: all ease-in-out 0.3s;
    }

    .enrolled-card:hover {
        background-color: #fdfdfd;
        box-shadow: rgba(0, 0, 0, 0.09) 0px 2px 1px, 
            rgba(0, 0, 0, 0.09) 0px 4px 2px,
            rgba(0, 0, 0, 0.09) 0px 8px 4px, 
            rgba(0, 0, 0, 0.09) 0px 16px 8px,
            rgba(0, 0, 0, 0.09) 0px 32px 16px;
    }

    /* Course Card Styles */
    .enrolled-card .user:hover {
        background-color: rgba(0, 0, 0, 0.03);
        transition: all 0.2s ease;
    }

    .enrolled-card .user .image {
        transition: transform 0.2s ease;
    }

    .enrolled-card .user:hover .image {
        transform: scale(1.1);
    }

    /* Course color variations */
    .course-image {
        background: linear-gradient(295deg, rgba(22,19,70,1) 41%, rgba(253,189,74,1) 100%);
    }

    .student-image {
        background: linear-gradient(295deg, rgba(22,19,70,1) 41%, rgba(89,177,237,1) 100%);
    }

    /* Chart Card Styles */
    .chart-container {
        padding: 10px;
        height: calc(100% - 40px);
        width: 100%;
    }

    /* Ensure chart container takes remaining space */
    .enrolled-card:has(#requirementsChart) {
        width: auto !important;
    }

    .enrolled-card:has(#requirementsChart) .chart-container {
        flex: 1;
        margin-top: 10px;
    }
</style>
@endpush