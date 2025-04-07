@extends('SuperAdmin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Manage Database: {{ $tenant->tenant_name }}</h1>
                <div>
                    <a href="{{ route('super-admin.tenant-data.view', $tenant->id) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Back to Tenant Data
                    </a>
                </div>
            </div>
            <div class="text-muted">Tenant ID: {{ $tenant->id }}</div>
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
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Database Information</h5>
                </div>
                <div class="card-body">
                    @if($tenant->tenantDatabase)
                        <div class="alert {{ $databaseExists ? 'alert-success' : 'alert-danger' }}">
                            @if($databaseExists)
                                <i class="fas fa-check-circle mr-1"></i> Database exists on server
                            @else
                                <i class="fas fa-exclamation-triangle mr-1"></i> Database does not exist on server
                            @endif
                        </div>
                        <table class="table table-bordered">
                            <tr>
                                <th>Database Name</th>
                                <td>{{ $tenant->tenantDatabase->database_name }}</td>
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
                                <td>{{ $tenant->tenantDatabase->database_username }}</td>
                            </tr>
                        </table>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle mr-1"></i> No database configured for this tenant.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Database Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Create Database</h5>
                                    <p class="card-text">Create a new separate database for this tenant.</p>
                                    <form action="{{ route('super-admin.tenant-data.database-action', $tenant->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="action" value="create">
                                        <button type="submit" class="btn btn-primary" {{ $tenant->tenantDatabase ? 'disabled' : '' }}>
                                            <i class="fas fa-database mr-1"></i> Create Database
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Create Tables</h5>
                                    <p class="card-text">Create tables in the tenant database using direct migrations.</p>
                                    <form action="{{ route('super-admin.tenant-data.database-action', $tenant->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="action" value="migrate">
                                        <button type="submit" class="btn btn-success" {{ !$databaseExists ? 'disabled' : '' }}>
                                            <i class="fas fa-table mr-1"></i> Create Tables
                                        </button>
                                    </form>
                                    <form action="{{ route('super-admin.tenant-data.database-action', $tenant->id) }}" method="POST" class="mt-2">
                                        @csrf
                                        <input type="hidden" name="action" value="auto-migrate">
                                        <button type="submit" class="btn btn-info" {{ !$databaseExists ? 'disabled' : '' }}>
                                            <i class="fas fa-sync mr-1"></i> Auto Migrate Database
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Seed Database</h5>
                                    <p class="card-text">Add initial data to the database.</p>
                                    <form action="{{ route('super-admin.tenant-data.database-action', $tenant->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="action" value="seed">
                                        <button type="submit" class="btn btn-success" {{ !$tenant->tenantDatabase || !$databaseExists ? 'disabled' : '' }}>
                                            <i class="fas fa-seedling mr-1"></i> Seed Database
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Backup Database</h5>
                                    <p class="card-text">Create a backup of the database.</p>
                                    <form action="{{ route('super-admin.tenant-data.database-action', $tenant->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="action" value="backup">
                                        <button type="submit" class="btn btn-primary" {{ !$tenant->tenantDatabase || !$databaseExists ? 'disabled' : '' }}>
                                            <i class="fas fa-save mr-1"></i> Backup Database
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <h5 class="text-danger">Danger Zone</h5>
                        <div class="card border-danger">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <h5 class="card-title">Fresh Migrations</h5>
                                        <p class="card-text">Wipe the database and run fresh migrations.</p>
                                        <form action="{{ route('super-admin.tenant-data.database-action', $tenant->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="action" value="fresh">
                                            <button type="submit" class="btn btn-warning" {{ !$tenant->tenantDatabase || !$databaseExists ? 'disabled' : '' }}>
                                                <i class="fas fa-sync mr-1"></i> Run Fresh Migrations
                                            </button>
                                        </form>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <h5 class="card-title">Drop Database</h5>
                                        <p class="card-text">Completely delete the tenant database.</p>
                                        <form action="{{ route('super-admin.tenant-data.database-action', $tenant->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="action" value="drop">
                                            <button type="submit" class="btn btn-danger" {{ !$tenant->tenantDatabase ? 'disabled' : '' }}>
                                                <i class="fas fa-trash mr-1"></i> Drop Database
                                            </button>
                                        </form>
                                    </div>
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