@extends('superadmin.layouts.app')

@section('title', 'Subscription Logs')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">Subscription Logs</h1>
        <div>
            <a href="{{ route('superadmin.payments.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Payments
            </a>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Subscription Plan Changes</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Tenant</th>
                            <th>Email</th>
                            <th>Current Plan</th>
                            <th>Previous Plan</th>
                            <th>Changed At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tenants as $tenant)
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
                                    @if(isset($tenant->data['previous_plan']))
                                        <span class="badge bg-secondary">{{ ucfirst($tenant->data['previous_plan']) }}</span>
                                    @else
                                        <span class="badge bg-secondary">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if(isset($tenant->data['plan_changed_at']))
                                        {{ \Carbon\Carbon::parse($tenant->data['plan_changed_at'])->format('M d, Y H:i:s') }}
                                    @else
                                        {{ $tenant->updated_at->format('M d, Y H:i:s') }}
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('superadmin.payments.show', $tenant->id) }}" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No subscription logs found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $tenants->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 