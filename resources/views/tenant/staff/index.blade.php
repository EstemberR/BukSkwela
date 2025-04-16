@extends('tenant.layouts.app')

@section('title', 'Staff Management')

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

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Staff Members</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                        Add Staff Member
                    </button>
                </div>
                <div class="card-body">
                    <!-- Search and filters -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form id="searchForm" action="{{ route('tenant.staff.index', ['tenant' => tenant('id')]) }}" method="GET">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control" 
                                           placeholder="Search staff..." 
                                           id="searchStaff"
                                           name="search"
                                           value="{{ request('search') }}"
                                           autocomplete="off">
                                </div>
                            </form>
                            </div>
                            <div class="col-md-6 text-end">
                                <select class="form-select d-inline-block w-auto" id="roleFilter" name="role" onchange="document.getElementById('searchForm').submit()">
                                    <option value="">All Roles</option>
                                    <option value="instructor" {{ request('role') == 'instructor' ? 'selected' : '' }}>Instructor</option>
                                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="staff" {{ request('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                                </select>
                        </div>
                    </div>

                    <!-- Staff Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Staff ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Department</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($staffMembers ?? [] as $staff)
                                <tr data-staff-id="{{ $staff->id }}">
                                    <td>{{ $staff->staff_id }}</td>
                                    <td>{{ $staff->name }}</td>
                                    <td>{{ $staff->email }}</td>
                                    <td>{{ ucfirst($staff->role) }}</td>
                                    <td>{{ optional($staff->department)->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $staff->status === 'active' ? 'success' : 'warning' }}">
                                            {{ ucfirst($staff->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info edit-staff-btn" 
                                                data-staff-id="{{ $staff->id }}"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editStaffModal{{ $staff->id }}">
                                            Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="showDeleteConfirmation('{{ $staff->id }}')">
                                            Delete
                                        </button>
                                        
                                        <!-- Hidden Delete Form -->
                                        <form id="delete-form-{{ $staff->id }}" 
                                              action="{{ route('tenant.staff.destroy', ['tenant' => tenant('id'), 'staff' => $staff->id]) }}" 
                                              method="POST" 
                                              style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>

                                        <!-- Edit Staff Modal -->
                                        <div class="modal fade edit-staff-modal" id="editStaffModal{{ $staff->id }}" tabindex="-1" aria-labelledby="editStaffModalLabel{{ $staff->id }}" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="editStaffModalLabel{{ $staff->id }}">Edit Staff Member</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="{{ route('tenant.staff.update', ['tenant' => tenant('id'), 'staff' => $staff->id]) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label">Staff ID</label>
                                                                <input type="text" class="form-control" name="staff_id" value="{{ $staff->staff_id }}" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Name</label>
                                                                <input type="text" class="form-control" name="name" value="{{ $staff->name }}" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Email</label>
                                                                <input type="email" class="form-control" name="email" value="{{ $staff->email }}" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Role</label>
                                                                <select class="form-select" name="role" required>
                                                                    <option value="instructor" {{ $staff->role === 'instructor' ? 'selected' : '' }}>Instructor</option>
                                                                    <option value="admin" {{ $staff->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                                                    <option value="staff" {{ $staff->role === 'staff' ? 'selected' : '' }}>Staff</option>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Department</label>
                                                                <input type="text" class="form-control" name="department" value="{{ optional($staff->department)->name }}" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Status</label>
                                                                <select class="form-select" name="status" required>
                                                                    <option value="active" {{ $staff->status === 'active' ? 'selected' : '' }}>Active</option>
                                                                    <option value="inactive" {{ $staff->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">No staff members found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            @if ($staffMembers instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                Showing {{ $staffMembers->firstItem() ?? 0 }} to {{ $staffMembers->lastItem() ?? 0 }} of {{ $staffMembers->total() }} entries
                            @else
                                Showing {{ $staffMembers->count() }} entries
                            @endif
                        </div>
                        <div>
                            @if ($staffMembers instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                {{ $staffMembers->links() }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Staff Modal -->
<div class="modal fade" id="addStaffModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Staff Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addStaffForm" action="{{ route('tenant.staff.store', ['tenant' => tenant('id')]) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Staff ID</label>
                        <input type="text" class="form-control" name="staff_id" id="new_staff_id" required>
                        <div class="invalid-feedback" id="staff_id_error"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" id="new_staff_email" required>
                        <div class="invalid-feedback" id="staff_email_error"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select class="form-select" name="role" required>
                            <option value="">Select Role</option>
                            <option value="instructor">Instructor</option>
                            <option value="admin">Admin</option>
                            <option value="staff">Staff</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Department</label>
                        <input type="text" class="form-control" name="department" required>
                        <div class="form-text">Enter the department name (e.g., General, Administration, Academic)</div>
                    </div>
                    <div class="alert alert-info">
                        <small><i class="fas fa-info-circle"></i> A secure password will be automatically generated and sent to the staff member's email.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="addStaffBtn">Add Staff Member</button>
                </div>
            </form>
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
</style>

<!-- Include Success Modal Component -->
@include('Modals.SuccessModal')

@endsection

@push('scripts')
<!-- Add SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Make sure Bootstrap JS is properly loaded -->
<script>
    // Check if Bootstrap is available
    if (typeof bootstrap === 'undefined') {
        // If not, load it
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js';
        script.integrity = 'sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz';
        script.crossOrigin = 'anonymous';
        document.head.appendChild(script);
        
        script.onload = function() {
            initializeModals();
            initializeOtherScripts();
        };
    } else {
        // Bootstrap is available, initialize everything
        document.addEventListener('DOMContentLoaded', function() {
            initializeModals();
            initializeOtherScripts();
        });
    }

    // Function to safely initialize modals
    function initializeModals() {
        const modalElements = document.querySelectorAll('.modal');
        modalElements.forEach(function(modalElement) {
            try {
                // Create new modal instance with explicit options
                const modalInstance = new bootstrap.Modal(modalElement, {
                    backdrop: true,
                    keyboard: true,
                    focus: true
                });
                
                // Store the instance on the element
                modalElement._bsModal = modalInstance;
            } catch (error) {
                console.warn('Error initializing modal:', error);
            }
        });
    }

    // Function to run all other scripts
    function initializeOtherScripts() {
        // Show success/error messages using SweetAlert2
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
        
        // Setup duplicate checking for staff add form
        setupStaffAddForm();
    }
    
    // Function to check for duplicate staff IDs and emails
    function setupStaffAddForm() {
        const addStaffForm = document.getElementById('addStaffForm');
        const staffIdInput = document.getElementById('new_staff_id');
        const staffEmailInput = document.getElementById('new_staff_email');
        const staffIdError = document.getElementById('staff_id_error');
        const staffEmailError = document.getElementById('staff_email_error');
        const addStaffBtn = document.getElementById('addStaffBtn');
        const fullScreenLoader = document.getElementById('fullScreenLoader');
        
        if (!addStaffForm) return;
        
        // Collect existing staff IDs and emails
        const existingStaffIds = [];
        const existingEmails = [];
        
        document.querySelectorAll('table tbody tr').forEach(row => {
            if (row.cells && row.cells.length >= 3) {
                // Staff ID is in the first column
                const staffId = row.cells[0].textContent.trim();
                if (staffId) existingStaffIds.push(staffId);
                
                // Email is in the third column
                const email = row.cells[2].textContent.trim();
                if (email) existingEmails.push(email);
            }
        });
        
        // Function to check for duplicates
        function checkDuplicates() {
            let isValid = true;
            
            // Check staff ID
            if (staffIdInput.value && existingStaffIds.includes(staffIdInput.value)) {
                staffIdInput.classList.add('is-invalid');
                staffIdError.textContent = 'This Staff ID is already in use';
                isValid = false;
            } else {
                staffIdInput.classList.remove('is-invalid');
                staffIdError.textContent = '';
            }
            
            // Check email
            if (staffEmailInput.value && existingEmails.includes(staffEmailInput.value)) {
                staffEmailInput.classList.add('is-invalid');
                staffEmailError.textContent = 'This email address is already in use';
                isValid = false;
            } else {
                staffEmailInput.classList.remove('is-invalid');
                staffEmailError.textContent = '';
            }
            
            // Enable/disable submit button
            addStaffBtn.disabled = !isValid;
            
            return isValid;
        }
        
        // Add event listeners for real-time validation
        staffIdInput.addEventListener('input', checkDuplicates);
        staffEmailInput.addEventListener('input', checkDuplicates);
        
        // Validate on form submission
        addStaffForm.addEventListener('submit', function(e) {
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
                addStaffBtn.disabled = true;
            }
        });
        
        // Reset validation when modal is opened
        const addStaffModal = document.getElementById('addStaffModal');
        if (addStaffModal) {
            addStaffModal.addEventListener('shown.bs.modal', function() {
                staffIdInput.classList.remove('is-invalid');
                staffEmailInput.classList.remove('is-invalid');
                staffIdError.textContent = '';
                staffEmailError.textContent = '';
                addStaffBtn.disabled = false;
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
                
                const staffId = this.id.replace('delete-form-', '');
                const formAction = this.action;
                const fullScreenLoader = document.getElementById('fullScreenLoader');
                
                // Create a new XMLHttpRequest
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
                                const row = document.querySelector(`tr[data-staff-id="${staffId}"]`);
                                if (row) {
                                    row.remove();
                                }
                                
                                showSuccessModal('Deleted!', data.message || 'Staff member deleted successfully');
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: data.message || 'Failed to delete staff member',
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

    function showDeleteConfirmation(staffId) {
        Swal.fire({
            title: 'Delete Staff Member',
            text: "Are you sure you want to delete this staff member? This action cannot be undone.",
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
                document.getElementById(`delete-form-${staffId}`).submit();
            }
        });
    }

    // Search and filter functionality
    document.addEventListener('DOMContentLoaded', function() {
        const searchForm = document.getElementById('searchForm');
        const searchInput = document.getElementById('searchStaff');
        let searchTimeout;

        // Handle search input with debounce
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                searchForm.submit();
            }, 300);
        });
    });

    // Initialize edit buttons
    function initializeEditButtons() {
        document.querySelectorAll('.edit-staff-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                const staffId = this.getAttribute('data-staff-id');
                const modalId = `editStaffModal${staffId}`;
                const modalElement = document.getElementById(modalId);
                
                if (modalElement) {
                    try {
                        // Get existing modal instance or create new one
                        let modalInstance = bootstrap.Modal.getInstance(modalElement);
                        if (!modalInstance) {
                            modalInstance = new bootstrap.Modal(modalElement, {
                                backdrop: true,
                                keyboard: true,
                                focus: true
                            });
                        }
                        modalInstance.show();
                    } catch (error) {
                        console.error('Error showing modal:', error);
                        // Fallback: try to show modal using jQuery if available
                        if (typeof $ !== 'undefined') {
                            try {
                                $(modalElement).modal('show');
                            } catch (jqError) {
                                console.error('Error showing modal with jQuery:', jqError);
                            }
                        }
                    }
                }
            });
        });
    }

    // Initialize everything when the page loads
    try {
        initializeModals();
        initializeEditButtons();
        setupDeleteForms();
        setupStaffAddForm();
    } catch (error) {
        console.error('Error during initialization:', error);
    }
</script>
@endpush 