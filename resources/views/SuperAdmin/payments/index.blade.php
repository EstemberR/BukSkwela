@extends('SuperAdmin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Payment Management</h2>
        <div class="d-flex gap-2">
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-file-pdf me-1"></i> Export to PDF
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="exportDropdown">
                    <li><h6 class="dropdown-header">Export Options</h6></li>
                    <li><a class="dropdown-item d-flex align-items-center" href="{{ url('superadmin/payments/export') }}?format=pdf">
                        <i class="fas fa-file-pdf me-2 text-danger"></i> PDF Format
                        <span class="badge bg-light text-dark ms-auto">Document</span>
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item d-flex align-items-center" href="#" data-bs-toggle="modal" data-bs-target="#exportOptionsModal">
                        <i class="fas fa-sliders-h me-2 text-secondary"></i> Advanced Export
                    </a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Export Options Modal -->
    <div class="modal fade" id="exportOptionsModal" tabindex="-1" aria-labelledby="exportOptionsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="exportOptionsModalLabel">
                        <i class="fas fa-file-pdf me-2"></i>PDF Export Options
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ url('superadmin/payments/export') }}" method="GET">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="exportFormat" class="form-label">Export Format</label>
                            <select class="form-select" id="exportFormat" name="format">
                                <option value="pdf" selected>PDF Document (.pdf)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Data Range</label>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="date" class="form-control" id="date_from" name="date_from">
                                        <label for="date_from">From Date</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="date" class="form-control" id="date_to" name="date_to">
                                        <label for="date_to">To Date</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Payment Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Statuses</option>
                                <option value="completed">Completed</option>
                                <option value="pending">Pending</option>
                                <option value="failed">Failed</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="plan" class="form-label">Subscription Plan</label>
                            <select class="form-select" id="plan" name="plan">
                                <option value="">All Plans</option>
                                @foreach($plans ?? [] as $plan)
                                    <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-file-pdf me-1"></i> Generate PDF
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Debug Information -->
 

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card dashboard-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="card-subtitle mb-2 text-muted">Total Sales</h6>
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
                            <h6 class="card-subtitle mb-2 text-muted">Total Subscribers</h6>
                            <h4 class="card-title text-primary mb-0">{{ $totalSubscribers }}</h4>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-users text-primary fs-4"></i>
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