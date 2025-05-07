@extends('SuperAdmin.layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Database Management</h1>
            <p class="mb-0 text-gray-600">Tenant: {{ $tenant->tenant_name }} <span class="badge badge-primary text-dark">{{ $tenant->id }}</span></p>
        </div>
        <a href="{{ route('super-admin.tenant-data.view', $tenant->id) }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Back to Tenant Data
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm">
            <i class="fas fa-check-circle mr-1"></i> {!! session('success') !!}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm">
            <i class="fas fa-exclamation-circle mr-1"></i> {!! session('error') !!}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <!-- Database Information Card -->
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-gradient-primary">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-info-circle mr-1"></i> Database Information
                    </h6>
                </div>
                <div class="card-body">
                    @if($tenant->tenantDatabase)
                        <div class="mb-3 text-center">
                            @if($databaseExists)
                                <div class="alert alert-success py-2 mb-3 shadow-sm">
                                    <i class="fas fa-check-circle mr-1"></i> <span class="text-dark">Database exists on server</span>
                                </div>
                            @else
                                <div class="alert alert-danger py-2 mb-3 shadow-sm">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> <span class="text-dark">Database does not exist on server</span>
                                </div>
                            @endif
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <tr class="bg-light">
                                    <th width="40%">Database Name</th>
                                    <td><code>{{ $tenant->tenantDatabase->database_name }}</code></td>
                                </tr>
                                <tr>
                                    <th>Host</th>
                                    <td>{{ $tenant->tenantDatabase->database_host }}</td>
                                </tr>
                                <tr>
                                    <th>Port</th>
                                    <td>{{ $tenant->tenantDatabase->database_port }}</td>
                                </tr>
                                <tr>
                                    <th>Username</th>
                                    <td><code>{{ $tenant->tenantDatabase->database_username }}</code></td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="mt-3 text-center">
                            <span class="badge badge-pill badge-light py-2 px-3 shadow-sm text-dark">
                                <i class="fas fa-lock mr-1"></i> Database password is hidden for security
                            </span>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle mr-1"></i> No database configured for this tenant.
                            <hr>
                            <p class="mb-0">Use the Create Database action to set up a new database.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Database Actions Card -->
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-gradient-info">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-database mr-1"></i> Database Actions
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Core Actions Row -->
                    <div class="row mb-4">
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card shadow-sm h-100 border-left-primary">
                                <div class="card-body py-3">
                                    <h6 class="font-weight-bold text-primary mb-2">Create Database</h6>
                                    <p class="card-text small mb-3">Create a new separate database for this tenant with proper credentials.</p>
                                    <form action="{{ route('super-admin.tenant-data.database-action', $tenant->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="action" value="create">
                                        <button type="submit" class="btn btn-primary btn-sm btn-block" {{ $tenant->tenantDatabase ? 'disabled' : '' }}>
                                            <i class="fas fa-plus-circle mr-1"></i> Create Database
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card shadow-sm h-100 border-left-success">
                                <div class="card-body py-3">
                                    <h6 class="font-weight-bold text-success mb-2">Create Tables</h6>
                                    <p class="card-text small mb-3">Create standard tables required for tenant operation.</p>
                                    <form action="{{ route('super-admin.tenant-data.database-action', $tenant->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="action" value="migrate">
                                        <button type="submit" class="btn btn-success btn-sm btn-block" {{ !$databaseExists ? 'disabled' : '' }}>
                                            <i class="fas fa-table mr-1"></i> Create Tables
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card shadow-sm h-100 border-left-info">
                                <div class="card-body py-3">
                                    <h6 class="font-weight-bold text-info mb-2">Auto-Migrate</h6>
                                    <p class="card-text small mb-3">Automatically create & migrate the tenant database in one step.</p>
                                    <form action="{{ route('super-admin.tenant-data.database-action', $tenant->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="action" value="auto-migrate">
                                        <button type="submit" class="btn btn-info btn-sm btn-block">
                                            <i class="fas fa-sync mr-1"></i> Auto-Migrate
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Actions Row -->
                    <div class="row mb-4">
                        <div class="col-lg-6 col-md-6 mb-3">
                            <div class="card shadow-sm h-100 border-left-warning">
                                <div class="card-body py-3">
                                    <h6 class="font-weight-bold text-warning mb-2">Recreate Database</h6>
                                    <p class="card-text small mb-3">Drop and recreate the tenant database with fresh tables.</p>
                                    <form action="{{ route('super-admin.tenant-data.database-action', $tenant->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="action" value="recreate">
                                        <button type="submit" class="btn btn-warning btn-sm btn-block" {{ !$databaseExists ? 'disabled' : '' }} onclick="return confirm('Are you sure you want to recreate the database? All data will be lost.')">
                                            <i class="fas fa-sync-alt mr-1"></i> Recreate Database
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-6 mb-3">
                            <div class="card shadow-sm h-100 border-left-danger">
                                <div class="card-body py-3">
                                    <h6 class="font-weight-bold text-danger mb-2">Drop Database</h6>
                                    <p class="card-text small mb-3">Completely delete the tenant database from the server.</p>
                                    <form action="{{ route('super-admin.tenant-data.database-action', $tenant->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="action" value="drop">
                                        <button type="submit" class="btn btn-danger btn-sm btn-block" {{ !$databaseExists ? 'disabled' : '' }}>
                                            <i class="fas fa-trash-alt mr-1"></i> Drop Database
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Database Status Box -->
                    <div class="card bg-light border-0 shadow-sm">
                        <div class="card-body py-3">
                            <div class="d-sm-flex align-items-center">
                                <div class="mr-3">
                                    <i class="fas fa-database fa-2x text-gray-400"></i>
                                </div>
                                <div>
                                    <h6 class="font-weight-bold mb-1">Database Status</h6>
                                    <p class="mb-0 small text-dark">
                                        @if($databaseExists) 
                                            Database <code>{{ $tenant->tenantDatabase->database_name ?? 'Not configured' }}</code> is properly set up as a separate database outside the main BukSkwela database.
                                        @else
                                            No database is currently configured for this tenant, or the database does not exist on the server.
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection 