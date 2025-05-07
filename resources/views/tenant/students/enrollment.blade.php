@extends('tenant.layouts.app')

@section('title', 'Student Enrollment')

@section('content')
<div class="container">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('tenant.student.dashboard', ['tenant' => tenant('id')]) }}">DASHBOARD</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Enrollment Overview</li>
                        </ol>
                    </nav>
                    <h2 class="mt-2">Enrollment Overview</h2>
                </div>
                @if(isset($applications) && $applications->where('status', 'approved')->count() > 0)
                    @php
                        $approvedApplication = $applications->where('status', 'approved')->first();
                        $approvedProgramName = $approvedApplication->program ? $approvedApplication->program->name : 'Unknown Program';
                    @endphp
                    <div class="alert alert-success alert-sm already-enrolled-alert mb-0">
                        <i class="fas fa-check-circle me-1"></i> Already enrolled in <strong>{{ $approvedProgramName }}</strong>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Available Programs/Courses Section -->
    <div class="row mb-4">
        <div class="col-12">
            <ul class="nav nav-tabs mb-3" id="enrollmentTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="programs-tab" data-bs-toggle="tab" data-bs-target="#programs-tab-pane" type="button" role="tab" aria-controls="programs-tab-pane" aria-selected="true">
                        <i class="fas fa-graduation-cap me-1"></i> Available Programs
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="applications-tab" data-bs-toggle="tab" data-bs-target="#applications-tab-pane" type="button" role="tab" aria-controls="applications-tab-pane" aria-selected="false">
                        <i class="fas fa-clipboard-list me-1"></i> Pending Applications
                        @if(isset($applications) && $applications->where('status', 'pending')->count() > 0)
                            <span class="badge bg-danger ms-1">{{ $applications->where('status', 'pending')->count() }}</span>
                        @endif
                    </button>
                </li>
            </ul>
            
            <div class="tab-content" id="enrollmentTabsContent">
                <!-- Available Programs Tab -->
                <div class="tab-pane fade show active" id="programs-tab-pane" role="tabpanel" aria-labelledby="programs-tab" tabindex="0">
                    <h4 class="mb-3">Available Programs for Enrollment</h4>
                    
                    @if(empty($programs) || count($programs) == 0)
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> No programs are currently available for enrollment. Please check back later.
                        </div>
                    @else
                        @php
                            // Check if the student has any approved applications
                            $hasApprovedApplication = isset($applications) && $applications->where('status', 'approved')->count() > 0;
                        @endphp
                        
                        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                            @foreach($programs as $program)
                                <div class="col">
                                    <div class="card h-100 program-card {{ $hasApprovedApplication ? 'disabled-card' : '' }}">
                                        <div class="program-card-image">
                                            <img src="{{ asset('assets/images/BacgroundEnrollment.jpg') }}" class="card-img-top" alt="{{ $program->name }}">
                                            <div class="program-logo">
                                                <img src="{{ asset('assets/images/LogoEnrollment.png') }}" alt="University Logo">
                                            </div>
                                            @php
                                                $hasApplied = false;
                                                $applicationStatus = null;
                                                if(isset($applications) && count($applications) > 0) {
                                                    foreach($applications as $application) {
                                                        if($application->program_id == $program->id) {
                                                            $hasApplied = true;
                                                            $applicationStatus = $application->status;
                                                            break;
                                                        }
                                                    }
                                                }
                                            @endphp
                                            
                                            @if($hasApplied)
                                                @if($applicationStatus == 'pending')
                                                    <div class="status-indicator pending">
                                                        <i class="fas fa-clock"></i> Applied - Pending
                                                    </div>
                                                @elseif($applicationStatus == 'approved')
                                                    <div class="status-indicator approved">
                                                        <i class="fas fa-check-circle"></i> Approved
                                                    </div>
                                                @elseif($applicationStatus == 'rejected')
                                                    <div class="status-indicator rejected">
                                                        <i class="fas fa-times-circle"></i> Applied - Rejected
                                                    </div>
                                                @else
                                                    <div class="status-indicator">
                                                        <i class="fas fa-info-circle"></i> Applied - {{ ucfirst($applicationStatus) }}
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $program->name }}</h5>
                                            <p class="card-text text-muted small">{{ Str::limit($program->description, 100) }}</p>
                                        </div>
                                        <div class="card-footer bg-white border-top-0">
                                            @if($hasApprovedApplication)
                                                <button class="btn btn-secondary btn-sm w-100" disabled>
                                                    <i class="fas fa-ban me-1"></i> Already Enrolled
                                                </button>
                                            @elseif($hasApplied)
                                                <button class="btn btn-secondary btn-sm w-100" disabled>
                                                    <i class="fas fa-check-circle me-1"></i> Already Applied
                                                </button>
                                            @else
                                                <a href="#" class="btn btn-primary btn-sm apply-btn" 
                                                   data-bs-toggle="modal" 
                                                   data-bs-target="#applyModal" 
                                                   data-program-id="{{ $program->id }}" 
                                                   data-program-name="{{ $program->name }}"
                                                   data-school-year-start="{{ $program->school_year_start ?? date('Y') }}"
                                                   data-school-year-end="{{ $program->school_year_end ?? (date('Y') + 1) }}">
                                                    Apply for This Program
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                
                <!-- Pending Applications Tab -->
                <div class="tab-pane fade" id="applications-tab-pane" role="tabpanel" aria-labelledby="applications-tab" tabindex="0">
                    <h4 class="mb-3">Your Enrollment Applications</h4>
                    
                    @if(isset($applications) && count($applications) > 0)
                        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                            @foreach($applications as $application)
                                <div class="col">
                                    <div class="card h-100 program-card">
                                        <div class="program-card-image">
                                            <img src="{{ asset('assets/images/BacgroundEnrollment.jpg') }}" class="card-img-top" alt="Application">
                                            <div class="program-logo">
                                                <img src="{{ asset('assets/images/LogoEnrollment.png') }}" alt="University Logo">
                                            </div>
                                            @if($application->status == 'pending')
                                                <div class="status-indicator pending">
                                                    <i class="fas fa-clock"></i> Pending
                                                </div>
                                            @elseif($application->status == 'approved')
                                                <div class="status-indicator approved">
                                                    <i class="fas fa-check-circle"></i> Approved
                                                </div>
                                            @elseif($application->status == 'rejected')
                                                <div class="status-indicator rejected">
                                                    <i class="fas fa-times-circle"></i> Rejected
                                                </div>
                                            @else
                                                <div class="status-indicator">
                                                    <i class="fas fa-info-circle"></i> {{ ucfirst($application->status) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $application->program ? $application->program->name : 'Unknown Program' }}</h5>
                                            <p class="card-text text-muted small">
                                                <strong>Application ID:</strong> #{{ $application->id }}<br>
                                                <strong>Year Level:</strong> {{ $application->year_level }}<br>
                                                <strong>Submitted:</strong> {{ $application->created_at->format('M d, Y') }}
                                            </p>
                                        </div>
                                        <div class="card-footer bg-white border-top-0">
                                            <button class="btn btn-primary btn-sm view-application w-100" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#viewApplicationModal"
                                                data-application-id="{{ $application->id }}"
                                                data-program-name="{{ $application->program ? $application->program->name : 'Unknown Program' }}"
                                                data-year-level="{{ $application->year_level }}"
                                                data-status="{{ $application->status }}"
                                                data-submitted-date="{{ $application->created_at->format('M d, Y') }}"
                                                data-school-year-start="{{ $application->school_year_start ?? '' }}"
                                                data-school-year-end="{{ $application->school_year_end ?? '' }}"
                                                data-notes="{{ $application->notes }}">
                                                <i class="fas fa-eye me-1"></i> View Details
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> You haven't submitted any applications yet.
                        </div>
                        
                        <!-- Debug button to check applications data - hidden in production -->
                        <div class="text-center mt-3">
                            <button id="debugApplicationsBtn" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-bug me-1"></i> Debug Applications
                            </button>
                            <div id="debugResults" class="mt-3 p-3 border rounded bg-light d-none">
                                <pre class="mb-0 text-start" style="max-height:400px;overflow:auto;"></pre>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Enrollment Applications Table - Removed as it's now in the tab interface -->
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
                        <label for="student_status" class="form-label">Student Status</label>
                        <select class="form-select" id="student_status" name="student_status" required>
                            <option value="">Select Status</option>
                            <option value="Regular">Regular</option>
                            <option value="Probation">Probation</option>
                            <option value="Irregular">Irregular</option>
                        </select>
                        <small class="text-muted mt-1 d-block">Your status determines which documents you need to submit.</small>
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
                        <label for="school_year" class="form-label">School Year (SY)</label>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <span id="school-year-display">School year is automatically set by the system</span>
                        </div>
                        <input type="hidden" id="school_year_start" name="school_year_start">
                        <input type="hidden" id="school_year_end" name="school_year_end">
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Additional Notes (Optional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Add any additional information about your application here..."></textarea>
                    </div>
                    
                    <div class="alert alert-warning d-none" id="google-drive-status">
                        <i class="fas fa-exclamation-triangle me-2"></i> 
                        Google Drive connection status is being checked...
                    </div>
                    
                    <!-- Requirements section -->
                    <div id="requirements-container">
                        <div class="alert alert-info mt-4">
                            <i class="fas fa-info-circle me-2"></i> Please select your student status to view required documents.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submit-application-btn">
                        <i class="fas fa-paper-plane me-1"></i> Submit Application
                    </button>
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
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="fw-bold">Student Status:</label>
                            <div id="view-student-status" class="form-text-static"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="fw-bold">School Year:</label>
                            <div id="view-school-year" class="form-text-static"></div>
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
    
    /* Disabled program cards when already enrolled - no opacity effect */
    .program-card.disabled-card {
        position: relative;
        pointer-events: none;
        box-shadow: none !important;
        transform: none !important;
    }
    
    /* Removing the white overlay */
    /* .program-card.opacity-50::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.1);
        z-index: 10;
        border-radius: inherit;
    } */
    
    .program-card.disabled-card:hover {
        transform: none !important;
        box-shadow: none !important;
    }
    
    /* Tab styling */
    #enrollmentTabs {
        border-bottom: 1px solid #dee2e6;
    }
    
    #enrollmentTabs .nav-link {
        color: #6c757d;
        font-weight: 500;
        border: 1px solid transparent;
        border-top-left-radius: 0.25rem;
        border-top-right-radius: 0.25rem;
        padding: 0.75rem 1rem;
        margin-bottom: -1px;
    }
    
    #enrollmentTabs .nav-link:hover {
        border-color: #e9ecef #e9ecef #dee2e6;
    }
    
    #enrollmentTabs .nav-link.active {
        color: #495057;
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
    }
    
    #enrollmentTabs .nav-link .badge {
        font-size: 0.7rem;
        padding: 0.2rem 0.4rem;
        vertical-align: top;
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
    
    /* Status indicator for application cards */
    .status-indicator {
        position: absolute;
        top: 0;
        right: 0;
        padding: 5px 12px;
        border-radius: 0;
        font-size: 0.8rem;
        font-weight: 600;
        color: white;
        background-color: #6c757d;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        margin: 0;
    }

    .status-indicator.pending {
        background-color: #ffc107;
        color: #212529;
    }

    .status-indicator.approved {
        background-color: #28a745;
    }

    .status-indicator.rejected {
        background-color: #dc3545;
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

    /* Drag & Drop Zone Styling (from requirements page) */
    .drag-drop-zone {
        transition: all 0.3s ease;
        background-color: #f8f9fa;
        min-height: 120px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: 2px dashed #dee2e6;
        border-radius: 5px;
        margin-bottom: 15px;
    }
    
    /* Compact version of drag-drop-zone */
    .drag-drop-zone.compact {
        min-height: 70px;
        margin-bottom: 10px;
        padding: 10px 5px;
    }
    
    .drag-drop-zone.compact i {
        font-size: 1.2rem;
        margin-bottom: 5px;
    }
    
    .drag-drop-zone.dragover {
        background-color: #e9ecef;
        border-color: #0d6efd !important;
    }
    
    .drag-drop-zone i {
        color: #6c757d;
        font-size: 2rem;
        margin-bottom: 10px;
    }
    
    .drag-drop-zone:hover {
        background-color: #e9ecef;
    }
    
    .file-preview {
        transition: all 0.3s ease;
        padding: 10px;
        background-color: #f8f9fa;
        border-radius: 5px;
        margin-bottom: 10px;
        display: none;
    }
    
    /* Compact version of file preview */
    .file-preview.compact {
        padding: 5px;
        margin-bottom: 5px;
        font-size: 0.9rem;
    }
    
    .file-preview.compact .file-icon {
        font-size: 1rem;
    }
    
    .file-preview.show {
        display: block;
    }
    
    .progress {
        height: 5px;
        margin-top: 5px;
    }
    
    .folder-name {
        font-weight: 600;
        color: #0d6efd;
        margin-bottom: 5px;
    }
    
    /* Modal styling for many requirements */
    #applyModal .modal-dialog {
        max-width: 900px;
    }
    
    #applyModal .modal-body {
        max-height: 75vh;
        overflow-y: auto;
        padding: 1.5rem;
    }
    
    /* Requirements card styling */
    .requirement-card {
        transition: all 0.2s ease;
        border: 1px solid #e0e0e0;
    }
    
    .requirement-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.08) !important;
    }
    
    .requirement-card .card-body {
        padding-top: 0.75rem;
    }
    
    /* Style when file is uploaded */
    .file-preview.show + input[data-uploaded="true"] ~ .drag-drop-zone {
        border-color: #28a745;
        background-color: rgba(40, 167, 69, 0.05);
    }
    
    /* Already enrolled alert styling */
    .already-enrolled-alert {
        width: auto;
        max-width: 350px;
        padding: 0.5rem 1rem;
        margin-bottom: 1rem;
        font-size: 0.85rem;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if student has an approved application
    const hasApprovedApplication = {{ isset($applications) && $applications->where('status', 'approved')->count() > 0 ? 'true' : 'false' }};
    
    // Handle apply button click to set program info in modal
    const applyButtons = document.querySelectorAll('.apply-btn');
    applyButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            // If student already has an approved application, prevent opening the modal
            if (hasApprovedApplication) {
                event.preventDefault();
                alert('You already have an approved enrollment. New applications are not permitted at this time.');
                return;
            }
            
            const programId = this.dataset.programId;
            const programName = this.dataset.programName;
            const schoolYearStart = this.dataset.schoolYearStart || new Date().getFullYear();
            const schoolYearEnd = this.dataset.schoolYearEnd || (parseInt(schoolYearStart) + 1);
            
            document.getElementById('program_id').value = programId;
            document.getElementById('selected-program-name').textContent = programName;
            
            // Set the school year values in the hidden fields
            document.getElementById('school_year_start').value = schoolYearStart;
            document.getElementById('school_year_end').value = schoolYearEnd;
            
            // Display the school year in the info alert
            document.getElementById('school-year-display').textContent = 
                `School year ${schoolYearStart} - ${schoolYearEnd} is set for this enrollment`;
            
            // Reset status and year level selections
            document.getElementById('student_status').value = '';
            document.getElementById('year_level').value = '';
            
            // Clear the requirements container until status is selected
            document.getElementById('requirements-container').innerHTML = `
                <div class="alert alert-info mt-4">
                    <i class="fas fa-info-circle me-2"></i> Please select your student status to view required documents.
                </div>
            `;
            
            // Check Google Drive connectivity
            checkGoogleDriveStatus();
        });
    });
    
    // Add validation for school year fields
    const schoolYearStart = document.getElementById('school_year_start');
    const schoolYearEnd = document.getElementById('school_year_end');
    
    if (schoolYearStart && schoolYearEnd) {
        // Validate that the values are present before submitting
        const applicationForm = document.getElementById('applicationForm');
        if (applicationForm) {
            applicationForm.addEventListener('submit', function(e) {
                // Ensure school year fields have values before submitting
                if (!schoolYearStart.value || !schoolYearEnd.value) {
                    e.preventDefault();
                    alert('School year information is missing. Please try again or contact support.');
                    return false;
                }
                
                // Also check that end year is >= start year
                if (parseInt(schoolYearEnd.value) < parseInt(schoolYearStart.value)) {
                    e.preventDefault();
                    alert('Invalid school year range. End year must be equal to or greater than start year.');
                    return false;
                }
                
                return true;
            });
        }
    }
    
    // Debug applications button
    const debugBtn = document.getElementById('debugApplicationsBtn');
    if (debugBtn) {
        debugBtn.addEventListener('click', function() {
            const resultsContainer = document.getElementById('debugResults');
            const preElement = resultsContainer.querySelector('pre');
            
            resultsContainer.classList.remove('d-none');
            preElement.textContent = 'Loading application data...';
            
            // Call the debug endpoint
            fetch('{{ route("tenant.student.enrollment.debug-applications", ["tenant" => tenant("id")]) }}')
                .then(response => response.json())
                .then(data => {
                    // Format the JSON response
                    preElement.textContent = JSON.stringify(data, null, 2);
                })
                .catch(error => {
                    preElement.textContent = 'Error fetching application data: ' + error.message;
                });
        });
    }
    
    // Tab management - Switch to applications tab on submission or if there's a success message
    const urlParams = new URLSearchParams(window.location.search);
    const successParam = urlParams.get('success');
    const errorParam = urlParams.get('error');
    
    // Check for session success message
    @if(session('success'))
        // Switch to applications tab
        const applicationsTab = document.getElementById('applications-tab');
        if (applicationsTab) {
            applicationsTab.click();
        }
    @endif
    
    // Add event listener to the form submission to switch tabs after successful submission
    const applicationForm = document.getElementById('applicationForm');
    if (applicationForm) {
        applicationForm.addEventListener('submit', function() {
            // Store a flag in sessionStorage to indicate we should switch to applications tab on next page load
            sessionStorage.setItem('showApplicationsTab', 'true');
        });
    }
    
    // Check if we should show applications tab based on sessionStorage
    if (sessionStorage.getItem('showApplicationsTab') === 'true') {
        const applicationsTab = document.getElementById('applications-tab');
        if (applicationsTab) {
            applicationsTab.click();
        }
        // Clear the flag after switching tabs
        sessionStorage.removeItem('showApplicationsTab');
    }
    
    // Function to check Google Drive connectivity
    function checkGoogleDriveStatus() {
        const statusAlert = document.getElementById('google-drive-status');
        statusAlert.classList.remove('d-none', 'alert-success', 'alert-danger');
        statusAlert.classList.add('alert-warning');
        statusAlert.innerHTML = `
            <i class="fas fa-sync fa-spin me-2"></i> 
            Checking Google Drive connection status...
        `;
        
        // Fetch the Google Drive status from the backend
        fetch('{{ route("tenant.student.enrollment.drive-status", ["tenant" => tenant("id")]) }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    statusAlert.classList.remove('alert-warning', 'alert-danger');
                    statusAlert.classList.add('alert-success');
                    statusAlert.innerHTML = `
                        <i class="fas fa-check-circle me-2"></i> 
                        Google Drive is connected. Your documents will be securely stored.
                    `;
                    
                    // Hide the alert after 5 seconds
                    setTimeout(() => {
                        statusAlert.classList.add('d-none');
                    }, 5000);
                } else {
                    statusAlert.classList.remove('alert-warning', 'alert-success');
                    statusAlert.classList.add('alert-danger');
                    statusAlert.innerHTML = `
                        <i class="fas fa-exclamation-circle me-2"></i> 
                        Google Drive connection issue: ${data.message || 'Unknown error'}. Your documents will be stored locally.
                    `;
                }
            })
            .catch(error => {
                console.error('Error checking Google Drive status:', error);
                statusAlert.classList.remove('alert-warning', 'alert-success');
                statusAlert.classList.add('alert-danger');
                statusAlert.innerHTML = `
                    <i class="fas fa-exclamation-circle me-2"></i> 
                    Could not check Google Drive status. Your documents will be stored locally.
                `;
            });
    }
    
    // Add event listener for student status change
    document.getElementById('student_status').addEventListener('change', function() {
        const programId = document.getElementById('program_id').value;
        const studentStatus = this.value;
        
        if (programId && studentStatus) {
            loadProgramRequirements(programId, studentStatus);
        } else {
            document.getElementById('requirements-container').innerHTML = `
                <div class="alert alert-info mt-4">
                    <i class="fas fa-info-circle me-2"></i> Please select your student status to view required documents.
                </div>
            `;
        }
    });
    
    // Function to handle file selection via click or drag & drop
    function setupFileUpload(containerId, fileInputId, previewId, progressId, folderUrl) {
        const container = document.getElementById(containerId);
        const fileInput = document.getElementById(fileInputId);
        const preview = document.getElementById(previewId);
        
        if (!container || !fileInput || !preview) return;
        
        // Prevent default drag behaviors
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            container.addEventListener(eventName, preventDefaults, false);
        });
        
        // Highlight drop zone when dragging over it
        ['dragenter', 'dragover'].forEach(eventName => {
            container.addEventListener(eventName, function() {
                container.classList.add('dragover');
            }, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            container.addEventListener(eventName, function() {
                container.classList.remove('dragover');
            }, false);
        });
        
        // Handle dropped files
        container.addEventListener('drop', function(e) {
            const dt = e.dataTransfer;
            fileInput.files = dt.files;
            updateFilePreview(fileInput, preview);
            
            // Auto-upload file if dropped
            if (fileInput.files && fileInput.files.length > 0) {
                const folderId = fileInputId.split('_').pop();
                if (folderId) {
                    uploadFileToFolder(fileInput, folderId);
                }
            }
        }, false);
        
        // Handle file selection via input
        fileInput.addEventListener('change', function() {
            updateFilePreview(this, preview);
        });
        
        // Handle click on drop zone to open file browser
        container.addEventListener('click', function() {
            fileInput.click();
        });
        
        // Remove file button event
        const removeBtn = preview.querySelector('.remove-file');
        if (removeBtn) {
            removeBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                fileInput.value = '';
                preview.classList.remove('show');
                
                // Reset progress bar
                const progressBar = document.getElementById(progressId);
                if (progressBar) {
                    progressBar.style.width = '0%';
                    progressBar.classList.remove('bg-success');
                    progressBar.textContent = '';
                }
                
                // Mark as not uploaded
                fileInput.dataset.uploaded = 'false';
                
                // Re-enable the submit button if it was disabled
                const submitBtn = document.getElementById('submit-application-btn');
                if (submitBtn) {
                    submitBtn.disabled = false;
                }
            });
        }
        
        // Add folder URL indicator if available
        if (folderUrl) {
            const folderLink = document.createElement('a');
            folderLink.href = folderUrl;
            folderLink.target = '_blank';
            folderLink.classList.add('btn', 'btn-sm', 'btn-outline-info', 'mt-2');
            folderLink.innerHTML = '<i class="fas fa-folder-open me-1"></i> View Folder';
            
            // Add the folder link after the preview div
            if (preview.parentElement) {
                preview.parentElement.insertBefore(folderLink, preview.nextSibling);
            }
        }
    }
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    function updateFilePreview(fileInput, preview) {
        if (fileInput.files && fileInput.files[0]) {
            const file = fileInput.files[0];
            const fileNameElement = preview.querySelector('.file-name');
            const fileSizeElement = preview.querySelector('.file-size');
            
            if (fileNameElement) fileNameElement.textContent = file.name;
            if (fileSizeElement) fileSizeElement.textContent = formatFileSize(file.size);
            
            // Update file type icon
            const iconElement = preview.querySelector('.file-icon');
            if (iconElement) {
                let iconClass = 'fa-file';
                if (file.type.includes('pdf')) {
                    iconClass = 'fa-file-pdf';
                } else if (file.type.includes('image')) {
                    iconClass = 'fa-file-image';
                } else if (file.type.includes('word')) {
                    iconClass = 'fa-file-word';
                } else if (file.type.includes('excel')) {
                    iconClass = 'fa-file-excel';
                }
                
                iconElement.className = `fas ${iconClass} text-primary`;
            }
            
            preview.classList.add('show');
        } else {
            preview.classList.remove('show');
        }
    }
    
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    // Function to load program requirements based on program ID and student status
    function loadProgramRequirements(programId, studentStatus) {
        const requirementsContainer = document.getElementById('requirements-container');
        requirementsContainer.innerHTML = '<div class="text-center my-3"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">Loading requirements...</p></div>';
        
        // Fix the route construction to properly include the programId parameter
        let url = "{{ route('tenant.student.enrollment.program-requirements', ['tenant' => tenant('id'), 'programId' => '__PROGRAM_ID__']) }}".replace('__PROGRAM_ID__', programId);
        
        // Add status parameter to the URL
        url += '?status=' + encodeURIComponent(studentStatus);
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let requirementsHtml = `<h5 class="mt-4 mb-3">Required Documents for ${studentStatus} Students</h5>`;
                    
                    // Display a message if we're in demo mode with no actual requirement folders
                    const hasRequirementFolders = Array.isArray(data.requirementFolders) && data.requirementFolders.length > 0;
                    
                    if (hasRequirementFolders) {
                        requirementsHtml += `
                            <div class="alert alert-info mb-3">
                                <i class="fas fa-info-circle me-2"></i> 
                                Please upload all required documents to complete your application.
                            </div>
                        `;
                        
                        // Create a file upload for each folder
                        requirementsHtml += `
                            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                        `;
                        
                        data.requirementFolders.forEach((folder, index) => {
                            const folderId = folder.id;
                            const folderName = folder.name;
                            const inputId = `folder_file_${folderId}`;
                            const dragDropId = `drag_drop_${folderId}`;
                            const previewId = `file_preview_${folderId}`;
                            const progressId = `upload_progress_${folderId}`;
                            
                            requirementsHtml += `
                                <div class="col">
                                    <div class="card h-100 shadow-sm requirement-card">
                                        <div class="card-body p-3">
                                            <h6 class="card-title mb-2 folder-name text-truncate" data-bs-toggle="tooltip" title="${folderName}">
                                                ${folderName}
                                                <span class="ms-1 text-muted" style="font-size: 0.7rem;">
                                                    <i class="fas fa-question-circle" data-bs-toggle="tooltip" title="Upload a PDF, JPG, or PNG file (Max: 5MB)"></i>
                                                </span>
                                            </h6>
                                            <div id="${dragDropId}" class="drag-drop-zone compact">
                                                <i class="fas fa-cloud-upload-alt"></i>
                                                <p class="mb-0 small">Click or drop file here</p>
                                            </div>
                                            <div id="${previewId}" class="file-preview compact">
                                                <div class="d-flex align-items-center">
                                                    <i class="file-icon fas fa-file me-2 text-primary"></i>
                                                    <div class="flex-grow-1">
                                                        <span class="file-name small text-truncate d-inline-block" style="max-width: 120px;">filename.pdf</span>
                                                        <div><small class="text-muted file-size" style="font-size: 0.7rem;">123 KB</small></div>
                                                    </div>
                                                    <button class="btn btn-sm btn-outline-danger remove-file py-0 px-1">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                                <div class="progress mt-1" style="height: 3px;">
                                                    <div id="${progressId}" class="progress-bar progress-bar-striped" role="progressbar" style="width: 0%"></div>
                                                </div>
                                            </div>
                                            <input type="file" id="${inputId}" name="${inputId}" class="d-none" 
                                                accept=".pdf,.jpg,.jpeg,.png" required>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        
                        requirementsHtml += `</div>`;
                        
                        // Add a checkbox to confirm understanding of requirements
                        requirementsHtml += `
                            <div class="form-check mb-4 mt-3">
                                <input class="form-check-input" type="checkbox" id="confirmRequirements" required>
                                <label class="form-check-label" for="confirmRequirements">
                                    I confirm that all uploaded documents are authentic and accurate.
                                </label>
                            </div>
                        `;
                    } else {
                        // No requirement folders found - show instruction message only
                        requirementsHtml += `
                            <div class="alert alert-warning mb-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>No Document Requirement Folders Found</strong><br>
                                ${data.message || 'Please set up the requirement folders in the Requirements module, or contact your system administrator.'}
                            </div>
                            <div class="mt-4 text-center">
                                <p>Your administrator needs to create document folders in the Requirements module first.</p>
                                <p>Each folder should be named with the appropriate student status tag:</p>
                                <ul class="list-unstyled">
                                    <li><code>[Regular]</code> - For regular student documents</li>
                                    <li><code>[Irregular]</code> - For irregular student documents</li>
                                    <li><code>[Probation]</code> - For probation student documents</li>
                                </ul>
                                <div class="d-grid gap-2 col-md-6 mx-auto mt-3">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        <i class="fas fa-times me-1"></i> Close
                                    </button>
                                </div>
                            </div>
                        `;
                        
                        // Add a checkbox to confirm understanding of requirements (hidden but may be required by validation)
                        requirementsHtml += `
                            <div class="form-check mb-4 d-none">
                                <input class="form-check-input" type="checkbox" id="confirmRequirements" checked>
                                <label class="form-check-label" for="confirmRequirements">
                                    I confirm that all uploaded documents are authentic and accurate.
                                </label>
                            </div>
                        `;
                    }
                    
                    requirementsContainer.innerHTML = requirementsHtml;
                    
                    // Initialize tooltips for folder names and help icons
                    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                    tooltips.forEach(tooltip => {
                        new bootstrap.Tooltip(tooltip);
                    });
                    
                    // Setup file uploads after adding elements to DOM
                    if (hasRequirementFolders) {
                        data.requirementFolders.forEach(folder => {
                            const folderId = folder.id;
                            setupFileUpload(
                                `drag_drop_${folderId}`, 
                                `folder_file_${folderId}`, 
                                `file_preview_${folderId}`,
                                `upload_progress_${folderId}`,
                                folder.url || null
                            );
                            
                            // Add immediate upload behavior for each file input
                            const fileInput = document.getElementById(`folder_file_${folderId}`);
                            if (fileInput) {
                                fileInput.addEventListener('change', function() {
                                    if (this.files && this.files.length > 0) {
                                        uploadFileToFolder(this, folderId);
                                    }
                                });
                            }
                        });
                    } else {
                        // Setup file uploads for demo fields
                        const setupFields = ['id_photo', 'transcript', 'birth_certificate', 
                                            'subject_list', 'dean_approval', 'probation_letter',
                                            'academic_plan', 'counselor_note'];
                                            
                        setupFields.forEach(field => {
                            const element = document.getElementById(field);
                            if (element) {
                                setupFileUpload(
                                    `drag_drop_${field}`, 
                                    field, 
                                    `file_preview_${field}`,
                                    `upload_progress_${field}`
                                );
                            }
                        });
                    }
                    
                    // Add submit handler to check all required files are uploaded
                    setupApplicationFormSubmitHandler(hasRequirementFolders, data.requirementFolders);
                    
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
    
    // Function to upload file to folder
    function uploadFileToFolder(fileInput, folderId) {
        // Get the progress bar element
        const progressId = `upload_progress_${folderId}`;
        const progressBar = document.getElementById(progressId);
        
        if (!fileInput.files || fileInput.files.length === 0) {
            console.error('No file selected for upload');
            return;
        }
        
        // Validate file size (max 5MB)
        const maxSize = 5 * 1024 * 1024; // 5MB
        if (fileInput.files[0].size > maxSize) {
            alert('File size exceeds the 5MB limit. Please choose a smaller file.');
            fileInput.value = '';
            const preview = document.getElementById(`file_preview_${folderId}`);
            if (preview) preview.classList.remove('show');
            return;
        }
        
        // Create form data for the upload
        const formData = new FormData();
        formData.append('file', fileInput.files[0]);
        formData.append('_token', '{{ csrf_token() }}');
        
        // Get custom filename - use application ID if possible
        const programId = document.getElementById('program_id').value;
        const studentStatus = document.getElementById('student_status').value;
        const customFilename = `[{{ tenant('id') }}]_App_${programId}_${studentStatus}_${fileInput.files[0].name}`;
        formData.append('custom_filename', customFilename);
        
        // Create and configure the XMLHttpRequest
        const xhr = new XMLHttpRequest();
        
        // Configure the upload progress handler
        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable && progressBar) {
                const percentComplete = Math.round((e.loaded / e.total) * 100);
                progressBar.style.width = percentComplete + '%';
                progressBar.textContent = percentComplete + '%';
                
                // Update the progress bar class based on progress
                if (percentComplete < 25) {
                    progressBar.className = 'progress-bar progress-bar-striped bg-danger';
                } else if (percentComplete < 75) {
                    progressBar.className = 'progress-bar progress-bar-striped bg-warning';
                } else if (percentComplete < 100) {
                    progressBar.className = 'progress-bar progress-bar-striped bg-info';
                } else {
                    progressBar.className = 'progress-bar bg-success';
                }
            }
        });
        
        // Handle upload completion
        xhr.addEventListener('load', function() {
            if (xhr.status >= 200 && xhr.status < 300) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    
                    if (response.success) {
                        console.log('File uploaded successfully:', response.file);
                        
                        // Update the progress bar to show completion
                        if (progressBar) {
                            progressBar.style.width = '100%';
                            progressBar.className = 'progress-bar bg-success';
                            progressBar.textContent = 'Uploaded';
                        }
                        
                        // Mark the file as successfully uploaded
                        fileInput.dataset.uploaded = 'true';
                        fileInput.dataset.fileId = response.file.id;
                        fileInput.dataset.filePath = response.file.webViewLink || '';
                        
                        // Add success indicator to the preview
                        const preview = document.getElementById(`file_preview_${folderId}`);
                        if (preview) {
                            const successIndicator = document.createElement('div');
                            successIndicator.className = 'text-success mt-1';
                            successIndicator.innerHTML = '<i class="fas fa-check-circle"></i> Successfully uploaded to Google Drive';
                            
                            // Check if success indicator already exists
                            const existingIndicator = preview.querySelector('.text-success');
                            if (!existingIndicator) {
                                preview.appendChild(successIndicator);
                            }
                        }
                    } else {
                        console.error('File upload failed:', response.message);
                        
                        // Update progress bar to show error
                        if (progressBar) {
                            progressBar.className = 'progress-bar bg-danger';
                            progressBar.textContent = 'Failed';
                        }
                        
                        alert('Failed to upload file: ' + (response.message || 'Unknown error'));
                    }
                } catch (e) {
                    console.error('Error parsing upload response:', e);
                    
                    if (progressBar) {
                        progressBar.className = 'progress-bar bg-danger';
                        progressBar.textContent = 'Error';
                    }
                    
                    alert('An error occurred during file upload. Please try again.');
                }
            } else {
                console.error('Upload request failed with status:', xhr.status);
                
                if (progressBar) {
                    progressBar.className = 'progress-bar bg-danger';
                    progressBar.textContent = 'Failed';
                }
                
                alert('Upload failed. Server returned status: ' + xhr.status);
            }
        });
        
        // Handle network errors
        xhr.addEventListener('error', function() {
            console.error('Network error during file upload');
            
            if (progressBar) {
                progressBar.className = 'progress-bar bg-danger';
                progressBar.textContent = 'Network Error';
            }
            
            alert('A network error occurred during file upload. Please check your connection and try again.');
        });
        
        // Handle upload abortion
        xhr.addEventListener('abort', function() {
            console.warn('File upload aborted');
            
            if (progressBar) {
                progressBar.className = 'progress-bar bg-warning';
                progressBar.textContent = 'Aborted';
            }
        });
        
        // Open and send the request
        xhr.open('POST', "{{ route('tenant.enrollment.uploads', ['tenant' => tenant('id'), 'folderId' => '__FOLDER_ID__']) }}".replace('__FOLDER_ID__', folderId), true);
        xhr.send(formData);
        
        console.log('Started upload for file:', fileInput.files[0].name, 'to folder:', folderId);
    }
    
    // Function to set up application form submit handler
    function setupApplicationFormSubmitHandler(hasRequirementFolders, folders) {
        const form = document.getElementById('applicationForm');
        const submitBtn = document.getElementById('submit-application-btn');
        
        if (!form) return;

        // Hide the submit button if no folders
        if (!hasRequirementFolders || !folders || folders.length === 0) {
            if (submitBtn) {
                submitBtn.style.display = 'none';
            }
            return; // Don't set up the handler if no folders
        }
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Check if status and year level are selected
            const status = document.getElementById('student_status').value;
            const yearLevel = document.getElementById('year_level').value;
            
            if (!status || !yearLevel) {
                alert('Please select your Student Status and Year Level.');
                return;
            }
            
            // Check if the confirmation checkbox is checked
            const confirmCheckbox = document.getElementById('confirmRequirements');
            if (confirmCheckbox && !confirmCheckbox.checked) {
                alert('Please confirm that your documents are authentic and accurate.');
                return;
            }
            
            // Verify all required files are uploaded
            let missingFiles = false;
            let notUploadedFiles = [];
            
            // Check if all folder files are uploaded
            folders.forEach(folder => {
                const folderId = folder.id;
                const fileInput = document.getElementById(`folder_file_${folderId}`);
                
                if (fileInput) {
                    if (!fileInput.files || fileInput.files.length === 0) {
                        missingFiles = true;
                        const dragDrop = document.getElementById(`drag_drop_${folderId}`);
                        if (dragDrop) {
                            dragDrop.classList.add('border-danger');
                        }
                        
                        notUploadedFiles.push(folder.name);
                    } else if (fileInput.dataset.uploaded !== 'true') {
                        // File selected but not uploaded
                        const progressBar = document.getElementById(`upload_progress_${folderId}`);
                        if (progressBar) {
                            progressBar.className = 'progress-bar bg-warning';
                            progressBar.textContent = 'Not uploaded';
                        }
                        
                        notUploadedFiles.push(folder.name);
                    }
                }
            });
            
            if (missingFiles || notUploadedFiles.length > 0) {
                alert('Please upload all required documents: ' + notUploadedFiles.join(', '));
                return;
            }
            
            // If all checks pass, submit the form
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Submitting...';
            }
            
            form.submit();
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
            const schoolYearStart = this.dataset.schoolYearStart;
            const schoolYearEnd = this.dataset.schoolYearEnd;
            const notes = this.dataset.notes || 'No notes provided.';
            
            // Update modal content
            document.getElementById('view-application-id').textContent = '#' + applicationId;
            document.getElementById('view-program-name').textContent = programName;
            document.getElementById('view-year-level').textContent = 'Year ' + yearLevel;
            document.getElementById('view-submitted-date').textContent = submittedDate;
            document.getElementById('view-notes').textContent = notes;
            
            // Set school year information
            if (schoolYearStart && schoolYearEnd) {
                document.getElementById('view-school-year').textContent = `${schoolYearStart} - ${schoolYearEnd}`;
            } else {
                document.getElementById('view-school-year').textContent = 'Not specified';
            }
            
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
            
            // Initialize student status with loading indicator
            document.getElementById('view-student-status').innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Loading...';
            
            // Load documents
            loadApplicationDocuments(applicationId);
            
            // Load application details to get student status and admin feedback
            fetch(`{{ route('tenant.student.enrollment.application.details', ['tenant' => tenant('id'), 'applicationId' => '__APP_ID__']) }}`.replace('__APP_ID__', applicationId), {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.application) {
                    // Display student status with appropriate badge
                    const studentStatus = data.application.student_status || 'Regular';
                    let statusClass = 'bg-primary';
                    
                    if (studentStatus === 'Probation') {
                        statusClass = 'bg-warning text-dark';
                    } else if (studentStatus === 'Irregular') {
                        statusClass = 'bg-info';
                    }
                    
                    document.getElementById('view-student-status').innerHTML = 
                        `<span class="badge ${statusClass}">${studentStatus}</span>`;
                    
                    // Display admin feedback if available
                    if (data.application.admin_notes) {
                        document.getElementById('view-admin-notes').textContent = data.application.admin_notes;
                        document.getElementById('admin-feedback').classList.remove('d-none');
                    } else {
                        document.getElementById('admin-feedback').classList.add('d-none');
                    }
                } else {
                    document.getElementById('view-student-status').textContent = 'Regular';
                    document.getElementById('view-school-year').textContent = 'Not specified';
                    document.getElementById('admin-feedback').classList.add('d-none');
                }
            })
            .catch(error => {
                console.error('Error loading application details:', error);
                document.getElementById('view-student-status').textContent = 'Regular';
                document.getElementById('view-school-year').textContent = 'Not specified';
            });
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
                    // Determine document icon based on mime type
                    let iconClass = 'fa-file';
                    if (document.mime_type) {
                        if (document.mime_type.includes('pdf')) {
                            iconClass = 'fa-file-pdf';
                        } else if (document.mime_type.includes('image')) {
                            iconClass = 'fa-file-image';
                        } else if (document.mime_type.includes('word')) {
                            iconClass = 'fa-file-word';
                        } else if (document.mime_type.includes('excel')) {
                            iconClass = 'fa-file-excel';
                        }
                    }
                    
                    // Get document name for display
                    let documentName = document.folder_name || document.field_name || 'Document';
                    
                    // Get file name for display
                    let fileName = document.display_name || document.file_name || 'File';
                    
                    // Clean up tenant prefix from file name if it still exists
                    if (fileName.includes(']_App')) {
                        fileName = fileName.replace(/\[[^\]]+\]_App\d+_\w+_/, '');
                    }
                    
                    // Format upload date
                    let uploadDate = 'Unknown date';
                    if (document.uploaded_at) {
                        try {
                            uploadDate = new Date(document.uploaded_at).toLocaleDateString();
                        } catch (e) {
                            console.error('Error formatting date:', e);
                        }
                    }
                    
                    // Build document item HTML
                    documentsHtml += `
                        <div class="document-item">
                            <div class="document-icon">
                                <i class="fas ${iconClass} text-primary"></i>
                            </div>
                            <div class="document-info">
                                <div class="document-name">${documentName}</div>
                                <div class="document-meta">
                                    ${fileName} - Uploaded on ${uploadDate}
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