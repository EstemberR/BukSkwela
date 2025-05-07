@extends('tenant.layouts.app')

@section('title', 'Student Enrollment')

@section('content')
<div class="container">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="d-flex align-items-center">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('tenant.student.dashboard', ['tenant' => tenant('id')]) }}">DASHBOARD</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Enrollment Overview</li>
                    </ol>
                </nav>
            </div>
            <h2 class="mt-2">Enrollment Overview</h2>
        </div>
        <div class="col-md-6 text-md-end">
            <div class="d-flex justify-content-md-end gap-2">
                <a href="#" class="btn btn-outline-secondary">Application History</a>
                <a href="#" class="btn btn-success">Apply Enrollment</a>
            </div>
        </div>
    </div>

    <!-- Available Programs/Courses Section -->
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="mb-3">Available Programs for Enrollment</h4>
            
            @if(empty($programs) || count($programs) == 0)
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> No programs are currently available for enrollment. Please check back later.
                </div>
            @else
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    @foreach($programs as $program)
                        <div class="col">
                            <div class="card h-100 program-card">
                                <div class="program-card-image">
                                    <img src="{{ asset('assets/images/BacgroundEnrollment.jpg') }}" class="card-img-top" alt="{{ $program->name }}">
                                    <div class="program-logo">
                                        <img src="{{ asset('assets/images/LogoEnrollment.png') }}" alt="University Logo">
                                    </div>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">{{ $program->name }}</h5>
                                    <p class="card-text text-muted small">{{ Str::limit($program->description, 100) }}</p>
                                </div>
                                <div class="card-footer bg-white border-top-0">
                                    <a href="#" class="btn btn-primary btn-sm apply-btn" 
                                       data-bs-toggle="modal" 
                                       data-bs-target="#applyModal" 
                                       data-program-id="{{ $program->id }}" 
                                       data-program-name="{{ $program->name }}">
                                        Apply for This Program
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Enrollment Applications Table -->
    <div class="card shadow-sm mb-4 {{ isset($hasApplications) && $hasApplications ? '' : 'd-none' }}">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Your Enrollment Applications</h5>
            <span class="badge bg-info">{{ isset($applications) ? count($applications) : 0 }} Application(s)</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Application ID</th>
                            <th scope="col">Program/Course</th>
                            <th scope="col">Submitted Date</th>
                            <th scope="col">Status</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($applications) && count($applications) > 0)
                            @foreach($applications as $application)
                                <tr>
                                    <td>#{{ $application->id }}</td>
                                    <td>{{ $application->program ? $application->program->name : 'Unknown Program' }}</td>
                                    <td>{{ $application->created_at->format('M d, Y') }}</td>
                                    <td>
                                        @if($application->status == 'pending')
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @elseif($application->status == 'approved')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif($application->status == 'rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($application->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary view-application" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#viewApplicationModal"
                                                data-application-id="{{ $application->id }}"
                                                data-program-name="{{ $application->program ? $application->program->name : 'Unknown Program' }}"
                                                data-year-level="{{ $application->year_level }}"
                                                data-status="{{ $application->status }}"
                                                data-submitted-date="{{ $application->created_at->format('M d, Y') }}"
                                                data-notes="{{ $application->notes }}">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="text-center py-3">You haven't submitted any applications yet.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Application Modal -->
<div class="modal fade" id="applyModal" tabindex="-1" aria-labelledby="applyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="applyModalLabel">Apply for Program</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('tenant.student.enrollment.apply', ['tenant' => tenant('id')]) }}" method="POST" enctype="multipart/form-data" id="applicationForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="program_id" id="program_id">
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> You are applying for <strong id="selected-program-name"></strong>
                    </div>
                    
                    <div class="mb-3">
                        <label for="year_level" class="form-label">Year Level</label>
                        <select class="form-select" id="year_level" name="year_level" required>
                            <option value="">Select Year Level</option>
                            <option value="1">First Year</option>
                            <option value="2">Second Year</option>
                            <option value="3">Third Year</option>
                            <option value="4">Fourth Year</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Additional Notes (Optional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                    
                    <!-- Requirements section (if any) -->
                    <div id="requirements-container">
                        <!-- Requirements will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Application</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Application Modal -->
<div class="modal fade" id="viewApplicationModal" tabindex="-1" aria-labelledby="viewApplicationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewApplicationModalLabel">Application Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="fw-bold">Program/Course:</label>
                            <div id="view-program-name" class="form-text-static"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="fw-bold">Submitted Date:</label>
                            <div id="view-submitted-date" class="form-text-static"></div>
                        </div>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="fw-bold">Application ID:</label>
                            <div id="view-application-id" class="form-text-static"></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="fw-bold">Year Level:</label>
                            <div id="view-year-level" class="form-text-static"></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="fw-bold">Status:</label>
                            <div id="view-status-badge" class="form-text-static"></div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="fw-bold">Your Notes:</label>
                    <div id="view-notes" class="form-text-static p-2 border rounded bg-light"></div>
                </div>
                
                <div class="mb-4">
                    <label class="fw-bold">Submitted Documents:</label>
                    <div id="view-documents" class="form-text-static">
                        <div class="d-flex justify-content-center align-items-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mb-0 ms-3">Loading documents...</p>
                        </div>
                    </div>
                </div>
                
                <div id="admin-feedback" class="mb-4 d-none">
                    <label class="fw-bold">Feedback from Administrator:</label>
                    <div id="view-admin-notes" class="form-text-static p-2 border rounded bg-light"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .breadcrumb-item a {
        color: #6c757d;
        text-decoration: none;
        font-size: 0.85rem;
    }
    
    .breadcrumb-item.active {
        font-size: 0.85rem;
    }
    
    .badge {
        font-weight: 500;
    }
    
    /* Program Card Styling */
    .program-card {
        transition: transform 0.3s, box-shadow 0.3s;
        overflow: hidden;
        border: 1px solid #e5e5e5;
    }
    
    .program-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    
    .program-card-image {
        position: relative;
        height: 150px;
        overflow: hidden;
    }
    
    .program-card-image img.card-img-top {
        height: 100%;
        width: 100%;
        object-fit: cover;
    }
    
    .program-logo {
        position: absolute;
        bottom: 10px;
        left: 20px;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        z-index: 10;
    }
    
    .program-logo img {
        max-width: 90%;
        max-height: 90%;
        object-fit: contain;
    }
    
    .card-body {
        padding-top: 2rem;
    }
    
    .program-code {
        display: inline-block;
        font-size: 0.8rem;
        font-weight: 600;
        color: #6c757d;
        background-color: #f8f9fa;
        padding: 0.2rem 0.5rem;
        border-radius: 4px;
    }
    
    .apply-btn {
        width: 100%;
    }
    
    /* Application Details Modal */
    .form-text-static {
        padding: 0.375rem 0;
        min-height: 24px;
    }
    
    #view-notes,
    #view-admin-notes {
        min-height: 80px;
        white-space: pre-line;
    }
    
    #view-documents .document-item {
        display: flex;
        align-items: center;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 4px;
        margin-bottom: 10px;
    }
    
    #view-documents .document-icon {
        font-size: 1.5rem;
        margin-right: 15px;
    }
    
    #view-documents .document-info {
        flex: 1;
    }
    
    #view-documents .document-name {
        font-weight: 500;
        margin-bottom: 2px;
    }
    
    #view-documents .document-meta {
        font-size: 0.8rem;
        color: #6c757d;
    }
    
    #view-documents .document-action {
        margin-left: 10px;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle apply button click to set program info in modal
    const applyButtons = document.querySelectorAll('.apply-btn');
    applyButtons.forEach(button => {
        button.addEventListener('click', function() {
            const programId = this.dataset.programId;
            const programName = this.dataset.programName;
            
            document.getElementById('program_id').value = programId;
            document.getElementById('selected-program-name').textContent = programName;
            
            // Load program-specific requirements
            loadProgramRequirements(programId);
        });
    });
    
    // Function to load program requirements
    function loadProgramRequirements(programId) {
        const requirementsContainer = document.getElementById('requirements-container');
        requirementsContainer.innerHTML = '<div class="text-center my-3"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">Loading requirements...</p></div>';
        
        // Fix the route construction to properly include the programId parameter
        const url = "{{ route('tenant.student.enrollment.program-requirements', ['tenant' => tenant('id'), 'programId' => '__PROGRAM_ID__']) }}".replace('__PROGRAM_ID__', programId);
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let requirementsHtml = '<h5 class="mt-4 mb-3">Required Documents</h5>';
                    
                    // Display a message if we're in demo mode with no actual requirement folders
                    const hasRequirementFolders = Array.isArray(data.requirementFolders) && data.requirementFolders.length > 0;
                    
                    if (hasRequirementFolders) {
                        data.requirementFolders.forEach(folder => {
                            requirementsHtml += `
                                <div class="mb-3">
                                    <label for="folder_file_${folder.id}" class="form-label">${folder.name}</label>
                                    <input class="form-control" type="file" id="folder_file_${folder.id}" name="folder_file_${folder.id}" required>
                                    <small class="text-muted">Accepted formats: PDF, JPG, PNG. Max size: 5MB</small>
                                </div>
                            `;
                        });
                    } else {
                        // For demo/testing - create some example document fields
                        requirementsHtml += `
                            <div class="alert alert-info mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                Please upload the following documents for your application.
                            </div>
                            
                            <div class="mb-3">
                                <label for="transcript" class="form-label">Academic Transcript</label>
                                <input class="form-control" type="file" id="transcript" name="transcript">
                                <small class="text-muted">Accepted formats: PDF. Max size: 5MB</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="id_photo" class="form-label">ID Photo (2x2)</label>
                                <input class="form-control" type="file" id="id_photo" name="id_photo">
                                <small class="text-muted">Accepted formats: JPG, PNG. Max size: 2MB</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="birth_certificate" class="form-label">Birth Certificate</label>
                                <input class="form-control" type="file" id="birth_certificate" name="birth_certificate">
                                <small class="text-muted">Accepted formats: PDF, JPG, PNG. Max size: 5MB</small>
                            </div>
                        `;
                    }
                    
                    requirementsContainer.innerHTML = requirementsHtml;
                } else {
                    requirementsContainer.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i> ${data.message || 'Failed to load requirements. Please try again.'}
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading requirements:', error);
                requirementsContainer.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i> Failed to load requirements. Please try again.
                    </div>
                `;
            });
    }
    
    // View application details
    const viewButtons = document.querySelectorAll('.view-application');
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Get data from button attributes
            const applicationId = this.dataset.applicationId;
            const programName = this.dataset.programName;
            const yearLevel = this.dataset.yearLevel;
            const status = this.dataset.status;
            const submittedDate = this.dataset.submittedDate;
            const notes = this.dataset.notes || 'No notes provided.';
            
            // Update modal content
            document.getElementById('view-application-id').textContent = '#' + applicationId;
            document.getElementById('view-program-name').textContent = programName;
            document.getElementById('view-year-level').textContent = 'Year ' + yearLevel;
            document.getElementById('view-submitted-date').textContent = submittedDate;
            document.getElementById('view-notes').textContent = notes;
            
            // Set status badge
            let statusBadge = '';
            if (status === 'pending') {
                statusBadge = '<span class="badge bg-warning text-dark">Pending</span>';
            } else if (status === 'approved') {
                statusBadge = '<span class="badge bg-success">Approved</span>';
            } else if (status === 'rejected') {
                statusBadge = '<span class="badge bg-danger">Rejected</span>';
            } else {
                statusBadge = '<span class="badge bg-secondary">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
            }
            document.getElementById('view-status-badge').innerHTML = statusBadge;
            
            // Load documents
            loadApplicationDocuments(applicationId);
            
            // Show/hide admin feedback
            if (status !== 'pending') {
                fetch(`{{ route('tenant.student.enrollment.application.details', ['tenant' => tenant('id'), 'applicationId' => '__APP_ID__']) }}`.replace('__APP_ID__', applicationId), {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.application) {
                        if (data.application.admin_notes) {
                            document.getElementById('view-admin-notes').textContent = data.application.admin_notes;
                            document.getElementById('admin-feedback').classList.remove('d-none');
                        } else {
                            document.getElementById('admin-feedback').classList.add('d-none');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading application details:', error);
                });
            } else {
                document.getElementById('admin-feedback').classList.add('d-none');
            }
        });
    });
    
    // Function to load application documents
    function loadApplicationDocuments(applicationId) {
        const documentsContainer = document.getElementById('view-documents');
        
        // Show loading
        documentsContainer.innerHTML = `
            <div class="d-flex justify-content-center align-items-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mb-0 ms-3">Loading documents...</p>
            </div>
        `;
        
        // Fetch documents
        fetch(`{{ route('tenant.student.enrollment.application.documents', ['tenant' => tenant('id'), 'applicationId' => '__APP_ID__']) }}`.replace('__APP_ID__', applicationId), {
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.documents && data.documents.length > 0) {
                let documentsHtml = '';
                
                data.documents.forEach(document => {
                    let iconClass = 'fa-file';
                    if (document.mime_type && document.mime_type.includes('pdf')) {
                        iconClass = 'fa-file-pdf';
                    } else if (document.mime_type && document.mime_type.includes('image')) {
                        iconClass = 'fa-file-image';
                    }
                    
                    documentsHtml += `
                        <div class="document-item">
                            <div class="document-icon">
                                <i class="fas ${iconClass} text-primary"></i>
                            </div>
                            <div class="document-info">
                                <div class="document-name">${document.folder_name || document.field_name || 'Document'}</div>
                                <div class="document-meta">
                                    ${document.file_name || 'File'} - Uploaded on ${new Date(document.uploaded_at).toLocaleDateString()}
                                </div>
                            </div>
                            ${document.file_path ? 
                                `<div class="document-action">
                                    <a href="${document.file_path}" class="btn btn-sm btn-outline-primary" target="_blank">
                                        <i class="fas fa-external-link-alt"></i> View
                                    </a>
                                </div>` : ''
                            }
                        </div>
                    `;
                });
                
                documentsContainer.innerHTML = documentsHtml;
            } else {
                documentsContainer.innerHTML = `
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> No documents available for this application.
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading documents:', error);
            documentsContainer.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i> Failed to load documents. Please try again.
                </div>
            `;
        });
    }
});
</script>
@endpush 