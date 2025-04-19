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
    
    /* Fix for SweetAlert modals */
    .swal2-container {
        z-index: 9999 !important; 
    }
    
    .swal2-popup {
        border-radius: 0.75rem !important;
        box-shadow: 0 0.5rem 2rem rgba(0, 0, 0, 0.2) !important;
    }
    
    .swal2-title {
        font-weight: 600 !important;
    }
    
    .swal2-html-container {
        margin-top: 1rem !important;
    }
    
    /* Plan card in SweetAlert */
    .plan-card {
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.1);
    }
    
    .plan-card-icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
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
        {!! session('success') !!}
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
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tenant Name</th>
                                    <th>Email</th>
                                    <th>Subscription</th>
                                    <th>Status</th>
                                    <th>Payment Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tenants as $tenant)
                                <tr>
                                    <td>{{ $tenant->tenant_name }}</td>
                                    <td>{{ $tenant->tenant_email }}</td>
                                    <td>
                                        <div class="subscription-status">
                                                <button type="button" 
                                                    class="btn {{ $tenant->subscription_plan === 'premium' ? 'btn-warning' : 'btn-outline-secondary' }}"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#subscriptionModal-{{ $tenant->id }}">
                                                    @if($tenant->subscription_plan === 'premium')
                                                        <i class="fas fa-crown text-warning me-1"></i>
                                                    @else
                                                        <i class="fas fa-cube me-1"></i>
                                                    @endif
                                                    {{ ucfirst($tenant->subscription_plan) }}
                                                </button>

                                            <!-- Subscription Plan Modal -->
                                            <div class="modal fade" id="subscriptionModal-{{ $tenant->id }}" tabindex="-1" aria-labelledby="subscriptionModalLabel-{{ $tenant->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="subscriptionModalLabel-{{ $tenant->id }}">Manage Subscription: {{ $tenant->tenant_name }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="current-plan mb-4">
                                                                <h6 class="text-muted mb-3">Current Plan</h6>
                                                                <div class="card bg-light">
                                                                    <div class="card-body d-flex align-items-center">
                                                                        <div class="me-3">
                                                                            @if($tenant->subscription_plan === 'premium')
                                                                                <div class="display-6 text-warning">
                                                                                    <i class="fas fa-crown"></i>
                                                                                </div>
                                                                            @else
                                                                                <div class="display-6 text-info">
                                                                                    <i class="fas fa-cube"></i>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                        <div>
                                                                            <h5 class="card-title mb-1">{{ ucfirst($tenant->subscription_plan) }}</h5>
                                                                            <p class="card-text">
                                                                                @if($tenant->subscription_plan === 'premium')
                                                                                    Premium features with priority support
                                                                                    @if(isset($tenant->data['subscription_ends_at']))
                                                                                        <br><small class="text-muted">Expires: {{ \Carbon\Carbon::parse($tenant->data['subscription_ends_at'])->format('M d, Y') }}</small>
                                                                                    @endif
                                                                                @else
                                                                                    Basic features with standard support
                                                                                @endif
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="change-plan">
                                                                <h6 class="text-muted mb-3">Change Plan</h6>
                                                                <div class="row">
                                                                    @if($tenant->subscription_plan === 'basic')
                                                                    <div class="col-12">
                                                                        <div class="card border-warning">
                                                                            <div class="card-body d-flex align-items-center">
                                                                                <div class="me-3">
                                                                                    <div class="display-6 text-warning">
                                                                                        <i class="fas fa-crown"></i>
                                                                                    </div>
                                                                                </div>
                                                                                <div>
                                                                                    <h5 class="card-title mb-1">Premium</h5>
                                                                                    <p class="card-text">₱5,000/month with advanced features</p>
                                                                                </div>
                                                                            </div>
                                                                            <div class="card-footer bg-transparent border-0 text-end">
                                                        <button type="button" 
                                                                                        class="btn btn-warning subscription-change-btn"
                                                                data-tenant-id="{{ $tenant->id }}"
                                                                data-tenant-name="{{ $tenant->tenant_name }}"
                                                                data-current-plan="{{ $tenant->subscription_plan }}"
                                                                                        data-target-plan="premium"
                                                                                        data-bs-dismiss="modal">
                                                                                    <i class="fas fa-arrow-up me-1"></i> Upgrade to Premium
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                            @else
                                                                    <div class="col-12">
                                                                        <div class="card border-info">
                                                                            <div class="card-body d-flex align-items-center">
                                                                                <div class="me-3">
                                                                                    <div class="display-6 text-info">
                                                                                        <i class="fas fa-cube"></i>
                                                                                    </div>
                                                                                </div>
                                                                                <div>
                                                                                    <h5 class="card-title mb-1">Basic</h5>
                                                                                    <p class="card-text">Free tier with limited features</p>
                                                                                </div>
                                                                            </div>
                                                                            <div class="card-footer bg-transparent border-0 text-end">
                                                                                <button type="button" 
                                                                                        class="btn btn-info text-white subscription-change-btn"
                                                                                        data-tenant-id="{{ $tenant->id }}"
                                                                                        data-tenant-name="{{ $tenant->tenant_name }}"
                                                                                        data-current-plan="{{ $tenant->subscription_plan }}"
                                                                                        data-target-plan="basic"
                                                                                        data-bs-dismiss="modal">
                                                                                    <i class="fas fa-arrow-down me-1"></i> Downgrade to Basic
                                                        </button>
                                                            </div>
                                                                        </div>
                                                                    </div>
                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $tenant->status === 'approved' ? 'success' : ($tenant->status === 'pending' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($tenant->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            // Determine payment status based on subscription plan
                                            $paymentStatus = $tenant->subscription_plan === 'premium' ? 'paid' : ($tenant->data['payment_status'] ?? 'not_paid');
                                            $badgeClass = match($paymentStatus) {
                                                'paid' => 'success',
                                                'not_paid' => 'danger',
                                                'downgraded' => 'secondary',
                                                default => 'warning'
                                            };
                                            $statusText = match($paymentStatus) {
                                                'paid' => 'Paid',
                                                'not_paid' => 'Not Paid',
                                                'downgraded' => 'Downgraded',
                                                default => ucfirst($paymentStatus)
                                            };
                                        @endphp
                                        <div class="payment-status">
                                            <span class="badge bg-{{ $badgeClass }}">
                                                @if($paymentStatus === 'paid')
                                                    <i class="fas fa-check-circle me-1"></i>
                                                @elseif($paymentStatus === 'not_paid')
                                                    <i class="fas fa-times-circle me-1"></i>
                                                @elseif($paymentStatus === 'downgraded')
                                                    <i class="fas fa-arrow-circle-down me-1"></i>
                                                @endif
                                                {{ $statusText }}
                                            </span>
                                            @if($tenant->subscription_plan === 'premium' && isset($tenant->data['subscription_ends_at']))
                                                <div class="mt-1">
                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar-alt me-1"></i>
                                                        Until: {{ \Carbon\Carbon::parse($tenant->data['subscription_ends_at'])->format('M d, Y') }}
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group">
                                            <a href="{{ route('superadmin.tenants.show', $tenant->id) }}" 
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            
                                            @if($tenant->status === 'pending')
                                                <form action="{{ route('superadmin.tenants.approve', $tenant->id) }}" 
                                                      method="POST" 
                                                      class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success ms-1">
                                                        <i class="fas fa-check"></i> Approve
                                                    </button>
                                                </form>
                                                <form action="{{ route('superadmin.tenants.reject', $tenant->id) }}" 
                                                      method="POST" 
                                                      class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger ms-1">
                                                        <i class="fas fa-times"></i> Reject
                                                    </button>
                                                </form>
                                            @endif

                                            @if($tenant->status === 'approved')
                                                <form action="{{ route('superadmin.tenants.disable', $tenant->id) }}" 
                                                      method="POST" 
                                                      class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-warning ms-1" 
                                                            onclick="return confirm('Are you sure you want to disable this tenant?')">
                                                        <i class="fas fa-ban"></i> Disable
                                                    </button>
                                                </form>
                                            @endif

                                            @if($tenant->status === 'disabled')
                                                <form action="{{ route('superadmin.tenants.enable', $tenant->id) }}" 
                                                      method="POST" 
                                                      class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success ms-1" 
                                                            onclick="return confirm('Are you sure you want to enable this tenant?')">
                                                        <i class="fas fa-check-circle"></i> Enable
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No tenants found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
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
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tenant Name</th>
                                    <th>Email</th>
                                    <th>Subscription</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingTenants as $tenant)
                                <tr>
                                    <td>{{ $tenant->tenant_name }}</td>
                                    <td>{{ $tenant->tenant_email }}</td>
                                    <td>
                                        <div class="subscription-status">
                                                <button type="button" 
                                                    class="btn {{ $tenant->subscription_plan === 'premium' ? 'btn-warning' : 'btn-outline-secondary' }}"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#subscriptionModal-{{ $tenant->id }}">
                                                    @if($tenant->subscription_plan === 'premium')
                                                        <i class="fas fa-crown text-warning me-1"></i>
                                                    @else
                                                        <i class="fas fa-cube me-1"></i>
                                                    @endif
                                                    {{ ucfirst($tenant->subscription_plan) }}
                                                </button>

                                            <!-- Subscription Plan Modal -->
                                            <div class="modal fade" id="subscriptionModal-{{ $tenant->id }}" tabindex="-1" aria-labelledby="subscriptionModalLabel-{{ $tenant->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="subscriptionModalLabel-{{ $tenant->id }}">Manage Subscription: {{ $tenant->tenant_name }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="current-plan mb-4">
                                                                <h6 class="text-muted mb-3">Current Plan</h6>
                                                                <div class="card bg-light">
                                                                    <div class="card-body d-flex align-items-center">
                                                                        <div class="me-3">
                                                                            @if($tenant->subscription_plan === 'premium')
                                                                                <div class="display-6 text-warning">
                                                                                    <i class="fas fa-crown"></i>
                                                                                </div>
                                                                            @else
                                                                                <div class="display-6 text-info">
                                                                                    <i class="fas fa-cube"></i>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                        <div>
                                                                            <h5 class="card-title mb-1">{{ ucfirst($tenant->subscription_plan) }}</h5>
                                                                            <p class="card-text">
                                                                                @if($tenant->subscription_plan === 'premium')
                                                                                    Premium features with priority support
                                                                                    @if(isset($tenant->data['subscription_ends_at']))
                                                                                        <br><small class="text-muted">Expires: {{ \Carbon\Carbon::parse($tenant->data['subscription_ends_at'])->format('M d, Y') }}</small>
                                                                                    @endif
                                                                                @else
                                                                                    Basic features with standard support
                                                                                @endif
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="change-plan">
                                                                <h6 class="text-muted mb-3">Change Plan</h6>
                                                                <div class="row">
                                                                    @if($tenant->subscription_plan === 'basic')
                                                                    <div class="col-12">
                                                                        <div class="card border-warning">
                                                                            <div class="card-body d-flex align-items-center">
                                                                                <div class="me-3">
                                                                                    <div class="display-6 text-warning">
                                                                                        <i class="fas fa-crown"></i>
                                                                                    </div>
                                                                                </div>
                                                                                <div>
                                                                                    <h5 class="card-title mb-1">Premium</h5>
                                                                                    <p class="card-text">₱5,000/month with advanced features</p>
                                                                                </div>
                                                                            </div>
                                                                            <div class="card-footer bg-transparent border-0 text-end">
                                                        <button type="button" 
                                                                                        class="btn btn-warning subscription-change-btn"
                                                                data-tenant-id="{{ $tenant->id }}"
                                                                data-tenant-name="{{ $tenant->tenant_name }}"
                                                                data-current-plan="{{ $tenant->subscription_plan }}"
                                                                                        data-target-plan="premium"
                                                                                        data-bs-dismiss="modal">
                                                                                    <i class="fas fa-arrow-up me-1"></i> Upgrade to Premium
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                            @else
                                                                    <div class="col-12">
                                                                        <div class="card border-info">
                                                                            <div class="card-body d-flex align-items-center">
                                                                                <div class="me-3">
                                                                                    <div class="display-6 text-info">
                                                                                        <i class="fas fa-cube"></i>
                                                                                    </div>
                                                                                </div>
                                                                                <div>
                                                                                    <h5 class="card-title mb-1">Basic</h5>
                                                                                    <p class="card-text">Free tier with limited features</p>
                                                                                </div>
                                                                            </div>
                                                                            <div class="card-footer bg-transparent border-0 text-end">
                                                                                <button type="button" 
                                                                                        class="btn btn-info text-white subscription-change-btn"
                                                                                        data-tenant-id="{{ $tenant->id }}"
                                                                                        data-tenant-name="{{ $tenant->tenant_name }}"
                                                                                        data-current-plan="{{ $tenant->subscription_plan }}"
                                                                                        data-target-plan="basic"
                                                                                        data-bs-dismiss="modal">
                                                                                    <i class="fas fa-arrow-down me-1"></i> Downgrade to Basic
                                                        </button>
                                                            </div>
                                                                        </div>
                                                                    </div>
                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $tenant->status === 'approved' ? 'success' : ($tenant->status === 'pending' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($tenant->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('superadmin.tenants.show', $tenant->id) }}" 
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            @if($tenant->status === 'pending')
                                                <form action="{{ route('superadmin.tenants.approve', $tenant->id) }}" 
                                                      method="POST" 
                                                      class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success ms-1">
                                                        <i class="fas fa-check"></i> Approve
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No tenants found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
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
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tenant Name</th>
                                    <th>Email</th>
                                    <th>Subscription</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activeTenants as $tenant)
                                <tr>
                                    <td>{{ $tenant->tenant_name }}</td>
                                    <td>{{ $tenant->tenant_email }}</td>
                                    <td>
                                        <div class="subscription-status">
                                                <button type="button" 
                                                    class="btn {{ $tenant->subscription_plan === 'premium' ? 'btn-warning' : 'btn-outline-secondary' }}"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#subscriptionModal-{{ $tenant->id }}">
                                                    @if($tenant->subscription_plan === 'premium')
                                                        <i class="fas fa-crown text-warning me-1"></i>
                                                    @else
                                                        <i class="fas fa-cube me-1"></i>
                                                    @endif
                                                    {{ ucfirst($tenant->subscription_plan) }}
                                                </button>

                                            <!-- Subscription Plan Modal -->
                                            <div class="modal fade" id="subscriptionModal-{{ $tenant->id }}" tabindex="-1" aria-labelledby="subscriptionModalLabel-{{ $tenant->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="subscriptionModalLabel-{{ $tenant->id }}">Manage Subscription: {{ $tenant->tenant_name }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="current-plan mb-4">
                                                                <h6 class="text-muted mb-3">Current Plan</h6>
                                                                <div class="card bg-light">
                                                                    <div class="card-body d-flex align-items-center">
                                                                        <div class="me-3">
                                                                            @if($tenant->subscription_plan === 'premium')
                                                                                <div class="display-6 text-warning">
                                                                                    <i class="fas fa-crown"></i>
                                                                                </div>
                                                                            @else
                                                                                <div class="display-6 text-info">
                                                                                    <i class="fas fa-cube"></i>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                        <div>
                                                                            <h5 class="card-title mb-1">{{ ucfirst($tenant->subscription_plan) }}</h5>
                                                                            <p class="card-text">
                                                                                @if($tenant->subscription_plan === 'premium')
                                                                                    Premium features with priority support
                                                                                    @if(isset($tenant->data['subscription_ends_at']))
                                                                                        <br><small class="text-muted">Expires: {{ \Carbon\Carbon::parse($tenant->data['subscription_ends_at'])->format('M d, Y') }}</small>
                                                                                    @endif
                                                                                @else
                                                                                    Basic features with standard support
                                                                                @endif
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="change-plan">
                                                                <h6 class="text-muted mb-3">Change Plan</h6>
                                                                <div class="row">
                                                                    @if($tenant->subscription_plan === 'basic')
                                                                    <div class="col-12">
                                                                        <div class="card border-warning">
                                                                            <div class="card-body d-flex align-items-center">
                                                                                <div class="me-3">
                                                                                    <div class="display-6 text-warning">
                                                                                        <i class="fas fa-crown"></i>
                                                                                    </div>
                                                                                </div>
                                                                                <div>
                                                                                    <h5 class="card-title mb-1">Premium</h5>
                                                                                    <p class="card-text">₱5,000/month with advanced features</p>
                                                                                </div>
                                                                            </div>
                                                                            <div class="card-footer bg-transparent border-0 text-end">
                                                        <button type="button" 
                                                                                        class="btn btn-warning subscription-change-btn"
                                                                data-tenant-id="{{ $tenant->id }}"
                                                                data-tenant-name="{{ $tenant->tenant_name }}"
                                                                data-current-plan="{{ $tenant->subscription_plan }}"
                                                                                        data-target-plan="premium"
                                                                                        data-bs-dismiss="modal">
                                                                                    <i class="fas fa-arrow-up me-1"></i> Upgrade to Premium
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                            @else
                                                                    <div class="col-12">
                                                                        <div class="card border-info">
                                                                            <div class="card-body d-flex align-items-center">
                                                                                <div class="me-3">
                                                                                    <div class="display-6 text-info">
                                                                                        <i class="fas fa-cube"></i>
                                                                                    </div>
                                                                                </div>
                                                                                <div>
                                                                                    <h5 class="card-title mb-1">Basic</h5>
                                                                                    <p class="card-text">Free tier with limited features</p>
                                                                                </div>
                                                                            </div>
                                                                            <div class="card-footer bg-transparent border-0 text-end">
                                                                                <button type="button" 
                                                                                        class="btn btn-info text-white subscription-change-btn"
                                                                                        data-tenant-id="{{ $tenant->id }}"
                                                                                        data-tenant-name="{{ $tenant->tenant_name }}"
                                                                                        data-current-plan="{{ $tenant->subscription_plan }}"
                                                                                        data-target-plan="basic"
                                                                                        data-bs-dismiss="modal">
                                                                                    <i class="fas fa-arrow-down me-1"></i> Downgrade to Basic
                                                        </button>
                                                            </div>
                                                                        </div>
                                                                    </div>
                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $tenant->status === 'approved' ? 'success' : ($tenant->status === 'pending' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($tenant->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('superadmin.tenants.show', $tenant->id) }}" 
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No tenants found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
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
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tenant Name</th>
                                    <th>Email</th>
                                    <th>Subscription</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rejectedTenants as $tenant)
                                <tr>
                                    <td>{{ $tenant->tenant_name }}</td>
                                    <td>{{ $tenant->tenant_email }}</td>
                                    <td>
                                        <div class="subscription-status">
                                                <button type="button" 
                                                    class="btn {{ $tenant->subscription_plan === 'premium' ? 'btn-warning' : 'btn-outline-secondary' }}"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#subscriptionModal-{{ $tenant->id }}">
                                                    @if($tenant->subscription_plan === 'premium')
                                                        <i class="fas fa-crown text-warning me-1"></i>
                                                    @else
                                                        <i class="fas fa-cube me-1"></i>
                                                    @endif
                                                    {{ ucfirst($tenant->subscription_plan) }}
                                                </button>

                                            <!-- Subscription Plan Modal -->
                                            <div class="modal fade" id="subscriptionModal-{{ $tenant->id }}" tabindex="-1" aria-labelledby="subscriptionModalLabel-{{ $tenant->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="subscriptionModalLabel-{{ $tenant->id }}">Manage Subscription: {{ $tenant->tenant_name }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="current-plan mb-4">
                                                                <h6 class="text-muted mb-3">Current Plan</h6>
                                                                <div class="card bg-light">
                                                                    <div class="card-body d-flex align-items-center">
                                                                        <div class="me-3">
                                                                            @if($tenant->subscription_plan === 'premium')
                                                                                <div class="display-6 text-warning">
                                                                                    <i class="fas fa-crown"></i>
                                                                                </div>
                                                                            @else
                                                                                <div class="display-6 text-info">
                                                                                    <i class="fas fa-cube"></i>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                        <div>
                                                                            <h5 class="card-title mb-1">{{ ucfirst($tenant->subscription_plan) }}</h5>
                                                                            <p class="card-text">
                                                                                @if($tenant->subscription_plan === 'premium')
                                                                                    Premium features with priority support
                                                                                    @if(isset($tenant->data['subscription_ends_at']))
                                                                                        <br><small class="text-muted">Expires: {{ \Carbon\Carbon::parse($tenant->data['subscription_ends_at'])->format('M d, Y') }}</small>
                                                                                    @endif
                                                                                @else
                                                                                    Basic features with standard support
                                                                                @endif
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="change-plan">
                                                                <h6 class="text-muted mb-3">Change Plan</h6>
                                                                <div class="row">
                                                                    @if($tenant->subscription_plan === 'basic')
                                                                    <div class="col-12">
                                                                        <div class="card border-warning">
                                                                            <div class="card-body d-flex align-items-center">
                                                                                <div class="me-3">
                                                                                    <div class="display-6 text-warning">
                                                                                        <i class="fas fa-crown"></i>
                                                                                    </div>
                                                                                </div>
                                                                                <div>
                                                                                    <h5 class="card-title mb-1">Premium</h5>
                                                                                    <p class="card-text">₱5,000/month with advanced features</p>
                                                                                </div>
                                                                            </div>
                                                                            <div class="card-footer bg-transparent border-0 text-end">
                                                        <button type="button" 
                                                                                        class="btn btn-warning subscription-change-btn"
                                                                data-tenant-id="{{ $tenant->id }}"
                                                                data-tenant-name="{{ $tenant->tenant_name }}"
                                                                data-current-plan="{{ $tenant->subscription_plan }}"
                                                                                        data-target-plan="premium"
                                                                                        data-bs-dismiss="modal">
                                                                                    <i class="fas fa-arrow-up me-1"></i> Upgrade to Premium
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                            @else
                                                                    <div class="col-12">
                                                                        <div class="card border-info">
                                                                            <div class="card-body d-flex align-items-center">
                                                                                <div class="me-3">
                                                                                    <div class="display-6 text-info">
                                                                                        <i class="fas fa-cube"></i>
                                                                                    </div>
                                                                                </div>
                                                                                <div>
                                                                                    <h5 class="card-title mb-1">Basic</h5>
                                                                                    <p class="card-text">Free tier with limited features</p>
                                                                                </div>
                                                                            </div>
                                                                            <div class="card-footer bg-transparent border-0 text-end">
                                                                                <button type="button" 
                                                                                        class="btn btn-info text-white subscription-change-btn"
                                                                                        data-tenant-id="{{ $tenant->id }}"
                                                                                        data-tenant-name="{{ $tenant->tenant_name }}"
                                                                                        data-current-plan="{{ $tenant->subscription_plan }}"
                                                                                        data-target-plan="basic"
                                                                                        data-bs-dismiss="modal">
                                                                                    <i class="fas fa-arrow-down me-1"></i> Downgrade to Basic
                                                        </button>
                                                            </div>
                                                                        </div>
                                                                    </div>
                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $tenant->status === 'approved' ? 'success' : ($tenant->status === 'pending' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($tenant->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('superadmin.tenants.show', $tenant->id) }}" 
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No tenants found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
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

