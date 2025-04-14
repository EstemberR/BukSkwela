@extends('superadmin.layouts.app')

@section('title', 'Tenants Management')

@section('styles')
<style>
    /* Subscription plan dropdown styling */
    .badge {
        font-size: 0.85rem;
        font-weight: 500;
    }
    
    .dropdown-item {
        padding: 0.75rem 1rem;
        transition: all 0.2s;
    }
    
    .dropdown-item:hover:not(.disabled) {
        transform: translateY(-2px);
        background-color: rgba(0, 123, 255, 0.05);
    }
    
    .dropdown-header {
        font-weight: 600;
        color: #495057;
    }
    
    .dropdown-menu {
        border-radius: 0.5rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    
    .btn-outline-info:hover, 
    .btn-outline-warning:hover, 
    .btn-outline-dark:hover {
        color: #fff;
    }
    
    /* Current plan highlight */
    .dropdown-item.disabled.bg-light {
        position: relative;
        background-color: rgba(0, 123, 255, 0.05) !important;
        opacity: 1;
        pointer-events: none;
    }
    
    /* Enlarge badges */
    .badge {
        padding: 0.5em 0.75em;
    }
    
    /* Smoother transitions */
    .dropdown-toggle {
        transition: all 0.3s ease;
    }
    
    /* Status badges */
    .badge.bg-success {
        background-color: #28a745 !important;
    }
    
    .badge.bg-warning {
        background-color: #ffc107 !important;
        color: #212529;
    }
    
    .badge.bg-danger {
        background-color: #dc3545 !important;
    }
    
    .badge.bg-info {
        background-color: #17a2b8 !important;
    }
    
    .badge.bg-dark {
        background-color: #343a40 !important;
    }
    
    /* Subscription Modal Styles */
    .plan-options .card {
        transition: all 0.3s ease;
        border-width: 1px;
    }
    
    .plan-options .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    }
    
    .plan-options .card.border-primary {
        border-width: 2px !important;
        background-color: rgba(0, 123, 255, 0.05);
    }
    
    .plan-options .card.border-warning {
        border-width: 2px !important;
        background-color: rgba(255, 193, 7, 0.05);
    }
    
    .plan-options .card.border-dark {
        border-width: 2px !important;
        background-color: rgba(52, 58, 64, 0.05);
    }
    
    .plan-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    
    .card:hover .plan-icon {
        transform: scale(1.1);
    }
    
    .modal-content {
        border: none;
        border-radius: 0.5rem;
        overflow: hidden;
    }
    
    .modal-header {
        padding: 1rem 1.5rem;
    }
    
    .modal-body {
        padding: 1.5rem;
    }
    
    .btn-outline-info:hover,
    .btn-outline-warning:hover,
    .btn-outline-dark:hover {
        color: #fff;
    }
    
    /* Pulse animation for Current badge */
    @keyframes pulse {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
        100% {
            transform: scale(1);
        }
    }
    
    .badge {
        position: relative;
    }
    
    .plan-options .badge {
        animation: pulse 2s infinite;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Tenant Management</h2>
        <div class="btn-group">
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="fas fa-filter"></i> Filter
            </button>
        </div>
    </div>

    <!-- Display Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('info'))
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        {{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Status Tabs -->
    <ul class="nav nav-tabs mb-4" id="tenantTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true">
                All <span class="badge bg-secondary rounded-pill ms-1">{{ $tenants->total() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="false">
                @php
                    $pendingCount = 0;
                    foreach($tenants as $tenant) {
                        if($tenant->status == 'pending') {
                            $pendingCount++;
                        }
                    }
                @endphp
                Pending <span class="badge bg-warning rounded-pill ms-1">{{ $pendingCount }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="active-tab" data-bs-toggle="tab" data-bs-target="#active" type="button" role="tab" aria-controls="active" aria-selected="false">
                @php
                    $activeCount = 0;
                    foreach($tenants as $tenant) {
                        if($tenant->status == 'approved') {
                            $activeCount++;
                        }
                    }
                @endphp
                Active <span class="badge bg-success rounded-pill ms-1">{{ $activeCount }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected" type="button" role="tab" aria-controls="rejected" aria-selected="false">
                @php
                    $rejectedCount = 0;
                    foreach($tenants as $tenant) {
                        if(in_array($tenant->status, ['rejected', 'disabled', 'denied'])) {
                            $rejectedCount++;
                        }
                    }
                @endphp
                Rejected/Disabled <span class="badge bg-danger rounded-pill ms-1">{{ $rejectedCount }}</span>
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="tenantTabsContent">
        <!-- All Tenants Tab -->
        <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
            <div class="card">
                <div class="card-body">
                    @include('superadmin.tenants.partials.tenant-table', ['tenants' => $tenants])
                </div>
            </div>
        </div>
        
        <!-- Pending Tenants Tab -->
        <div class="tab-pane fade" id="pending" role="tabpanel" aria-labelledby="pending-tab">
            <div class="card">
                <div class="card-body">
                    @php 
                        $pendingTenants = $tenants->getCollection()->filter(function($t) { 
                            return $t->status == 'pending'; 
                        });
                    @endphp
                    @include('superadmin.tenants.partials.tenant-table', ['tenants' => $pendingTenants])
                </div>
            </div>
        </div>
        
        <!-- Active Tenants Tab -->
        <div class="tab-pane fade" id="active" role="tabpanel" aria-labelledby="active-tab">
            <div class="card">
                <div class="card-body">
                    @php 
                        $activeTenants = $tenants->getCollection()->filter(function($t) { 
                            return $t->status == 'approved'; 
                        });
                    @endphp
                    @include('superadmin.tenants.partials.tenant-table', ['tenants' => $activeTenants])
                </div>
            </div>
        </div>
        
        <!-- Rejected Tenants Tab -->
        <div class="tab-pane fade" id="rejected" role="tabpanel" aria-labelledby="rejected-tab">
            <div class="card">
                <div class="card-body">
                    @php 
                        $rejectedTenants = $tenants->getCollection()->filter(function($t) { 
                            return in_array($t->status, ['rejected', 'disabled', 'denied']); 
                        });
                    @endphp
                    @include('superadmin.tenants.partials.tenant-table', ['tenants' => $rejectedTenants])
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filter Tenants</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="filterForm">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="">All</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="disabled">Disabled</option>
                            <option value="rejected">Rejected</option>
                            <option value="denied">Denied</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Status</label>
                        <select class="form-select" name="payment_status">
                            <option value="">All</option>
                            <option value="paid">Paid</option>
                            <option value="unpaid">Unpaid</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subscription Plan</label>
                        <select class="form-select" name="subscription_plan">
                            <option value="">All</option>
                            <option value="basic">Basic</option>
                            <option value="premium">Premium</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="applyFilter()">Apply Filter</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function applyFilter() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    // Add your filter logic here
    $('#filterModal').modal('hide');
}

// Function to confirm subscription plan change with a professional dialog
function confirmPlanChange(buttonElement, planType, tenantName) {
    // Prevent accidental double-click
    buttonElement.disabled = true;
    
    // Find the form element
    const form = buttonElement.closest('form.subscription-form');
    
    // Define plan details
    const planDetails = {
        'basic': { 
            title: 'Basic Plan',
            icon: '<i class="fas fa-cube text-info me-2"></i>',
            description: 'Free tier with limited features',
            color: '#17a2b8'
        },
        'premium': { 
            title: 'Premium Plan',
            icon: '<i class="fas fa-crown text-warning me-2"></i>',
            description: '$29.99/month with advanced features',
            color: '#ffc107'
        }
    };
    
    // Create confirmation dialog with Bootstrap
    const modalHtml = `
        <div class="modal fade" id="confirmSubscriptionModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: ${planDetails[planType].color}; color: #fff;">
                        <h5 class="modal-title">${planDetails[planType].icon} Change Subscription</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="color: #fff;"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-4">
                            <div class="display-1 mb-3" style="color: ${planDetails[planType].color};">${planDetails[planType].icon.replace('me-2', '')}</div>
                            <h4>Confirm Subscription Change</h4>
                            <p class="text-muted">You are about to change the subscription for <strong>${tenantName}</strong> to:</p>
                            <div class="alert alert-light border">
                                <strong>${planDetails[planType].title}</strong><br>
                                <small>${planDetails[planType].description}</small>
                            </div>
                        </div>
                        <p class="text-center">This action will take effect immediately. Are you sure?</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" id="cancelPlanChange">
                            <i class="fas fa-times me-1"></i> Cancel
                        </button>
                        <button type="button" class="btn btn-primary" id="confirmPlanBtn" style="background-color: ${planDetails[planType].color}; border-color: ${planDetails[planType].color};">
                            <i class="fas fa-check me-1"></i> Confirm Change
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Append modal to body
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Initialize the modal
    const modal = new bootstrap.Modal(document.getElementById('confirmSubscriptionModal'));
    modal.show();
    
    // Handle cancel
    document.getElementById('cancelPlanChange').addEventListener('click', function() {
        buttonElement.disabled = false;
        document.getElementById('confirmSubscriptionModal').remove();
    });
    
    // Handle confirm
    document.getElementById('confirmPlanBtn').addEventListener('click', function() {
        // Submit the form
        form.submit();
        
        // Show loading state
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Processing...';
        this.disabled = true;
    });
    
    // Remove modal on hidden
    document.getElementById('confirmSubscriptionModal').addEventListener('hidden.bs.modal', function() {
        buttonElement.disabled = false;
        document.getElementById('confirmSubscriptionModal').remove();
    });
}

// Activate the correct tab based on URL hash or session flash data
document.addEventListener('DOMContentLoaded', function() {
    // Check if a tab was specified in the session (from controller redirect)
    const tabFromSession = "{{ session('tab') }}";
    if (tabFromSession) {
        const tabElement = document.getElementById(`${tabFromSession}-tab`);
        if (tabElement) {
            const tab = new bootstrap.Tab(tabElement);
            tab.show();
        }
    } 
    // Otherwise check for hash in URL
    else if(window.location.hash) {
        // Get the hash without # and try to activate that tab
        const hash = window.location.hash.substring(1);
        const tabElement = document.getElementById(`${hash}-tab`);
        if(tabElement) {
            const tab = new bootstrap.Tab(tabElement);
            tab.show();
        }
    }
    
    // Update URL when tab changes
    const tabList = [].slice.call(document.querySelectorAll('button[data-bs-toggle="tab"]'));
    tabList.forEach(function(tabEl) {
        tabEl.addEventListener('shown.bs.tab', function(event) {
            const id = event.target.id.replace('-tab', '');
            if (id !== 'all') {
                window.location.hash = id;
            } else {
                history.replaceState(null, null, ' ');
            }
        });
    });
});
</script>
@endpush 