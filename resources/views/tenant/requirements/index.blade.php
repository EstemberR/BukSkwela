@extends('tenant.layouts.app')

@section('title', 'Requirements Management')

@section('styles')
<style>
    .dropzone {
        border: 2px dashed #ccc;
        border-radius: 4px;
        padding: 20px;
        text-align: center;
        background: #f8f9fa;
        cursor: pointer;
    }
    .dropzone.dragover {
        background: #e9ecef;
        border-color: #6c757d;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Requirements List -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Requirements by Category</h5>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addRequirementModal">
                        Add Requirement
                    </button>
                </div>
                <div class="card-body">
                    <div class="accordion" id="requirementsAccordion">
                        @foreach(['Regular', 'Irregular', 'Probation'] as $category)
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#category{{ $category }}">
                                    {{ $category }} Requirements
                                    <span class="badge bg-primary ms-2">{{ isset($requirements[$category]) ? $requirements[$category]->count() : 0 }}</span>
                                </button>
                            </h2>
                            <div id="category{{ $category }}" class="accordion-collapse collapse show">
                                <div class="accordion-body">
                                    <ul class="list-group">
                                        @forelse($requirements[$category] ?? [] as $requirement)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ $requirement->name }}
                                            <span class="badge bg-warning">{{ $requirement->students_count }} pending</span>
                                        </li>
                                        @empty
                                        <li class="list-group-item text-center text-muted">
                                            No requirements for {{ $category }} students
                                        </li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Students by Category -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#regular">Regular Students</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#irregular">Irregular Students</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#probation">Probation Students</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        @foreach(['regular' => 'Regular', 'irregular' => 'Irregular', 'probation' => 'Probation'] as $status => $label)
                        <div class="tab-pane fade {{ $status === 'regular' ? 'show active' : '' }}" id="{{ $status }}">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Student ID</th>
                                            <th>Name</th>
                                            <th>Course</th>
                                            <th>Year</th>
                                            <th>Requirements Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($students[$status] ?? [] as $student)
                                        <tr>
                                            <td>{{ $student->student_id }}</td>
                                            <td>{{ $student->name }}</td>
                                            <td>{{ $student->course ? $student->course->name : 'N/A' }}</td>
                                            <td>{{ $student->year_level }}</td>
                                            <td>
                                                @php
                                                    $total = $student->requirements->count();
                                                    $approved = $student->requirements->where('pivot.status', 'approved')->count();
                                                    $pending = $student->requirements->where('pivot.status', 'pending')->count();
                                                    $rejected = $student->requirements->where('pivot.status', 'rejected')->count();
                                                    $percentage = $total ? ($approved/$total*100) : 0;
                                                @endphp
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-success" role="progressbar" 
                                                        style="width: {{ $percentage }}%" 
                                                        title="{{ $approved }} Approved">
                                                        {{ $approved }}
                                                    </div>
                                                    <div class="progress-bar bg-warning" role="progressbar" 
                                                        style="width: {{ $total ? ($pending/$total*100) : 0 }}%"
                                                        title="{{ $pending }} Pending">
                                                        {{ $pending }}
                                                    </div>
                                                    <div class="progress-bar bg-danger" role="progressbar" 
                                                        style="width: {{ $total ? ($rejected/$total*100) : 0 }}%"
                                                        title="{{ $rejected }} Rejected">
                                                        {{ $rejected }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <button class="btn btn-info btn-sm view-requirements-btn" data-student-id="{{ $student->id }}">
                                                    <i class="fas fa-folder-open"></i> View Requirements
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No {{ strtolower($label) }} students found</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
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
                        <label class="form-label">Student Category</label>
                        <select class="form-select" name="student_category" required>
                            <option value="">Select Category</option>
                            <option value="Regular">Regular Students</option>
                            <option value="Irregular">Irregular Students</option>
                            <option value="Probation">Probation Students</option>
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
                            <input class="form-check-input" type="checkbox" name="is_required" value="1" id="isRequired" checked>
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
@endsection

@section('scripts')
<script>
    // Debug log to confirm script is loaded
    console.log('Requirements script loaded');

    // Wait for DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM Content Loaded');
        
        // Add click event listeners to all view requirements buttons
        const buttons = document.querySelectorAll('.view-requirements-btn');
        console.log('Found buttons:', buttons.length);
        
        buttons.forEach(button => {
            console.log('Adding click listener to button:', button);
            button.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Button clicked');
                const studentId = this.getAttribute('data-student-id');
                console.log('Student ID:', studentId);
                viewRequirements(studentId);
            });
        });
    });

    // Define viewRequirements function in the global scope
    function viewRequirements(studentId) {
        console.log('viewRequirements called with studentId:', studentId);
        
        // Show loading state
        const modalElement = document.getElementById('viewRequirementsModal');
        console.log('Modal element:', modalElement);
        
        if (!modalElement) {
            console.error('Modal element not found!');
            return;
        }

        const modal = new bootstrap.Modal(modalElement);
        modal.show();
        
        const requirementsList = document.getElementById('requirementsList');
        requirementsList.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"></div></div>';

        // Add error handling for the fetch request
        const url = `/tenant/{{ tenant('id') }}/admin/students/${studentId}/requirements`;
        console.log('Fetching from URL:', url);

        fetch(url)
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Received data:', data);
                
                if (!data.success) {
                    throw new Error(data.message || 'Failed to fetch requirements');
                }

                const student = data.student;
                const requirements = data.requirements;

                // Update modal title with student info
                const modalTitle = document.querySelector('#viewRequirementsModal .modal-title');
                modalTitle.innerHTML = `
                    Requirements for ${student.name} (${student.student_id})
                    <div class="text-muted small">
                        ${student.course} - Year ${student.year_level}
                        <span class="badge bg-info ms-2">${student.status}</span>
                    </div>
                `;

                // Generate HTML for requirements list
                let html = `
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Requirement Name</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                // Add each requirement to the table
                requirements.forEach(req => {
                    const status = req.pivot.status;
                    const statusClass = status === 'approved' ? 'success' : 
                                      status === 'rejected' ? 'danger' : 'warning';
                    
                    html += `
                        <tr>
                            <td>
                                ${req.name}
                                ${req.is_required ? '<span class="badge bg-danger ms-1">Required</span>' : ''}
                            </td>
                            <td>${req.description || 'No description provided'}</td>
                            <td>
                                <span class="badge bg-${statusClass}">
                                    ${status.charAt(0).toUpperCase() + status.slice(1)}
                                </span>
                            </td>
                            <td>
                                ${status === 'pending' ? `
                                    <button class="btn btn-sm btn-primary upload-file-btn" data-student-id="${studentId}" data-requirement-id="${req.id}">
                                        <i class="fas fa-upload"></i> Upload
                                    </button>
                                ` : `
                                    <div class="btn-group">
                                        <a href="/storage/${req.pivot.file_path}" 
                                           target="_blank" 
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        ${status === 'rejected' ? `
                                            <button class="btn btn-sm btn-primary upload-file-btn" data-student-id="${studentId}" data-requirement-id="${req.id}">
                                                <i class="fas fa-upload"></i> Upload New
                                            </button>
                                        ` : ''}
                                    </div>
                                `}
                            </td>
                        </tr>
                    `;
                });

                html += `
                            </tbody>
                        </table>
                    </div>
                `;

                requirementsList.innerHTML = html;

                // Add event listeners to upload buttons
                const uploadButtons = document.querySelectorAll('.upload-file-btn');
                console.log('Found upload buttons:', uploadButtons.length);
                
                uploadButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const studentId = this.getAttribute('data-student-id');
                        const requirementId = this.getAttribute('data-requirement-id');
                        uploadFile(studentId, requirementId);
                    });
                });
            })
            .catch(error => {
                console.error('Error:', error);
                requirementsList.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i> Failed to load requirements. Please try again.
                        <br>
                        <small class="text-muted">Error: ${error.message}</small>
                    </div>
                `;
            });
    }

    function uploadFile(studentId, requirementId) {
        console.log('uploadFile called with studentId:', studentId, 'requirementId:', requirementId);
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = '.pdf,.doc,.docx,.jpg,.jpeg,.png';
        input.onchange = function(e) {
            const file = e.target.files[0];
            if (file) {
                const formData = new FormData();
                formData.append('file', file);
                formData.append('_token', '{{ csrf_token() }}');

                // Show loading state
                const button = e.target.closest('button');
                if (button) {
                    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
                    button.disabled = true;
                }

                fetch(`/tenant/{{ tenant('id') }}/admin/students/${studentId}/requirements/${requirementId}/upload`, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Refresh the requirements view
                        viewRequirements(studentId);
                    } else {
                        throw new Error('Upload failed');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (button) {
                        button.innerHTML = '<i class="fas fa-upload"></i> Upload';
                        button.disabled = false;
                    }
                });
            }
        };
        input.click();
    }

    // Make sure the functions are available globally
    window.viewRequirements = viewRequirements;
    window.uploadFile = uploadFile;
</script>

<style>
    .dropzone {
        border: 2px dashed #ccc;
        border-radius: 4px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .dropzone:hover {
        border-color: #666;
    }

    .dropzone.dragover {
        border-color: #0d6efd;
        background-color: rgba(13, 110, 253, 0.1);
    }

    .dropzone p {
        margin: 0;
        color: #666;
    }

    #viewRequirementsModal .modal-dialog {
        max-width: 90%;
    }

    #viewRequirementsModal .card {
        margin-bottom: 1rem;
    }

    #viewRequirementsModal .card-body {
        padding: 1rem;
    }

    #viewRequirementsModal .alert {
        margin-bottom: 0;
    }
</style>
@endsection 