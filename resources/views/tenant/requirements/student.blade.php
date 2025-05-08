@extends('layouts.app')

@section('content')
<div class="container py-4">
    <!-- Student Information Card -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h4>{{ $student->name }}</h4>
                    <p class="mb-1"><strong>Student ID:</strong> {{ $student->student_id }}</p>
                    <p class="mb-1"><strong>Course:</strong> {{ $student->course ? $student->course->name : 'N/A' }}</p>
                    <p class="mb-0"><strong>Year Level:</strong> {{ $student->year_level }}</p>
                </div>
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h5 class="card-title">Requirements Progress</h5>
                            <div class="progress mb-3" style="height: 20px;">
                                <div class="progress-bar bg-success" role="progressbar" 
                                    style="width: {{ ($completedRequirements / $totalRequirements) * 100 }}%" 
                                    title="{{ $completedRequirements }} Approved">
                                    {{ $completedRequirements }}
                                </div>
                                <div class="progress-bar bg-warning" role="progressbar" 
                                    style="width: {{ ($pendingRequirements / $totalRequirements) * 100 }}%"
                                    title="{{ $pendingRequirements }} Pending">
                                    {{ $pendingRequirements }}
                                </div>
                                <div class="progress-bar bg-danger" role="progressbar" 
                                    style="width: {{ ($rejectedRequirements / $totalRequirements) * 100 }}%"
                                    title="{{ $rejectedRequirements }} Rejected">
                                    {{ $rejectedRequirements }}
                                </div>
                            </div>
                            <div class="d-flex justify-content-between small">
                                <span><i class="fas fa-check-circle text-success"></i> {{ $completedRequirements }} Approved</span>
                                <span><i class="fas fa-clock text-warning"></i> {{ $pendingRequirements }} Pending</span>
                                <span><i class="fas fa-times-circle text-danger"></i> {{ $rejectedRequirements }} Rejected</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Pending Requirements -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Pending Requirements</h5>
                    <span class="badge bg-warning">{{ $pendingRequirements }} items</span>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @forelse($studentRequirements->get('pending', collect()) as $requirement)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-1">{{ $requirement->name }}</h6>
                                    @if($requirement->is_required)
                                        <span class="badge bg-danger">Required</span>
                                    @endif
                                </div>
                                <p class="text-muted small mb-2">{{ $requirement->description }}</p>
                                <div class="dropzone mt-2" 
                                     ondrop="dropHandler(event, {{ $student->id }}, {{ $requirement->id }})" 
                                     ondragover="dragOverHandler(event)" 
                                     ondragleave="dragLeaveHandler(event)"
                                     onclick="triggerFileInput(this)">
                                    <p>
                                        <i class="fas fa-upload"></i>
                                        Drag and drop {{ strtoupper($requirement->file_type) }} file here or click to upload
                                    </p>
                                    <input type="file" 
                                           class="d-none" 
                                           accept=".{{ $requirement->file_type }}" 
                                           onchange="handleFileSelect(event, {{ $student->id }}, {{ $requirement->id }})">
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-success mb-0">
                                <i class="fas fa-check-circle"></i> No pending requirements!
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Submitted Requirements -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Submitted Requirements</h5>
                    <div>
                        <span class="badge bg-success">{{ $completedRequirements }} approved</span>
                        <span class="badge bg-danger ms-1">{{ $rejectedRequirements }} rejected</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @forelse($studentRequirements->get('approved', collect())->merge($studentRequirements->get('rejected', collect())) as $requirement)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-1">{{ $requirement->name }}</h6>
                                    <span class="badge bg-{{ $requirement->pivot->status === 'approved' ? 'success' : 'danger' }}">
                                        {{ ucfirst($requirement->pivot->status) }}
                                    </span>
                                </div>
                                <p class="text-muted small mb-2">{{ $requirement->description }}</p>
                                @if($requirement->is_required)
                                    <span class="badge bg-danger mb-2">Required</span>
                                @endif
                                <div class="mt-2">
                                    <a href="/storage/{{ $requirement->pivot->file_path }}" 
                                       target="_blank" 
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-file-{{ $requirement->file_type }}"></i> View File
                                    </a>
                                </div>
                                @if($requirement->pivot->remarks)
                                    <div class="alert alert-{{ $requirement->pivot->status === 'rejected' ? 'danger' : 'info' }} mt-2 mb-0 py-2 small">
                                        <strong>Remarks:</strong> {{ $requirement->pivot->remarks }}
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle"></i> No submitted requirements yet.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function triggerFileInput(dropzone) {
        dropzone.querySelector('input[type="file"]').click();
    }

    function dragOverHandler(event) {
        event.preventDefault();
        event.currentTarget.classList.add('dragover');
    }

    function dragLeaveHandler(event) {
        event.currentTarget.classList.remove('dragover');
    }

    function dropHandler(event, studentId, requirementId) {
        event.preventDefault();
        event.currentTarget.classList.remove('dragover');
        
        const file = event.dataTransfer.files[0];
        if (file) {
            uploadFile(file, studentId, requirementId);
        }
    }

    function handleFileSelect(event, studentId, requirementId) {
        const file = event.target.files[0];
        if (file) {
            uploadFile(file, studentId, requirementId);
        }
    }

    function uploadFile(file, studentId, requirementId) {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('_token', '{{ csrf_token() }}');

        fetch(`/tenant/{{ tenant('id') }}/admin/students/${studentId}/requirements/${requirementId}/upload`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to upload file. Please try again.');
        });
    }
</script>

<style>
    .dropzone {
        border: 2px dashed #ccc;
        border-radius: 4px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .dropzone:hover {
        border-color: #666;
    }

    .dropzone.dragover {
        border-color: #0d6efd;
        background-color: rgba(13, 110, 253, 0.1);
    }

    .dropzone p {
        margin: 0;
        color: #666;
    }
</style>
@endsection 