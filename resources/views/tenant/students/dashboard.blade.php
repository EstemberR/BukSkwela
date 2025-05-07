@extends('tenant.layouts.app')

@section('title', 'Student Dashboard')

@section('content')
<div class="container">
    <!-- Information Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <!-- Information Tabs -->
                <div class="card-body pt-3 pb-2">
                    <ul class="nav nav-tabs smaller-tabs" id="infoTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button" role="tab" aria-controls="personal" aria-selected="true">
                                <i class="fas fa-user me-1"></i> Personal Information
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="academic-tab" data-bs-toggle="tab" data-bs-target="#academic" type="button" role="tab" aria-controls="academic" aria-selected="false">
                                <i class="fas fa-graduation-cap me-1"></i> Academic Information
                            </button>
                        </li>
                    </ul>
                
                    <!-- Button Section -->
                    <div class="row mt-3 mb-1">
                        <div class="col-12 d-flex gap-3">
                            <a href="#" class="btn btn-outline-primary btn-sm">Update Personal Information</a>
                            <a href="#" class="btn btn-outline-primary btn-sm">Update Academic Information</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- Personal Information Tab Content -->
        <div class="tab-pane fade show active" id="personal" role="tabpanel" aria-labelledby="personal-tab">
            <!-- User Information Section -->
    <div class="row mb-4">
                <div class="col-12">
                    <p class="text-muted user-info-heading mb-2">USER INFORMATION</p>
                    <div class="card">
                        <div class="card-body p-0">
                            <table class="user-info-table w-100">
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="info-label">First Name</div>
                                            <div class="info-value">JORELL</div>
                                        </td>
                                        <td>
                                            <div class="info-label">Middle Name</div>
                                            <div class="info-value">EBALES</div>
                                        </td>
                                        <td>
                                            <div class="info-label">Last Name</div>
                                            <div class="info-value">ABECIA</div>
                                        </td>
                                        <td>
                                            <div class="info-label">Suffix Name</div>
                                            <div class="info-value">--</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="info-label">Sex at Birth</div>
                                            <div class="info-value">MALE</div>
                                        </td>
                                        <td>
                                            <div class="info-label">Birth Date</div>
                                            <div class="info-value">SEPT. 9, 2003</div>
                                        </td>
                                        <td>
                                            <div class="info-label">Civil Status</div>
                                            <div class="info-value">SINGLE</div>
                                        </td>
                                        <td>
                                            <div class="info-label">Religion</div>
                                            <div class="info-value">BAPTIST</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="info-label">Contact Number</div>
                                            <div class="info-value">09514532566</div>
                                        </td>
                                        <td>
                                            <div class="info-label">Email Address</div>
                                            <div class="info-value">jorellabeciatnt@gmail.com</div>
                                        </td>
                                        <td>
                                            <div class="info-label">Facebook Username</div>
                                            <div class="info-value"><a href="#" class="text-primary">Jorell Abecia</a></div>
                                        </td>
                                        <td>
                                            <div class="info-label">Blood Type</div>
                                            <div class="info-value">A+</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="info-label">Has indigenous group</div>
                                            <div class="info-value">No</div>
                                        </td>
                                        <td>
                                            <div class="info-label">Indigenous group</div>
                                            <div class="info-value">None</div>
                                        </td>
                                        <td>
                                            <div class="info-label">Other Indigenous group</div>
                                            <div class="info-value"></div>
                                        </td>
                                        <td>
                                            <div class="info-label">DSWD 4Ps Number</div>
                                            <div class="info-value"></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="info-label">Disability</div>
                                            <div class="info-value"></div>
                                        </td>
                                        <td colspan="3"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Academic Information Tab Content -->
        <div class="tab-pane fade" id="academic" role="tabpanel" aria-labelledby="academic-tab">
            <!-- Overview Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <p class="text-muted user-info-heading mb-2">OVERVIEW</p>
                    <div class="card">
                        <div class="card-body p-0">
                            <table class="user-info-table w-100">
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="info-label">Educational Status</div>
                                            <div class="info-value">College Level</div>
                                        </td>
                                        <td>
                                            <div class="info-label">LRN (Learner's Reference Number)</div>
                                            <div class="info-value"></div>
                                        </td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Academic History Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <p class="text-muted user-info-heading mb-2">ACADEMIC HISTORY</p>
                    <div class="card">
                        <div class="card-body p-0">
                            <table class="user-info-table w-100">
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="info-label">School Name</div>
                                            <div class="info-value">HALAPITAN NATIONAL HIGH SCHOOL</div>
                                        </td>
                                        <td>
                                            <div class="info-label">Year From</div>
                                            <div class="info-value">2021</div>
                                        </td>
                                        <td>
                                            <div class="info-label">Year To</div>
                                            <div class="info-value">2022</div>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="info-label">Level</div>
                                            <div class="info-value">Senior High</div>
                                        </td>
                                        <td>
                                            <div class="info-label">Type of School</div>
                                            <div class="info-value">Public</div>
                                        </td>
                                        <td>
                                            <div class="info-label">Strand</div>
                                            <div class="info-value">STEM</div>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
        </div>
                    </div>
                </div>
            </div>

            <!-- School Address Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <p class="text-muted user-info-heading mb-2">SCHOOL ADDRESS</p>
                    <div class="card">
                        <div class="card-body p-0">
                            <table class="user-info-table w-100">
                                <tbody>
                                    <tr class="bg-light">
                                        <td>
                                            <div class="info-label">1. Is the address located in the Philippines?</div>
                                            <div class="info-value text-primary">Yes</div>
                                        </td>
                                        <td>
                                            <div class="info-label">Region</div>
                                            <div class="info-value text-primary">REGION X - NORTHERN MINDANAO</div>
                                        </td>
                                        <td>
                                            <div class="info-label">Province</div>
                                            <div class="info-value text-primary">BUKIDNON</div>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr class="bg-light">
                                        <td>
                                            <div class="info-label">City</div>
                                            <div class="info-value text-primary">SAN FERNANDO, BUKIDNON</div>
                                        </td>
                                        <td>
                                            <div class="info-label">Barangay</div>
                                            <div class="info-value text-primary">HALAPITAN (POB.)</div>
                                        </td>
                                        <td>
                                            <div class="info-label">Street</div>
                                            <div class="info-value text-primary">Purok-5</div>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enrollment Process Section -->
    <div class="row mb-4">
        <div class="col-12">
            <p class="text-muted user-info-heading mb-2">ENROLLMENT PROCESS</p>
            <div class="card">
                <div class="card-body p-0">
                    <div class="enrollment-process">
                        <div class="accordion compact-accordion" id="enrollmentAccordion">
                            <!-- Step 1 -->
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button compact-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        <i class="fas fa-file-alt me-2 text-primary"></i>
                                        <strong>Step 1: Application Submission</strong>
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#enrollmentAccordion">
                                    <div class="accordion-body compact-body">
                                        <p class="mb-2">Submit your application with personal information, program preferences, and academic details.</p>
                                        <div>
                                            <span class="badge bg-success me-2">Required</span>
                                            <small class="text-muted">Estimated completion time: 15-20 minutes</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Step 2 -->
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button compact-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        <i class="fas fa-file-upload me-2 text-primary"></i>
                                        <strong>Step 2: Document Upload</strong>
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#enrollmentAccordion">
                                    <div class="accordion-body compact-body">
                                        <p class="mb-2">Upload academic transcripts, identification, and other required documentation.</p>
                                        <div>
                                            <h6 class="mb-1 small">Required Documents:</h6>
                                            <ul class="mb-0 small">
                                                <li>High School Transcript / College Transcript</li>
                                                <li>Valid Government-issued ID</li>
                                                <li>Passport-sized photo (2x2)</li>
                                                <li>Proof of residence</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Step 3 -->
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingThree">
                                    <button class="accordion-button compact-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                        <i class="fas fa-tasks me-2 text-primary"></i>
                                        <strong>Step 3: Application Review</strong>
                                    </button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#enrollmentAccordion">
                                    <div class="accordion-body compact-body">
                                        <p class="mb-2">Your application will be reviewed by the admissions committee. This typically takes 5-7 business days.</p>
                                        <div class="alert alert-info py-2 px-3 mb-0 small">
                                            <i class="fas fa-info-circle me-2"></i>
                                            You will receive email notifications about your application status during this period.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Step 4 -->
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingFour">
                                    <button class="accordion-button compact-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                        <i class="fas fa-clipboard-check me-2 text-primary"></i>
                                        <strong>Step 4: Decision Notification</strong>
                                    </button>
                                </h2>
                                <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#enrollmentAccordion">
                                    <div class="accordion-body compact-body">
                                        <p class="mb-2">Receive notification about your application status (Approved, Pending, or Rejected).</p>
                                        <div class="d-flex text-center small">
                                            <div class="flex-fill">
                                                <span class="badge bg-success p-2 d-block mx-2 mb-1">Approved</span>
                                                <small>Ready to enroll</small>
                                            </div>
                                            <div class="flex-fill">
                                                <span class="badge bg-warning text-dark p-2 d-block mx-2 mb-1">Pending</span>
                                                <small>Additional info needed</small>
                                            </div>
                                            <div class="flex-fill">
                                                <span class="badge bg-danger p-2 d-block mx-2 mb-1">Rejected</span>
                                                <small>Not eligible</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Step 5 -->
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingFive">
                                    <button class="accordion-button compact-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                        <i class="fas fa-user-graduate me-2 text-primary"></i>
                                        <strong>Step 5: Enrollment Complete</strong>
                                    </button>
                                </h2>
                                <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#enrollmentAccordion">
                                    <div class="accordion-body compact-body">
                                        <p class="mb-2">Receive your student ID and course schedule to begin your academic journey.</p>
                                        <div class="text-center">
                                            <i class="fas fa-check-circle text-success fa-2x mb-1"></i>
                                            <p class="mb-0 small">Congratulations! You are now officially enrolled.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer p-2 text-center">
                    <a href="{{ route('tenant.student.enrollment', ['tenant' => tenant('id')]) }}" class="btn btn-success btn-sm">Apply for Enrollment</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 

@push('styles')
<style>
    /* Custom styles for user information table to exactly match the image */
    .user-info-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem; /* Smaller text for the entire table */
    }
    
    .user-info-table tr:nth-child(odd) {
        background-color: #f9f9f9;
    }
    
    .user-info-table tr:nth-child(even) {
        background-color: #ffffff;
    }
    
    .user-info-table td {
        padding: 10px 12px; /* Smaller padding */
        border: none;
        vertical-align: top;
        width: 25%;
    }
    
    .info-label {
        font-size: 0.75rem; /* Even smaller text for labels */
        color: #6c757d;
        margin-bottom: 3px; /* Reduced margin */
    }
    
    .info-value {
        font-size: 0.875rem; /* Smaller text for values */
        font-weight: 400;
        color: #212529;
    }
    
    /* User info heading style - even smaller */
    .user-info-heading {
        font-size: 0.8rem;
        font-weight: 500;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.4rem;
    }
    
    /* Customize nav tabs and buttons */
    .nav-tabs {
        border-bottom: 1px solid #dee2e6;
    }
    
    /* Smaller tabs styling */
    .smaller-tabs .nav-link {
        font-size: 0.9rem;
        padding: 0.4rem 0;
        margin-right: 1.5rem;
        color: #212529;
        border: none;
        border-bottom: 2px solid transparent;
    }
    
    .smaller-tabs .nav-link.active {
        font-weight: 600;
        color: #212529;
        background-color: transparent;
        border: none;
        border-bottom: 2px solid #212529;
    }
    
    .smaller-tabs .nav-link:hover {
        border: none;
        border-bottom: 2px solid #dee2e6;
    }
    
    /* Button styles */
    .btn-outline-primary {
        color: #212529;
        border-color: #dee2e6;
    }
    
    .btn-outline-primary:hover {
        background-color: #f8f9fa;
        color: #0d6efd;
        border-color: #0d6efd;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    
    /* Card styles with lighter shadow */
    .card {
        border-color: #dee2e6;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    
    /* Remove default Bootstrap card shadow if any */
    .card.shadow-sm {
        box-shadow: 0 1px 3px rgba(0,0,0,0.05) !important;
    }
    
    /* School address styling */
    .bg-light {
        background-color: #f0f8ff !important; /* Light blue background */
    }
    
    .text-primary {
        color: #0d6efd !important; /* Bright blue text */
    }
    
    /* Ensure other tables don't get affected */
    .table:not(.user-info-table) {
        border: 1px solid #dee2e6;
    }

    /* Enrollment process timeline styling */
    .enrollment-process {
        padding: 10px 5px;
    }
    
    /* Compact accordion styling for enrollment process */
    .compact-accordion .accordion-button {
        padding: 0.6rem 1rem;
        font-size: 0.85rem;
    }
    
    .compact-accordion .accordion-body {
        padding: 0.75rem 1rem;
        font-size: 0.8rem;
    }
    
    .compact-accordion p {
        margin-bottom: 0.5rem;
    }
    
    .compact-accordion ul {
        padding-left: 1.25rem;
    }
    
    .compact-accordion .badge {
        font-size: 0.7rem;
        padding: 0.2rem 0.4rem;
    }
    
    .compact-accordion .alert {
        font-size: 0.8rem;
        padding: 0.4rem 0.8rem;
    }
    
    .compact-accordion .text-muted {
        font-size: 0.75rem;
    }
    
    .compact-accordion h6 {
        font-size: 0.8rem;
    }
    
    .compact-accordion .fa-2x {
        font-size: 1.5em;
    }
    
    /* Make the Apply button smaller */
    .card-footer {
        padding: 0.5rem !important;
    }
    
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    
    .timeline:before {
        content: '';
        position: absolute;
        top: 0;
        left: 15px;
        height: 100%;
        width: 2px;
        background-color: #e9ecef;
    }
    
    .timeline-item {
        position: relative;
        padding-bottom: 25px;
    }
    
    .timeline-point {
        position: absolute;
        left: -30px;
        width: 30px;
        height: 30px;
        background-color: #f8f9fa;
        border: 2px solid #dee2e6;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1;
    }
    
    .timeline-point i {
        color: #6c757d;
        font-size: 0.9rem;
    }
    
    .timeline-item.active .timeline-point {
        background-color: #4f46e5;
        border-color: #4f46e5;
    }
    
    .timeline-item.active .timeline-point i {
        color: #fff;
    }
    
    .timeline-content {
        padding-left: 15px;
    }
    
    /* Dark mode support */
    .dark-mode .timeline:before {
        background-color: #374151;
    }
    
    .dark-mode .timeline-point {
        background-color: #1f2937;
        border-color: #374151;
    }
    
    .dark-mode .timeline-point i {
        color: #9ca3af;
    }
    
    .dark-mode .timeline-item.active .timeline-point {
        background-color: #4f46e5;
        border-color: #4f46e5;
    }
    
    .dark-mode .timeline-item.active .timeline-point i {
        color: #fff;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Any specific JavaScript for the student dashboard can go here
        console.log('Student dashboard loaded');
    });
</script>
@endpush 