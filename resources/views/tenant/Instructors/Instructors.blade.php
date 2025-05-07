@extends('tenant.layouts.app')

@section('title', 'Instructor Dashboard')

@section('content')
<div class="container py-4">
    @if(session('success'))
        <input type="hidden" id="success-message" value="{{ session('success') }}">
    @endif
    @if(session('error'))
        <input type="hidden" id="error-message" value="{{ session('error') }}">
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <!-- Total Pending Applications Card -->
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card border-0 shadow h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h6 class="text-muted mb-0">Total Pending</h6>
                            <h3 class="mt-2 mb-0">{{ $pendingCount ?? 0 }}</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="fas fa-clock text-warning fa-2x"></i>
                        </div>
                    </div>
                    <p class="text-muted small mb-0">Applications awaiting review</p>
                </div>
                <div class="card-footer bg-light px-4 py-2 border-0">
                    <a href="{{ route('tenant.instructor.enrollment.approval', ['tenant' => tenant('id')]) }}" class="text-decoration-none d-flex align-items-center">
                        <small class="text-primary">View pending applications</small>
                        <i class="fas fa-arrow-right ms-auto text-primary small"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Total Enrolled Card -->
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card border-0 shadow h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h6 class="text-muted mb-0">Total Enrolled</h6>
                            <h3 class="mt-2 mb-0">{{ $enrolledCount ?? 0 }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-user-check text-success fa-2x"></i>
                        </div>
                    </div>
                    <p class="text-muted small mb-0">Successfully enrolled students</p>
                </div>
                <div class="card-footer bg-light px-4 py-2 border-0">
                    <a href="{{ route('tenant.instructor.dashboard', ['tenant' => tenant('id')]) }}#students" class="text-decoration-none d-flex align-items-center">
                        <small class="text-primary">View enrolled students</small>
                        <i class="fas fa-arrow-right ms-auto text-primary small"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Total Rejected Card -->
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card border-0 shadow h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h6 class="text-muted mb-0">Total Rejected</h6>
                            <h3 class="mt-2 mb-0">{{ $rejectedCount ?? 0 }}</h3>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-3 rounded">
                            <i class="fas fa-times-circle text-danger fa-2x"></i>
                        </div>
                    </div>
                    <p class="text-muted small mb-0">Applications not approved</p>
                </div>
                <div class="card-footer bg-light px-4 py-2 border-0">
                    <a href="{{ route('tenant.instructor.enrollment.approval', ['tenant' => tenant('id'), 'status' => 'rejected']) }}" class="text-decoration-none d-flex align-items-center">
                        <small class="text-primary">View rejected applications</small>
                        <i class="fas fa-arrow-right ms-auto text-primary small"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Requirements Cards -->
    <div class="row mb-4">
        <div class="col-12 mb-3">
            <h5 class="text-muted">Requirements by Category</h5>
        </div>
        
        <!-- Regular Requirements Card -->
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card border-0 shadow h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h6 class="text-muted mb-0">Regular</h6>
                            <h3 class="mt-2 mb-0">{{ $regularRequirementsCount ?? 0 }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="fas fa-clipboard-list text-primary fa-2x"></i>
                        </div>
                    </div>
                    <p class="text-muted small mb-0">Standard requirements</p>
                </div>
                <div class="card-footer bg-light px-4 py-2 border-0">
                    <a href="{{ route('tenant.instructor.requirements.index', ['tenant' => tenant('id'), 'category' => 'Regular']) }}" class="text-decoration-none d-flex align-items-center">
                        <small class="text-primary">Manage regular requirements</small>
                        <i class="fas fa-arrow-right ms-auto text-primary small"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Irregular Requirements Card -->
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card border-0 shadow h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h6 class="text-muted mb-0">Irregular</h6>
                            <h3 class="mt-2 mb-0">{{ $irregularRequirementsCount ?? 0 }}</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="fas fa-clipboard-check text-info fa-2x"></i>
                        </div>
                    </div>
                    <p class="text-muted small mb-0">For irregular students</p>
                </div>
                <div class="card-footer bg-light px-4 py-2 border-0">
                    <a href="{{ route('tenant.instructor.requirements.index', ['tenant' => tenant('id'), 'category' => 'Irregular']) }}" class="text-decoration-none d-flex align-items-center">
                        <small class="text-primary">Manage irregular requirements</small>
                        <i class="fas fa-arrow-right ms-auto text-primary small"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Probation Requirements Card -->
        <div class="col-md-4">
            <div class="card border-0 shadow h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h6 class="text-muted mb-0">Probation</h6>
                            <h3 class="mt-2 mb-0">{{ $probationRequirementsCount ?? 0 }}</h3>
                        </div>
                        <div class="bg-secondary bg-opacity-10 p-3 rounded">
                            <i class="fas fa-clipboard text-secondary fa-2x"></i>
                        </div>
                    </div>
                    <p class="text-muted small mb-0">For students on probation</p>
                </div>
                <div class="card-footer bg-light px-4 py-2 border-0">
                    <a href="{{ route('tenant.instructor.requirements.index', ['tenant' => tenant('id'), 'category' => 'Probation']) }}" class="text-decoration-none d-flex align-items-center">
                        <small class="text-primary">Manage probation requirements</small>
                        <i class="fas fa-arrow-right ms-auto text-primary small"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Empty content container -->
    <div class="row">
        <div class="col-12">
            <!-- Content will be added here -->
        </div>
    </div>
</div>

<!-- Include Success Modal Component -->
@include('Modals.SuccessModal')
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show success/error messages using SweetAlert2
        const successMessage = document.getElementById('success-message');
        const errorMessage = document.getElementById('error-message');

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
    });
</script>
@endpush

