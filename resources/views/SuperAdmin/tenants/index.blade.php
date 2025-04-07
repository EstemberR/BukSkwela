@extends('superadmin.layouts.app')

@section('title', 'Tenants Management')

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
                            <option value="enterprise">Enterprise</option>
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