<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Tenant Name</th>
                <th>Email</th>
                <th>Subscription Plan</th>
                <th>Status</th>
                <th>Payment Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tenants as $tenant)
            <tr>
                <td>{{ $tenant->tenant_name }}</td>
                <td>{{ $tenant->tenant_email }}</td>
                <td>
                    @php
                        $plan = $tenant->subscription_plan;
                        $planClass = 'info';
                        $planIcon = 'fa-info-circle';
                        
                        if ($plan == 'premium') {
                            $planClass = 'warning';
                            $planIcon = 'fa-crown';
                        } elseif ($plan == 'enterprise') {
                            $planClass = 'dark';
                            $planIcon = 'fa-building';
                        } else {
                            $planIcon = 'fa-cube';
                        }
                    @endphp
                    <div class="d-flex align-items-center">
                        <span class="badge bg-{{ $planClass }} py-2 px-3">
                            <i class="fas {{ $planIcon }} me-1"></i> {{ ucfirst($plan) }}
                        </span>
                        
                        <button type="button" class="btn btn-sm btn-outline-{{ $planClass }} rounded-pill ms-2" 
                                data-bs-toggle="modal" data-bs-target="#subscriptionModal{{ $tenant->id }}">
                            <i class="fas fa-sync-alt me-1"></i> Change
                        </button>
                        
                        <!-- Subscription Change Modal -->
                        <div class="modal fade" id="subscriptionModal{{ $tenant->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-{{ $planClass }} text-white">
                                        <h5 class="modal-title">
                                            <i class="fas {{ $planIcon }} me-2"></i> Change Subscription Plan
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="text-center mb-4">
                                            <h5 class="fw-bold mb-3">Current Plan: <span class="badge bg-{{ $planClass }}">{{ ucfirst($plan) }}</span></h5>
                                            <p class="text-muted">Select a new subscription plan for <strong>{{ $tenant->tenant_name }}</strong></p>
                                            
                                            @if($plan == 'enterprise')
                                            <div class="alert alert-warning mt-3">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                <strong>Note:</strong> Enterprise plan is being phased out. Please select either Premium or Basic plan.
                                            </div>
                                            @endif
                                        </div>
                                        
                                        <div class="plan-options">
                                            <!-- Basic Plan Card -->
                                            <div class="card mb-3 {{ $plan == 'basic' ? 'border-primary' : '' }}">
                                                <div class="card-body d-flex align-items-center">
                                                    <div class="plan-icon bg-light rounded-circle p-3 me-3">
                                                        <i class="fas fa-cube text-info fs-3"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h5 class="card-title mb-1">Basic Plan</h5>
                                                        <p class="card-text text-muted small mb-0">Free tier with limited features</p>
                                                    </div>
                                                    <div>
                                                        @if($plan == 'basic')
                                                            <span class="badge bg-primary">Current</span>
                                                        @else
                                                            <form action="{{ route('superadmin.tenants.update-subscription', $tenant->id) }}" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="subscription_plan" value="basic">
                                                                <button type="button" class="btn btn-sm btn-outline-info" onclick="confirmPlanChange(this, 'basic', '{{ $tenant->tenant_name }}')">
                                                                    Select
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Premium Plan Card -->
                                            <div class="card {{ $plan == 'premium' ? 'border-warning' : '' }}">
                                                <div class="card-body d-flex align-items-center">
                                                    <div class="plan-icon bg-light rounded-circle p-3 me-3">
                                                        <i class="fas fa-crown text-warning fs-3"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h5 class="card-title mb-1">Premium Plan</h5>
                                                        <p class="card-text text-muted small mb-0">$29.99/month with advanced features</p>
                                                    </div>
                                                    <div>
                                                        @if($plan == 'premium')
                                                            <span class="badge bg-warning text-dark">Current</span>
                                                        @else
                                                            <form action="{{ route('superadmin.tenants.update-subscription', $tenant->id) }}" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="subscription_plan" value="premium">
                                                                <button type="button" class="btn btn-sm btn-outline-warning" onclick="confirmPlanChange(this, 'premium', '{{ $tenant->tenant_name }}')">
                                                                    Select
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    @php
                        $status = $tenant->status;
                        $statusClass = 'secondary';
                        
                        if ($status == 'approved') {
                            $statusClass = 'success';
                        } elseif ($status == 'pending') {
                            $statusClass = 'warning';
                        } elseif (in_array($status, ['rejected', 'disabled', 'denied'])) {
                            $statusClass = 'danger';
                        }
                    @endphp
                    <span class="badge bg-{{ $statusClass }}">
                        {{ ucfirst($status) }}
                    </span>
                </td>
                <td>
                    @php
                        $paymentStatus = $tenant->data['payment_status'] ?? 'unpaid';
                        
                        // Always ensure premium subscribers show as paid
                        if ($tenant->subscription_plan === 'premium') {
                            $paymentStatus = 'paid';
                        }
                        
                        $paymentClass = 'danger';
                        $paymentIcon = 'fa-times-circle';
                        
                        if ($paymentStatus == 'paid') {
                            $paymentClass = 'success';
                            $paymentIcon = 'fa-check-circle';
                        } elseif ($paymentStatus == 'pending') {
                            $paymentClass = 'warning';
                            $paymentIcon = 'fa-clock';
                        } elseif ($paymentStatus == 'downgraded') {
                            $paymentClass = 'secondary';
                            $paymentIcon = 'fa-arrow-circle-down';
                        }
                    @endphp
                    <span class="badge bg-{{ $paymentClass }}">
                        <i class="fas {{ $paymentIcon }} me-1"></i> {{ ucfirst($paymentStatus) }}
                    </span>
                </td>
                <td>
                    <div class="btn-group">
                        <a href="{{ route('superadmin.tenants.show', $tenant->id) }}" class="btn btn-sm btn-info" title="View Details">
                            <i class="fas fa-eye"></i>
                        </a>
                        
                        @if($tenant->status == 'pending')
                        <form action="{{ route('superadmin.tenants.approve', $tenant->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success" title="Approve">
                                <i class="fas fa-check"></i>
                            </button>
                        </form>
                        <form action="{{ route('superadmin.tenants.reject', $tenant->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger" title="Reject">
                                <i class="fas fa-times"></i>
                            </button>
                        </form>
                        @endif
                        
                        @if($tenant->status == 'approved')
                        <form action="{{ route('superadmin.tenants.disable', $tenant->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-warning" title="Disable">
                                <i class="fas fa-ban"></i>
                            </button>
                        </form>
                        @endif
                        
                        @if(in_array($tenant->status, ['disabled', 'rejected', 'denied']))
                        <form action="{{ route('superadmin.tenants.enable', $tenant->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success" title="Enable">
                                <i class="fas fa-check-circle"></i>
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