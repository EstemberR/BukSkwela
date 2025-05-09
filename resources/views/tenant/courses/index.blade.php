@extends('tenant.layouts.app')

@section('title', 'Course Management')

@section('content')
<!-- Add SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  // Define the showDeleteConfirmation function globally
  function showDeleteConfirmation(courseId) {
    Swal.fire({
        title: 'Delete Course',
        text: "Are you sure you want to delete this course? This action cannot be undone.",
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
            if (fullScreenLoader) {
                fullScreenLoader.classList.remove('d-none');
            }
            
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
                // Hide the loader
                if (fullScreenLoader) {
                    fullScreenLoader.classList.add('d-none');
                }
                
                if (data.success) {
                    // Course was deleted successfully
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'The course has been deleted successfully.',
                        icon: 'success'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    // Show the modal with the error message
                    const errorMsgElement = document.getElementById('cannotDeleteMessage');
                    if (errorMsgElement) {
                        errorMsgElement.textContent = data.message;
                    }
                    
                    try {
                        const modalElement = document.getElementById('cannotDeleteCourseModal');
                        if (modalElement) {
                            const modal = new bootstrap.Modal(modalElement);
                            modal.show();
                        }
                    } catch (error) {
                        console.error('Error showing modal:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: data.message || 'An error occurred',
                            icon: 'error'
                        });
                    }
                }
            })
            .catch(error => {
                // Hide the loader
                if (fullScreenLoader) {
                    fullScreenLoader.classList.add('d-none');
                }
                
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'An error occurred while deleting the course. Please try again.',
                    icon: 'error'
                });
            });
        }
    });
  }
  
  // Global error handler to catch and log any JavaScript errors
  window.onerror = function(message, source, lineno, colno, error) {
    console.error('JavaScript Error:', message);
    console.error('Source:', source, 'Line:', lineno, 'Column:', colno);
    console.error('Error Object:', error);
    return false; // Let default error handler run as well
  };
  
  // Log when page is fully loaded
  window.addEventListener('load', function() {
    console.log('Page fully loaded at', new Date().toISOString());
    console.log('Checking for #addCourseForm:', document.querySelector('#addCourseForm'));
  });
