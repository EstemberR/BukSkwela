@extends('tenant.layouts.app')

@section('title', 'Staff Management')

@section('content')
<div class="container">
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
                                <tr>
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
                                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editStaffModal{{ $staff->id }}">
                                            Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteStaff({{ $staff->id }})">
                                            Delete
                                        </button>
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

<!-- Edit Staff Modal -->
@foreach($staffMembers ?? [] as $staff)
<div class="modal fade" id="editStaffModal{{ $staff->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Staff Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                            <option value="">Select Role</option>
                            <option value="instructor" {{ $staff->role === 'instructor' ? 'selected' : '' }}>Instructor</option>
                            <option value="admin" {{ $staff->role === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="staff" {{ $staff->role === 'staff' ? 'selected' : '' }}>Staff</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Department</label>
                        <input type="text" class="form-control" name="department" value="{{ optional($staff->department)->name ?? 'N/A' }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" required>
                            <option value="active" {{ $staff->status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $staff->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password (leave blank to keep current)</label>
                        <input type="password" class="form-control" name="password">
                        <div class="form-text">Enter a new password only if you want to change it.</div>
                    </div>
                    <div class="alert alert-info">
                        <small><i class="fas fa-info-circle"></i> Changing the password will send an email notification to the staff member.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Staff Member</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Auto-dismiss success and error alerts after 3 seconds
    document.addEventListener('DOMContentLoaded', function() {
        // Select all success and error alerts
        const alerts = document.querySelectorAll('.alert-success, .alert-danger');
        
        // Set a timeout to fade them out after 3 seconds
        if (alerts.length > 0) {
            setTimeout(function() {
                alerts.forEach(function(alert) {
                    // Add fade-out transition
                    alert.style.transition = 'opacity 1s';
                    alert.style.opacity = '0';
                    
                    // Remove from DOM after fade completes
                    setTimeout(function() {
                        alert.remove();
                    }, 1000);
                });
            }, 3000);
        }

        // Handle staff form submission
        $('#addStaffForm').on('submit', function(e) {
            e.preventDefault();
            
            // Clear previous errors
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text('');
            
            // Get form data
            const formData = $(this).serialize();
            
            // Disable submit button
            $('#addStaffBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Adding...');
            
            // Submit form via AJAX
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        const successAlert = $('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                            response.message +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                            '</div>');
                        
                        // Insert alert at the top of the page
                        $('.container').prepend(successAlert);
                        
                        // Close modal
                        $('#addStaffModal').modal('hide');
                        
                        // Reset form
                        $('#addStaffForm')[0].reset();
                        
                        // Reload page after a short delay
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    } else {
                        // Show error message
                        const errorAlert = $('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                            response.error +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                            '</div>');
                        
                        // Insert alert at the top of the page
                        $('.container').prepend(errorAlert);
                    }
                },
                error: function(xhr) {
                    // Show error message
                    const errorAlert = $('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                        'An error occurred while adding the staff member. Please try again.' +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                        '</div>');
                    
                    // Insert alert at the top of the page
                    $('.container').prepend(errorAlert);
                    
                    // Handle validation errors
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        for (const field in errors) {
                            $(`#${field}_error`).text(errors[field][0]);
                            $(`[name="${field}"]`).addClass('is-invalid');
                        }
                    }
                },
                complete: function() {
                    // Re-enable submit button
                    $('#addStaffBtn').prop('disabled', false).text('Add Staff Member');
                }
            });
        });
    });

    // Delete staff function
    function deleteStaff(staffId) {
        if (confirm('Are you sure you want to delete this staff member?')) {
            // Create a form for proper CSRF token submission
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ route('tenant.staff.destroy', ['tenant' => tenant('id'), 'staff' => ':staffId']) }}".replace(':staffId', staffId);
            
            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            // Add method spoofing for DELETE request
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            form.appendChild(methodField);
            
            // Append form to body, submit it, and then remove it
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }
    }

    // Wait for document to be ready
    $(document).ready(function() {
        const searchForm = document.getElementById('searchForm');
        const searchInput = document.getElementById('searchStaff');
        let searchTimeout;

        // Handle search input with debounce
        $("#searchStaff").on("keyup", function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                searchForm.submit();
            }, 300);
        });
        
        // Proper form validation
        $('form').on('submit', function(e) {
            let isValid = true;
            
            // Check all required fields
            $(this).find('input[required], select[required]').each(function() {
                if ($(this).val() === '') {
                    $(this).addClass('is-invalid');
                    isValid = false;
                } else {
                    $(this).removeClass('is-invalid');
                }
            });
            
            // Prevent form submission if validation fails
            if (!isValid) {
                e.preventDefault();
                // Focus on the first invalid field
                $(this).find('.is-invalid').first().focus();
            }
        });
    });

    // Function to check for duplicate staff IDs and emails
    function setupStaffAddForm() {
        const addStaffForm = document.getElementById('addStaffForm');
        const staffIdInput = document.getElementById('new_staff_id');
        const staffEmailInput = document.getElementById('new_staff_email');
        const staffIdError = document.getElementById('staff_id_error');
        const staffEmailError = document.getElementById('staff_email_error');
        const addStaffBtn = document.getElementById('addStaffBtn');
        
        if (!addStaffForm) return;
        
        // Collect existing staff IDs and emails
        const existingStaffIds = [];
        const existingEmails = [];
        
        document.querySelectorAll('table tbody tr').forEach(row => {
            if (row.cells && row.cells.length >= 4) {
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
            });
        }
    }

    // Initialize the staff form validation when the document is ready
    document.addEventListener('DOMContentLoaded', function() {
        setupStaffAddForm();
        // ... existing code ...
    });
</script>
@endpush 