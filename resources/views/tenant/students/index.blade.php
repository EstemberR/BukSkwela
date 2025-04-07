@extends('tenant.layouts.app')

@section('title', 'Students Management')

@section('content')
<div class="container">
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
            <form id="addStudentForm" method="POST">
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
            <form id="editStudentForm{{ $student->id }}" method="POST" data-student-id="{{ $student->id }}">
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
    document.addEventListener('DOMContentLoaded', function() {
        // Debug: Log current tenant info
        const currentTenant = '{{ tenant("id") }}';
        const currentDomain = window.location.hostname;
        console.log('Current tenant:', currentTenant);
        console.log('Current domain:', currentDomain);

        // Handle form submissions
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', async function(e) {
                // Let search form submit normally
                if (this.id === 'searchForm') {
                    return true;
                }

                // Prevent default form submission
                e.preventDefault();

                try {
                    let url;
                    const method = this.id.startsWith('editStudentForm') ? 'PUT' : 'POST';
                    
                    if (this.id.startsWith('editStudentForm')) {
                        const studentId = this.getAttribute('data-student-id');
                        // Use Laravel's route helper to generate the correct URL
                        url = '{{ route("tenant.students.update", ["tenant" => "__TENANT__", "student" => "__ID__"]) }}'
                            .replace('__TENANT__', currentTenant)
                            .replace('__ID__', studentId);
                        
                        // Debug log for student update
                        console.log('Updating student:', {
                            studentId,
                            formData: Object.fromEntries(new FormData(this)),
                            tenant: currentTenant,
                            url
                        });
                    } else if (this.id === 'addStudentForm') {
                        url = '{{ route("tenant.students.store", ["tenant" => "__TENANT__"]) }}'
                            .replace('__TENANT__', currentTenant);
                    } else {
                        url = this.action;
                    }

                    console.log('Form submission details:', {
                        url,
                        method,
                        tenant: currentTenant,
                        formId: this.id
                    });

                    const formData = new FormData(this);
                    if (method === 'PUT') {
                        formData.append('_method', 'PUT');
                    }

                    const response = await fetch(url, {
                        method: 'POST', // Always POST, Laravel will handle method spoofing
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    if (!response.ok) {
                        const contentType = response.headers.get('content-type');
                        let errorMessage;
                        if (contentType && contentType.includes('application/json')) {
                            const errorData = await response.json();
                            console.error('Server error response:', errorData);
                            errorMessage = errorData.message || 'Unknown error occurred';
                        } else {
                            errorMessage = await response.text();
                            console.error('Server error (non-JSON):', errorMessage);
                        }
                        throw new Error(`HTTP error! status: ${response.status}, message: ${errorMessage}`);
                    }

                    const result = await response.json();
                    console.log('Server response:', result);

                    Swal.fire({
                        title: 'Success!',
                        text: result.message || 'Operation completed successfully',
                        icon: 'success'
                    }).then(() => {
                        window.location.reload();
                    });
                } catch (error) {
                    console.error('Form submission error:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: error.message || 'Something went wrong',
                        icon: 'error'
                    });
                }
            });
        });

        // Update delete function
        window.deleteStudent = async function(studentId) {
            try {
                const url = '{{ route("tenant.students.destroy", ["tenant" => "__TENANT__", "student" => "__ID__"]) }}'
                    .replace('__TENANT__', currentTenant)
                    .replace('__ID__', studentId);

                console.log('Deleting student:', {
                    studentId,
                    url,
                    tenant: currentTenant
                });

                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('_method', 'DELETE');

                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (!response.ok) {
                    const contentType = response.headers.get('content-type');
                    let errorMessage;
                    if (contentType && contentType.includes('application/json')) {
                        const errorData = await response.json();
                        console.error('Server error response:', errorData);
                        errorMessage = errorData.message || 'Unknown error occurred';
                    } else {
                        errorMessage = await response.text();
                        console.error('Server error (non-JSON):', errorMessage);
                    }
                    throw new Error(`HTTP error! status: ${response.status}, message: ${errorMessage}`);
                }

                const result = await response.json();
                console.log('Server response:', result);

                Swal.fire({
                    title: 'Deleted!',
                    text: result.message || 'Student has been deleted.',
                    icon: 'success'
                }).then(() => {
                    window.location.reload();
                });
            } catch (error) {
                console.error('Delete error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: error.message || 'Failed to delete student',
                    icon: 'error'
                });
            }
        };
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
</script>
@endpush 