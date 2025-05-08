@extends('tenant.layouts.app')

@section('title', 'Student Dashboard')

@section('content')
<!-- Toast Container for Notifications -->
<div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index: 1050;"></div>

<!-- Debug Information (only shown during development) -->


<!-- Loading Spinner - Add this after the debug-info div -->


<!-- No Data Alert - Add after loading spinner -->


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
                            <a href="#" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#updatePersonalInfoModal">Update Personal Information</a>
                            <a href="#" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#updateAcademicInfoModal">Update Academic Information</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Personal Information Modal -->
    <div class="modal fade" id="updatePersonalInfoModal" tabindex="-1" aria-labelledby="updatePersonalInfoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updatePersonalInfoModalLabel">Update Personal Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="personalInfoForm" action="{{ route('tenant.student.update-profile', ['tenant' => tenant('id')]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="middle_name" class="form-label">Middle Name</label>
                                    <input type="text" class="form-control" id="middle_name" name="middle_name">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="suffix" class="form-label">Suffix</label>
                                    <input type="text" class="form-control" id="suffix" name="suffix">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="sex" class="form-label">Sex at Birth</label>
                                    <select class="form-select" id="sex" name="sex" required>
                                        <option value="">Select</option>
                                        <option value="MALE">MALE</option>
                                        <option value="FEMALE">FEMALE</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="birth_date" class="form-label">Birth Date</label>
                                    <input type="date" class="form-control" id="birth_date" name="birth_date" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="civil_status" class="form-label">Civil Status</label>
                                    <select class="form-select" id="civil_status" name="civil_status" required>
                                        <option value="">Select</option>
                                        <option value="SINGLE">SINGLE</option>
                                        <option value="MARRIED">MARRIED</option>
                                        <option value="DIVORCED">DIVORCED</option>
                                        <option value="WIDOWED">WIDOWED</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="religion" class="form-label">Religion</label>
                                    <input type="text" class="form-control" id="religion" name="religion">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="blood_type" class="form-label">Blood Type</label>
                                    <select class="form-select" id="blood_type" name="blood_type">
                                        <option value="">Select</option>
                                        <option value="A+">A+</option>
                                        <option value="A-">A-</option>
                                        <option value="B+">B+</option>
                                        <option value="B-">B-</option>
                                        <option value="AB+">AB+</option>
                                        <option value="AB-">AB-</option>
                                        <option value="O+">O+</option>
                                        <option value="O-">O-</option>
                                        <option value="Unknown">Unknown</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="contact_number" class="form-label">Contact Number</label>
                                    <input type="text" class="form-control" id="contact_number" name="contact_number">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="facebook" class="form-label">Facebook Username</label>
                                    <input type="text" class="form-control" id="facebook" name="facebook">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="has_indigenous" class="form-label">Has Indigenous Group</label>
                                    <select class="form-select" id="has_indigenous" name="has_indigenous">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 indigenous-group-fields d-none">
                                <div class="form-group">
                                    <label for="indigenous_group" class="form-label">Indigenous Group</label>
                                    <select class="form-select" id="indigenous_group" name="indigenous_group">
                                        <option value="">Select Indigenous Group</option>
                                        <option value="Lumad">Lumad</option>
                                        <option value="Manobo">Manobo</option>
                                        <option value="Bagobo">Bagobo</option>
                                        <option value="B'laan">B'laan</option>
                                        <option value="Higaonon">Higaonon</option>
                                        <option value="Other">Other (please specify)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4 other-indigenous-field d-none">
                                <div class="form-group">
                                    <label for="other_indigenous" class="form-label">Other Indigenous Group</label>
                                    <input type="text" class="form-control" id="other_indigenous" name="other_indigenous">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="dswd_4ps" class="form-label">DSWD 4Ps Number</label>
                                    <input type="text" class="form-control" id="dswd_4ps" name="dswd_4ps">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="disability" class="form-label">Disability</label>
                                    <input type="text" class="form-control" id="disability" name="disability">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="personalInfoForm" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Academic Information Modal -->
    <div class="modal fade" id="updateAcademicInfoModal" tabindex="-1" aria-labelledby="updateAcademicInfoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateAcademicInfoModalLabel">Update Academic Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="academicInfoForm" action="{{ route('tenant.student.update-academic', ['tenant' => tenant('id')]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="educational_status" class="form-label">Educational Status</label>
                                    <select class="form-select" id="educational_status" name="educational_status" required>
                                        <option value="">Select Status</option>
                                        <option value="Elementary Level">Elementary Level</option>
                                        <option value="Elementary Graduate">Elementary Graduate</option>
                                        <option value="Junior High School Level">Junior High School Level</option>
                                        <option value="Junior High School Graduate">Junior High School Graduate</option>
                                        <option value="Senior High School Level">Senior High School Level</option>
                                        <option value="Senior High School Graduate">Senior High School Graduate</option>
                                        <option value="College Level">College Level</option>
                                        <option value="College Graduate">College Graduate</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lrn" class="form-label">Learner's Reference Number</label>
                                    <input type="text" class="form-control" id="lrn" name="lrn">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="school_name" class="form-label">School Name</label>
                                    <input type="text" class="form-control" id="school_name" name="school_name" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="year_from" class="form-label">Year From</label>
                                    <input type="text" class="form-control" id="year_from" name="year_from" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="year_to" class="form-label">Year To</label>
                                    <input type="text" class="form-control" id="year_to" name="year_to" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="academic_level" class="form-label">Academic Level</label>
                                    <select class="form-select" id="academic_level" name="academic_level" required>
                                        <option value="">Select Level</option>
                                        <option value="Elementary">Elementary</option>
                                        <option value="Junior High">Junior High</option>
                                        <option value="Senior High">Senior High</option>
                                        <option value="College">College</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="school_type" class="form-label">Type of School</label>
                                    <select class="form-select" id="school_type" name="school_type" required>
                                        <option value="">Select Type</option>
                                        <option value="Public">Public</option>
                                        <option value="Private">Private</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="strand" class="form-label">Strand (for Senior High)</label>
                                    <select class="form-select" id="strand" name="strand">
                                        <option value="">Select Strand</option>
                                        <option value="STEM">STEM</option>
                                        <option value="ABM">ABM</option>
                                        <option value="HUMSS">HUMSS</option>
                                        <option value="TVL">TVL</option>
                                        <option value="GAS">GAS</option>
                                        <option value="Arts and Design">Arts and Design</option>
                                        <option value="Sports">Sports</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <h6 class="mt-4 mb-3">School Address</h6>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="region" class="form-label">Region</label>
                                    <input type="text" class="form-control" id="region" name="region" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="province" class="form-label">Province</label>
                                    <input type="text" class="form-control" id="province" name="province" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="city" class="form-label">City/Municipality</label>
                                    <input type="text" class="form-control" id="city" name="city" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="barangay" class="form-label">Barangay</label>
                                    <input type="text" class="form-control" id="barangay" name="barangay" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="street" class="form-label">Street</label>
                                    <input type="text" class="form-control" id="street" name="street">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="academicInfoForm" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- Personal Information Tab Content -->
        <div class="tab-pane fade show active" id="personal" role="tabpanel" aria-labelledby="personal-tab">
            <!-- Student ID Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <p class="text-muted user-info-heading mb-2">STUDENT INFO</p>
                    <div class="card">
                        <div class="card-body p-0">
                            <table class="user-info-table w-100">
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="info-label">Student ID</div>
                                            <div class="info-value" id="student-id"></div>
                                        </td>
                                        <td>
                                            <div class="info-label">Student Name</div>
                                            <div class="info-value" id="student-name"></div>
                                        </td>
                                        <td>
                                            <div class="info-label">Email</div>
                                            <div class="info-value" id="student-email"></div>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
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
                                            <div class="info-value" id="user-first-name">--</div>
                                        </td>
                                        <td>
                                            <div class="info-label">Middle Name</div>
                                            <div class="info-value" id="user-middle-name">--</div>
                                        </td>
                                        <td>
                                            <div class="info-label">Last Name</div>
                                            <div class="info-value" id="user-last-name">--</div>
                                        </td>
                                        <td>
                                            <div class="info-label">Suffix Name</div>
                                            <div class="info-value" id="user-suffix">--</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="info-label">Sex at Birth</div>
                                            <div class="info-value" id="user-sex">--</div>
                                        </td>
                                        <td>
                                            <div class="info-label">Birth Date</div>
                                            <div class="info-value" id="user-birth-date">--</div>
                                        </td>
                                        <td>
                                            <div class="info-label">Civil Status</div>
                                            <div class="info-value" id="user-civil-status">--</div>
                                        </td>
                                        <td>
                                            <div class="info-label">Religion</div>
                                            <div class="info-value" id="user-religion">--</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="info-label">Contact Number</div>
                                            <div class="info-value" id="user-contact">--</div>
                                        </td>
                                        <td>
                                            <div class="info-label">Email Address</div>
                                            <div class="info-value" id="user-email">--</div>
                                        </td>
                                        <td>
                                            <div class="info-label">Facebook Username</div>
                                            <div class="info-value" id="user-facebook">--</div>
                                        </td>
                                        <td>
                                            <div class="info-label">Blood Type</div>
                                            <div class="info-value" id="user-blood-type">--</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="info-label">Has indigenous group</div>
                                            <div class="info-value" id="user-has-indigenous">--</div>
                                        </td>
                                        <td>
                                            <div class="info-label">Indigenous group</div>
                                            <div class="info-value" id="user-indigenous-group">--</div>
                                        </td>
                                        <td>
                                            <div class="info-label">Other Indigenous group</div>
                                            <div class="info-value" id="user-other-indigenous">--</div>
                                        </td>
                                        <td>
                                            <div class="info-label">DSWD 4Ps Number</div>
                                            <div class="info-value" id="user-dswd-4ps">--</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="info-label">Disability</div>
                                            <div class="info-value" id="user-disability">--</div>
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
                                            <div class="info-value" id="academic-educational-status">--</div>
                                        </td>
                                        <td>
                                            <div class="info-label">LRN (Learner's Reference Number)</div>
                                            <div class="info-value" id="academic-lrn">--</div>
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
                                            <div class="info-value" id="academic-school-name">--</div>
                                        </td>
                                        <td>
                                            <div class="info-label">Year From</div>
                                            <div class="info-value" id="academic-year-from">--</div>
                                        </td>
                                        <td>
                                            <div class="info-label">Year To</div>
                                            <div class="info-value" id="academic-year-to">--</div>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="info-label">Level</div>
                                            <div class="info-value" id="academic-level">--</div>
                                        </td>
                                        <td>
                                            <div class="info-label">Type of School</div>
                                            <div class="info-value" id="academic-school-type">--</div>
                                        </td>
                                        <td>
                                            <div class="info-label">Strand</div>
                                            <div class="info-value" id="academic-strand">--</div>
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
                                            <div class="info-value text-primary" id="academic-region">--</div>
                                        </td>
                                        <td>
                                            <div class="info-label">Province</div>
                                            <div class="info-value text-primary" id="academic-province">--</div>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr class="bg-light">
                                        <td>
                                            <div class="info-label">City</div>
                                            <div class="info-value text-primary" id="academic-city">--</div>
                                        </td>
                                        <td>
                                            <div class="info-label">Barangay</div>
                                            <div class="info-value text-primary" id="academic-barangay">--</div>
                                        </td>
                                        <td>
                                            <div class="info-label">Street</div>
                                            <div class="info-value text-primary" id="academic-street">--</div>
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
<!-- Add Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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

    #loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.7);
        z-index: 1000;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .spinner-container {
        text-align: center;
        background-color: white;
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add debug button to view and toggle debug info
        const debugBtn = document.createElement('button');
        debugBtn.className = 'btn btn-sm btn-outline-primary position-fixed';
        debugBtn.textContent = 'Debug';
        debugBtn.style.bottom = '10px';
        debugBtn.style.right = '10px';
        debugBtn.style.zIndex = '1050';
        debugBtn.onclick = toggleDebugInfo;
        document.body.appendChild(debugBtn);
        
        // Load student profile data
        fetchStudentData();
        
        // Form submission for personal info
        const personalInfoForm = document.getElementById('personalInfoForm');
        if (personalInfoForm) {
            personalInfoForm.addEventListener('submit', function(e) {
                e.preventDefault();
                updatePersonalInfo();
            });
        }
        
        // Form submission for academic info
        const academicInfoForm = document.getElementById('academicInfoForm');
        if (academicInfoForm) {
            academicInfoForm.addEventListener('submit', function(e) {
                e.preventDefault();
                updateAcademicInfo();
            });
        }
        
        // Handle indigenous group fields visibility
        const hasIndigenousSelect = document.getElementById('has_indigenous');
        const indigenousGroupFields = document.querySelector('.indigenous-group-fields');
        const indigenousGroupSelect = document.getElementById('indigenous_group');
        const otherIndigenousField = document.querySelector('.other-indigenous-field');
        
        // Initialize based on default value
        toggleIndigenousFields();
        
        // Add event listeners
        if (hasIndigenousSelect) {
            hasIndigenousSelect.addEventListener('change', toggleIndigenousFields);
        }
        
        if (indigenousGroupSelect) {
            indigenousGroupSelect.addEventListener('change', toggleOtherIndigenous);
        }
        
        function toggleIndigenousFields() {
            if (hasIndigenousSelect && indigenousGroupFields) {
                if (hasIndigenousSelect.value === '1') {
                    indigenousGroupFields.classList.remove('d-none');
                    toggleOtherIndigenous();
                } else {
                    indigenousGroupFields.classList.add('d-none');
                    if (otherIndigenousField) {
                        otherIndigenousField.classList.add('d-none');
                    }
                }
            }
        }
        
        function toggleOtherIndigenous() {
            if (indigenousGroupSelect && otherIndigenousField) {
                if (indigenousGroupSelect.value === 'Other') {
                    otherIndigenousField.classList.remove('d-none');
                } else {
                    otherIndigenousField.classList.add('d-none');
                }
            }
        }
    });
    
    // Function to show/hide debug information
    function toggleDebugInfo() {
        const debugInfo = document.getElementById('debug-info');
        if (debugInfo.style.display === 'none') {
            debugInfo.style.display = 'block';
        } else {
            debugInfo.style.display = 'none';
        }
    }
    
    // Function to update debug information
    function updateDebugInfo(data) {
        const debugContent = document.getElementById('debug-content');
        if (debugContent) {
            let html = '<div class="alert alert-info">';
            
            // Display tenant information
            if (data.debug) {
                html += '<h6>Tenant Database Information</h6>';
                html += `<p>Database: <strong>${data.debug.database || 'unknown'}</strong><br>`;
                html += `Tenant ID: <strong>${data.debug.tenant_id || 'unknown'}</strong><br>`;
                html += `Using Tenant DB: <strong>${data.debug.using_tenant_db ? 'Yes' : 'No'}</strong></p>`;
                
                // Display table information
                html += '<h6>Database Tables</h6>';
                html += `<p>Students Table Exists: <strong>${data.debug.students_table_exists ? 'Yes' : 'No'}</strong><br>`;
                html += `Student Info Table Exists: <strong>${data.debug.students_info_table_exists ? 'Yes' : 'No'}</strong></p>`;
                
                // Display student data
                if (data.student) {
                    html += '<h6>Student Data</h6>';
                    html += `<p>ID: <strong>${data.student.id}</strong><br>`;
                    html += `Student ID: <strong>${data.student.student_id || 'not set'}</strong><br>`;
                    html += `Email: <strong>${data.student.email}</strong></p>`;
                }
                
                // If there's an error, display it prominently
                if (data.message && !data.success) {
                    html += `<div class="alert alert-danger mt-2">${data.message}</div>`;
                }
                
                // Show table structure if available
                if (data.debug.table_structure) {
                    html += '<h6>Students Information Table Structure</h6>';
                    html += '<div style="max-height: 200px; overflow-y: auto;">';
                    html += '<table class="table table-sm table-bordered">';
                    html += '<thead><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr></thead>';
                    html += '<tbody>';
                    
                    data.debug.table_structure.forEach(column => {
                        html += '<tr>';
                        html += `<td>${column.Field}</td>`;
                        html += `<td>${column.Type}</td>`;
                        html += `<td>${column.Null}</td>`;
                        html += `<td>${column.Key}</td>`;
                        html += `<td>${column.Default || 'NULL'}</td>`;
                        html += '</tr>';
                    });
                    
                    html += '</tbody></table></div>';
                }
            }
            
            // Show raw data in pre tag
            html += '<div class="mt-3">';
            html += '<button class="btn btn-sm btn-outline-secondary" onclick="toggleRawData()">Toggle Raw Data</button>';
            html += '<div id="raw-data" style="display: none;">';
            html += '<pre class="mt-2" style="max-height: 300px; overflow: auto;">' + JSON.stringify(data, null, 2) + '</pre>';
            html += '</div></div>';
            
            html += '</div>';
            debugContent.innerHTML = html;
        }
    }
    
    // Function to toggle raw data visibility
    function toggleRawData() {
        const rawData = document.getElementById('raw-data');
        if (rawData) {
            rawData.style.display = rawData.style.display === 'none' ? 'block' : 'none';
        }
    }
    
    // Function to show loading state
    function showLoading() {
        document.getElementById('loading-overlay').classList.remove('d-none');
    }
    
    // Function to hide loading state
    function hideLoading() {
        document.getElementById('loading-overlay').classList.add('d-none');
    }
    
    // Function to check if student information is empty
    function checkIfStudentInfoEmpty(info) {
        if (!info) return true;
        
        // Count filled fields
        let filledFieldCount = 0;
        const keysToCheck = [
            'first_name', 'last_name', 'sex', 'birth_date', 
            'civil_status', 'email', 'contact_number'
        ];
        
        for (const key of keysToCheck) {
            if (info[key]) filledFieldCount++;
        }
        
        // If less than 3 key fields are filled, consider it empty
        return filledFieldCount < 3;
    }
    
    // Enhanced fetchStudentData function with empty data handling
    function fetchStudentData() {
        // Hide no-data alert if shown
        document.getElementById('no-data-alert').classList.add('d-none');
        
        // Show loading indicator
        showLoading();
        showNotification('info', 'Loading your profile data...');
        
        fetch("{{ route('tenant.student.profile-data') }}")
            .then(response => response.json())
            .then(data => {
                // Hide loading
                hideLoading();
                
                if (data.success) {
                    // Store the student data in a global variable for later use
                    window.studentData = data.student;
                    window.studentInfo = data.information;
                    
                    // Check if information is empty
                    const isEmpty = checkIfStudentInfoEmpty(data.information);
                    if (isEmpty) {
                        document.getElementById('no-data-alert').classList.remove('d-none');
                        showNotification('warning', 'Profile data is incomplete. Please update your information.');
                    } else {
                        document.getElementById('no-data-alert').classList.add('d-none');
                        showNotification('success', 'Profile data loaded successfully');
                    }
                    
                    populateStudentData(data.student, data.information);
                    updateDebugInfo(data);
                } else {
                    // Display error message and show debug info
                    showNotification('error', 'Failed to load student data: ' + data.message);
                    
                    // Update debug information with error details 
                    updateDebugInfo(data);
                    
                    // Display debug info automatically on error
                    document.getElementById('debug-info').style.display = 'block';
                    
                    // Show the no data alert
                    document.getElementById('no-data-alert').classList.remove('d-none');
                    
                    // Log to console for developers
                    console.error('Error loading student data:', data);
                }
            })
            .catch(error => {
                // Hide loading
                hideLoading();
                
                console.error('Fetch Error:', error);
                showNotification('error', 'Failed to load student data. Please try again later.');
                
                // Show the no data alert
                document.getElementById('no-data-alert').classList.remove('d-none');
                
                // Show debug info with error details
                const debugContent = document.getElementById('debug-content');
                if (debugContent) {
                    debugContent.innerHTML = '<div class="alert alert-danger">Error fetching data: ' + error.message + '</div>';
                    document.getElementById('debug-info').style.display = 'block';
                }
            });
    }
    
    // Function to populate student data in the profile section and forms
    function populateStudentData(student, info) {
        // Ensure we have student data 
        if (!student) {
            console.warn('No student data available to populate fields');
            return;
        }
        
        // Update profile display
        const studentIdEl = document.getElementById('student-id');
        const studentNameEl = document.getElementById('student-name');
        const studentEmailEl = document.getElementById('student-email');
        
        if (studentIdEl) studentIdEl.textContent = student.student_id || '--';
        if (studentNameEl) studentNameEl.textContent = student.name || '--';
        if (studentEmailEl) studentEmailEl.textContent = student.email || '--';
        
        // Update the user information section
        updateUserInfoDisplay(info);
        
        // Update the academic information section
        updateAcademicInfoDisplay(info);
        
        // Populate personal info form if it exists
        const personalForm = document.getElementById('personalInfoForm');
        if (personalForm) {
            // Always populate email from student data (required field)
            setFormValue(personalForm, 'email', student.email);
            
            // Only set other fields if we have info data
            if (info) {
                // Set form values from information data
                setFormValue(personalForm, 'first_name', info.first_name);
                setFormValue(personalForm, 'middle_name', info.middle_name);
                setFormValue(personalForm, 'last_name', info.last_name);
                setFormValue(personalForm, 'suffix', info.suffix);
                setFormValue(personalForm, 'sex', info.sex);
                setFormValue(personalForm, 'birth_date', info.birth_date);
                setFormValue(personalForm, 'civil_status', info.civil_status);
                setFormValue(personalForm, 'religion', info.religion);
                setFormValue(personalForm, 'blood_type', info.blood_type);
                setFormValue(personalForm, 'contact_number', info.contact_number);
                setFormValue(personalForm, 'facebook', info.facebook);
                setFormValue(personalForm, 'has_indigenous', info.has_indigenous ? '1' : '0');
                setFormValue(personalForm, 'indigenous_group', info.indigenous_group);
                setFormValue(personalForm, 'other_indigenous', info.other_indigenous);
                setFormValue(personalForm, 'dswd_4ps', info.dswd_4ps);
                setFormValue(personalForm, 'disability', info.disability);
            }
            
            // Trigger change events to update dependent fields
            const hasIndigenousSelect = document.getElementById('has_indigenous');
            if (hasIndigenousSelect) {
                const event = new Event('change');
                hasIndigenousSelect.dispatchEvent(event);
            }
        }
        
        // Populate academic info form if it exists
        const academicForm = document.getElementById('academicInfoForm');
        if (academicForm && info) {
            setFormValue(academicForm, 'educational_status', info.educational_status);
            setFormValue(academicForm, 'lrn', info.lrn);
            setFormValue(academicForm, 'school_name', info.school_name);
            setFormValue(academicForm, 'year_from', info.year_from);
            setFormValue(academicForm, 'year_to', info.year_to);
            setFormValue(academicForm, 'academic_level', info.academic_level);
            setFormValue(academicForm, 'school_type', info.school_type);
            setFormValue(academicForm, 'strand', info.strand);
            setFormValue(academicForm, 'region', info.region);
            setFormValue(academicForm, 'province', info.province);
            setFormValue(academicForm, 'city', info.city);
            setFormValue(academicForm, 'barangay', info.barangay);
            setFormValue(academicForm, 'street', info.street);
        }
    }
    
    // Function to update the user information display with actual data
    function updateUserInfoDisplay(info) {
        // If no information, show default placeholders
        if (!info) {
            // Set all fields to placeholder
            const infoValueElements = document.querySelectorAll('#personal .info-value');
            infoValueElements.forEach(el => {
                if (el.id !== 'student-id' && el.id !== 'student-name' && el.id !== 'student-email') {
                    el.textContent = '--';
                }
            });
            return;
        }
        
        // Format date function
        const formatDate = (dateString) => {
            if (!dateString) return '--';
            try {
                const date = new Date(dateString);
                return date.toLocaleDateString('en-US', { 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                });
            } catch (e) {
                return dateString;
            }
        };
        
        // Update personal information fields
        document.getElementById('user-first-name').textContent = info.first_name || '--';
        document.getElementById('user-middle-name').textContent = info.middle_name || '--';
        document.getElementById('user-last-name').textContent = info.last_name || '--';
        document.getElementById('user-suffix').textContent = info.suffix || '--';
        document.getElementById('user-sex').textContent = info.sex || '--';
        document.getElementById('user-birth-date').textContent = formatDate(info.birth_date);
        document.getElementById('user-civil-status').textContent = info.civil_status || '--';
        document.getElementById('user-religion').textContent = info.religion || '--';
        document.getElementById('user-contact').textContent = info.contact_number || '--';
        document.getElementById('user-email').textContent = info.email || '--';
        
        // Handle Facebook display - make it a link if available
        const facebookEl = document.getElementById('user-facebook');
        if (info.facebook) {
            facebookEl.innerHTML = `<a href="#" class="text-primary">${info.facebook}</a>`;
        } else {
            facebookEl.textContent = '--';
        }
        
        document.getElementById('user-blood-type').textContent = info.blood_type || '--';
        document.getElementById('user-has-indigenous').textContent = info.has_indigenous ? 'Yes' : 'No';
        document.getElementById('user-indigenous-group').textContent = info.indigenous_group || 'None';
        document.getElementById('user-other-indigenous').textContent = info.other_indigenous || '--';
        document.getElementById('user-dswd-4ps').textContent = info.dswd_4ps || '--';
        document.getElementById('user-disability').textContent = info.disability || '--';
    }
    
    // Function to update the academic information display
    function updateAcademicInfoDisplay(info) {
        // If no information, show default placeholders
        if (!info) {
            // Set all fields to placeholder
            const academicValueElements = document.querySelectorAll('#academic .info-value');
            academicValueElements.forEach(el => {
                el.textContent = '--';
            });
            return;
        }
        
        // Update the overview section
        document.getElementById('academic-educational-status').textContent = info.educational_status || '--';
        document.getElementById('academic-lrn').textContent = info.lrn || '--';
        
        // Update the academic history section
        document.getElementById('academic-school-name').textContent = info.school_name || '--';
        document.getElementById('academic-year-from').textContent = info.year_from || '--';
        document.getElementById('academic-year-to').textContent = info.year_to || '--';
        document.getElementById('academic-level').textContent = info.academic_level || '--';
        document.getElementById('academic-school-type').textContent = info.school_type || '--';
        document.getElementById('academic-strand').textContent = info.strand || '--';
        
        // Update the school address section
        document.getElementById('academic-region').textContent = info.region || '--';
        document.getElementById('academic-province').textContent = info.province || '--';
        document.getElementById('academic-city').textContent = info.city || '--';
        document.getElementById('academic-barangay').textContent = info.barangay || '--';
        document.getElementById('academic-street').textContent = info.street || '--';
    }
    
    // Helper function to set form field values
    function setFormValue(form, fieldName, value) {
        const field = form.querySelector(`[name="${fieldName}"]`);
        if (!field) return;
        
        if (field.type === 'checkbox') {
            field.checked = !!value;
        } else if (field.type === 'select-one') {
            // Handle select dropdown
            if (value) {
                // First try exact match
                const option = Array.from(field.options).find(opt => opt.value === value);
                if (option) {
                    field.value = value;
                } else {
                    // If no exact match, try case-insensitive match
                    const caseInsensitiveOption = Array.from(field.options).find(
                        opt => opt.value.toLowerCase() === String(value).toLowerCase()
                    );
                    if (caseInsensitiveOption) {
                        field.value = caseInsensitiveOption.value;
                    } else {
                        // If still no match, default to the first non-empty option if available
                        const firstOption = Array.from(field.options).find(opt => opt.value);
                        if (firstOption) field.value = firstOption.value;
                    }
                }
            } else {
                // If no value provided, select the default empty option if available
                const emptyOption = Array.from(field.options).find(opt => !opt.value);
                if (emptyOption) field.selectedIndex = emptyOption.index;
            }
        } else if (field.type === 'date' && value) {
            // Format date for date input (YYYY-MM-DD)
            try {
                // If it's already a valid YYYY-MM-DD string, use it
                if (/^\d{4}-\d{2}-\d{2}$/.test(value)) {
                    field.value = value;
                } else {
                    // Otherwise try to convert
                    const date = new Date(value);
                    if (!isNaN(date.getTime())) {
                        field.value = date.toISOString().split('T')[0];
                    }
                }
            } catch (e) {
                console.error('Error formatting date:', e);
                field.value = value || '';
            }
        } else {
            // For all other field types
            field.value = value || '';
        }
    }
    
    // Function to update personal information
    function updatePersonalInfo() {
        const form = document.getElementById('personalInfoForm');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        // Reset any previous error styling
        const allFields = form.querySelectorAll('.form-control, .form-select');
        allFields.forEach(field => {
            field.classList.remove('is-invalid');
            const feedback = field.parentNode.querySelector('.invalid-feedback');
            if (feedback) feedback.remove();
        });
        
        // Convert checkbox values to boolean
        data.has_indigenous = data.has_indigenous === '1';
        
        // Add the real student_id to the data if available
        if (window.studentData && window.studentData.id) {
            data.student_id = window.studentData.id;
        }
        
        // Show loading notification
        showNotification('info', 'Updating your information...');
        
        fetch("{{ route('tenant.student.update-profile') }}", {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            // Check if the response is ok
            if (!response.ok) {
                // Update debug information
                const debugContent = document.getElementById('debug-content');
                if (debugContent) {
                    debugContent.innerHTML = `<div class="alert alert-danger">
                        HTTP Error: ${response.status} ${response.statusText}<br>
                        URL: ${response.url}
                    </div>`;
                    document.getElementById('debug-info').style.display = 'block';
                }
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showNotification('success', 'Personal information updated successfully');
                // Refresh student data
                fetchStudentData();
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('updatePersonalInfoModal'));
                if (modal) {
                    modal.hide();
                }
            } else {
                showNotification('error', 'Failed to update: ' + (data.message || 'Unknown error'));
                
                // Update debug info with error details
                updateDebugInfo(data);
                document.getElementById('debug-info').style.display = 'block';
                
                // Show validation errors if any
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const errorField = form.querySelector(`[name="${field}"]`);
                        if (errorField) {
                            errorField.classList.add('is-invalid');
                            const errorFeedback = document.createElement('div');
                            errorFeedback.className = 'invalid-feedback';
                            errorFeedback.textContent = data.errors[field][0];
                            errorField.parentNode.appendChild(errorFeedback);
                        }
                    });
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('error', 'An error occurred. Please try again later.');
            
            // Update debug info with error details
            const debugContent = document.getElementById('debug-content');
            if (debugContent) {
                debugContent.innerHTML = `<div class="alert alert-danger">
                    JavaScript Error: ${error.message}<br>
                    <pre>${error.stack}</pre>
                </div>`;
                document.getElementById('debug-info').style.display = 'block';
            }
        });
    }
    
    // Function to update academic information
    function updateAcademicInfo() {
        const form = document.getElementById('academicInfoForm');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        // Reset any previous error styling
        const allFields = form.querySelectorAll('.form-control, .form-select');
        allFields.forEach(field => {
            field.classList.remove('is-invalid');
            const feedback = field.parentNode.querySelector('.invalid-feedback');
            if (feedback) feedback.remove();
        });
        
        // Add the real student_id to the data if available
        if (window.studentData && window.studentData.id) {
            data.student_id = window.studentData.id;
        }
        
        // Show loading notification
        showLoading();
        showNotification('info', 'Updating your academic information...');
        
        fetch("{{ route('tenant.student.update-academic') }}", {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            // Check if the response is ok
            if (!response.ok) {
                // Update debug information
                const debugContent = document.getElementById('debug-content');
                if (debugContent) {
                    debugContent.innerHTML = `<div class="alert alert-danger">
                        HTTP Error: ${response.status} ${response.statusText}<br>
                        URL: ${response.url}
                    </div>`;
                    document.getElementById('debug-info').style.display = 'block';
                }
            }
            return response.json();
        })
        .then(data => {
            // Hide loading
            hideLoading();
            
            if (data.success) {
                showNotification('success', 'Academic information updated successfully');
                // Refresh student data
                fetchStudentData();
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('updateAcademicInfoModal'));
                if (modal) {
                    modal.hide();
                }
            } else {
                showNotification('error', 'Failed to update: ' + (data.message || 'Unknown error'));
                
                // Update debug info with error details
                updateDebugInfo(data);
                document.getElementById('debug-info').style.display = 'block';
                
                // Show validation errors if any
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const errorField = form.querySelector(`[name="${field}"]`);
                        if (errorField) {
                            errorField.classList.add('is-invalid');
                            const errorFeedback = document.createElement('div');
                            errorFeedback.className = 'invalid-feedback';
                            errorFeedback.textContent = data.errors[field][0];
                            errorField.parentNode.appendChild(errorFeedback);
                        }
                    });
                }
            }
        })
        .catch(error => {
            // Hide loading
            hideLoading();
            
            console.error('Error:', error);
            showNotification('error', 'An error occurred. Please try again later.');
            
            // Update debug info with error details
            const debugContent = document.getElementById('debug-content');
            if (debugContent) {
                debugContent.innerHTML = `<div class="alert alert-danger">
                    JavaScript Error: ${error.message}<br>
                    <pre>${error.stack}</pre>
                </div>`;
                document.getElementById('debug-info').style.display = 'block';
            }
        });
    }
    
    // Function to show notifications
    function showNotification(type, message) {
        const toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            // Create toast container if it doesn't exist
            const container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'position-fixed top-0 end-0 p-3';
            container.style.zIndex = '1050';
            document.body.appendChild(container);
        }
        
        const toastId = 'toast-' + Date.now();
        const toastHtml = `
            <div id="${toastId}" class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'}" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;
        
        document.getElementById('toast-container').insertAdjacentHTML('beforeend', toastHtml);
        
        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, { delay: 5000 });
        toast.show();
        
        toastElement.addEventListener('hidden.bs.toast', function() {
            toastElement.remove();
        });
    }
</script>
@endpush 