</script>

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
                Add Enrollment Form
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
                                   <button type="submit" class="btn btn-primary">Search</button>
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
                            <th class="fw-bold">School Year</th>
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
                                @if($course->school_year_start && $course->school_year_end)
                                    {{ $course->school_year_start }} - {{ $course->school_year_end }}
                                @else
                                    <span class="text-muted">Not set</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $course->status === 'active' ? 'success' : 'warning' }}">
                                    {{ ucfirst($course->status) }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editCourseModal{{ $course->id }}">
                                    Edit
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="showDeleteConfirmation('{{ $course->id }}')">
                                    Delete
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No courses found</td>
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
<div class="modal fade" id="addCourseModal" tabindex="-1" aria-labelledby="addCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCourseModalLabel">Add New Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addCourseForm" action="{{ route('tenant.courses.store', ['tenant' => tenant('id')]) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="course-name" class="form-label">Title</label>
                        <input type="text" class="form-control" id="course-name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="course-description" class="form-label">Description</label>
                        <textarea class="form-control" id="course-description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">School Year (SY)</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text">Start</span>
                                    <input type="number" class="form-control" name="school_year_start" min="2000" max="2100" placeholder="e.g., 2023">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text">End</span>
                                    <input type="number" class="form-control" name="school_year_end" min="2000" max="2100" placeholder="e.g., 2024">
                                </div>
                            </div>
                        </div>
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
<div class="modal fade" id="editCourseModal{{ $course->id }}" tabindex="-1" aria-labelledby="editCourseModalLabel{{ $course->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCourseModalLabel{{ $course->id }}">Edit Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('tenant.courses.update.direct', ['tenant' => tenant('id'), 'id' => $course->id]) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit-name-{{ $course->id }}" class="form-label">Title</label>
                        <input type="text" class="form-control" id="edit-name-{{ $course->id }}" name="name" value="{{ $course->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-description-{{ $course->id }}" class="form-label">Description</label>
                        <textarea class="form-control" id="edit-description-{{ $course->id }}" name="description" rows="3">{{ $course->description }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">School Year (SY)</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text">Start</span>
                                    <input type="number" class="form-control" name="school_year_start" min="2000" max="2100" value="{{ $course->school_year_start }}" placeholder="e.g., 2023">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text">End</span>
                                    <input type="number" class="form-control" name="school_year_end" min="2000" max="2100" value="{{ $course->school_year_end }}" placeholder="e.g., 2024">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit-status-{{ $course->id }}" class="form-label">Status</label>
                        <select class="form-select" id="edit-status-{{ $course->id }}" name="status" required>
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
<div class="modal fade" id="cannotDeleteCourseModal" tabindex="-1" aria-labelledby="cannotDeleteCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #152238; color: white;">
                <h5 class="modal-title" id="cannotDeleteCourseModalLabel">Cannot Delete Course</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-exclamation-triangle fa-4x" style="color: #DAA520;"></i>
                </div>
                <p id="cannotDeleteMessage" class="text-center"></p>
                <p class="text-center mt-3">Please reassign these students to another course before deleting.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="{{ route('tenant.students.index', ['tenant' => tenant('id')]) }}" class="btn" style="background-color: #DAA520; color: white;">
                    <i class="fas fa-users me-1"></i> Manage Students
                </a>
            </div>
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
</style>

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

document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchForm = document.getElementById('searchForm');
    const searchInput = document.getElementById('searchCourse');
    let searchTimeout;

    // Handle search input with debounce
    if (searchInput) {
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            searchForm.submit();
        }, 300);
    });
    }
    
    // School year auto-fill functionality
    const addCourseForm = document.querySelector('#addCourseForm');
    if (addCourseForm) {
        const startYearInput = addCourseForm.querySelector('[name="school_year_start"]');
        const endYearInput = addCourseForm.querySelector('[name="school_year_end"]');
        
        if (startYearInput && endYearInput) {
            startYearInput.addEventListener('change', function() {
                if (this.value && !endYearInput.value) {
                    // Auto-set end year to start year + 1
                    endYearInput.value = parseInt(this.value) + 1;
                }
            });
            
            // Validate the years relationship before submission
            addCourseForm.addEventListener('submit', function(e) {
                const startYear = parseInt(startYearInput.value);
                const endYear = parseInt(endYearInput.value);
                
                if (startYear && endYear && endYear < startYear) {
                    e.preventDefault();
                    alert('The end year must be equal to or greater than the start year.');
                    endYearInput.value = startYear + 1;
                    return false;
                }
            });
        }
    }
    
    // Apply the same for edit forms
    const editForms = document.querySelectorAll('form[action*="courses.update"]');
    editForms.forEach(function(form) {
        const startYearInput = form.querySelector('[name="school_year_start"]');
        const endYearInput = form.querySelector('[name="school_year_end"]');
        
        if (startYearInput && endYearInput) {
            startYearInput.addEventListener('change', function() {
                if (this.value && (!endYearInput.value || parseInt(endYearInput.value) < parseInt(this.value))) {
                    // Auto-set end year to start year + 1
                    endYearInput.value = parseInt(this.value) + 1;
                }
            });
            
            // Validate the years relationship before submission
            form.addEventListener('submit', function(e) {
                const startYear = parseInt(startYearInput.value);
                const endYear = parseInt(endYearInput.value);
                
                if (startYear && endYear && endYear < startYear) {
                    e.preventDefault();
                    alert('The end year must be equal to or greater than the start year.');
                    endYearInput.value = startYear + 1;
                    return false;
                }
            });
        }
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

    // Form validation function
    function validateCourseForm(form) {
        let isValid = true;
        const name = form.querySelector('[name="name"]').value.trim();
        
        if (!name) {
            isValid = false;
            Swal.fire({
                title: 'Validation Error',
                text: 'Course name is required',
                icon: 'error'
            });
        }
        
        return isValid;
    }

    // Add Course form submission handler
    const addCourseForm = document.querySelector('#addCourseForm');
    if (addCourseForm) {
        console.log('Form found:', addCourseForm);
        addCourseForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Form submitted');
            
            // Validate form
            if (!validateCourseForm(this)) {
                return;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
            
            // Create FormData
            const formData = new FormData(this);
            
            // Log FormData for debugging
            console.log('Form action:', this.action);
            for (let [key, value] of formData.entries()) {
                console.log(`${key}: ${value}`);
            }
            
            // Submit the form via fetch for better error handling
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json().then(data => {
                    if (!response.ok) {
                        throw new Error(data.message || 'Network response was not ok');
                    }
                    return data;
                });
            })
            .then(data => {
                console.log('Success response:', data);
                
                // Show success message
                Swal.fire({
                    title: 'Success!',
                    text: 'The course has been added successfully.',
                    icon: 'success'
                }).then(() => {
                    // Hide modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addCourseModal'));
                    if (modal) modal.hide();
                    
                    // Reload the page to show the new course
                    window.location.reload();
                });
            })
            .catch(error => {
                console.error('Error:', error);
                
                // Reset button state
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
                
                // Show error message
                Swal.fire({
                    title: 'Error!',
                    text: error.message || 'Failed to add the course. Please try again.',
                    icon: 'error'
                });
            });
        });
    } else {
        console.error('Form not found: #addCourseForm');
    }
});
</script>
@endpush
@endsection 