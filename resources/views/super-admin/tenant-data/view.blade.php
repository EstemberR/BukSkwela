@extends('SuperAdmin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tenant Data: {{ $tenant->tenant_name }}</h3>
                </div>
                <div class="card-body">
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
                    
                    <div class="alert alert-info">
                        <strong>Database Information:</strong>
                        <ul>
                            <li><strong>Database Name:</strong> {{ $tenant->tenantDatabase->database_name ?? 'Not configured' }}</li>
                            <li><strong>Host:</strong> {{ $tenant->tenantDatabase->database_host ?? 'Not configured' }}</li>
                            <li><strong>Port:</strong> {{ $tenant->tenantDatabase->database_port ?? 'Not configured' }}</li>
                            <li><strong>Username:</strong> {{ $tenant->tenantDatabase->database_username ?? 'Default root user' }}</li>
                        </ul>
                        <p>This tenant uses a separate database for better isolation and performance.</p>
                    </div>

                    <div class="mb-3">
                        <form action="{{ route('super-admin.tenant-data.manage-database', ['tenant' => $tenant->id]) }}" method="POST">
                            @csrf
                            <input type="hidden" name="action" value="migrate">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-database"></i> Create Tables Directly
                            </button>
                        </form>
                    </div>
                    
                    @if(count($tables) > 0)
                        <h4>Tables in Tenant Database</h4>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Table Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tables as $fullTableName => $tableName)
                                <tr>
                                    <td>{{ $tableName }}</td>
                                    <td>
                                        <a href="{{ route('super-admin.tenant-data.table', ['tenant' => $tenant->id, 'table' => $tableName]) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-table"></i> View Data
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="alert alert-warning">
                            <h5><i class="icon fas fa-exclamation-triangle"></i> No tables found for this tenant.</h5>
                            <p>The database exists but has no tables. Use the "Manage Database" button to create tables.</p>
                        </div>
                        
                        <div class="card mb-3">
                            <div class="card-header bg-secondary">
                                <h5 class="card-title">Check Database</h5>
                            </div>
                            <div class="card-body">
                                <p>Verify with direct SQL if tables exist:</p>
                                <pre>SHOW TABLES FROM `{{ $tenant->tenantDatabase->database_name ?? 'tenant_database' }}`;</pre>
                                <hr>
                                <p>Verify if the database exists:</p>
                                <pre>SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '{{ $tenant->tenantDatabase->database_name ?? 'tenant_database' }}';</pre>
                            </div>
                        </div>
                    @endif
                    
                    <div class="mt-3">
                        <a href="{{ route('super-admin.tenant-data.check-database', $tenant->id) }}" class="btn btn-warning">
                            <i class="fas fa-search"></i> Check Database
                        </a>
                        <a href="{{ route('super-admin.tenant-data.manage-database', $tenant->id) }}" class="btn btn-info">
                            <i class="fas fa-database"></i> Manage Database
                        </a>
                        <a href="{{ route('super-admin.tenant-data.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Tenant List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 