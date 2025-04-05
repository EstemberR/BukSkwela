@extends('tenant.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Dashboard</h5>
                </div>
                <div class="card-body">
                    <!-- View Requirements Button -->
                    <button type="button" class="btn btn-primary admin-requirements-btn" data-bs-toggle="modal" data-bs-target="#adminRequirementsModal">
                        <i class="fas fa-folder-open me-2"></i>View Requirements
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Requirements Modal -->
<div class="modal fade" id="adminRequirementsModal" tabindex="-1" aria-labelledby="adminRequirementsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="adminRequirementsModalLabel">Student Requirements</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Categories Tabs -->
                <ul class="nav nav-tabs" id="requirementsTabs" role="tablist">
                    @foreach($categories as $category)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $loop->first ? 'active' : '' }}" 
                                id="tab-{{ Str::slug($category) }}" 
                                data-bs-toggle="tab" 
                                data-bs-target="#content-{{ Str::slug($category) }}" 
                                type="button" 
                                role="tab" 
                                aria-controls="content-{{ Str::slug($category) }}" 
                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                            {{ $category }}
                        </button>
                    </li>
                    @endforeach
                </ul>

                <!-- Tab Content -->
                <div class="tab-content mt-3" id="requirementsTabContent">
                    @foreach($categories as $category)
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" 
                         id="content-{{ Str::slug($category) }}" 
                         role="tabpanel" 
                         aria-labelledby="tab-{{ Str::slug($category) }}">
                        
                        <div class="list-group">
                            @foreach($folders->where('category', $category) as $folder)
                            <div class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-folder text-warning me-2"></i>
                                        <span>{{ $folder->name }}</span>
                                    </div>
                                    <div>
                                        <span class="badge bg-info">{{ $folder->files_count ?? 0 }} files</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        @if($folders->where('category', $category)->isEmpty())
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-folder-open mb-2" style="font-size: 2rem;"></i>
                            <p>No folders found in this category</p>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .modal-dialog {
        max-width: 80%;
    }
    
    .nav-tabs .nav-link {
        color: #4b5563;
        border: none;
        border-bottom: 2px solid transparent;
        padding: 0.75rem 1rem;
    }
    
    .nav-tabs .nav-link.active {
        color: rgb(3, 1, 43);
        border-bottom: 2px solid rgb(3, 1, 43);
        font-weight: 600;
    }
    
    .list-group-item {
        border: 1px solid rgba(0,0,0,0.1);
        margin-bottom: 0.5rem;
        border-radius: 0.5rem !important;
        transition: all 0.2s ease;
    }
    
    .list-group-item:hover {
        background-color: rgba(3, 1, 43, 0.05);
        border-color: rgb(3, 1, 43);
    }
    
    .badge {
        font-weight: 500;
        padding: 0.5em 1em;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize tabs
    var triggerTabList = [].slice.call(document.querySelectorAll('#requirementsTabs button'));
    triggerTabList.forEach(function (triggerEl) {
        var tabTrigger = new bootstrap.Tab(triggerEl);
        triggerEl.addEventListener('click', function (event) {
            event.preventDefault();
            tabTrigger.show();
        });
    });

    // Initialize admin requirements modal
    const adminModal = document.getElementById('adminRequirementsModal');
    if (adminModal) {
        const modal = new bootstrap.Modal(adminModal);
        document.querySelector('.admin-requirements-btn')?.addEventListener('click', function(e) {
            e.preventDefault();
            modal.show();
        });
    }
});
</script>
@endpush
@endsection