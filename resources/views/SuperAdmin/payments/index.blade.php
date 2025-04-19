@extends('SuperAdmin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Payment Management</h2>
        <div class="d-flex gap-2">
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-file-export me-1"></i> Export
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                    <li><a class="dropdown-item" href="{{ route('superadmin.payments.export') }}?format=csv"><i class="fas fa-file-csv me-2"></i> CSV</a></li>
                    <li><a class="dropdown-item" href="{{ route('superadmin.payments.export') }}?format=excel"><i class="fas fa-file-excel me-2"></i> Excel</a></li>
                    <li><a class="dropdown-item" href="{{ route('superadmin.payments.export') }}?format=pdf"><i class="fas fa-file-pdf me-2"></i> PDF</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-subtitle mb-2 text-muted">Total Revenue</h6>
                            <h4 class="card-title text-success mb-0">₱{{ number_format($totalRevenue, 2) }}</h4>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-money-bill-wave text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-subtitle mb-2 text-muted">Premium Subscribers</h6>
                            <h4 class="card-title text-primary mb-0">{{ $paidSubscriptions }}</h4>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-crown text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-subtitle mb-2 text-muted">Recent Upgrades</h6>
                            <h4 class="card-title text-warning mb-0">{{ count($premiumUpgrades) }}</h4>
                        </div>
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-arrow-up text-warning fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Premium Upgrades Section -->
    <div class="card mb-4">
        <div class="card-header bg-warning bg-opacity-10">
            <h5 class="card-title mb-0 text-warning"><i class="fas fa-crown me-2"></i>Premium Subscription Upgrades</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info mb-3">
                <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Auto-Approval System</h6>
                <p class="mb-0">Premium upgrades are now automatically processed when tenants submit their payment details. No manual approval is required.</p>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Upgrade ID</th>
                            <th>Tenant</th>
                            <th>Current Plan</th>
                            <th>Payment Method</th>
                            <th>Reference</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($premiumUpgrades ?? [] as $upgrade)
                            <tr>
                                <td>#{{ $upgrade->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar-container me-2" style="width: 2rem; height: 2rem;">
                                            <div class="d-flex align-items-center justify-content-center rounded-3 bg-primary text-white" style="width: 100%; height: 100%;">
                                                <span style="font-size: 0.8rem; font-weight: bold;">{{ substr($upgrade->tenant->name, 0, 1) }}</span>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $upgrade->tenant->name }}</div>
                                            <div class="text-muted small">{{ $upgrade->tenant->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $upgrade->from_plan }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $upgrade->payment_method)) }}</td>
                                <td><code>{{ $upgrade->receipt_number }}</code></td>
                                <td><span class="fw-bold">₱{{ number_format($upgrade->amount, 2) }}</span></td>
                                <td>
                                    <span class="badge bg-{{ $upgrade->status === 'approved' ? 'success' : ($upgrade->status === 'pending' ? 'warning' : 'danger') }} px-3 py-2">
                                        {{ ucfirst($upgrade->status) }}
                                    </span>
                                </td>
                                <td>{{ $upgrade->created_at->format('M d, Y') }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#upgradeDetailsModal{{ $upgrade->id }}">
                                        <i class="fas fa-eye"></i> View Details
                                    </button>
                                    
                                    <!-- Upgrade Details Modal -->
                                    <div class="modal fade" id="upgradeDetailsModal{{ $upgrade->id }}" tabindex="-1" aria-labelledby="upgradeDetailsModalLabel{{ $upgrade->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="upgradeDetailsModalLabel{{ $upgrade->id }}">
                                                        <i class="fas fa-crown text-warning me-2"></i>Premium Upgrade #{{ $upgrade->id }}
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <div class="d-flex justify-content-center mb-3">
                                                            <div class="user-avatar-container" style="width: 5rem; height: 5rem;">
                                                                <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary text-white" style="width: 100%; height: 100%;">
                                                                    <span style="font-size: 2rem; font-weight: bold;">{{ substr($upgrade->tenant->name, 0, 1) }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <h4 class="text-center">{{ $upgrade->tenant->name }}</h4>
                                                        <p class="text-muted text-center">{{ $upgrade->tenant->email }}</p>
                                                    </div>
                                                    
                                                    <div class="card mb-3">
                                                        <div class="card-header bg-light">
                                                            <h6 class="mb-0">Upgrade Details</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="row mb-2">
                                                                <div class="col-5 fw-bold">Previous Plan:</div>
                                                                <div class="col-7">{{ $upgrade->from_plan }}</div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-5 fw-bold">New Plan:</div>
                                                                <div class="col-7">{{ $upgrade->to_plan }}</div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-5 fw-bold">Amount:</div>
                                                                <div class="col-7">₱{{ number_format($upgrade->amount, 2) }}</div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-5 fw-bold">Payment Method:</div>
                                                                <div class="col-7">{{ ucfirst(str_replace('_', ' ', $upgrade->payment_method)) }}</div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-5 fw-bold">Reference Number:</div>
                                                                <div class="col-7"><code>{{ $upgrade->receipt_number }}</code></div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-5 fw-bold">Upgrade Date:</div>
                                                                <div class="col-7">{{ $upgrade->created_at->format('M d, Y h:i A') }}</div>
                                                            </div>
                                                            <div class="row mb-2">
                                                                <div class="col-5 fw-bold">Processed At:</div>
                                                                <div class="col-7">{{ $upgrade->processed_at ? $upgrade->processed_at->format('M d, Y h:i A') : 'Not processed yet' }}</div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-5 fw-bold">Status:</div>
                                                                <div class="col-7">
                                                                    <span class="badge bg-{{ $upgrade->status === 'approved' ? 'success' : ($upgrade->status === 'pending' ? 'warning' : 'danger') }}">
                                                                        {{ ucfirst($upgrade->status) }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="alert alert-success mb-0">
                                                        <h6><i class="fas fa-info-circle me-2"></i>Auto-Approval</h6>
                                                        <p class="mb-0">Premium upgrades are automatically processed when tenants submit their payment details. The tenant has been upgraded to premium status.</p>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No premium upgrades found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 