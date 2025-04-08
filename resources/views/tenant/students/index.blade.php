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
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Students</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                        Add Student
                    </button>
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
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6 text-end">
                            <select class="form-select d-inline-block w-auto" id="courseFilter" name="course_id" onchange="document.getElementById('searchForm').submit()">
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
                            {{ $students->links() }}
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
            <form id="addStudentForm" action="{{ route('tenant.students.store.direct') }}" method="POST">
                @csrf
                <div class="modal-body">
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="addStudentBtn">Add Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

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
            Swal.fire({
                title: 'Success!',
                text: successMessage.value,
                icon: 'success',
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false
            });
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
    });
    
    // Function to check for duplicate student IDs and emails
    function setupStudentAddForm() {
        const addStudentForm = document.getElementById('addStudentForm');
        const studentIdInput = document.getElementById('new_student_id');
        const studentEmailInput = document.getElementById('new_student_email');
        const studentIdError = document.getElementById('student_id_error');
        const studentEmailError = document.getElementById('student_email_error');
        const addStudentBtn = document.getElementById('addStudentBtn');
        
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
        
        console.log('Existing Student IDs:', existingStudentIds);
        console.log('Existing Emails:', existingEmails);
        
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
                
                // Create a new XMLHttpRequest instead of fetch for more control
                const xhr = new XMLHttpRequest();
                xhr.open('POST', formAction, true);
                xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.setRequestHeader('Accept', 'application/json');
                
                // Get form data
                const formData = new FormData(this);
                
                xhr.onload = function() {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        try {
                            const data = JSON.parse(xhr.responseText);
                            
                            if (data.success) {
                                // Remove the row from the table
                                const row = document.querySelector(`tr[data-student-id="${studentId}"]`);
                                if (row) {
                                    row.remove();
                                }
                                
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: data.message || 'Student deleted successfully',
                                    icon: 'success',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
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
                // Show loading state
                Swal.fire({
                    title: 'Deleting...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Submit the delete form that's already defined for this student
                document.getElementById(`delete-form-${studentId}`).submit();
            }
        });
    }

    // Search and filter functionality
    document.addEventListener('DOMContentLoaded', function() {
        const searchForm = document.getElementById('searchForm');
        const searchInput = document.getElementById('searchStudent');
        let searchTimeout;

        // Handle search input with shorter debounce
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                searchForm.submit();
            }, 300); // Reduced to 300ms for faster response
        });
    });
</script>
@endpush 