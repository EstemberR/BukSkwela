@extends('SuperAdmin.layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Tenant Database</h1>
            <p class="mb-0 text-gray-600">{{ $tenant->tenant_name }} <span class="badge badge-primary text-dark">{{ $tenant->id }}</span></p>
        </div>
        <a href="{{ route('super-admin.tenant-data.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 mr-1"></i> Back to Tenant List
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
        <div class="col-xl-4 col-lg-5 mb-4">
            <!-- Database Info Card -->
            <div class="card shadow mb-4 border-left-info">
                <div class="card-header py-3 bg-gradient-info">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-database mr-1"></i> Database Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm mb-0">
                            <tr class="bg-light">
                                <th width="40%">Database Name</th>
                                <td><code>{{ $tenant->tenantDatabase->database_name ?? 'Not configured' }}</code></td>
                            </tr>
                            <tr>
                                <th>Host</th>
                                <td>{{ $tenant->tenantDatabase->database_host ?? 'Not configured' }}</td>
                            </tr>
                            <tr>
                                <th>Port</th>
                                <td>{{ $tenant->tenantDatabase->database_port ?? 'Not configured' }}</td>
                            </tr>
                            <tr>
                                <th>Username</th>
                                <td><code>{{ $tenant->tenantDatabase->database_username ?? 'Default root user' }}</code></td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="mt-3 alert alert-info py-2 shadow-sm">
                        <small class="text-dark"><i class="fas fa-info-circle mr-1"></i> This tenant uses a separate database for better isolation and performance.</small>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions Card -->
            <div class="card shadow border-left-success">
                <div class="card-header py-3 bg-gradient-success">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-tools mr-1"></i> Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 mb-2">
                            <form action="{{ route('super-admin.tenant-data.manage-database', ['tenant' => $tenant->id]) }}" method="POST">
                                @csrf
                                <input type="hidden" name="action" value="migrate">
                                <button type="submit" class="btn btn-success btn-sm btn-block">
                                    <i class="fas fa-database mr-1"></i> Create Tables
                                </button>
                            </form>
                        </div>
                        <div class="col-6 mb-2">
                            <a href="{{ route('super-admin.tenant-data.check-database', $tenant->id) }}" class="btn btn-warning btn-sm btn-block">
                                <i class="fas fa-search mr-1"></i> Check Database
                            </a>
                        </div>
                        <div class="col-12">
                            <a href="{{ route('super-admin.tenant-data.manage-database', $tenant->id) }}" class="btn btn-primary btn-block">
                                <i class="fas fa-cogs mr-1"></i> Manage Database
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-8 col-lg-7 mb-4">
            <!-- Tables Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-gradient-primary">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-table mr-1"></i> Database Tables
                    </h6>
                </div>
                <div class="card-body">
                    @if(count($tables) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-sm" id="dataTable" width="100%" cellspacing="0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Table Name</th>
                                        <th width="150" class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tables as $fullTableName => $tableName)
                                    <tr>
                                        <td><code>{{ $tableName }}</code></td>
                                        <td class="text-center">
                                            <a href="{{ route('super-admin.tenant-data.table', ['tenant' => $tenant->id, 'table' => $tableName]) }}" 
                                               class="btn btn-sm btn-primary">
                                                <i class="fas fa-table mr-1"></i> View Data
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-warning shadow-sm mb-4">
                            <h6 class="font-weight-bold text-dark"><i class="icon fas fa-exclamation-triangle mr-1"></i> No tables found</h6>
                            <p class="mb-0 text-dark">The database exists but has no tables. Use the "Create Tables" or "Manage Database" buttons to set up database tables.</p>
                        </div>
                        
                        <div class="card bg-light shadow-sm">
                            <div class="card-header py-3 bg-secondary text-white">
                                <h6 class="m-0 font-weight-bold">SQL Verification Commands</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <h6 class="font-weight-bold text-gray-700">Check Tables:</h6>
                                    <div class="bg-dark text-white p-2 rounded">
                                        <code>SHOW TABLES FROM `{{ $tenant->tenantDatabase->database_name ?? 'tenant_database' }}`;</code>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="font-weight-bold text-gray-700">Verify Database Exists:</h6>
                                    <div class="bg-dark text-white p-2 rounded">
                                        <code>SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{{ $tenant->tenantDatabase->database_name ?? 'tenant_database' }}';</code>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "pageLength": 10,
            "order": [[ 0, "asc" ]]
        });
    });
</script>
@endpush

@endsection 