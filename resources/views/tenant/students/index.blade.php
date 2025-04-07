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
                                    {{ $course->name }}
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
    });

    function showDeleteConfirmation(studentId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                deleteStudent(studentId);
            }
        });
    }

    async function deleteStudent(studentId) {
        try {
            const tenant = '{{ tenant("id") }}';
            const deleteUrl = `/admin/students/${studentId}`;
            
            console.log('Attempting to delete student:', {
                studentId,
                deleteUrl,
                tenant
            });

            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                throw new Error('CSRF token not found. Please refresh the page and try again.');
            }

            // Show loading state
            Swal.fire({
                title: 'Deleting...',
                text: 'Please wait',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            const response = await fetch(deleteUrl, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken.content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            let result;
            try {
                result = await response.json();
            } catch (e) {
                console.error('Failed to parse response:', e);
                const text = await response.text();
                console.error('Raw response:', text);
                throw new Error('Invalid response from server');
            }

            if (!response.ok) {
                console.error('Delete response:', {
                    status: response.status,
                    statusText: response.statusText,
                    result
                });
                throw new Error(result?.message || `Failed to delete student (${response.status})`);
            }

            if (result.success) {
                Swal.fire({
                    title: 'Success!',
                    text: 'Student deleted successfully',
                    icon: 'success'
                }).then(() => {
                    // Remove the row from the table
                    const row = document.querySelector(`tr[data-student-id="${studentId}"]`);
                    if (row) {
                        row.remove();
                    } else {
                        window.location.reload();
                    }
                });
            } else {
                throw new Error(result.message || 'Failed to delete student');
            }
        } catch (error) {
            console.error('Delete error:', error);
            Swal.fire({
                title: 'Error!',
                text: error.message || 'An error occurred while deleting the student',
                icon: 'error'
            });
        }
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