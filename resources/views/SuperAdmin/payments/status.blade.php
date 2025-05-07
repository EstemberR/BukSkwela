@extends('superadmin.layouts.app')

@section('title', 'Payment Status')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">Payment Status</h1>
        <div>
            <a href="{{ route('superadmin.payments.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Payments
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Paid Tenants</h6>
                            <h2 class="mb-0">{{ $paidTenants }}</h2>
                        </div>
                        <div>
                            <i class="bi bi-check-circle fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Pending Payments</h6>
                            <h2 class="mb-0">{{ $pendingTenants }}</h2>
                        </div>
                        <div>
                            <i class="bi bi-clock fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card bg-danger text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase">Overdue Payments</h6>
                            <h2 class="mb-0">{{ $overdueTenants }}</h2>
                        </div>
                        <div>
                            <i class="bi bi-exclamation-circle fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Recent Payments</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Tenant</th>
                                    <th>Email</th>
                                    <th>Plan</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPayments as $tenant)
                                    <tr>
                                        <td>{{ $tenant->data['name'] ?? 'N/A' }}</td>
                                        <td>{{ $tenant->data['email'] ?? 'N/A' }}</td>
                                        <td>
                                            @if(isset($tenant->data['subscription_plan']))
                                                <span class="badge bg-info">{{ ucfirst($tenant->data['subscription_plan']) }}</span>
                                            @else
                                                <span class="badge bg-secondary">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(isset($tenant->data['last_payment_amount']))
                                                ${{ number_format($tenant->data['last_payment_amount'], 2) }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            @if(isset($tenant->data['last_payment_date']))
                                                {{ \Carbon\Carbon::parse($tenant->data['last_payment_date'])->format('M d, Y') }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            @if(isset($tenant->data['payment_status']))
                                                @if($tenant->data['payment_status'] == 'paid')
                                                    <span class="badge bg-success">Paid</span>
                                                @elseif($tenant->data['payment_status'] == 'pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                @elseif($tenant->data['payment_status'] == 'overdue')
                                                    <span class="badge bg-danger">Overdue</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $tenant->data['payment_status'] }}</span>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary">Unknown</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No recent payments found.</td>
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
@endsection 