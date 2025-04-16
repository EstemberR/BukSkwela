@extends('tenant.layouts.app')

@section('title', 'Course Management')

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

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Courses</h5>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                Add Course
            </button>
        </div>
        <div class="card-body">
            <!-- Search and filters -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <form id="searchForm" action="{{ route('tenant.courses.index', ['tenant' => tenant('id')]) }}" method="GET">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" 
                                   class="form-control" 
                                   placeholder="Search courses..." 
                                   id="searchCourse"
                                   name="search"
                                   value="{{ request('search') }}"
                                   autocomplete="off">
                        </div>
                </div>
                <div class="col-md-6 text-end">
                    <select class="form-select d-inline-block w-auto" id="statusFilter" name="status" onchange="document.getElementById('searchForm').submit()">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    </form>
                </div>
            </div>

            <!-- Courses Table -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="fw-bold">Title</th>
                            <th class="fw-bold">Description</th>
                            <th class="fw-bold">Status</th>
                            <th class="fw-bold">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courses as $course)
                        <tr>
                            <td>{{ $course->name }}</td>
                            <td>{{ Str::limit($course->description, 50) }}</td>
                            <td>
                                <span class="badge bg-{{ $course->status === 'active' ? 'success' : 'warning' }}">
                                    {{ ucfirst($course->status) }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editCourseModal{{ $course->id }}">
                                    Edit
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteCourse({{ $course->id }})">
                                    Delete
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">No courses found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Showing {{ $courses->firstItem() ?? 0 }} to {{ $courses->lastItem() ?? 0 }} of {{ $courses->total() }} entries
                </div>
                <div>
                    {{ $courses->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Course Modal -->
<div class="modal fade" id="addCourseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('tenant.courses.store', ['tenant' => tenant('id')]) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Course</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Course Modals -->
@foreach($courses as $course)
<div class="modal fade" id="editCourseModal{{ $course->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('tenant.courses.update.direct', ['tenant' => tenant('id'), 'id' => $course->id]) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" name="name" value="{{ $course->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3">{{ $course->description }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" required>
                            <option value="active" {{ $course->status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $course->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Course</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<!-- Cannot Delete Course Modal -->
<div class="modal fade" id="cannotDeleteCourseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Cannot Delete Course</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-exclamation-triangle text-warning fa-4x"></i>
                </div>
                <p id="cannotDeleteMessage" class="text-center"></p>
                <p class="text-center mt-3">Please reassign these students to another course before deleting.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="{{ route('tenant.students.index', ['tenant' => tenant('id')]) }}" class="btn btn-primary">
                    <i class="fas fa-users me-1"></i> Manage Students
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- Make sure Bootstrap JS is loaded properly -->
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
    };
} else {
    // Bootstrap is available, initialize modals
    document.addEventListener('DOMContentLoaded', function() {
        initializeModals();
    });
}

// Function to safely initialize modals
function initializeModals() {
    const modalElements = document.querySelectorAll('.modal');
    modalElements.forEach(function(modalElement) {
        try {
            new bootstrap.Modal(modalElement, {
                backdrop: true,
                keyboard: true,
                focus: true
            });
        } catch (error) {
            console.warn('Error initializing modal:', error);
        }
    });
}

function deleteCourse(courseId) {
    if (confirm('Are you sure you want to delete this course?')) {
        // Create a fetch request to check for enrolled students first
        const url = "{{ route('tenant.courses.delete.direct', ['tenant' => tenant('id'), 'id' => ':id']) }}".replace(':id', courseId);
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Course was deleted successfully, reload the page
                window.location.reload();
            } else {
                // Show the modal with the error message
                document.getElementById('cannotDeleteMessage').textContent = data.message;
                try {
                    const modal = new bootstrap.Modal(document.getElementById('cannotDeleteCourseModal'));
                    modal.show();
                } catch (error) {
                    console.error('Error showing modal:', error);
                    alert(data.message);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the course. Please try again.');
        });
    }
}

// Search and filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('searchForm');
    const searchInput = document.getElementById('searchCourse');
    let searchTimeout;

    // Handle search input with debounce
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            searchForm.submit();
        }, 300);
    });
    
    // Auto-dismiss success messages after 3 seconds
    const successAlerts = document.querySelectorAll('.alert-success');
    if (successAlerts.length > 0) {
        setTimeout(function() {
            successAlerts.forEach(function(alert) {
                // Create a fade-out effect
                alert.style.transition = 'opacity 1s';
                alert.style.opacity = '0';
                
                // Remove the element after the fade-out
                setTimeout(function() {
                    alert.remove();
                }, 1000);
            });
        }, 3000); // 3 seconds before starting the fade-out
    }
});
</script>
@endpush
@endsection 