@extends('tenant.layouts.app')

@section('title', 'Students Management')

@section('content')
<div class="container">
    @if(session('success'))
        <input type="hidden" id="success-message" value="{{ session('success') }}">
    @endif
    @if(session('error'))
        <input type="hidden" id="error-message" value="{{ session('error') }}">
    @endif
    @if(session('warning'))
        <input type="hidden" id="warning-message" value="{{ session('warning') }}">
    @endif
    
    @php
        // Get tenant information
        $url = request()->url();
        preg_match('/^https?:\/\/([^\.]+)\./', $url, $matches);
        $tenantDomain = $matches[1] ?? null;
        
        // Get tenant from domain or tenant helper
        if ($tenantDomain) {
            $currentTenant = \App\Models\Tenant::where('id', $tenantDomain)->first();
        } else {
            $tenantId = tenant('id') ?? null;
            $currentTenant = $tenantId ? \App\Models\Tenant::find($tenantId) : null;
        }
        
        $isPremium = $currentTenant && $currentTenant->subscription_plan === 'premium';
        
        // Count total students for non-premium tenants
        $studentCount = $students->total();
        $canAddMoreStudents = $isPremium || $studentCount < 10;
    @endphp

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Students</h5>
                    <div>
                        @if(!$isPremium)
                            <span class="badge bg-warning text-dark me-2">
                                <i class="fas fa-users me-1"></i> {{ $studentCount }}/10 Students
                                @if(!$canAddMoreStudents)
                                    <i class="fas fa-lock ms-1"></i>
                                @endif
                            </span>
                        @endif
                        <button class="btn btn-primary" data-bs-toggle="modal" 
                            data-bs-target="{{ !$canAddMoreStudents ? '#upgradePremiumModal' : '#addStudentModal' }}">
                            Add Student
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Search and filters -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form id="searchForm" action="{{ route('tenant.students.index') }}" method="GET">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control" 
                                           placeholder="Search students..." 
                                           id="searchStudent" 
                                           name="search"
                                           value="{{ request('search') }}"
                                           autocomplete="off">
                                    <input type="hidden" name="course_id" id="courseIdInput" value="{{ request('course_id') }}">
                                    <button type="submit" class="btn btn-primary">Search</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6 text-end">
                            <select class="form-select d-inline-block w-auto" id="courseFilter">
                                <option value="">All Courses</option>
                                @foreach($courses ?? [] as $course)
                                    <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Students Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th class="fw-bold">ID Number</th>
                                    <th class="fw-bold">Name</th>
                                    <th class="fw-bold">Course</th>
                                    <th class="fw-bold">Email</th>
                                    <th class="fw-bold">Status</th>
                                    <th class="fw-bold">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students ?? [] as $student)
                                <tr data-student-id="{{ $student->id }}">
                                    <td>{{ $student->student_id }}</td>
                                    <td>{{ $student->name }}</td>
                                    <td>{{ $student->course->name ?? 'N/A' }}</td>
                                    <td>{{ $student->email }}</td>
                                    <td>
                                        <span class="badge bg-{{ $student->status === 'active' ? 'success' : 'warning' }}">
                                            {{ ucfirst($student->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editStudentModal{{ $student->id }}">
                                            Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="showDeleteConfirmation('{{ $student->id }}')">
                                            Delete
                                        </button>
                                        
                                        <!-- Hidden Delete Form -->
                                        <form id="delete-form-{{ $student->id }}" 
                                              action="{{ route('tenant.students.delete.direct.post', ['id' => $student->id]) }}" 
                                              method="POST" 
                                              style="display: none;">
                                            @csrf
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No students found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            Showing {{ $students->firstItem() ?? 0 }} to {{ $students->lastItem() ?? 0 }} of {{ $students->total() }} entries
                        </div>
                        <div>
                            {{ $students->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            @if(!$canAddMoreStudents)
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <h5><i class="fas fa-exclamation-triangle me-2"></i>Student Limit Reached</h5>
                        <p>You have reached the maximum limit of 10 students for the basic plan.</p>
                        <hr>
                        <p class="mb-0">Please upgrade to Premium to add unlimited students and access additional features.</p>
                    </div>
                    <div class="text-center my-3">
                        <a href="#" class="btn btn-warning" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#upgradePremiumModal">
                            <i class="fas fa-crown me-2"></i>Upgrade to Premium
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            @else
                <form id="addStudentForm" action="{{ route('tenant.students.store.direct') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div id="addStudentFormContent">
                            <div class="mb-3">
                                <label class="form-label">Student ID</label>
                                <input type="text" class="form-control" name="student_id" id="new_student_id" required>
                                <div class="invalid-feedback" id="student_id_error"></div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" id="new_student_email" required>
                                <div class="invalid-feedback" id="student_email_error"></div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Course</label>
                                <select class="form-select" name="course_id" required>
                                    <option value="">Select Course</option>
                                    @foreach($courses ?? [] as $course)
                                        <option value="{{ $course->id }}">{{ $course->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="alert alert-info">
                                <small><i class="fas fa-info-circle"></i> A secure password will be automatically generated and sent to the student's email.</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="addStudentBtn">Add Student</button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

<!-- Full Screen Loader Overlay -->
<div id="fullScreenLoader" class="full-screen-loader d-none">
    <div class="loader-container">
        @include('Loaders.Loaders')
    </div>
</div>

<style>
.full-screen-loader {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.8);
    z-index: 9999;
    display: flex;
    justify-content: center;
    align-items: center;
}

.loader-container {
    transform: scale(2); /* Make the loader twice as big */
}

.loader {
    position: relative;
    width: 2.5em;
    height: 2.5em;
    transform: rotate(165deg);
}

.input-icon-wrapper {
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    display: flex;
    align-items: center;
    padding-left: 1rem;
    color: #6c757d;
}

.input-icon {
    color: #6c757d;
}
</style>

<!-- Edit Student Modals -->
@foreach($students ?? [] as $student)
<div class="modal fade" id="editStudentModal{{ $student->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('tenant.students.update.direct', ['id' => $student->id]) }}" method="POST">
                @csrf
                <!-- Hidden ID field to ensure we're editing the right record -->
                <input type="hidden" name="student_db_id" value="{{ $student->id }}">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Student ID</label>
                        <input type="text" class="form-control" name="student_id" value="{{ $student->student_id }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" value="{{ $student->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" value="{{ $student->email }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Course</label>
                        <select class="form-select" name="course_id" required>
                            <option value="">Select Course</option>
                            @foreach($courses ?? [] as $course)
                                <option value="{{ $course->id }}" {{ $student->course_id == $course->id ? 'selected' : '' }}>
                                    {{ $course->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" required>
                            <option value="active" {{ $student->status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $student->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password (leave blank to keep current)</label>
                        <input type="password" class="form-control" name="password">
                        <div class="form-text">Enter a new password only if you want to change it.</div>
                    </div>
                    <div class="alert alert-info">
                        <small><i class="fas fa-info-circle"></i> Changing the password will send an email notification to the student.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Student</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Include Success Modal Component -->
@include('Modals.SuccessModal')

<!-- Add the Premium Upgrade Modal -->
<div class="modal fade" id="upgradePremiumModal" tabindex="-1" aria-labelledby="upgradePremiumModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="upgradePremiumModalLabel">
                    <i class="fas fa-crown text-warning me-2"></i>Upgrade to Premium
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <div class="display-1 text-warning mb-3">
                        <i class="fas fa-crown"></i>
                    </div>
                    <h4>Unlock Premium Benefits</h4>
                    <p class="text-muted">Upgrade to premium to access all features including:</p>
                </div>
                
                <ul class="list-group mb-4">
                    <li class="list-group-item d-flex align-items-center">
                        <i class="fas fa-check-circle text-success me-3"></i>
                        <span>Unlimited students</span>
                    </li>
                    <li class="list-group-item d-flex align-items-center">
                        <i class="fas fa-check-circle text-success me-3"></i>
                        <span>View student submission status</span>
                    </li>
                    <li class="list-group-item d-flex align-items-center">
                        <i class="fas fa-check-circle text-success me-3"></i>
                        <span>Advanced reporting and analytics</span>
                    </li>
                </ul>
                
                <div class="alert alert-info">
                    <p class="mb-0">Premium subscription costs ₱999 per year.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-warning" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#paymentModal">
                    <i class="fas fa-crown me-2"></i>Proceed to Payment
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title" id="paymentModalLabel">
                    <i class="fas fa-credit-card me-2"></i>Payment Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('tenant.subscription.upgrade', ['tenant' => tenant('id')]) }}" method="POST" id="paymentForm">
                    @csrf
                    
                    <div class="mb-4">
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#upgradePremiumModal">
                            <i class="fas fa-arrow-left me-1"></i> Back to features
                        </button>
                    </div>
                    
                    <div class="card mb-4 border-0 bg-light">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-warning bg-opacity-10 p-3 rounded-circle me-3">
                                    <i class="fas fa-crown text-warning fs-4"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Premium Subscription</h6>
                                    <p class="mb-0 text-muted">Complete your payment to unlock all premium features</p>
                                </div>
                                <div class="ms-auto">
                                    <h5 class="mb-0 text-primary">₱999.00</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="payment_method" class="form-label fw-medium">Payment Method</label>
                        <div class="payment-methods">
                            <div class="row g-2">
                                <div class="col-4">
                                    <input type="radio" class="btn-check" name="payment_method" id="bank_transfer" value="bank_transfer" required>
                                    <label class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center" for="bank_transfer">
                                        <i class="fas fa-university fs-3 mb-2"></i>
                                        <span class="small">Bank Transfer</span>
                                    </label>
                                </div>
                                <div class="col-4">
                                    <input type="radio" class="btn-check" name="payment_method" id="gcash" value="gcash" required>
                                    <label class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center" for="gcash">
                                        <i class="fas fa-wallet fs-3 mb-2"></i>
                                        <span class="small">GCash</span>
                                    </label>
                                </div>
                                <div class="col-4">
                                    <input type="radio" class="btn-check" name="payment_method" id="paymaya" value="paymaya" required>
                                    <label class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center" for="paymaya">
                                        <i class="fas fa-credit-card fs-3 mb-2"></i>
                                        <span class="small">PayMaya</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div id="bankTransferDetails" class="payment-details mb-4 d-none">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light py-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-university text-primary me-2"></i>
                                    <h6 class="mb-0">Bank Transfer Instructions</h6>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="mb-3">Please transfer ₱999.00 to the following account:</p>
                                <div class="bg-light p-3 rounded mb-3">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <p class="mb-1 text-muted small">BANK</p>
                                            <p class="mb-0 fw-medium">BDO</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1 text-muted small">ACCOUNT NAME</p>
                                            <p class="mb-0 fw-medium">BukSkwela Inc.</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1 text-muted small">ACCOUNT NUMBER</p>
                                            <p class="mb-0 fw-medium">1234-5678-9012</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1 text-muted small">REFERENCE</p>
                                            <p class="mb-0 fw-medium">Premium-{{ tenant('id') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="alert alert-warning d-flex align-items-center small p-2 rounded">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <span>Please include your reference code in the deposit slip/transfer notes</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div id="gcashDetails" class="payment-details mb-4 d-none">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light py-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-wallet text-primary me-2"></i>
                                    <h6 class="mb-0">GCash Instructions</h6>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="mb-3">Please send ₱999.00 to the following GCash account:</p>
                                <div class="bg-light p-3 rounded mb-3">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <p class="mb-1 text-muted small">GCASH NUMBER</p>
                                            <p class="mb-0 fw-medium">0917-123-4567</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1 text-muted small">ACCOUNT NAME</p>
                                            <p class="mb-0 fw-medium">BukSkwela Inc.</p>
                                        </div>
                                        <div class="col-12">
                                            <p class="mb-1 text-muted small">REFERENCE</p>
                                            <p class="mb-0 fw-medium">Premium-{{ tenant('id') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="alert alert-warning d-flex align-items-center small p-2 rounded">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <span>Please include the reference code in the GCash notes</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div id="paymayaDetails" class="payment-details mb-4 d-none">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light py-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-credit-card text-primary me-2"></i>
                                    <h6 class="mb-0">PayMaya Instructions</h6>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="mb-3">Please send ₱999.00 to the following PayMaya account:</p>
                                <div class="bg-light p-3 rounded mb-3">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <p class="mb-1 text-muted small">PAYMAYA NUMBER</p>
                                            <p class="mb-0 fw-medium">0918-765-4321</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="mb-1 text-muted small">ACCOUNT NAME</p>
                                            <p class="mb-0 fw-medium">BukSkwela Inc.</p>
                                        </div>
                                        <div class="col-12">
                                            <p class="mb-1 text-muted small">REFERENCE</p>
                                            <p class="mb-0 fw-medium">Premium-{{ tenant('id') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="alert alert-warning d-flex align-items-center small p-2 rounded">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <span>Please include your reference code in the PayMaya notes</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="reference_number" class="form-label fw-medium">Reference Number</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-hashtag"></i></span>
                            <input type="text" class="form-control" id="reference_number" name="reference_number" placeholder="Enter your payment reference number" required>
                        </div>
                        <div class="form-text">Please enter the reference number from your payment transaction.</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('paymentForm').submit();">
                    <i class="fas fa-check-circle me-2"></i>Complete Payment
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<!-- Add SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Show success/error messages using SweetAlert2
    document.addEventListener('DOMContentLoaded', function() {
        const successMessage = document.getElementById('success-message');
        const errorMessage = document.getElementById('error-message');
        const warningMessage = document.getElementById('warning-message');

        if (successMessage) {
            showSuccessModal('Success!', successMessage.value);
        }

        if (errorMessage) {
            Swal.fire({
                title: 'Error!',
                text: errorMessage.value,
                icon: 'error'
            });
        }

        if (warningMessage) {
            Swal.fire({
                title: 'Warning!',
                text: warningMessage.value,
                icon: 'warning'
            });
        }
        
        // Setup delete form submission with AJAX
        setupDeleteForms();
        
        // Setup duplicate checking for student add form
        setupStudentAddForm();
        
        // Check if max students reached and open upgrade modal automatically after a slight delay
        @if(!$canAddMoreStudents && session('max_students_reached'))
        setTimeout(() => {
            const upgradeModal = new bootstrap.Modal(document.getElementById('upgradePremiumModal'));
            upgradeModal.show();
        }, 500);
        @endif

        // Handle payment method selection
        document.querySelectorAll('input[name="payment_method"]').forEach(input => {
            input.addEventListener('change', function() {
                // Hide all payment details
                document.querySelectorAll('.payment-details').forEach(el => {
                    el.classList.add('d-none');
                });
                
                // Show selected payment method details
                const method = this.value;
                if (method) {
                    document.getElementById(method + 'Details').classList.remove('d-none');
                }
            });
        });
    });
    
    // Function to check for duplicate student IDs and emails
    function setupStudentAddForm() {
        const addStudentForm = document.getElementById('addStudentForm');
        const studentIdInput = document.getElementById('new_student_id');
        const studentEmailInput = document.getElementById('new_student_email');
        const studentIdError = document.getElementById('student_id_error');
        const studentEmailError = document.getElementById('student_email_error');
        const addStudentBtn = document.getElementById('addStudentBtn');
        const fullScreenLoader = document.getElementById('fullScreenLoader');
        
        if (!addStudentForm) return;
        
        // Collect existing student IDs and emails
        const existingStudentIds = [];
        const existingEmails = [];
        
        document.querySelectorAll('table tbody tr').forEach(row => {
            if (row.cells && row.cells.length >= 4) {
                // Student ID is in the first column
                const studentId = row.cells[0].textContent.trim();
                if (studentId) existingStudentIds.push(studentId);
                
                // Email is in the fourth column
                const email = row.cells[3].textContent.trim();
                if (email) existingEmails.push(email);
            }
        });
        
        // Function to check for duplicates
        function checkDuplicates() {
            let isValid = true;
            
            // Check student ID
            if (studentIdInput.value && existingStudentIds.includes(studentIdInput.value)) {
                studentIdInput.classList.add('is-invalid');
                studentIdError.textContent = 'This Student ID is already in use';
                isValid = false;
            } else {
                studentIdInput.classList.remove('is-invalid');
                studentIdError.textContent = '';
            }
            
            // Check email
            if (studentEmailInput.value && existingEmails.includes(studentEmailInput.value)) {
                studentEmailInput.classList.add('is-invalid');
                studentEmailError.textContent = 'This email address is already in use';
                isValid = false;
            } else {
                studentEmailInput.classList.remove('is-invalid');
                studentEmailError.textContent = '';
            }
            
            // Enable/disable submit button
            addStudentBtn.disabled = !isValid;
            
            return isValid;
        }
        
        // Add event listeners for real-time validation
        studentIdInput.addEventListener('input', checkDuplicates);
        studentEmailInput.addEventListener('input', checkDuplicates);
        
        // Validate on form submission
        addStudentForm.addEventListener('submit', function(e) {
            if (!checkDuplicates()) {
                e.preventDefault();
                // Show error message
                Swal.fire({
                    title: 'Validation Error',
                    text: 'Please correct the errors before submitting',
                    icon: 'error'
                });
            } else {
                // Show full screen loader
                fullScreenLoader.classList.remove('d-none');
                addStudentBtn.disabled = true;
            }
        });
        
        // Reset validation when modal is opened
        const addStudentModal = document.getElementById('addStudentModal');
        if (addStudentModal) {
            addStudentModal.addEventListener('shown.bs.modal', function() {
                studentIdInput.classList.remove('is-invalid');
                studentEmailInput.classList.remove('is-invalid');
                studentIdError.textContent = '';
                studentEmailError.textContent = '';
                addStudentBtn.disabled = false;
                // Hide loader if it's visible
                fullScreenLoader.classList.add('d-none');
            });
        }
    }
    
    function setupDeleteForms() {
        // Get all delete forms
        const deleteForms = document.querySelectorAll('form[id^="delete-form-"]');
        
        // Add submit handler to each form
        deleteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const studentId = this.id.replace('delete-form-', '');
                const formAction = this.action;
                const fullScreenLoader = document.getElementById('fullScreenLoader');
                
                // Create a new XMLHttpRequest instead of fetch for more control
                const xhr = new XMLHttpRequest();
                xhr.open('POST', formAction, true);
                xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.setRequestHeader('Accept', 'application/json');
                
                // Get form data
                const formData = new FormData(this);
                
                xhr.onload = function() {
                    // Hide the loader
                    fullScreenLoader.classList.add('d-none');
                    
                    if (xhr.status >= 200 && xhr.status < 300) {
                        try {
                            const data = JSON.parse(xhr.responseText);
                            
                            if (data.success) {
                                // Remove the row from the table
                                const row = document.querySelector(`tr[data-student-id="${studentId}"]`);
                                if (row) {
                                    row.remove();
                                }
                                
                                showSuccessModal('Deleted!', data.message || 'Student deleted successfully');
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: data.message || 'Failed to delete student',
                                    icon: 'error'
                                });
                            }
                        } catch (e) {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Error processing server response',
                                icon: 'error'
                            });
                        }
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: `Server error: ${xhr.status}`,
                            icon: 'error'
                        });
                    }
                };
                
                xhr.onerror = function() {
                    // Hide the loader
                    fullScreenLoader.classList.add('d-none');
                    
                    Swal.fire({
                        title: 'Error!',
                        text: 'Network error occurred',
                        icon: 'error'
                    });
                };
                
                xhr.send(formData);
            });
        });
    }

    function showDeleteConfirmation(studentId) {
        Swal.fire({
            title: 'Delete Student',
            text: "Are you sure you want to delete this student? This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show the full screen loader
                const fullScreenLoader = document.getElementById('fullScreenLoader');
                fullScreenLoader.classList.remove('d-none');
                
                // Submit the delete form
                document.getElementById(`delete-form-${studentId}`).submit();
            }
        });
    }

    // Search and filter functionality
    document.addEventListener('DOMContentLoaded', function() {
        const searchForm = document.getElementById('searchForm');
        const searchInput = document.getElementById('searchStudent');
        const courseFilter = document.getElementById('courseFilter');
        const courseIdInput = document.getElementById('courseIdInput');
        let searchTimeout;

        // Handle search input with shorter debounce
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                searchForm.submit();
            }, 300); // Reduced to 300ms for faster response
        });
        
        // Update course_id input when course filter changes
        courseFilter.addEventListener('change', function() {
            courseIdInput.value = this.value;
            searchForm.submit();
        });
    });
</script>
@endpush 