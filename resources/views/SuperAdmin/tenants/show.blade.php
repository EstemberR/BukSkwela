@extends('superadmin.layouts.app')

@section('title', 'Tenant Details')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">Tenant Details</h1>
        <a href="{{ route('superadmin.tenants.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Tenants
        </a>
    </div>
    
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tenant Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 30%">ID</th>
                            <td>{{ $tenant->id }}</td>
                        </tr>
                        <tr>
                            <th>Name</th>
                            <td>{{ $tenant->tenant_name }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $tenant->tenant_email }}</td>
                        </tr>
                        <tr>
                            <th>Admin Name</th>
                            <td>{{ $tenant->data['admin_name'] ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($tenant->status == 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($tenant->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($tenant->status == 'disabled')
                                    <span class="badge bg-danger">Disabled</span>
                                @elseif($tenant->status == 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @elseif($tenant->status == 'denied')
                                    <span class="badge bg-danger">Denied</span>
                                @else
                                    <span class="badge bg-secondary">{{ $tenant->status }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Subscription Plan</th>
                            <td>
                                <span class="badge bg-info">{{ ucfirst($tenant->subscription_plan) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $tenant->created_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At</th>
                            <td>{{ $tenant->updated_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tenant Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($tenant->status == 'pending')
                            <form action="{{ route('superadmin.tenants.approve', $tenant->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success w-100 mb-2" onclick="return confirm('Are you sure you want to approve this tenant?')">
                                    <i class="bi bi-check-lg"></i> Approve Tenant
                                </button>
                            </form>
                            
                            <form action="{{ route('superadmin.tenants.reject', $tenant->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger w-100 mb-2" onclick="return confirm('Are you sure you want to reject this tenant?')">
                                    <i class="bi bi-x-lg"></i> Reject Tenant
                                </button>
                            </form>
                        @endif
                        
                        @if($tenant->status == 'approved')
                            <form action="{{ route('superadmin.tenants.disable', $tenant->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-warning w-100 mb-2" onclick="return confirm('Are you sure you want to disable this tenant?')">
                                    <i class="bi bi-pause-fill"></i> Disable Tenant
                                </button>
                            </form>
                            
                            <form action="{{ route('superadmin.tenants.deny', $tenant->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger w-100 mb-2" onclick="return confirm('Are you sure you want to deny this tenant?')">
                                    <i class="bi bi-slash-circle"></i> Deny Tenant
                                </button>
                            </form>
                            
                            <form action="{{ route('superadmin.tenants.update-subscription', $tenant->id) }}" method="POST" class="mt-3">
                                @csrf
                                <div class="form-group mb-3">
                                    <label for="subscription_plan" class="form-label"><strong>Change Subscription Plan</strong></label>
                                    <select name="subscription_plan" id="subscription_plan" class="form-select">
                                        <option value="basic" {{ $tenant->subscription_plan == 'basic' ? 'selected' : '' }}>Basic</option>
                                        <option value="premium" {{ $tenant->subscription_plan == 'premium' ? 'selected' : '' }}>Premium</option>
                                        <option value="enterprise" {{ $tenant->subscription_plan == 'enterprise' ? 'selected' : '' }}>Enterprise</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary w-100" onclick="return confirm('Are you sure you want to update the subscription plan?')">
                                    <i class="bi bi-arrow-up-circle"></i> Update Subscription Plan
                                </button>
                            </form>
                        @endif
                        
                        @if(in_array($tenant->status, ['disabled', 'rejected', 'denied']))
                            <form action="{{ route('superadmin.tenants.enable', $tenant->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success w-100 mb-2" onclick="return confirm('Are you sure you want to enable this tenant?')">
                                    <i class="bi bi-play-fill"></i> Enable Tenant
                                </button>
                            </form>
                        @endif
                        
                        @if($tenant->subscription_plan != 'basic')
                            <form action="{{ route('superadmin.tenants.downgrade', $tenant->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-secondary w-100 mb-2" onclick="return confirm('Are you sure you want to downgrade this tenant to basic plan?')">
                                    <i class="bi bi-arrow-down-circle"></i> Downgrade to Basic Plan
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Payment History</h5>
                </div>
                <div class="card-body">
                    @if(isset($tenant->data['payment_history']) && count($tenant->data['payment_history']) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Plan</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tenant->data['payment_history'] as $payment)
                                        <tr>
                                            <td>{{ isset($payment['date']) ? \Carbon\Carbon::parse($payment['date'])->format('M d, Y') : 'N/A' }}</td>
                                            <td>{{ isset($payment['amount']) ? '$' . number_format($payment['amount'], 2) : 'N/A' }}</td>
                                            <td>{{ isset($payment['plan']) ? ucfirst($payment['plan']) : 'N/A' }}</td>
                                            <td>
                                                @if(isset($payment['status']))
                                                    @if($payment['status'] == 'paid')
                                                        <span class="badge bg-success">Paid</span>
                                                    @elseif($payment['status'] == 'pending')
                                                        <span class="badge bg-warning">Pending</span>
                                                    @elseif($payment['status'] == 'failed')
                                                        <span class="badge bg-danger">Failed</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ $payment['status'] }}</span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-secondary">Unknown</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">No payment history available for this tenant.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 