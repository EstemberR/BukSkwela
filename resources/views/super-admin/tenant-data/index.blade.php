@extends('SuperAdmin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Database System Check</h5>
                </div>
                <div class="card-body">
                    <p>Check MySQL connection status before running tenant migrations:</p>
                    <a href="{{ route('super-admin.system-check.mysql') }}" class="btn btn-info btn-lg btn-block w-100">
                        <i class="fas fa-database mr-2"></i> Check MySQL Connection Status
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Safe Migration Options</h5>
                </div>
                <div class="card-body">
                    <p>Run migrations in batches to prevent connection issues:</p>
                    <a href="{{ route('super-admin.tenant-data.run-batched-migration') }}" class="btn btn-success btn-lg btn-block w-100">
                        <i class="fas fa-database mr-2"></i> Run Batched Migration (Recommended)
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {!! session('success') !!}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {!! session('error') !!}
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Tenant Data Management</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Tenant ID</th>
                                    <th>Tenant Name</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tenants as $tenant)
                                <tr>
                                    <td>{{ $tenant->id }}</td>
                                    <td>{{ $tenant->tenant_name }}</td>
                                    <td>{{ $tenant->tenant_email }}</td>
                                    <td>
                                        <span class="badge bg-{{ $tenant->status == 'active' ? 'success' : ($tenant->status == 'pending' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($tenant->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('super-admin.tenant-data.view', $tenant->id) }}" 
                                           class="btn btn-sm btn-primary">
                                            View Data
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card bg-warning h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">Database Management</h5>
                    <p class="card-text">Create and manage tenant-specific databases</p>
                    <div class="mt-auto">
                        <a href="{{ route('super-admin.tenant-data.run-batched-migration') }}" class="btn btn-success btn-lg btn-block w-100">
                            <i class="fas fa-database"></i> Create All Tenant Databases (Batched)
                        </a>
                        <a href="{{ route('super-admin.tenant-data.auto-setup') }}" class="btn btn-primary btn-lg btn-block w-100 mt-2">
                            <i class="fas fa-magic"></i> Auto Setup Tenant Databases
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 