<!-- Hidden Forms Container -->
<div id="subscriptionFormsContainer" style="display: none;">
    @foreach($tenants as $tenant)
    <form id="subscription-form-{{ $tenant->id }}" 
          action="{{ route('superadmin.tenants.update-subscription', $tenant->id) }}" 
          method="POST">
        @csrf
        <input type="hidden" name="subscription_plan" value="{{ $tenant->subscription_plan === 'basic' ? 'premium' : 'basic' }}">
    </form>
    @endforeach
</div>

@endsection

@push('scripts')
<!-- Add SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Initialize everything when the page loads
document.addEventListener('DOMContentLoaded', function() {
    // Initialize subscription change buttons
    document.querySelectorAll('.subscription-change-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get data attributes
            const tenantId = this.dataset.tenantId;
            const tenantName = this.dataset.tenantName;
            const targetPlan = this.dataset.targetPlan;
            
            // Handle subscription change
            handleSubscriptionChange(tenantId, tenantName, targetPlan);
        });
    });

    // Initialize tabs
    initializeTabs();
});

function handleSubscriptionChange(tenantId, tenantName, targetPlan) {
    const planDetails = {
        'basic': { 
            title: 'Basic Plan',
            icon: '<i class="fas fa-cube text-info"></i>',
            description: 'Free tier with limited features',
            color: '#17a2b8',
            buttonClass: 'btn-info'
        },
        'premium': { 
            title: 'Premium Plan',
            icon: '<i class="fas fa-crown text-warning"></i>',
            description: '₱5,000/month with advanced features',
            color: '#ffc107',
            buttonClass: 'btn-warning'
        }
    };
    
    Swal.fire({
        title: `Change Subscription Plan`,
        html: `
            <div class="text-center">
                <div class="plan-card" style="background-color: ${planDetails[targetPlan].color}15; border: 2px solid ${planDetails[targetPlan].color}">
                    <div class="plan-card-icon" style="color: ${planDetails[targetPlan].color}">
                    ${planDetails[targetPlan].icon}
                    </div>
                    <h4>${planDetails[targetPlan].title}</h4>
                    <p>${planDetails[targetPlan].description}</p>
                </div>
                <p>You are about to change the subscription for:</p>
                <h5 class="mb-3">${tenantName}</h5>
                            <div class="alert alert-light border">
                    <p class="mb-0">This change will be applied immediately and may affect available features for this tenant.</p>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: `Yes, change to ${planDetails[targetPlan].title}`,
        cancelButtonText: 'Cancel',
        confirmButtonColor: planDetails[targetPlan].color,
        cancelButtonColor: '#6c757d',
        reverseButtons: true,
        customClass: {
            confirmButton: planDetails[targetPlan].buttonClass,
            title: 'text-dark',
            popup: 'subscription-modal'
        },
        backdrop: 'rgba(0,0,0,0.4)',
        focusConfirm: false
    }).then((result) => {
        if (result.isConfirmed) {
            submitPlanChange(tenantId);
        }
    });
}

function submitPlanChange(tenantId) {
    try {
        const form = document.getElementById(`subscription-form-${tenantId}`);
        if (!form) {
            console.error('Form not found for tenant ID:', tenantId);
            Swal.fire({
                title: 'Error',
                text: 'Could not process subscription change. Please try again.',
                icon: 'error',
                confirmButtonColor: '#dc3545'
            });
            return;
        }

        // Show loading state
        Swal.fire({
            title: 'Processing Subscription Change',
            html: '<div class="d-flex flex-column align-items-center"><div class="spinner-border text-primary mb-3"></div><p>Please wait while we update the subscription plan...</p></div>',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Submit the form
        form.submit();
        
    } catch (error) {
        console.error('Error submitting form:', error);
        Swal.fire({
            title: 'Error',
            text: 'An error occurred while processing your request. Please try again.',
            icon: 'error',
            confirmButtonColor: '#dc3545'
        });
    }
}

function initializeTabs() {
    const tabFromSession = "{{ session('tab') }}";
    if (tabFromSession) {
        const tabElement = document.getElementById(`${tabFromSession}-tab`);
        if (tabElement) {
            const tab = new bootstrap.Tab(tabElement);
            tab.show();
        }
    } else if(window.location.hash) {
        const hash = window.location.hash.substring(1);
        const tabElement = document.getElementById(`${hash}-tab`);
        if(tabElement) {
            const tab = new bootstrap.Tab(tabElement);
            tab.show();
        }
    }
    
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
}

function initializeDropdowns() {
    const dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
    dropdownElementList.forEach(function(dropdownToggleEl) {
        new bootstrap.Dropdown(dropdownToggleEl);
    });
}
</script>
@endpush 