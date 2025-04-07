@extends('SuperAdmin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-2 text-gray-800">Tenant Database Management</h1>
            <p class="mb-4">Manage and configure databases for all tenant accounts in the system.</p>
        </div>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {!! session('success') !!}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {!! session('error') !!}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Database Actions Dashboard -->
    <div class="row mb-4">
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-primary">
                    <h6 class="m-0 font-weight-bold text-white">System Check</h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">Verify MySQL connection status before performing database operations.</p>
                    <a href="{{ route('super-admin.system-check.mysql') }}" class="btn btn-info btn-block">
                        <i class="fas fa-database mr-2"></i> Check MySQL Connection
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8 col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-success">
                    <h6 class="m-0 font-weight-bold text-white">Database Operations</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 mb-2">
                            <a href="{{ route('super-admin.tenant-data.run-batched-migration') }}" class="btn btn-outline-success btn-block">
                                <i class="fas fa-database mr-1"></i> Batched Migration
                            </a>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <a href="{{ route('super-admin.tenant-data.auto-setup') }}" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-magic mr-1"></i> Auto Setup
                            </a>
                        </div>
                        <div class="col-lg-4 mb-2">
                            <a href="{{ route('super-admin.tenant-data.auto-migrate') }}" class="btn btn-outline-warning btn-block">
                                <i class="fas fa-sync mr-1"></i> Auto Migrate
                            </a>
                        </div>
                    </div>
                    <div class="mt-3">
                        <p class="text-sm text-muted mb-0">
                            <i class="fas fa-info-circle mr-1"></i> These operations affect all tenant databases. Use with caution.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tenant Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-light">
            <h6 class="m-0 font-weight-bold text-primary">Tenant Databases</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="tenantTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>Tenant ID</th>
                            <th>Tenant Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th class="text-center">Database</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tenants as $tenant)
                        <tr>
                            <td>{{ $tenant->id }}</td>
                            <td>{{ $tenant->tenant_name }}</td>
                            <td>{{ $tenant->tenant_email }}</td>
                            <td>
                                <span class="badge badge-{{ $tenant->status == 'active' ? 'success' : ($tenant->status == 'pending' ? 'warning' : 'danger') }} py-1 px-2 text-dark">
                                    {{ ucfirst($tenant->status) }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($tenant->tenantDatabase)
                                    <span class="badge badge-info py-1 px-2 text-dark">
                                        <i class="fas fa-check-circle mr-1"></i> Configured
                                    </span>
                                @else
                                    <span class="badge badge-danger py-1 px-2 text-dark">
                                        <i class="fas fa-exclamation-triangle mr-1"></i> Not Set Up
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('super-admin.tenant-data.view', $tenant->id) }}" 
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-database mr-1"></i> Manage
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
@endsection