@extends('superadmin.layouts.app')

@section('title', 'Payment History')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">Payment History</h1>
        <div>
            <a href="{{ route('superadmin.payments.show', $tenant->id) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Details
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tenant Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-sm me-2">
                            @if(isset($tenant->data['avatar']))
                                <img src="{{ $tenant->data['avatar'] }}" alt="Avatar" class="rounded-circle" width="48">
                            @else
                                <div class="avatar-initial rounded-circle bg-primary" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem;">
                                    {{ substr($tenant->data['name'] ?? 'A', 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $tenant->data['name'] ?? 'N/A' }}</h6>
                            <small class="text-muted">{{ $tenant->data['email'] ?? 'N/A' }}</small>
                        </div>
                    </div>
                    <hr>
                    <div class="mb-2">
                        <small class="text-muted">Current Plan</small>
                        <div>
                            @if(isset($tenant->data['subscription_plan']))
                                <span class="badge bg-info">{{ ucfirst($tenant->data['subscription_plan']) }}</span>
                            @else
                                <span class="badge bg-secondary">N/A</span>
                            @endif
                        </div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Payment Status</small>
                        <div>
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
                        </div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Billing Cycle</small>
                        <div>{{ ucfirst($tenant->data['billing_cycle'] ?? 'N/A') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Payment History</h5>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-funnel"></i> Filter
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                                <li><a class="dropdown-item" href="{{ route('superadmin.payments.history', $tenant->id) }}">All</a></li>
                                <li><a class="dropdown-item" href="{{ route('superadmin.payments.history', ['tenant' => $tenant->id, 'status' => 'paid']) }}">Paid</a></li>
                                <li><a class="dropdown-item" href="{{ route('superadmin.payments.history', ['tenant' => $tenant->id, 'status' => 'pending']) }}">Pending</a></li>
                                <li><a class="dropdown-item" href="{{ route('superadmin.payments.history', ['tenant' => $tenant->id, 'status' => 'failed']) }}">Failed</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if(isset($tenant->data['payment_history']) && count($tenant->data['payment_history']) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Plan</th>
                                        <th>Status</th>
                                        <th>Transaction ID</th>
                                        <th>Payment Method</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tenant->data['payment_history'] as $payment)
                                        <tr>
                                            <td>{{ isset($payment['date']) ? \Carbon\Carbon::parse($payment['date'])->format('M d, Y') : 'N/A' }}</td>
                                            <td>${{ isset($payment['amount']) ? number_format($payment['amount'], 2) : '0.00' }}</td>
                                            <td>
                                                @if(isset($payment['plan']))
                                                    <span class="badge bg-info">{{ ucfirst($payment['plan']) }}</span>
                                                @else
                                                    <span class="badge bg-secondary">N/A</span>
                                                @endif
                                            </td>
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
                                            <td>
                                                <small class="text-muted">{{ $payment['transaction_id'] ?? 'N/A' }}</small>
                                            </td>
                                            <td>{{ ucfirst($payment['payment_method'] ?? 'N/A') }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="actionsDropdown{{ $payment['transaction_id'] ?? '' }}" data-bs-toggle="dropdown" aria-expanded="false">
                                                        Actions
                                                    </button>
                                                    <ul class="dropdown-menu" aria-labelledby="actionsDropdown{{ $payment['transaction_id'] ?? '' }}">
                                                        <li>
                                                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#paymentDetailsModal{{ $payment['transaction_id'] ?? '' }}">
                                                                <i class="bi bi-eye"></i> View Details
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="#" onclick="window.print()">
                                                                <i class="bi bi-printer"></i> Print Receipt
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>

                                                <!-- Payment Details Modal -->
                                                <div class="modal fade" id="paymentDetailsModal{{ $payment['transaction_id'] ?? '' }}" tabindex="-1" aria-labelledby="paymentDetailsModalLabel{{ $payment['transaction_id'] ?? '' }}" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="paymentDetailsModalLabel{{ $payment['transaction_id'] ?? '' }}">Payment Details</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <table class="table table-bordered">
                                                                    <tr>
                                                                        <th style="width: 30%">Date</th>
                                                                        <td>{{ isset($payment['date']) ? \Carbon\Carbon::parse($payment['date'])->format('M d, Y H:i') : 'N/A' }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Amount</th>
                                                                        <td>${{ isset($payment['amount']) ? number_format($payment['amount'], 2) : '0.00' }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Plan</th>
                                                                        <td>{{ ucfirst($payment['plan'] ?? 'N/A') }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Status</th>
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
                                                                    <tr>
                                                                        <th>Transaction ID</th>
                                                                        <td><small class="text-muted">{{ $payment['transaction_id'] ?? 'N/A' }}</small></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Payment Method</th>
                                                                        <td>{{ ucfirst($payment['payment_method'] ?? 'N/A') }}</td>
                                                                    </tr>
                                                                    @if(isset($payment['notes']))
                                                                        <tr>
                                                                            <th>Notes</th>
                                                                            <td>{{ $payment['notes'] }}</td>
                                                                        </tr>
                                                                    @endif
                                                                </table>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                <button type="button" class="btn btn-primary" onclick="window.print()">Print Receipt</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">No payment history available for this tenant.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.avatar-initial {
    background-color: #e9ecef;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endpush
@endsection 