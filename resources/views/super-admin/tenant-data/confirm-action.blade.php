@extends('superadmin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Confirm Action: {{ $tenant->tenant_name }}</h1>
                <div>
                    <a href="{{ route('super-admin.tenant-data.manage-database', $tenant->id) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Back to Database Management
                    </a>
                </div>
            </div>
            <div class="text-muted">Tenant ID: {{ $tenant->id }}</div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Confirm Destructive Action</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle mr-2"></i> <strong>Warning!</strong> This action cannot be undone.
                    </div>
                    
                    <p class="lead">{{ $message }}</p>
                    
                    <p>Please confirm that you want to proceed with this action for tenant: <strong>{{ $tenant->tenant_name }}</strong></p>
                    
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('super-admin.tenant-data.manage-database', $tenant->id) }}" class="btn btn-secondary">
                            Cancel
                        </a>
                        
                        <form action="{{ route('super-admin.tenant-data.database-action', $tenant->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="action" value="{{ $action }}">
                            <input type="hidden" name="confirmed" value="1">
                            <button type="submit" class="btn btn-danger">
                                {{ $actionText }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 