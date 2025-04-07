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
                            <form id="searchForm" action="{{ route('tenant.students.index', ['tenant' => tenant('id')]) }}" method="GET">
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
                            <select class="form-select d-inline-block w-auto" id="courseFilter" name="course_id">
                                <option value="">All Courses</option>
                                @foreach($courses ?? [] as $course)
                                    <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->title }}
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
                                    <td>{{ $student->course->title ?? 'N/A' }}</td>
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
                                              action="{{ route('tenant.students.delete.direct.post', ['tenant' => tenant('id'), 'id' => $student->id]) }}" 
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
            <form action="{{ route('tenant.students.store', ['tenant' => tenant('id')]) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Student ID</label>
                        <input type="text" class="form-control" name="student_id" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Course</label>
                        <select class="form-select" name="course_id" required>
                            <option value="">Select Course</option>
                            @foreach($courses ?? [] as $course)
                                <option value="{{ $course->id }}">{{ $course->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="alert alert-info">
                        <small><i class="fas fa-info-circle"></i> A secure password will be automatically generated and sent to the student's email.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Student</button>
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
            <form action="{{ route('tenant.students.update', ['tenant' => tenant('id'), 'student' => $student->id]) }}" method="POST">
                @csrf
                @method('PUT')
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
                                    {{ $course->title }}
                                </option>
                            @endforeach
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
    });
    
    function setupDeleteForms() {
        // Get all delete forms
        const deleteForms = document.querySelectorAll('form[id^="delete-form-"]');
        
        // Add submit handler to each form
        deleteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const studentId = this.id.replace('delete-form-', '');
                const formAction = this.action;
                
                console.log('Form action URL:', formAction);
                
                // Create a new XMLHttpRequest instead of fetch for more control
                const xhr = new XMLHttpRequest();
                xhr.open('POST', formAction, true);
                xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.setRequestHeader('Accept', 'application/json');
                
                // Get form data
                const formData = new FormData(this);
                
                xhr.onload = function() {
                    console.log('Response status:', xhr.status);
                    console.log('Response text:', xhr.responseText);
                    
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
                            console.error('Error parsing JSON:', e);
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
                    console.error('Network error');
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
        // Ensure studentId is treated as a number
        studentId = parseInt(studentId, 10);
        
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
                
                // Create a temporary form to submit
                const form = document.createElement('form');
                form.method = 'POST';
                
                // Use the tenant ID from the page
                const tenantId = '{{ tenant("id") }}';
                // Use the simple endpoint that doesn't need ID in URL
                const deleteUrl = '/admin/students/delete-simple';
                
                // Create the full URL
                form.action = deleteUrl;
                
                // Add CSRF token
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;
                form.appendChild(csrfInput);
                
                // Add an explicit student ID field
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'student_id';
                idInput.value = studentId;
                form.appendChild(idInput);
                
                // Add to body, submit, then remove
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // Search and filter functionality
    document.addEventListener('DOMContentLoaded', function() {
        const searchForm = document.getElementById('searchForm');
        const searchInput = document.getElementById('searchStudent');
        const courseFilter = document.getElementById('courseFilter');
        let searchTimeout;

        // Handle search input with shorter debounce
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                searchForm.submit();
            }, 300); // Reduced to 300ms for faster response
        });

        // Handle course filter change
        courseFilter.addEventListener('change', function() {
            const url = new URL(window.location.href);
            url.searchParams.set('course_id', this.value);
            window.location.href = url.toString();
        });
    });
</script>
@endpush 