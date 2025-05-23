@extends('SuperAdmin.layouts.app')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">Dashboard Overview</h2>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card dashboard-card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="card-title">Total Tenants</h5>
                            <h2 class="card-text">{{ $totalTenants ?? 0 }}</h2>
                            <p class="mb-0">Active: {{ $activeTenants ?? 0 }}</p>
                        </div>
                        <div class="text-white">
                            <i class="fas fa-users fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card dashboard-card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="card-title">Active Subscriptions</h5>
                            <h2 class="card-text">{{ $activeSubscriptions ?? 0 }}</h2>
                            <p class="mb-0">Pending: {{ $pendingSubscriptions ?? 0 }}</p>
                        </div>
                        <div class="text-white">
                            <i class="fas fa-check-circle fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card dashboard-card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="card-title">Monthly Revenue</h5>
                            <h2 class="card-text">₱{{ number_format($monthlyRevenue ?? 0, 2) }}</h2>
                            <p class="mb-0">This Month</p>
                        </div>
                        <div class="text-white">
                            <i class="fas fa-money-bill-wave fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Tenants Table -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Recent Tenant Accounts</h5>
            <a href="{{ route('superadmin.tenants.index') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-eye"></i> View All
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Tenant Name</th>
                            <th>Email</th>
                            <th>Subscription Plan</th>
                            <th>Status</th>
                            <th>Last Payment</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTenants ?? [] as $tenant)
                        <tr>
                            <td>{{ $tenant->name }}</td>
                            <td>{{ $tenant->email }}</td>
                            <td>{{ $tenant->subscription_plan }}</td>
                            <td>
                                <span class="badge bg-{{ $tenant->status === 'active' ? 'success' : 'warning' }}">
                                    {{ ucfirst($tenant->status) }}
                                </span>
                            </td>
                            <td>{{ $tenant->last_payment_date }}</td>
                            <td>
                                <a href="{{ route('superadmin.tenants.show', $tenant->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
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

    <!-- Recent Payments -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Recent Payments</h5>
            <a href="{{ route('superadmin.payments.index') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-eye"></i> View All
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Tenant</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentPayments ?? [] as $payment)
                        <tr>
                            <td>{{ $payment->tenant_name }}</td>
                            <td>₱{{ number_format($payment->amount, 2) }}</td>
                            <td>{{ $payment->date }}</td>
                            <td>
                                <span class="badge bg-{{ $payment->status === 'completed' ? 'success' : 'warning' }}">
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('superadmin.payments.show', $payment->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No recent payments</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection