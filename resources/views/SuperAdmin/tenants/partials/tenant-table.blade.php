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
                        
                        if ($plan == 'premium') {
                            $planClass = 'primary';
                        } elseif ($plan == 'enterprise') {
                            $planClass = 'dark';
                        }
                    @endphp
                    <span class="badge bg-{{ $planClass }}">
                        {{ ucfirst($plan) }}
                    </span>
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
                    <span class="badge bg-{{ isset($tenant->data['payment_status']) && $tenant->data['payment_status'] === 'paid' ? 'success' : 'danger' }}">
                        {{ ucfirst($tenant->data['payment_status'] ?? 'Unpaid') }}
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